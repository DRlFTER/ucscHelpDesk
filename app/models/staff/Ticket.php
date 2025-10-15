<?php
require_once __DIR__ . '/../../core/config.php';

class StaffTicket
{
    private static function getConnection()
    {
        $conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);
        if ($conn->connect_error) {
            throw new Exception('DB Connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    /**
     * Get all tickets ordered by created_at desc, including student name via users join.
     */
    public function getAllTickets(): array
    {
        $conn = self::getConnection();
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.category, t.status, t.priority, t.meeting_requested
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                ORDER BY t.created_at DESC";

        $result = $conn->query($sql);
        if ($result === false) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Query failed: ' . $err);
        }

        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
        $conn->close();
        return $tickets;
    }

    public function getTicketById(int $ticket_id): ?array
    {
        $conn = self::getConnection();
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.category, t.status, t.priority, t.meeting_requested, t.description
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                WHERE t.ticket_id = ?
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $ticket_id);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $conn->close();
            throw new Exception('Execute failed: ' . $err);
        }
        $result = $stmt->get_result();
        $ticket = $result->fetch_assoc() ?: null;
        $stmt->close();
        $conn->close();
        return $ticket;
    }

    // Backwards compatible method name used elsewhere in the codebase
    public function getById(int $ticket_id): ?array
    {
        return $this->getTicketById($ticket_id);
    }
}
?>