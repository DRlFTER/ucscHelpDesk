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
        $sql = "SELECT d.did, d.name 
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
            $divisions[] = [ 'did' => (int)$row['did'], 'name' => $row['name'] ];
        }
        $stmt->close();

        // Fallback: if no explicit mapping, try users.designation
        if (empty($divisions)) {
            $stmt2 = $conn->prepare("SELECT designation FROM users WHERE u_id = ? AND role = 'staff' LIMIT 1");
            if ($stmt2) {
                $stmt2->bind_param('i', $staff_id);
                if ($stmt2->execute()) {
                    $res2 = $stmt2->get_result();
                    $row2 = $res2->fetch_assoc();
                    if ($row2 && !empty($row2['designation'])) {
                        $did = (int)$row2['designation'];
                        if ($did > 0) {
                            // Optionally resolve name
                            $name = null;
                            if ($stmt3 = $conn->prepare('SELECT name FROM division WHERE did = ? LIMIT 1')) {
                                $stmt3->bind_param('i', $did);
                                if ($stmt3->execute()) {
                                    $r3 = $stmt3->get_result()->fetch_assoc();
                                    $name = $r3['name'] ?? null;
                                }
                                $stmt3->close();
                            }
                            $divisions[] = [ 'did' => $did, 'name' => $name ?? ('Division #' . $did) ];
                        }
                    }
                }
                $stmt2->close();
            }
        }

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
    $divisions = $this->getStaffDivisions($staff_id);
        if (empty($divisions)) {
            throw new Exception('No division mapping found for your staff account. Please contact admin to assign your division.');
        }

        $conn = self::getConnection();
    $placeholders = implode(',', array_fill(0, count($divisions), '?'));
    $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, d.name AS category, t.status, t.priority, t.meeting_requested
        FROM tickets t
        INNER JOIN users u ON t.u_id = u.u_id
        LEFT JOIN division d ON d.did = t.division
        WHERE t.division IN ($placeholders)
        ORDER BY t.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
    $didParams = array_map(fn($d) => (int)$d['did'], $divisions);
    $types = str_repeat('i', count($didParams));
    $stmt->bind_param($types, ...$didParams);
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
        $current_staff = (int)($_SESSION['user']['u_id'] ?? 0);
                        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, d.name AS category, t.status, t.priority, t.meeting_requested, t.description, t.assigned_to
                                FROM tickets t
                                INNER JOIN users u ON t.u_id = u.u_id
                                LEFT JOIN division d ON d.did = t.division
                                WHERE t.ticket_id = ?
                                    AND t.division IN (SELECT did FROM staff_division WHERE u_id = ?)
                                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('ii', $ticket_id, $current_staff);
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
 * Assign ticket to staff (update status to 'agent assigned' and assigned_to).
 */
public function assignToStaff(int $ticket_id, int $staff_id): bool
{
    $conn = self::getConnection();
    $sql = "UPDATE tickets SET status = 'agent assigned', assigned_to = ?
            WHERE ticket_id = ?
              AND status = 'pending'
              AND division IN (SELECT did FROM staff_division WHERE u_id = ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $err = $conn->error;
        $conn->close();
        throw new Exception('Prepare failed: ' . $err);
    }
    $stmt->bind_param('iii', $staff_id, $ticket_id, $staff_id);
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
 * Resolve ticket (update status to 'agent-closed').
 */
/**
 * Resolve ticket (update status to 'agent-closed').
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
 * Reject/Close ticket (update status to 'agent-closed' or 'closed').
 */
public function rejectTicket(int $ticket_id): bool
{
    $conn = self::getConnection();
    $sql = "UPDATE tickets SET status = 'agent-closed' WHERE ticket_id = ? AND assigned_to = ?";  // Change to 'closed' if preferred
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