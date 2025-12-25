<?php

class Settings extends Controller
{
    public function index()
    {
        $role = strtolower($_SESSION['user']['role'] ?? '');
        if ($role) {
            if ($role === 'counsellor') { $role = 'counselor'; }
            header('Location: /' . $role . '/settings');
            exit;
        }

        $headContent = '
        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => null,
            'roleLabel' => null,
            'roleMessage' => 'Please log in to access your settings.',
        ]);
    }

    /**
     * Get current user profile data as JSON (for AJAX)
     */
    public function getProfile()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $user = $_SESSION['user'];
        echo json_encode([
            'success' => true,
            'user' => [
                'u_id' => $user['u_id'] ?? null,
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'number' => $user['number'] ?? '',
                'role' => $user['role'] ?? '',
                'year' => $user['year'] ?? '',
                'designation' => $user['designation'] ?? '',
                'profile_photo' => $user['profile_photo'] ?? null,
            ]
        ]);
    }

    /**
     * Update user profile (AJAX endpoint)
     */
    public function updateProfile()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];
        $name = trim($input['name'] ?? '');
        $number = trim($input['number'] ?? '');

        // Validation
        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required']);
            return;
        }

        if (strlen($name) > 50) {
            http_response_code(400);
            echo json_encode(['error' => 'Name must be 50 characters or less']);
            return;
        }

        if (!empty($number) && strlen($number) > 15) {
            http_response_code(400);
            echo json_encode(['error' => 'Phone number must be 15 characters or less']);
            return;
        }

        try {
            $userModel = $this->model('User');
            $result = $userModel->updateProfile($userId, $name, $number);

            if ($result) {
                // Update session data
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['number'] = $number;

                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update profile']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload profile photo (AJAX endpoint)
     */
    public function uploadPhoto()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
            ];
            $errorCode = $_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE;
            $errorMsg = $errorMessages[$errorCode] ?? 'Unknown upload error';
            echo json_encode(['error' => $errorMsg]);
            return;
        }

        $file = $_FILES['photo'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP']);
            return;
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large. Maximum size is 5MB']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];
        
        // Generate unique filename
        $extension = match($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        $objectKey = 'profiles/' . $userId . '_' . time() . '.' . $extension;

        try {
            // Load R2 storage
            require_once __DIR__ . '/../core/R2Storage.php';
            $r2 = new R2Storage();
            
            // Delete old photo if exists
            $userModel = $this->model('User');
            $oldPhotoUrl = $userModel->getProfilePhotoUrl($userId);
            if ($oldPhotoUrl) {
                $oldKey = $r2->getObjectKeyFromUrl($oldPhotoUrl);
                if ($oldKey) {
                    $r2->deleteObject($oldKey);
                }
            }

            // Upload new photo
            $result = $r2->uploadFile($file['tmp_name'], $objectKey, $mimeType);
            
            if (!$result['success']) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload: ' . $result['error']]);
                return;
            }

            // Update database
            $updateResult = $userModel->updateProfilePhoto($userId, $result['url']);
            
            if (!$updateResult) {
                // Try to delete uploaded file if DB update fails
                $r2->deleteObject($objectKey);
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update profile']);
                return;
            }

            // Update session
            $_SESSION['user']['profile_url'] = $result['url'];

            echo json_encode([
                'success' => true,
                'url' => $result['url'],
                'message' => 'Profile photo updated successfully'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete profile photo (AJAX endpoint)
     */
    public function deletePhoto()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];

        try {
            require_once __DIR__ . '/../core/R2Storage.php';
            $r2 = new R2Storage();
            $userModel = $this->model('User');
            
            // Get current photo URL
            $currentPhotoUrl = $userModel->getProfilePhotoUrl($userId);
            
            if (!$currentPhotoUrl) {
                http_response_code(400);
                echo json_encode(['error' => 'No profile photo to delete']);
                return;
            }

            // Delete from R2
            $objectKey = $r2->getObjectKeyFromUrl($currentPhotoUrl);
            if ($objectKey) {
                $deleteResult = $r2->deleteObject($objectKey);
                if (!$deleteResult['success']) {
                    // Log error but continue - photo might already be deleted
                    error_log('R2 delete failed: ' . $deleteResult['error']);
                }
            }

            // Update database
            $updateResult = $userModel->updateProfilePhoto($userId, null);
            
            if (!$updateResult) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update profile']);
                return;
            }

            // Update session
            $_SESSION['user']['profile_url'] = null;

            echo json_encode([
                'success' => true,
                'message' => 'Profile photo removed successfully'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}