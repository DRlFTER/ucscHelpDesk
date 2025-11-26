<?php

require_once __DIR__ . '/../../core/config.php';

class KB {
    /**
     * Fetch all knowledge base articles, grouped-ready
     */
    public function getAllArticles() {
        $db = Database::getInstance();
        $sql = "SELECT base_id, topic, description, section, updated, created_by FROM knowledgebase ORDER BY section ASC, updated DESC";
        $res = $db->query($sql);
        // ===== TEMP DEBUG: ADD THIS BLOCK =====
    $count = $res ? $res->num_rows : 0;
    error_log("KB Query Rows: " . $count);  // Check your error log
    
    if ($count === 0) {
        error_log("No rows returned. Check table 'knowledgebase' has data.");
        // Optional: Test connection with a simple query
        $testRes = $db->query("SELECT 1 as test");
        error_log("DB Connection Test: " . ($testRes ? "OK" : "Failed"));
    }
    // ===== END TEMP DEBUG =====
        $articles = [];
        while ($row = $res->fetch_assoc()) {
            $articles[] = $row;
        }
        return $articles;
    }

    /**
     * Fetch files for a single article (for downloads)
     */
    public function getFilesByArticle($base_id) {
        $db = Database::getInstance();
        $sql = "SELECT file_id, file_path, file_name FROM knowledgebase_files WHERE base_id = ? ORDER BY uploaded_at DESC";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return [];
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
    }
}