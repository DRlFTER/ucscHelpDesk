<?php
// MeetingScheduler.php
// Save to: /app/controllers/MeetingScheduler.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

class MeetingScheduler extends Controller
{
    private $db;
    private const APP_TIMEZONE = 'Asia/Colombo';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Schedule a new meeting
     * Receives data from JavaScript, saves to database
     */
    public function scheduleMeeting()
    {
        header('Content-Type: application/json');
        
        try {
            // Check authentication
            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized. Please log in.', 401);
            }

            $counselor_id = (int)$_SESSION['user']['u_id'];
            
            // Get data from JavaScript
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('No data received', 400);
            }

            // Validate required fields
            if (empty($input['meeting_date'])) {
                throw new Exception('Meeting date is required', 400);
            }
            if (empty($input['start_time'])) {
                throw new Exception('Start time is required', 400);
            }
            if (empty($input['duration'])) {
                throw new Exception('Duration is required', 400);
            }
            if (empty($input['mode'])) {
                throw new Exception('Mode is required', 400);
            }

            // Clean and prepare data (prevent SQL injection)
            $ticket_id = isset($input['ticket_id']) && $input['ticket_id'] !== '' ? (int)$input['ticket_id'] : null;
            $student_id = isset($input['student_id']) && $input['student_id'] !== '' ? (int)$input['student_id'] : null;
            $meeting_date = $this->db->real_escape_string($input['meeting_date']);
            $start_time = $this->db->real_escape_string($input['start_time']);
            $duration = (int)$input['duration'];
            $mode = $this->db->real_escape_string($input['mode']);
            $room_location = isset($input['room_location']) ? $this->db->real_escape_string(trim($input['room_location'])) : '';
            $meeting_link = isset($input['meeting_link']) ? $this->db->real_escape_string(trim($input['meeting_link'])) : '';
            $notes = isset($input['notes']) ? $this->db->real_escape_string(trim($input['notes'])) : '';
            
            // Resolve student from ticket when not explicitly provided
            if (!$student_id && $ticket_id) {
                $student_id = $this->resolveStudentIdFromTicket($ticket_id);
            }

            // Calculate end time (start_time + duration)
            $start_timestamp = strtotime($start_time);
            $end_timestamp = $start_timestamp + ($duration * 60);
            $end_time = date('H:i:s', $end_timestamp);
            
            // Create meeting title
            $title = "Counseling Session";
            if ($ticket_id) {
                // Get ticket title
                $ticket_query = "SELECT title FROM tickets WHERE ticket_id = $ticket_id";
                $ticket_result = $this->db->query($ticket_query);
                if ($ticket_result && $ticket_result->num_rows > 0) {
                    $ticket_data = $ticket_result->fetch_assoc();
                    $title = "Meeting: " . $ticket_data['title'];
                }
            }
            
            // Escape title
            $title = $this->db->real_escape_string($title);

            // Insert into database
            $query = "INSERT INTO meeting_schedules (
                counselor_id, 
                student_id, 
                ticket_id, 
                title,
                meeting_date, 
                start_time, 
                end_time, 
                duration,
                mode, 
                room_location, 
                meeting_link, 
                notes, 
                status
            ) VALUES (
                $counselor_id, 
                " . ($student_id ? $student_id : "NULL") . ", 
                " . ($ticket_id ? $ticket_id : "NULL") . ", 
                '$title',
                '$meeting_date', 
                '$start_time', 
                '$end_time', 
                $duration,
                '$mode', 
                " . ($room_location ? "'$room_location'" : "NULL") . ", 
                " . ($meeting_link ? "'$meeting_link'" : "NULL") . ", 
                " . ($notes ? "'$notes'" : "NULL") . ", 
                'scheduled'
            )";
            
            if (!$this->db->query($query)) {
                throw new Exception('Database error: ' . $this->db->error, 500);
            }

            // Get the new meeting ID
            $meeting_id = $this->db->insert_id;

            // Create calendar events for counselor and student
            $this->createCalendarEvents($counselor_id, $student_id, $meeting_id, $title, $meeting_date, $start_time, $end_time);

            // Post chat update immediately when a meeting is scheduled.
            $this->postScheduledMeetingChatMessage(
                $meeting_id,
                $ticket_id,
                $counselor_id,
                $student_id,
                $title,
                $meeting_date,
                $start_time,
                $mode,
                $room_location,
                $meeting_link,
                $notes
            );

            // Send success response
            echo json_encode([
                'success' => true,
                'message' => 'Meeting scheduled successfully!',
                'meeting_id' => $meeting_id,
                'data' => [
                    'meeting_date' => $meeting_date,
                    'start_time' => substr($start_time, 0, 5),
                    'duration' => $duration,
                    'mode' => $mode
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create calendar events for both counselor and student
     */
    private function createCalendarEvents($counselor_id, $student_id, $meeting_id, $title, $date, $start_time, $end_time)
    {
        try {
            $description = "Meeting ID: $meeting_id";
            
            // Create event for counselor
            $query1 = "INSERT INTO calendar_events 
                      (u_id, title, description, event_date, start_time, end_time, is_all_day) 
                      VALUES 
                      ($counselor_id, '$title', '$description', '$date', '$start_time', '$end_time', 0)";
            
            $this->db->query($query1);
            $counselor_event_id = $this->db->insert_id;

            // Create event for student (if student_id exists)
            if ($student_id) {
                $query2 = "INSERT INTO calendar_events 
                          (u_id, title, description, event_date, start_time, end_time, is_all_day) 
                          VALUES 
                          ($student_id, '$title', '$description', '$date', '$start_time', '$end_time', 0)";
                
                $this->db->query($query2);
                $student_event_id = $this->db->insert_id;
            }

            // Link calendar event to meeting (using counselor's event)
            $update_query = "UPDATE meeting_schedules 
                            SET event_id = $counselor_event_id 
                            WHERE meeting_id = $meeting_id";
            $this->db->query($update_query);

            return true;
            
        } catch (\Exception $e) {
            // Don't fail the whole meeting if calendar creation fails
            error_log("Calendar event creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send meeting email to student using PHPMailer
     */
    private function sendMeetingEmailToStudent($student_id, $title, $meeting_date, $start_time, $end_time, $duration, $mode, $meeting_link, $room_location)
    {
        $query = "SELECT name, email FROM users WHERE u_id = $student_id LIMIT 1";
        $result = $this->db->query($query);

        if (!$result || $result->num_rows == 0) {
            return;
        }

        $student = $result->fetch_assoc();
        $student_name = $student['name'];
        $student_email = $student['email'];

        $mail = new PHPMailer(true);

        try {
            // SMTP SETTINGS (CHANGE THESE)
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;   // sender email
            $mail->Password = SMTP_PASS;      // gmail app password
            $mail->SMTPSecure = "tls";
            $mail->Port = SMTP_PORT;

            // Sender
            $mail->setFrom(SMTP_FROM_NAME);

            // Receiver
            $mail->addAddress($student_email, $student_name);

            // Email format
            $mail->isHTML(true);

            $mail->Subject = "Meeting Scheduled: $title";

            $body = "
                <h2>Hello $student_name,</h2>
                <p>Your counseling meeting has been scheduled successfully.</p>

                <h3>Meeting Details</h3>
                <ul>
                    <li><b>Title:</b> $title</li>
                    <li><b>Date:</b> $meeting_date</li>
                    <li><b>Start Time:</b> $start_time</li>
                    <li><b>End Time:</b> $end_time</li>
                    <li><b>Duration:</b> $duration minutes</li>
                    <li><b>Mode:</b> $mode</li>
                </ul>
            ";

            if (!empty($meeting_link)) {
                $body .= "<p><b>Zoom Link:</b> <a href='$meeting_link'>$meeting_link</a></p>";
            }

            if (!empty($room_location)) {
                $body .= "<p><b>Room Location:</b> $room_location</p>";
            }

            $body .= "<br><p>Thank you,<br><b>Counseling Unit</b></p>";

            $mail->Body = $body;

            $mail->send();

        } catch (MailException $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }
    }
    /**
     * Get all meetings for a counselor
     */
    public function getMeetings()
    {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $counselor_id = (int)$_SESSION['user']['u_id'];
            
            $query = "SELECT 
                        m.*,
                        u.name as student_name,
                        u.email as student_email,
                        t.title as ticket_title
                      FROM meeting_schedules m
                      LEFT JOIN users u ON m.student_id = u.u_id
                      LEFT JOIN tickets t ON m.ticket_id = t.ticket_id
                      WHERE m.counselor_id = $counselor_id
                      AND m.status != 'cancelled'
                      ORDER BY m.meeting_date DESC, m.start_time DESC";
            
            $result = $this->db->query($query);
            
            $meetings = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $meetings[] = $row;
                }
            }

            echo json_encode([
                'success' => true,
                'count' => count($meetings),
                'data' => $meetings
            ]);

        } catch (\Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Post due meeting reminders to ticket chat and return popup payload.
     */
    public function processDueMeetingNotifications()
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $user_id = (int)$_SESSION['user']['u_id'];
            $role = strtolower((string)($_SESSION['user']['role'] ?? ''));

            $role_filter = '';
            if ($role === 'student') {
                $role_filter = " AND m.student_id = $user_id";
            } elseif ($role === 'counselor') {
                $role_filter = " AND m.counselor_id = $user_id";
            } else {
                echo json_encode([
                    'success' => true,
                    'notifications' => []
                ]);
                return;
            }

                            $query = "SELECT m.meeting_id, m.ticket_id, m.counselor_id, m.student_id, m.title, m.meeting_date,
                                m.start_time, m.mode, m.room_location, m.meeting_link, m.notes
                      FROM meeting_schedules m
                      WHERE m.status = 'scheduled'
                                                AND m.ticket_id IS NOT NULL"
                        . $role_filter .
                      " ORDER BY m.meeting_date ASC, m.start_time ASC
                        LIMIT 20";

            $result = $this->db->query($query);
            if (!$result) {
                throw new Exception('Database error: ' . $this->db->error, 500);
            }

            require_once __DIR__ . '/../models/TicketChat.php';
            $chatModel = new TicketChat();
            $notifications = [];

            while ($meeting = $result->fetch_assoc()) {
                $meeting_id = (int)$meeting['meeting_id'];
                $ticket_id = (int)$meeting['ticket_id'];
                $counselor_id = (int)$meeting['counselor_id'];
                $student_id = !empty($meeting['student_id']) ? (int)$meeting['student_id'] : 0;

                if (!$this->isMeetingDueNow((string)$meeting['meeting_date'], (string)$meeting['start_time'])) {
                    continue;
                }

                if ($ticket_id <= 0 || $counselor_id <= 0) {
                    continue;
                }

                // Fallback for student_id from ticket owner
                if ($student_id <= 0) {
                    $student_id = $this->resolveStudentIdFromTicket($ticket_id);
                }

                if ($student_id <= 0) {
                    continue;
                }

                $chat = $chatModel->getChatByTicketId($ticket_id);
                if (!$chat) {
                    $chat_id = $chatModel->createChat($ticket_id, $student_id, $counselor_id);
                    if (!$chat_id) {
                        continue;
                    }
                    $chat_id = (int)$chat_id;
                } else {
                    $chat_id = (int)$chat['chat_id'];
                }

                // Prevent duplicate reminders for the same meeting
                                $exists_query = "SELECT id FROM ticket_messages
                                 WHERE chat_id = $chat_id
                                   AND message LIKE '%(Meeting ID: $meeting_id)%'
                                 LIMIT 1";
                $exists_result = $this->db->query($exists_query);
                if ($exists_result && $exists_result->num_rows > 0) {
                    continue;
                }

                $meeting_title = trim((string)$meeting['title']) !== '' ? $meeting['title'] : 'Counseling Session';
                $meeting_date = (string)$meeting['meeting_date'];
                $start_time = substr((string)$meeting['start_time'], 0, 5);
                $mode = (string)$meeting['mode'];
                $venue = trim((string)($meeting['room_location'] ?? ''));
                $link = trim((string)($meeting['meeting_link'] ?? ''));
                $notes = trim((string)($meeting['notes'] ?? ''));

                $message = "Meeting reminder: Your scheduled session is due now.\n"
                    . "Title: {$meeting_title}\n"
                    . "Date: {$meeting_date}\n"
                    . "Time: {$start_time}\n"
                    . "Mode: {$mode}";

                if ($venue !== '') {
                    $message .= "\nVenue: {$venue}";
                }

                if ($link !== '') {
                    $message .= "\nMeeting Link: {$link}";
                }

                if ($notes !== '') {
                    $message .= "\nNotes: {$notes}";
                }

                $message .= "\n(Meeting ID: {$meeting_id})";

                $sent = $chatModel->sendMessage($chat_id, $counselor_id, $message, 'text', null);
                if (!$sent) {
                    continue;
                }

                $notifications[] = [
                    'meeting_id' => $meeting_id,
                    'ticket_id' => $ticket_id,
                    'title' => $meeting_title,
                    'start_time' => $start_time,
                    'venue' => $venue,
                    'meeting_link' => $link
                ];
            }

            echo json_encode([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function resolveStudentIdFromTicket($ticket_id)
    {
        $ticket_id = (int)$ticket_id;
        if ($ticket_id <= 0) {
            return null;
        }

        $query = "SELECT u_id FROM tickets WHERE ticket_id = $ticket_id LIMIT 1";
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['u_id'])) {
                return (int)$row['u_id'];
            }
        }

        return null;
    }

    private function isMeetingDueNow($meetingDate, $startTime)
    {
        try {
            $tz = new DateTimeZone(self::APP_TIMEZONE);
            $meetingAt = new DateTime($meetingDate . ' ' . $startTime, $tz);
            $now = new DateTime('now', $tz);
            return $meetingAt <= $now;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function postScheduledMeetingChatMessage($meetingId, $ticketId, $counselorId, $studentId, $title, $meetingDate, $startTime, $mode, $roomLocation, $meetingLink, $notes = '')
    {
        $meetingId = (int)$meetingId;
        $ticketId = (int)$ticketId;
        $counselorId = (int)$counselorId;
        $studentId = (int)$studentId;

        if ($meetingId <= 0 || $ticketId <= 0 || $counselorId <= 0 || $studentId <= 0) {
            return false;
        }

        require_once __DIR__ . '/../models/TicketChat.php';
        $chatModel = new TicketChat();

        $chat = $chatModel->getChatByTicketId($ticketId);
        if (!$chat) {
            $chatId = $chatModel->createChat($ticketId, $studentId, $counselorId);
            if (!$chatId) {
                return false;
            }
            $chatId = (int)$chatId;
        } else {
            $chatId = (int)$chat['chat_id'];
        }

                $existsQuery = "SELECT id FROM ticket_messages
                        WHERE chat_id = $chatId
                          AND message LIKE '%(Meeting Scheduled ID: $meetingId)%'
                        LIMIT 1";
        $existsResult = $this->db->query($existsQuery);
        if ($existsResult && $existsResult->num_rows > 0) {
            return true;
        }

        $msg = "Meeting scheduled successfully.\n"
            . "Title: {$title}\n"
            . "Date: {$meetingDate}\n"
            . "Time: " . substr((string)$startTime, 0, 5) . "\n"
            . "Mode: {$mode}";

        if (trim((string)$roomLocation) !== '') {
            $msg .= "\nVenue: {$roomLocation}";
        }

        if (trim((string)$meetingLink) !== '') {
            $msg .= "\nMeeting Link: " . trim((string)$meetingLink);
        }

        if (trim((string)$notes) !== '') {
            $msg .= "\nNotes: " . trim((string)$notes);
        }

        $msg .= "\n(Meeting Scheduled ID: {$meetingId})";

        return $chatModel->sendMessage($chatId, $counselorId, $msg, 'text', null);
    }

    /**
     * API-style meeting join URL for chat/popup sharing.
     */
    private function getMeetingApiLink($meetingId)
    {
        $base = rtrim((string)ROOT, '/');
        $meetingId = (int)$meetingId;
        return $base . '/meetingscheduler/joinMeeting?meeting_id=' . $meetingId;
    }

    /**
     * Redirect users to the real external meeting link.
     */
    public function joinMeeting()
    {
        if (!isset($_SESSION['user']['u_id'])) {
            http_response_code(401);
            echo 'Unauthorized';
            return;
        }

        $meetingId = isset($_GET['meeting_id']) ? (int)$_GET['meeting_id'] : 0;
        if ($meetingId <= 0) {
            http_response_code(400);
            echo 'Invalid meeting id';
            return;
        }

        $userId = (int)($_SESSION['user']['u_id'] ?? 0);
        $query = "SELECT counselor_id, student_id, meeting_link
                  FROM meeting_schedules
                  WHERE meeting_id = $meetingId
                  LIMIT 1";
        $result = $this->db->query($query);
        if (!$result || $result->num_rows === 0) {
            http_response_code(404);
            echo 'Meeting not found';
            return;
        }

        $meeting = $result->fetch_assoc();
        $counselorId = (int)($meeting['counselor_id'] ?? 0);
        $studentId = (int)($meeting['student_id'] ?? 0);
        $externalLink = trim((string)($meeting['meeting_link'] ?? ''));

        if ($userId !== $counselorId && $userId !== $studentId) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        if ($externalLink === '' || !preg_match('/^https?:\/\//i', $externalLink)) {
            http_response_code(400);
            echo 'Meeting link is not configured.';
            return;
        }

        header('Location: ' . $externalLink);
    }
}

