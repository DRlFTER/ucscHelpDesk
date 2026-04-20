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
    $sql = "INSERT INTO tickets (created_at, title, flag, u_id, status, priority, description, meeting_requested, division, t_type, expiration_date)
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $title = $data['title'];
        $flag = $data['flag'] ?? null;
        $expiration_date = $data['expiration_date'] ?? null;
        $u_id = (int)$data['u_id'];
        // Category string from UI (used only for mapping to division)
        $category = trim($data['category']);
        $status = $data['status'] ?? 'pending';
        $priority = $data['priority'];
        $description = $data['description'];
        $meetingRequested = $data['meeting_requested'] ?? null;
        $t_type = $data['type'] ?? 'private';

        // Map category to division id via canonical map first (handles minor name differences)
        $canonMap = [
            'general administration' => ['id' => 1, 'label' => 'General Administration'],
            'establishment' => ['id' => 2, 'label' => 'Establishment'],
            'academic publication and welfare' => ['id' => 3, 'label' => 'Academic Publication and Welfare'],
            'postgraduate research and project' => ['id' => 4, 'label' => 'Postgraduate Research and Project'],
            'examination and registration' => ['id' => 5, 'label' => 'Examination and Registration'],
            'examinations and registration' => ['id' => 5, 'label' => 'Examination and Registration'],
            'engineering' => ['id' => 6, 'label' => 'Engineering'],
            'finance' => ['id' => 7, 'label' => 'Finance'],
            'library' => ['id' => 8, 'label' => 'Library'],
            'csc and noc' => ['id' => 9, 'label' => 'CSC and NOC'],
            'counselling' => ['id' => 10, 'label' => 'Counsellor']
        ];
        $key = strtolower(trim($category));
        $divisionId = 0;
        if (isset($canonMap[$key])) {
            $divisionId = (int)$canonMap[$key]['id'];
            // normalize category label to canonical
            $category = $canonMap[$key]['label'];
        } else {
            // Fallback: attempt DB lookup by name
            $q = $conn->prepare('SELECT did, name FROM division WHERE LOWER(name) = LOWER(?) LIMIT 1');
            if ($q) {
                $q->bind_param('s', $category);
                if ($q->execute()) {
                    $r = $q->get_result()->fetch_assoc();
                    if ($r && isset($r['did'])) { $divisionId = (int)$r['did']; $category = $r['name']; }
                }
                $q->close();
            }
        }

    $stmt->bind_param('ssissssiss', $title, $flag, $u_id, $status, $priority, $description, $meetingRequested, $divisionId, $t_type, $expiration_date);
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
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS division_name, t.status, t.priority
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE t.u_id = ?
                ORDER BY t.created_at DESC
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
            // keep shape: map division_name -> category for UI compatibility
            $row['category'] = $row['division_name'] ?? '';
            unset($row['division_name']);
            $rows[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function getByIdForUser(int $ticket_id, int $u_id): ?array
    {
        $conn = self::getConnection();
    $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.description, t.meeting_requested, t.t_type, t.flag, t.expiration_date
        FROM tickets t
        LEFT JOIN division d ON d.did = t.division
        WHERE t.ticket_id = ? AND (t.u_id = ? OR t.t_type = 'public')
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

        // Recent tickets (category removed from schema; show division name as category)
        $sql1 = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE t.u_id = ?
                ORDER BY t.created_at DESC
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

    // Add this method to models/student/Ticket.php (StudentTicket class)
// This handles associating a file with the newly created ticket.kavindu added this

public function addFile(int $ticket_id, string $file_path, string $original_name = '', int $uId = 0): bool
{
    $conn = self::getConnection();
    // Uses the new unified attachments table
    $sql = "INSERT INTO attachments (entity_type, entity_id, file_name, file_path, file_size, file_type, uploaded_by, uploaded_at) VALUES ('ticket', ?, ?, ?, 0, 'unknown', ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('issi', $ticket_id, $original_name, $file_path, $uId);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}
}
