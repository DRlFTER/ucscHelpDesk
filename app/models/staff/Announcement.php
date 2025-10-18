
<?php

require_once __DIR__ . '/../../core/config.php';
class Announcement {
    /**
     * Get a single announcement by ID (with author/division info)
     */
    public function getById($id)
    {
        $db = Database::getInstance();
        $sql = "SELECT a.id, a.topic, a.content, a.date_time, u.name AS staff_name, d.name AS division_name FROM announcement a JOIN users u ON a.u_id = u.u_id LEFT JOIN staff_division sd ON u.u_id = sd.u_id LEFT JOIN division d ON sd.did = d.did WHERE a.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * Update an announcement's topic/content by ID
     */
    public function update($id, $topic, $content)
    {
        $db = Database::getInstance();
        $sql = "UPDATE announcement SET topic = ?, content = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssi', $topic, $content, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Delete an announcement by ID
     */
    public function delete($id)
    {
        $db = Database::getInstance();
        $sql = "DELETE FROM announcement WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Get attached files for an announcement
     */
    public function getFiles($announcement_id)
    {
        $db = Database::getInstance();
        $sql = "SELECT file_name, file_path, file_type, file_size FROM announcement_files WHERE announcement_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $announcement_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $files = [];
        while ($row = $res->fetch_assoc()) {
            $files[] = $row;
        }
        $stmt->close();
        return $files;
    }
    /** @var string|null */
    private $lastError = null;
    /**
     * Get all announcements with staff and division info.
     * @return array
     */
    public function getAll()
    {
        $db = Database::getInstance();

    $sql = "SELECT a.id, a.topic, a.content, a.date_time, u.name AS staff_name, d.name AS division_name
        FROM announcement a
        JOIN users u ON a.u_id = u.u_id
        LEFT JOIN staff_division sd ON u.u_id = sd.u_id
        LEFT JOIN division d ON sd.did = d.did
        ORDER BY a.date_time DESC";

        $res = $db->query($sql);
        if ($res === false) {
            $this->lastError = $db->error;
            error_log('Announcement query failed: ' . $this->lastError);
            return [];
        }
        $rows = [];
        if ($res && $res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }

    /**
     * Return the last error string if query failed.
     * @return string|null
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    
}
