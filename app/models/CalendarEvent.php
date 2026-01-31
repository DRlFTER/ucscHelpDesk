<?php

require_once __DIR__ . '/../core/Database.php';

class CalendarEvent
{
    public function getUpcomingEvents(int $u_id, int $limit = 3): array
    {
        $db = Database::getInstance();
        $sql = "SELECT event_id, title, description, event_date, start_time, end_time, is_all_day
                FROM calendar_events
                WHERE u_id = ? AND event_date >= CURDATE()
                ORDER BY event_date ASC, start_time ASC
                LIMIT ?";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
             return [];
        }
        $stmt->bind_param('ii', $u_id, $limit);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
            return $rows;
        }
        $stmt->close();
        return [];
    }
}
