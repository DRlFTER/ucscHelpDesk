
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
  public function getFiles(int $announcement_id): array
{
    $db = Database::getInstance();
    $sql = "SELECT file_name, file_path, file_type, file_size FROM announcement_files WHERE announcement_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $files = [];
    while ($row = $result->fetch_assoc()) {
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

/**
 * Get divisions for a specific staff member.
 */
/**
 * Get divisions for a specific staff member.
 */
public function getStaffDivisions(int $staff_id): array
{
    $db = Database::getInstance();
    $sql = "SELECT d.did, d.name 
            FROM division d
            JOIN staff_division sd ON d.did = sd.did  # Fixed: 'sd.did' instead of 'sd.d_id'
            WHERE sd.u_id = ?
            ORDER BY d.name";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $divisions = [];
    while ($row = $result->fetch_assoc()) {
        $divisions[] = $row;
    }
    $stmt->close();
    return $divisions;
}

/**
 * Create a new announcement with optional file upload.
 * Returns true on success, false on failure.
 */
public function create(array $data, ?array $file = null): bool
{
    $db = Database::getInstance();
    $staff_id = $data['staff_id'];
    $topic = $data['topic'];
    $content = $data['content'];
    $division_id = $data['division_id'];

    // Validate division association (unchanged)
    $sql = "SELECT 1 FROM staff_division WHERE u_id = ? AND did = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $staff_id, $division_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $stmt->close();
        return false;  // Invalid division
    }
    $stmt->close();

    // Insert announcement (unchanged)
    $sql = "INSERT INTO announcement (topic, content, u_id, date_time) VALUES (?, ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssi", $topic, $content, $staff_id);
    if (!$stmt->execute()) {
        $stmt->close();
        return false;
    }
    $announcement_id = $db->insert_id;
    $stmt->close();

    // Handle file upload if provided
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024;  // 5MB
        
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            error_log("Invalid file for announcement $announcement_id: " . $file['name']);
            return true;
        }

        // Server path for upload (unchanged)
        $base_upload_dir = __DIR__ . '/../../../public/uploads/announcements/';
        $staff_upload_dir = $base_upload_dir . $staff_id . '/';
        if (!is_dir($staff_upload_dir)) {
            mkdir($staff_upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($file['name']);
        $server_file_path = $staff_upload_dir . $file_name;  // Server path for move_uploaded_file()
        
        if (move_uploaded_file($file['tmp_name'], $server_file_path)) {
            // NEW: Compute web-relative path for DB (e.g., 'uploads/announcements/1/filename.pdf')
            $web_file_path = 'uploads/announcements/' . $staff_id . '/' . $file_name;
            
            // Insert web-relative path into DB
            $sql = "INSERT INTO announcement_files (announcement_id, file_name, file_path, file_type, file_size) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("isssi", $announcement_id, $file['name'], $web_file_path, $file['type'], $file['size']);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Failed to upload file for announcement $announcement_id");
        }
    }

    return true;
}
}
