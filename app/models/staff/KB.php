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
     * Fetch files for a single article (for downloads)
     */
    /**
 * Fetch files for a single article (for downloads) - Graceful if table/columns missing
 */
public function getFilesByArticle($base_id) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT file_id, file_path, file_name FROM knowledgebase_files WHERE base_id = ? ORDER BY uploaded_at DESC";
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
 */
public function deleteArticle($base_id) {
    $db = Database::getInstance();
    
    // Delete files first (safe if no table)
    $files = $this->getFilesByArticle($base_id);
    foreach ($files as $file) {
        // Optionally: unlink($file['file_path']); // Delete physical file if needed
    }
    try {
        $sql_files = "DELETE FROM knowledgebase_files WHERE base_id = ?";
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

        if ($ok && $file) {
            $base_id = $stmt->insert_id;
            $file_name = $file['name'];
            $file_path = $file['path'];
            $sql = "INSERT INTO knowledgebase_files (base_id, file_name, file_path) VALUES (?,?,?)";
            $stmt_file = $db->prepare($sql);
            $stmt_file->bind_param('iss', $base_id, $file_name, $file_path);
            $stmt_file->execute();
            $stmt_file->close();
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
     * Delete article (and associated files)
     */

}