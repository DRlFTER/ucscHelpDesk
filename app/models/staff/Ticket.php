<?php
require_once __DIR__ . '/../../core/config.php';

// PHPMailer includes (adjusted path from staff/model to app/lib/PHPMailer/src)
require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
     * Get staff level from hierarchy via division.
     */
    public function getStaffLevel(int $staff_id): ?int
    {
        $conn = self::getConnection();
        $sql = "SELECT level FROM staff_hierachy WHERE h_id = (SELECT h_id FROM staff_division WHERE u_id = ? LIMIT 1) LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['level'] ?? null;
    }
    /**
     * Get all emails of staff members in a specific level.
     */
    private function getStaffEmailsByLevel(int $level): array
    {
        $conn = self::getConnection();
        $sql = "SELECT DISTINCT u.email 
                FROM users u
                INNER JOIN staff_division sd ON u.u_id = sd.u_id
                INNER JOIN staff_hierachy sh ON sd.h_id = sh.h_id
                WHERE u.role = 'staff' 
                  AND sh.level = ? 
                  AND u.email IS NOT NULL 
                  AND u.email != ''";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed for emails: ' . $err);
        }
        $stmt->bind_param('i', $level);
        $stmt->execute();
        $result = $stmt->get_result();
        $emails = [];
        while ($row = $result->fetch_assoc()) {
            $emails[] = $row['email'];
        }
        $stmt->close();
        $conn->close();
        return $emails;
    }

    /**
     * Get basic ticket details for email notification (title, description, student_name, category).
     */
    private function getTicketDetailsForEmail(int $ticket_id): ?array
    {
        $conn = self::getConnection();
        $sql = "SELECT t.ticket_id, t.title, t.description, u.name AS student_name, d.name AS category
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                LEFT JOIN division d ON d.did = t.division
                WHERE t.ticket_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            return null;
        }
        $stmt->bind_param('i', $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result->fetch_assoc() ?: null;
        $stmt->close();
        $conn->close();
        return $ticket;
    }

    /**
     * Send escalation email to all staff in the given level using PHPMailer.
     * Uses SMTP settings from config.php.
     */
    private function sendEscalationEmail(int $ticket_id, int $level): bool
    {
        // DEBUG EMAIL: Starting sendEscalationEmail for ticket_id=$ticket_id, level=$level
        // echo "<pre>DEBUG EMAIL: Starting sendEscalationEmail for ticket_id=$ticket_id, level=$level</pre>";

        $ticket_details = $this->getTicketDetailsForEmail($ticket_id);
        if (!$ticket_details) {
            // echo "<pre>Failed to fetch ticket details for email: ticket_id=$ticket_id</pre>";
            // DEBUG EMAIL: Failed to get ticket details
            // echo "<pre>DEBUG EMAIL: Failed to get ticket details for ticket_id=$ticket_id</pre>";
            return false;
        }
        // DEBUG EMAIL: Got ticket details - Title: " . substr($ticket_details['title'], 0, 50) . "..."
        // echo "<pre>DEBUG EMAIL: Got ticket details - Title: " . substr($ticket_details['title'], 0, 50) . "... Student: " . $ticket_details['student_name'] . "</pre>";

        $emails = $this->getStaffEmailsByLevel($level);
        if (empty($emails)) {
            // echo "<pre>No staff emails found for level $level</pre>";
            // DEBUG EMAIL: No emails found for level $level
            // echo "<pre>DEBUG EMAIL: No emails found for level $level</pre>";
            return false;
        }
        // DEBUG EMAIL: Found " . count($emails) . " emails: " . implode(', ', $emails)
        // echo "<pre>DEBUG EMAIL: Found " . count($emails) . " emails: " . implode(', ', $emails) . "</pre>";
        

        $mail = new PHPMailer(true);
        try {
            // DEBUG EMAIL: Created PHPMailer instance
            // echo "<pre>DEBUG EMAIL: Created PHPMailer instance</pre>";

            // Server settings from config.php
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // DEBUG EMAIL: SMTP settings applied - Host: $mail->Host, Port: $mail->Port, User: $mail->Username (password masked)
            // echo "<pre>DEBUG EMAIL: SMTP settings applied - Host: " . $mail->Host . ", Port: " . $mail->Port . ", User: " . $mail->Username . " (password masked)</pre>";

            // Enable PHPMailer SMTP debug output if DEBUG is true (outputs to php://output, visible in browser/error logs)
            if (defined('DEBUG') && DEBUG) {
                $mail->SMTPDebug = 0;  // Changed to 0 for production - no verbose logs
                // echo "<pre>DEBUG EMAIL: Enabled PHPMailer SMTPDebug level DEBUG_SERVER</pre>";
            }
            if (defined('DEBUG') && DEBUG) {
             $mail->SMTPDebug = 0;  // Silent—emails send without chatty output
}

            $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
            $mail->addReplyTo(SMTP_USER, SMTP_FROM_NAME);

            // DEBUG EMAIL: From/ReplyTo set to $mail->Username
            // echo "<pre>DEBUG EMAIL: From/ReplyTo set to " . $mail->Username . "</pre>";

            // Content - Updated subject/body for overdue notification to all levels
            $subject = "Overdue Ticket Alert - Level $level: #{$ticket_details['ticket_id']} - {$ticket_details['title']}";
            $body = "
                <h2>Overdue Ticket Notification</h2>
                <p><strong>Ticket ID:</strong> {$ticket_details['ticket_id']}</p>
                <p><strong>Title:</strong> {$ticket_details['title']}</p>
                <p><strong>Student:</strong> {$ticket_details['student_name']}</p>
                <p><strong>Category:</strong> {$ticket_details['category']}</p>
                <p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($ticket_details['description'])) . "</p>
                <p>This ticket is overdue (pending >3 days) and escalated to <strong>all Levels 1-3</strong>. Level $level staff: Please review, assign, and handle immediately.</p>
                <p>Best regards,<br>UCSC Help Desk</p>
            ";
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // DEBUG EMAIL: Subject set: $subject, Body length: " . strlen($body) . " chars, HTML: true
            // echo "<pre>DEBUG EMAIL: Subject set: " . $subject . ", Body length: " . strlen($body) . " chars, HTML: true</pre>";

            // Send to each email individually
            foreach ($emails as $email) {
                $mail->clearAddresses();
                $mail->addAddress($email);
                // DEBUG EMAIL: Cleared addresses and added $email for ticket $ticket_id
                // echo "<pre>DEBUG EMAIL: Cleared addresses and added $email for ticket $ticket_id</pre>";
                
                // DEBUG EMAIL: About to call send() for $email
                // echo "<pre>DEBUG EMAIL: About to call send() for $email</pre>";
                
                if (!$mail->send()) {
                    // echo "<pre>Failed to send email to $email for ticket $ticket_id: {$mail->ErrorInfo}</pre>";
                    // DEBUG EMAIL: Send FAILED for $email - Error: {$mail->ErrorInfo}
                    // echo "<pre>DEBUG EMAIL: Send FAILED for $email - Error: " . $mail->ErrorInfo . "</pre>";
                } else {
                    // echo "<pre>Escalation email sent successfully to $email for ticket $ticket_id</pre>";
                    // DEBUG EMAIL: Send SUCCESS for $email
                    // echo "<pre>DEBUG EMAIL: Send SUCCESS for $email</pre>";
                }
            }

            // DEBUG EMAIL: Finished loop - all emails processed for ticket $ticket_id
            // echo "<pre>DEBUG EMAIL: Finished loop - all emails processed for ticket $ticket_id</pre>";

            return true;

        } catch (Exception $e) {
            // echo "<pre>PHPMailer error for ticket $ticket_id: {$mail->ErrorInfo}</pre>";
            // DEBUG EMAIL: PHPMailer Exception caught: " . $e->getMessage() . " | ErrorInfo: {$mail->ErrorInfo}
            // echo "<pre>DEBUG EMAIL: PHPMailer Exception caught: " . $e->getMessage() . " | ErrorInfo: " . $mail->ErrorInfo . "</pre>";
            return false;
        }
    }

    /**
     * Get all tickets ordered by created_at desc, including student name via users join.
     * Filters by staff's division names (t.category = division name).
     * For levels <=2: Include overdue pending (>3 days) in division.
     */
 public function getAllTickets(): array
{
    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
    $divisions = $this->getStaffDivisions($staff_id);
    $staff_level = $this->getStaffLevel($staff_id);

    if (empty($divisions)) {
        throw new Exception('No division mapping found for your staff account. Please contact admin to assign your division.');
    }

    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);

// Cache divisions if not set
if (!isset($_SESSION['staff_divisions_' . $staff_id])) {
    $_SESSION['staff_divisions_' . $staff_id] = $this->getStaffDivisions($staff_id);
}
$divisions = $_SESSION['staff_divisions_' . $staff_id];

// Cache level if not set
if (!isset($_SESSION['staff_level_' . $staff_id])) {
    $_SESSION['staff_level_' . $staff_id] = $this->getStaffLevel($staff_id);
}
$staff_level = $_SESSION['staff_level_' . $staff_id];

    // Overdue fetch and notifications (unchanged—no email tweaks)
    $overdue_tickets = $this->getoverduependingtickets($divisions);
    if (!isset($_SESSION['overdues_checked'])) {
        $this->handleOverdueNotifications($overdue_tickets);
        $_SESSION['overdues_checked'] = true;
    }

    $today = date('Y-m-d');
    $session_key = 'overdues_checked_' . $today;

if (!isset($_SESSION[$session_key])) {
    $this->handleOverdueNotifications($overdue_tickets);
    $_SESSION[$session_key] = true;
}

    $conn = self::getConnection();
    $tickets = [];
    
    try {
        $placeholders = implode(',', array_fill(0, count($divisions), '?'));
        $didParams = array_map(fn($d) => (int)$d['did'], $divisions);

        // Base SELECT (unchanged: keeps is_overdue_pending flag for view styling)
       $baseSelect = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, d.name AS category, t.status, t.priority, t.meeting_requested,
                           COALESCE(sh.level, 99) AS assigned_level,
                           CASE WHEN t.status = 'pending' AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY) THEN 1 ELSE 0 END AS is_overdue_pending,
                           t.division, t.assigned_to
                    FROM tickets t
                    INNER JOIN users u ON t.u_id = u.u_id
                    LEFT JOIN division d ON d.did = t.division
                    LEFT JOIN staff_division sd ON sd.u_id = t.assigned_to
                    LEFT JOIN staff_hierachy sh ON sh.h_id = sd.h_id";

            if ($staff_level !== null) {
                if ($staff_level <= 2) {  // Levels 1-2: All tickets in division
                    $where = "t.division IN ($placeholders)";
                    $sql = $baseSelect . " WHERE $where ORDER BY t.created_at DESC";
                    
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception('Prepare failed: ' . $conn->error);
                    }
                    $stmt->bind_param(str_repeat('i', count($didParams)), ...$didParams);
                    
                } elseif ($staff_level === 3) {  // Level 3: Assigned to self OR overdue pending in division
                    $where = "(t.assigned_to = ?) OR (t.status = 'pending' AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY) AND t.division IN ($placeholders))";
                    $sql = $baseSelect . " WHERE $where ORDER BY t.created_at DESC";
                    
                    $allParams = array_merge([$staff_id], $didParams);  // Staff ID + divs for overdue
                    $types = str_repeat('i', count($allParams));
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception('Prepare failed: ' . $conn->error);
                    }
                    $stmt->bind_param($types, ...$allParams);
                    
                } else {
                    // Fallback for higher/unknown levels: Only assigned to self
                    $sql = $baseSelect . " WHERE t.assigned_to = ? ORDER BY t.created_at DESC";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception('Prepare failed: ' . $conn->error);
                    }
                    $stmt->bind_param('i', $staff_id);
                }
            } else {
                // No level? Fallback to assigned only
                $sql = $baseSelect . " WHERE t.assigned_to = ? ORDER BY t.created_at DESC";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Prepare failed: ' . $conn->error);
                }
                $stmt->bind_param('i', $staff_id);
            }
            
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
        $stmt->close();
        
    } catch (Exception $e) {
        error_log('getAllTickets error: ' . $e->getMessage());
        throw $e;
    } finally {
        $conn->close();
    }
    
    return $tickets;
}
    /**
     * Updated: Filter overdues by divisions (staff-specific).
     */
    public function getoverduependingtickets(array $divisions): array
    {
        // echo "<pre>TRACE: getoverduependingtickets() STARTED - Divisions: " . json_encode($divisions) . "</pre>";

        if (empty($divisions)) return [];

        $placeholders = implode(',', array_fill(0, count($divisions), '?'));
        $didParams = array_map(fn($d) => (int)$d['did'], $divisions);

        $conn = self::getConnection();
        $sql = "SELECT ticket_id, created_at, title
                FROM tickets
                WHERE status = 'pending'
                  AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
                  AND division IN ($placeholders)
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param(str_repeat('i', count($didParams)), ...$didParams);
        $stmt->execute();
        $result = $stmt->get_result();
        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
        $stmt->close();
        $conn->close();

        // echo "<pre>TRACE: getoverduependingtickets() FINISHED - Returning " . count($tickets) . " tickets</pre>";
        return $tickets;
    }

    /**
     * Revised: Process overdues—escalate to ALL levels 1-3 if not already, notify each level's staff via email.
     */
    private function handleOverdueNotifications(array $overdue_tickets): void
    {
        // echo "<pre>TRACE: handleOverdueNotifications() STARTED - " . count($overdue_tickets) . " overdue tickets to process: " . json_encode(array_column($overdue_tickets, 'ticket_id')) . "</pre>";

        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $levels_to_notify = [1, 2, 3];  // Notify all levels 1-3 for overdues

        foreach ($overdue_tickets as $ticket) {
            $ticket_id = (int)$ticket['ticket_id'];
            // echo "<pre>TRACE: handleOverdueNotifications() - Processing ticket $ticket_id for levels " . implode(', ', $levels_to_notify) . "</pre>";

            foreach ($levels_to_notify as $level) {
                // echo "<pre>TRACE: handleOverdueNotifications() - Checking escalation for ticket $ticket_id to level $level</pre>";
                
                if (!$this->isTicketEscalated($ticket_id, $level)) {
                    // echo "<pre>TRACE: handleOverdueNotifications() - Level $level NOT escalated - proceeding to escalate/notify</pre>";
                    // echo "<pre>TRACE: handleOverdueNotifications() - Calling setTicketLevel($ticket_id, $staff_id, $level)</pre>";
                    
                    $success = $this->setTicketLevel($ticket_id, $staff_id, $level);  // Updates timeline + sends email to level's staff
                    if ($success) {
                        // echo "<pre>TRACE: handleOverdueNotifications() - setTicketLevel SUCCESS for ticket $ticket_id to level $level</pre>";
                    } else {
                        // echo "<pre>TRACE: handleOverdueNotifications() - setTicketLevel FAILED for ticket $ticket_id to level $level</pre>";
                    }
                } else {
                    // echo "<pre>TRACE: handleOverdueNotifications() - Ticket $ticket_id already escalated to level $level - skipping</pre>";
                }
            }
        }

        // echo "<pre>TRACE: handleOverdueNotifications() FINISHED - All levels notified for overdues</pre>";
    }

    /**
     * Helper: Check if escalated to specific level - Ignores zero-dates as "not escalated".
     */
    private function isTicketEscalated(int $ticket_id, int $level): bool
    {
        // echo "<pre>DEBUG: isTicketEscalated() - Querying for ticket $ticket_id, level $level (column: level_$level)</pre>";

        $conn = self::getConnection();
        $column = "level_$level";
        $sql = "SELECT $column FROM ticket_timeline WHERE ticket_id = ? AND $column IS NOT NULL AND $column != '0000-00-00 00:00:00' LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // echo "<pre>DEBUG: isTicketEscalated() - Prepare FAILED for ticket $ticket_id</pre>";
            $conn->close();
            return false;
        }
        $stmt->bind_param('i', $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $value = $row[$column] ?? 'NO ROW';
        // echo "<pre>DEBUG: isTicketEscalated() - Query result for ticket $ticket_id: $value (row exists? " . ($row !== null ? 'YES' : 'NO') . ")</pre>";
        
        $is_escalated = $row !== null;
        // echo "<pre>DEBUG: isTicketEscalated() - Returning: " . ($is_escalated ? 'TRUE (skip)' : 'FALSE (proceed)') . "</pre>";
        
        $stmt->close();
        $conn->close();
        return $is_escalated;
    }

    public function getticketassignedstaffname(int $ticket_id): ?string
    {
        $conn = self::getConnection();
        $sql = "SELECT u.name AS staff_name
                FROM tickets t
                INNER JOIN users u ON t.assigned_to = u.u_id
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
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['staff_name'] ?? null;
    }

    public function getTicketById(int $ticket_id): ?array
    {
        $conn = self::getConnection();
        $staff_name = $this->getticketassignedstaffname($ticket_id);
        $current_staff = (int)($_SESSION['user']['u_id'] ?? 0);
        $sql = "SELECT t.ticket_id, t.u_id, t.created_at, t.title, u.name AS student_name, d.name AS category, t.status, t.priority, t.meeting_requested, t.description, t.assigned_to , ? AS staff_name,t.t_type
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
        $stmt->bind_param('sii', $staff_name, $ticket_id, $current_staff);
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

    public function setTicketupdateTimeline(int $ticket_id):bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE ticket_timeline SET assigned = CURRENT_TIMESTAMP WHERE ticket_id = ? ";
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $ticket_id);
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

    public function setTimeLineReview(int $ticket_id):bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE ticket_timeline SET under_review = CURRENT_TIMESTAMP WHERE ticket_id = ? ";
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $ticket_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
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
     * Set ticket level in timeline (dynamic column: level_1, level_2, etc.) and send escalation email to all staff in that level.
     */
    public function setTicketLevel(int $ticket_id, int $new_staff_id , int $level): bool
    {
        // echo "<pre>TRACE: setTicketLevel() STARTED - ticket_id=$ticket_id, staff_id=$new_staff_id, level=$level (column: level_$level)</pre>";

        $conn = self::getConnection();
        $column = "level_$level";
        $sql = "UPDATE ticket_timeline SET $column = CURRENT_TIMESTAMP WHERE ticket_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // echo "<pre>TRACE: setTicketLevel() - Prepare FAILED for $column</pre>";
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $ticket_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        // echo "<pre>TRACE: setTicketLevel() - UPDATE affected_rows: " . $stmt->affected_rows . " for ticket $ticket_id</pre>";
        $stmt->close();
        $conn->close();

        if ($ok) {
            // DEBUG EMAIL: setTicketLevel success - now calling sendEscalationEmail for ticket $ticket_id, level $level
            // echo "<pre>DEBUG EMAIL: setTicketLevel success - now calling sendEscalationEmail for ticket $ticket_id, level $level</pre>";
            // Send escalation email to all staff in the new level
            $this->sendEscalationEmail($ticket_id, $level);
        } else {
            // DEBUG EMAIL: setTicketLevel FAILED - affected_rows=0 for ticket $ticket_id
            // echo "<pre>DEBUG EMAIL: setTicketLevel FAILED - affected_rows=0 for ticket $ticket_id (check if timeline row exists?)</pre>";
        }

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

    public function resolveTicketTimeLine(int $ticket_id):bool{
        $conn = self::getConnection();
        $sql = "UPDATE ticket_timeline SET resolved = CURRENT_TIMESTAMP WHERE ticket_id = ? ";
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param('i', $ticket_id);
        $ok = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Reject/Close ticket (update status to 'agent-closed').
     */
    public function rejectTicket(int $ticket_id): bool
    {
        $conn = self::getConnection();
        $sql = "UPDATE tickets SET status = 'agent-closed' WHERE ticket_id = ? AND assigned_to = ?";  
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