<?php
// MeetingScheduler.php
// Save to: /app/controllers/MeetingScheduler.php

class MeetingScheduler extends Controller
{
    private $db;
    
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

        } catch (Exception $e) {
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
            
        } catch (Exception $e) {
            // Don't fail the whole meeting if calendar creation fails
            error_log("Calendar event creation failed: " . $e->getMessage());
            return false;
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

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}