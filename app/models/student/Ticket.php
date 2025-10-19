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
    // Preserve the category string as provided (keep capitalization like "Examination and Registration")
    $category = trim($data['category']);
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

    public function getRecentByUser(int $u_id, int $limit = 5): array
    {
        $conn = self::getConnection();
        $sql = "SELECT ticket_id, created_at, title, category, status, priority
                FROM tickets
                WHERE u_id = ?
                ORDER BY created_at DESC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ii', $u_id, $limit);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function getByIdForUser(int $ticket_id, int $u_id): ?array
    {
        $conn = self::getConnection();
        $sql = "SELECT ticket_id, created_at, title, category, status, priority, description, meeting_requested
                FROM tickets
                WHERE ticket_id = ? AND u_id = ?
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ii', $ticket_id, $u_id);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc() ?: null;
        $stmt->close();
        $conn->close();
        return $row;
    }
    
    public function deleteByIdForUser(int $ticket_id, int $u_id): bool
    {
        $conn = self::getConnection();
        $sql = "DELETE FROM tickets WHERE ticket_id = ? AND u_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            throw new Exception('Failed to prepare delete statement');
        }
        $stmt->bind_param('ii', $ticket_id, $u_id);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        return $ok && $affected > 0;
    }

    /**
     * Count tickets considered "open" for a user. Here, any status not equal to 'resolved' counts as open.
     */
    public function countOpenByUser(int $u_id): int
    {
        $conn = self::getConnection();
        $sql = "SELECT COUNT(*) AS c FROM tickets WHERE u_id = ? AND LOWER(status) <> 'resolved'";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('i', $u_id);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $conn->close();
            throw new Exception('Execute failed: ' . $err);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return (int)($row['c'] ?? 0);
    }

    /**
     * Get the most recent activity timestamp for a user's tickets. Using created_at as activity.
     */
    public function getLastActivityByUser(int $u_id): ?string
    {
        $conn = self::getConnection();
        $sql = "SELECT MAX(created_at) AS last FROM tickets WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('i', $u_id);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $conn->close();
            throw new Exception('Execute failed: ' . $err);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row && !empty($row['last']) ? $row['last'] : null;
    }

    /**
     * Fetch dashboard data in a single connection: recent tickets, open count and last activity.
     * Returns ['recent' => array, 'openCount' => int, 'lastActivity' => ?string]
     */
    public function getDashboardData(int $u_id, int $limit = 5): array
    {
        $conn = self::getConnection();
        $data = ['recent' => [], 'openCount' => 0, 'lastActivity' => null];

        // Recent tickets
        $sql1 = "SELECT ticket_id, created_at, title, category, status, priority
                FROM tickets
                WHERE u_id = ?
                ORDER BY created_at DESC
                LIMIT ?";
        $stmt1 = $conn->prepare($sql1);
        if ($stmt1) {
            $stmt1->bind_param('ii', $u_id, $limit);
            if ($stmt1->execute()) {
                $res = $stmt1->get_result();
                while ($row = $res->fetch_assoc()) {
                    $data['recent'][] = $row;
                }
            }
            $stmt1->close();
        }

        // Open count
        $sql2 = "SELECT COUNT(*) AS c FROM tickets WHERE u_id = ? AND LOWER(status) <> 'resolved'";
        $stmt2 = $conn->prepare($sql2);
        if ($stmt2) {
            $stmt2->bind_param('i', $u_id);
            if ($stmt2->execute()) {
                $res = $stmt2->get_result();
                $row = $res->fetch_assoc();
                $data['openCount'] = (int)($row['c'] ?? 0);
            }
            $stmt2->close();
        }

        // Last activity
        $sql3 = "SELECT MAX(created_at) AS last FROM tickets WHERE u_id = ?";
        $stmt3 = $conn->prepare($sql3);
        if ($stmt3) {
            $stmt3->bind_param('i', $u_id);
            if ($stmt3->execute()) {
                $res = $stmt3->get_result();
                $row = $res->fetch_assoc();
                $data['lastActivity'] = $row && !empty($row['last']) ? $row['last'] : null;
            }
            $stmt3->close();
        }

        $conn->close();
        return $data;
    }
}
