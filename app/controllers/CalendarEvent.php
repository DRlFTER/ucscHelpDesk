<?php
// CalendarEvent.php - Save as /app/controllers/CalendarEvent.php

class CalendarEvent extends Controller
{
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Default index method
    public function index() {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Calendar API is running',
            'endpoints' => [
                'POST /calendarevent/create',
                'GET /calendarevent/read',
                'PUT /calendarevent/update',
                'DELETE /calendarevent/delete'
            ]
        ]);
    }

    // CREATE - Add new event
    public function create()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $u_id = $_SESSION['user']['u_id'];
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('Invalid JSON data', 400);
            }

            if (empty($input['title'])) {
                throw new Exception('Event title is required', 400);
            }
            
            if (empty($input['event_date'])) {
                throw new Exception('Event date is required', 400);
            }

            $date = DateTime::createFromFormat('Y-m-d', $input['event_date']);
            if (!$date || $date->format('Y-m-d') !== $input['event_date']) {
                throw new Exception('Invalid date format. Use YYYY-MM-DD', 400);
            }

            $title = $this->db->real_escape_string(trim($input['title']));
            $description = isset($input['description']) ? $this->db->real_escape_string(trim($input['description'])) : null;
            $event_date = $this->db->real_escape_string($input['event_date']);
            $start_time = isset($input['start_time']) ? $this->db->real_escape_string($input['start_time']) : null;
            $end_time = isset($input['end_time']) ? $this->db->real_escape_string($input['end_time']) : null;
            $is_all_day = isset($input['is_all_day']) ? (int)$input['is_all_day'] : 1;

            $query = "INSERT INTO calendar_events 
                      (u_id, title, description, event_date, start_time, end_time, is_all_day, created_at) 
                      VALUES ($u_id, '$title', " . 
                      ($description ? "'$description'" : "NULL") . ", '$event_date', " .
                      ($start_time ? "'$start_time'" : "NULL") . ", " .
                      ($end_time ? "'$end_time'" : "NULL") . ", $is_all_day, NOW())";
            
            if (!$this->db->query($query)) {
                throw new Exception('Failed to create event: ' . $this->db->error, 500);
            }

            $event_id = $this->db->insert_id;

            echo json_encode([
                'success' => true,
                'message' => 'Event created successfully',
                'event_id' => $event_id,
                'data' => [
                    'event_id' => $event_id,
                    'title' => $input['title'],
                    'event_date' => $event_date
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

    // READ - Get events for a user
    public function read()
    {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $u_id = (int)$_SESSION['user']['u_id'];
            $month = isset($_GET['month']) ? (int)$_GET['month'] : null;
            $year = isset($_GET['year']) ? (int)$_GET['year'] : null;

            $query = "SELECT event_id, title, description, event_date, start_time, end_time, 
                      is_all_day, created_at, updated_at 
                      FROM calendar_events 
                      WHERE u_id = $u_id";

            if ($month && $year) {
                $query .= " AND MONTH(event_date) = $month AND YEAR(event_date) = $year";
            }

            $query .= " ORDER BY event_date ASC, start_time ASC";

            $result = $this->db->query($query);
            
            if (!$result) {
                throw new Exception('Failed to fetch events: ' . $this->db->error, 500);
            }

            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }

            echo json_encode([
                'success' => true,
                'count' => count($events),
                'data' => $events
            ]);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // UPDATE - Modify existing event
    public function update()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
                throw new Exception('Method not allowed', 405);
            }

            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $u_id = (int)$_SESSION['user']['u_id'];
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['event_id'])) {
                throw new Exception('Event ID is required', 400);
            }

            $event_id = (int)$input['event_id'];

            // Verify event belongs to user
            $checkQuery = "SELECT event_id FROM calendar_events WHERE event_id = $event_id AND u_id = $u_id";
            $result = $this->db->query($checkQuery);
            
            if (!$result || $result->num_rows === 0) {
                throw new Exception('Event not found or access denied', 404);
            }

            // Build dynamic update query
            $updates = [];

            if (isset($input['title'])) {
                $title = $this->db->real_escape_string(trim($input['title']));
                $updates[] = "title = '$title'";
            }

            if (isset($input['description'])) {
                $description = $this->db->real_escape_string(trim($input['description']));
                $updates[] = "description = '$description'";
            }

            if (isset($input['event_date'])) {
                $date = DateTime::createFromFormat('Y-m-d', $input['event_date']);
                if (!$date) {
                    throw new Exception('Invalid date format', 400);
                }
                $event_date = $this->db->real_escape_string($input['event_date']);
                $updates[] = "event_date = '$event_date'";
            }

            if (isset($input['start_time'])) {
                $start_time = $this->db->real_escape_string($input['start_time']);
                $updates[] = "start_time = '$start_time'";
            }

            if (isset($input['end_time'])) {
                $end_time = $this->db->real_escape_string($input['end_time']);
                $updates[] = "end_time = '$end_time'";
            }

            if (isset($input['is_all_day'])) {
                $is_all_day = (int)$input['is_all_day'];
                $updates[] = "is_all_day = $is_all_day";
            }

            if (empty($updates)) {
                throw new Exception('No fields to update', 400);
            }

            $updates[] = "updated_at = NOW()";

            $query = "UPDATE calendar_events SET " . implode(', ', $updates) . 
                     " WHERE event_id = $event_id AND u_id = $u_id";

            if (!$this->db->query($query)) {
                throw new Exception('Failed to update event: ' . $this->db->error, 500);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Event updated successfully'
            ]);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // DELETE - Remove event
    public function delete()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Method not allowed', 405);
            }

            if (!isset($_SESSION['user']['u_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            $u_id = (int)$_SESSION['user']['u_id'];
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['event_id'])) {
                throw new Exception('Event ID is required', 400);
            }

            $event_id = (int)$input['event_id'];

            // Verify event belongs to user
            $checkQuery = "SELECT event_id FROM calendar_events WHERE event_id = $event_id AND u_id = $u_id";
            $result = $this->db->query($checkQuery);
            
            if (!$result || $result->num_rows === 0) {
                throw new Exception('Event not found or access denied', 404);
            }

            // Delete the event
            $query = "DELETE FROM calendar_events WHERE event_id = $event_id AND u_id = $u_id";
            
            if (!$this->db->query($query)) {
                throw new Exception('Failed to delete event: ' . $this->db->error, 500);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Event deleted successfully'
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