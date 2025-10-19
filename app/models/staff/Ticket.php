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
     * Get staff's division names from staff_division + division table.
     */
    private function getStaffDivisions(int $staff_id): array
    {
        $conn = self::getConnection();
        $sql = "SELECT d.name 
                FROM division d
                INNER JOIN staff_division sd ON d.did = sd.did
                WHERE sd.u_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $divisions = [];
        while ($row = $result->fetch_assoc()) {
            $divisions[] = $row['name'];
        }
        $stmt->close();
        $conn->close();
        return $divisions;
    }

    /**
     * Get all tickets ordered by created_at desc, including student name via users join.
     * Filters by staff's division names (t.category = division name).
     */
    public function getAllTickets(): array
    {
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $division_names = $this->getStaffDivisions($staff_id);
        if (empty($division_names)) {
            return [];  // No divisions, no tickets
        }

        $conn = self::getConnection();
        $placeholders = implode(',', array_fill(0, count($division_names), '?'));
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.category, t.status, t.priority, t.meeting_requested
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                WHERE t.category IN ($placeholders)
                ORDER BY t.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param(str_repeat('s', count($division_names)), ...$division_names);
        $stmt->execute();
        $result = $stmt->get_result();
        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $tickets;
    }

    public function getTicketById(int $ticket_id): ?array
    {
        $conn = self::getConnection();
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.category, t.status, t.priority, t.meeting_requested, t.description, t.assigned_to
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
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result->fetch_assoc() ?: null;
        $stmt->close();
        $conn->close();
        return $ticket;
    }

    // Backwards compatible method name used elsewhere in the code base
    public function getById(int $ticket_id): ?array
    {
        return $this->getTicketById($ticket_id);
    }

    /**
     * Assign ticket to staff (update status and assigned_to).
     */
    public function assignToStaff(int $ticket_id, int $staff_id): bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE tickets SET status = 'assigned', assigned_to = ? WHERE ticket_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('ii', $staff_id, $ticket_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Add response to ticket (insert into ticket_response).
     */
    public function addResponse(int $ticket_id, int $staff_id, string $response_text): bool
    {
        $conn = self::getConnection();
        $sql = "INSERT INTO ticket_response (ticket_id, u_id, response, date_time) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('iss', $ticket_id, $staff_id, $response_text);
        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Forward ticket to another staff (update assigned_to).
     */
    public function forwardTicket(int $ticket_id, int $new_staff_id): bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE tickets SET assigned_to = ? WHERE ticket_id = ? AND assigned_to = ?";  // Only if current
        $current_staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('iii', $new_staff_id, $ticket_id, $current_staff_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Resolve ticket (update status to 'resolved').
     */
    public function resolveTicket(int $ticket_id): bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE tickets SET status = 'resolved' WHERE ticket_id = ? AND assigned_to = ?";
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('ii', $ticket_id, $staff_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Reject ticket (update status to 'rejected').
     */
    public function rejectTicket(int $ticket_id): bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE tickets SET status = 'rejected' WHERE ticket_id = ? AND assigned_to = ?";
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('ii', $ticket_id, $staff_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Get all staff members for forward dropdown (from users where role = 'staff').
     */
    public function getStaffMembers(): array
    {
        $conn = self::getConnection();
        $sql = "SELECT u_id, name FROM users WHERE role = 'staff' ORDER BY name";
        $result = $conn->query($sql);
        if ($result === false) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Query failed: ' . $err);
        }
        $staff = [];
        while ($row = $result->fetch_assoc()) {
            $staff[] = $row;
        }
        $conn->close();
        return $staff;
    }
}
?>