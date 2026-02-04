<?php

require_once __DIR__ . '/../../core/config.php';

class KB {
    /**
     * Fetch all knowledge base articles, grouped-ready
     */
    public function getAllArticles() {
        $db = Database::getInstance();
        $sql = "SELECT base_id, topic, description, section, updated, created_by, type FROM knowledgebase ORDER BY section ASC, updated DESC";
        $res = $db->query($sql);
        $articles = [];
        while ($row = $res->fetch_assoc()) {
            $articles[] = $row;
        }
        return $articles;
    }

    /**
     * Fetch files for a single article (for downloads) - Graceful if table/columns missing
     * FIXED: Select all needed fields (file_type, file_size too)
     */
    public function getFilesByArticle($base_id) {
        try {
            $db = Database::getInstance();
            $sql = "SELECT file_id, file_name, file_path, file_type, file_size FROM kb_files WHERE kb_id = ? ";
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return [];  // Table/columns missing? Skip silently
            }
            $stmt->bind_param('i', $base_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $files = [];
            while ($row = $res->fetch_assoc()) {
                $files[] = $row;
            }
            $stmt->close();
            return $files;
        } catch (mysqli_sql_exception $e) {
            error_log("File query failed (missing schema?): " . $e->getMessage());  // Log but don't crash
            return [];  // Graceful fallback
        }
    }

    /**
     * Delete article (and associated files) - Robust if files table missing
     * FIXED: Consistent table/column names
     */
    public function deleteArticle($base_id) {
        $db = Database::getInstance();
        
        // Delete files first (safe if no table)
        $files = $this->getFilesByArticle($base_id);
        foreach ($files as $file) {
            // Optionally: unlink full server path if web-relative
            $fullPath = __DIR__ . '/../../../public/' . $file['file_path'];
            if (file_exists($fullPath)) unlink($fullPath);
        }
        try {
            $sql_files = "DELETE FROM kb_files WHERE kb_id = ?";
            $stmt_files = $db->prepare($sql_files);
            if ($stmt_files) {  // Only execute if prepare succeeds
                $stmt_files->bind_param('i', $base_id);
                $stmt_files->execute();
                $stmt_files->close();
            }
        } catch (mysqli_sql_exception $e) {
            error_log("File delete failed (missing table?): " . $e->getMessage());  // Log but continue
        }
        
        // Delete article (core table should exist)
        $sql = "DELETE FROM knowledgebase WHERE base_id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('i', $base_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function create(array $data, ?array $file = null): bool {
        $db = Database::getInstance();
        $staff_id = $data['staff_id'];
        $topic = $data['topic'];
        $description = $data['description'];
        $section = $data['section'];
        $type = $data['type'];
        $sql = "INSERT INTO knowledgebase (created_by, topic, description, section, type) VALUES (?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('issss', $staff_id, $topic, $description, $section, $type);
        $ok = $stmt->execute();

        if ($ok && $file && $file['error'] === UPLOAD_ERR_OK) {
            $base_id = $stmt->insert_id;
            
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $max_size = 5 * 1024 * 1024;  // 5MB
            
            if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
                error_log("Invalid file for KB article $base_id: " . $file['name']);
                // Article created, but file invalid – proceed without file
            } else {
                // Server path for upload
                $base_upload_dir = __DIR__ . '/../../../public/uploads/kb/';
                $staff_upload_dir = $base_upload_dir . $staff_id . '/';
                if (!is_dir($staff_upload_dir)) {
                    mkdir($staff_upload_dir, 0777, true);
                }

                $file_name = time() . '_' . basename($file['name']);
                $server_file_path = $staff_upload_dir . $file_name;
                
                if (move_uploaded_file($file['tmp_name'], $server_file_path)) {
                    // Web-relative path for DB
                    $web_file_path = 'uploads/kb/' . $staff_id . '/' . $file_name;
                    
                    // Insert into consistent table: kb_files
                    $sql_file = "INSERT INTO kb_files (kb_id, file_name, file_path, file_type, file_size) 
                                 VALUES (?, ?, ?, ?, ?)";
                    $stmt_file = $db->prepare($sql_file);
                    if ($stmt_file) {
                        $stmt_file->bind_param('isssi', $base_id, $file['name'], $web_file_path, $file['type'], $file['size']);
                        $stmt_file->execute();
                        $stmt_file->close();
                    } else {
                        error_log("Failed to prepare file insert for KB $base_id (missing table/columns?)");
                    }
                } else {
                    error_log("Failed to upload file for KB article $base_id");
                }
            }
        }
        $stmt->close();
        return $ok; 
    }

    /**
     * Fetch single article by ID
     */
    public function getArticleById($base_id) {
        $db = Database::getInstance();
        $sql = "SELECT base_id, topic, description, section, type, updated, created_by FROM knowledgebase WHERE base_id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('i', $base_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    /**
     * Update article
     */
    public function updateArticle($base_id, $data) {
        $db = Database::getInstance();
        $sql = "UPDATE knowledgebase SET topic = ?, description = ?, section = ?, type = ?, updated = NOW() WHERE base_id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('ssssi', $data['topic'], $data['description'], $data['section'], $data['type'], $base_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Add/replace a file for an existing article (for updates)
     * FIXED: Consistent table/columns (knowledgebase_files, base_id); added uploaded_at
     */
    public function addFile(int $base_id, ?array $file = null, int $staff_id = 0): bool {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return true; // No file? Success (no change)
        }

        if ($staff_id <= 0) {
            error_log("Invalid staff_id for KB file upload on article $base_id");
            return false;
        }

        $db = Database::getInstance();
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB (match create)

        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            error_log("Invalid file for KB article $base_id: " . $file['name']);
            return false;
        }

        // Server path for upload (match create)
        $base_upload_dir = __DIR__ . '/../../../public/uploads/kb/';
        $staff_upload_dir = $base_upload_dir . $staff_id . '/';
        if (!is_dir($staff_upload_dir)) {
            mkdir($staff_upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($file['name']);
        $server_file_path = $staff_upload_dir . $file_name;
        
        if (!move_uploaded_file($file['tmp_name'], $server_file_path)) {
            error_log("Failed to upload file for KB article $base_id");
            return false;
        }

        // Web-relative path for DB
        $web_file_path = 'uploads/kb/' . $staff_id . '/' . $file_name;
        
        // Insert into kb_files (use existing base_id)
        $sql_file = "INSERT INTO kb_files (kb_id, file_name, file_path, file_type, file_size) 
                     VALUES (?, ?, ?, ?, ?)";
        $stmt_file = $db->prepare($sql_file);
        if (!$stmt_file) {
            error_log("Failed to prepare file insert for KB $base_id (missing table/columns?)");
            return false;
        }
        $stmt_file->bind_param('isssi', $base_id, $file['name'], $web_file_path, $file['type'], $file['size']);
        $success = $stmt_file->execute();
        $stmt_file->close();
        
        return $success;
    }

}