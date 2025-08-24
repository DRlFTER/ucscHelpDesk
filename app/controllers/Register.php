<?php

class Register extends Controller
{
    public function index()
    {
        // If POST, attempt basic registration flow
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $role = $_POST['role'] ?? '';
                $fullName = $_POST['fullName'] ?? '';
                $userName = $_POST['userName'] ?? null; // not required for counselor form
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? ($_POST['confirmPassword'] ?? '');
                $regNumber = $_POST['regNumber'] ?? null;
                $department = $_POST['department'] ?? null;
                $staffId = $_POST['staffId'] ?? null;

                // minimal check to avoid empty required core fields
                if (!$role || !$fullName || !$email || !$password) {
                    throw new Exception('Missing required fields.');
                }

                $userModel = $this->model('User');
                $userId = $userModel->createUser($role, $fullName, $userName, $email, $password);

                switch ($role) {
                    case 'student':
                        if (!$regNumber) throw new Exception('Registration number required.');
                        $userModel->createStudent((int)$userId, $regNumber);
                        break;
                    case 'lecturer':
                        if (!$department) throw new Exception('Department required.');
                        $userModel->createLecturer((int)$userId, $department);
                        break;
                    case 'staff':
                        if (!$staffId) throw new Exception('Staff ID required.');
                        $userModel->createStaff((int)$userId, $staffId);
                        break;
                    case 'counselor':
                        $userModel->createCounselor((int)$userId);
                        break;
                    default:
                        throw new Exception('Invalid role.');
                }

                $flash = ['type' => 'success', 'message' => 'Registration successful.'];
            } catch (Throwable $e) {
                $flash = ['type' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $flash = null;
        }

        $headContent = '
            <style>
                .role-buttons button { margin: 5px; padding: 10px 15px; cursor: pointer; }
                .form-container { display: none; margin-top: 20px; }
                .form-container.active { display: block; }
                form input, form select { display: block; margin: 10px 0; padding: 8px; width: 280px; }
                .role-buttons .active-btn { background-color: #007bff; color: #fff; }
                .alert { padding: 10px 14px; border-radius: 6px; margin: 12px 0; }
                .alert.error { background: #fde2e1; color: #7a1c1c; border: 1px solid #f5b5b2; }
                .alert.success { background: #e7f7ea; color: #145c2c; border: 1px solid #bfe6c9; }
            </style>
        ';
        // render register view once; layout will wrap it
        $this->view('register', [
            'title' => 'UCSC Help Desk - Register',
            'head'  => $headContent,
            'flash' => $flash,
        ]);
    }
}
