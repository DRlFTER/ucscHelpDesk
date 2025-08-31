<?php
require_once __DIR__ . '/../../core/config.php';

class StudentTicket
{
    private static function getConnection()
    {
        $conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);
        if ($conn->connect_error) {
            die("DB Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    public function create(array $data): int
    {
        $conn = self::getConnection();
        $sql = "INSERT INTO tickets (created_at, title, u_id, category, status, priority, description, meeting_requested)
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $title = $data['title'];
        $u_id = (int)$data['u_id'];
        $category = ucfirst(strtolower(trim($data['category'])));
        $status = $data['status'] ?? 'pending';
        $priority = $data['priority'];
        $description = $data['description'];
        $meetingRequested = $data['meeting_requested'] ?? null;

        $stmt->bind_param('sisssss', $title, $u_id, $category, $status, $priority, $description, $meetingRequested);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $id = (int)$conn->insert_id;
        $stmt->close();
        $conn->close();
        return $id;
    }
}
