<?php

require_once __DIR__ . '/../../core/Database.php';

class Announcement
{
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
