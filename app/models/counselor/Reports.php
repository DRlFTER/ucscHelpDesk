<?php

/**
 * CounselorReports Model
 * Location: models/counselor/Reports.php
 */

class CounselorReports
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get overview statistics for counselor reports
     */
    public function getOverviewStats($startDate, $endDate, $counselorId = null)
    {
        $whereClauses = ["d.did = 10"];
        $params = [];
        $types = '';

        if ($startDate) {
            $whereClauses[] = "t.created_at >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $whereClauses[] = "t.created_at <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
        if ($counselorId) {
            $whereClauses[] = "t.assigned_to = ?";
            $params[] = $counselorId;
            $types .= 'i';
        }

        $whereSQL = implode(' AND ', $whereClauses);

        $sql = "SELECT 
                    COUNT(*) as total_tickets,
                    SUM(CASE WHEN status IN ('resolved', 'closed', 'agent-closed') THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'agent assigned' THEN 1 ELSE 0 END) as assigned,
                    AVG(CASE 
                        WHEN tl.resolved IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, t.created_at, tl.resolved) 
                    END) as avg_resolution_hours
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                LEFT JOIN ticket_timeline tl ON tl.ticket_id = t.ticket_id
                WHERE $whereSQL";

        return $this->executeQuery($sql, $params, $types, true);
    }

    /**
     * Get tickets by category
     */
    public function getTicketsByCategory($startDate, $endDate)
    {
        $sql = "SELECT 
                    d.name as category,
                    COUNT(*) as count
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE d.did = 10
                    AND t.created_at >= ?
                    AND t.created_at <= ?
                GROUP BY d.name
                ORDER BY count DESC";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss');
    }

    /**
     * Get tickets by priority
     */
    public function getTicketsByPriority($startDate, $endDate)
    {
        $sql = "SELECT 
                    t.priority,
                    COUNT(*) as count,
                    SUM(CASE WHEN status IN ('resolved', 'closed', 'agent-closed') THEN 1 ELSE 0 END) as resolved
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE d.did = 10
                    AND t.created_at >= ?
                    AND t.created_at <= ?
                GROUP BY t.priority
                ORDER BY FIELD(t.priority, 'high', 'normal', 'low')";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss');
    }

    /**
     * Get daily ticket volume
     */
    public function getDailyVolume($startDate, $endDate)
    {
        $sql = "SELECT 
                    DATE(t.created_at) as date,
                    COUNT(*) as count
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE d.did = 10
                    AND t.created_at >= ?
                    AND t.created_at <= ?
                GROUP BY DATE(t.created_at)
                ORDER BY date ASC";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss');
    }

    /**
     * Get counselor performance
     */
    public function getCounselorPerformance($startDate, $endDate)
    {
        $sql = "SELECT 
                    u.u_id,
                    u.name as counselor_name,
                    COUNT(t.ticket_id) as total_assigned,
                    SUM(CASE WHEN t.status IN ('resolved', 'closed', 'agent-closed') THEN 1 ELSE 0 END) as resolved_count,
                    AVG(CASE 
                        WHEN tl.resolved IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, tl.assigned, tl.resolved) 
                    END) as avg_resolution_hours
                FROM users u
                LEFT JOIN tickets t ON t.assigned_to = u.u_id 
                    AND t.created_at >= ? 
                    AND t.created_at <= ?
                LEFT JOIN division d ON d.did = t.division
                LEFT JOIN ticket_timeline tl ON tl.ticket_id = t.ticket_id
                WHERE u.role = 'counselor'
                    AND (t.ticket_id IS NULL OR d.did = 10)
                GROUP BY u.u_id, u.name
                HAVING total_assigned > 0
                ORDER BY total_assigned DESC";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss');
    }

    /**
     * Get meeting statistics
     */
    public function getMeetingStats($startDate, $endDate)
    {
        $sql = "SELECT 
                    COUNT(*) as total_tickets,
                    SUM(CASE WHEN meeting_requested = 'requested' THEN 1 ELSE 0 END) as requested,
                    SUM(CASE WHEN meeting_requested = 'scheduled' THEN 1 ELSE 0 END) as scheduled
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE d.did = 10
                    AND t.created_at >= ?
                    AND t.created_at <= ?";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss', true);
    }

    /**
     * Get student engagement stats
     */
    public function getStudentEngagement($startDate, $endDate)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT t.u_id) as unique_students,
                    COUNT(t.ticket_id) as total_tickets
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                WHERE d.did = 10
                    AND t.created_at >= ?
                    AND t.created_at <= ?";

        return $this->executeQuery($sql, [$startDate, $endDate], 'ss', true);
    }

    /**
     * Execute query helper
     */
    private function executeQuery($sql, $params = [], $types = '', $singleRow = false)
    {
        if (empty($params)) {
            $result = $this->db->query($sql);
            if (!$result) return $singleRow ? null : [];
            
            if ($singleRow) {
                $row = $result->fetch_assoc();
                $result->free();
                return $row;
            }
            
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
            return $rows;
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return $singleRow ? null : [];

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($singleRow) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row;
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }



/**
 * ADD THIS METHOD TO: models/counselor/Reports.php
 * Add to the CounselorReports class
 */

/**
 * Get filtered tickets for report generation
 */
public function getFilteredTickets($startDate, $endDate, $status = null, $priority = null)
{
    $whereClauses = ["d.did = 10"];  // Counselling division
    $params = [];
    $types = '';

    // Date range filters
    if ($startDate) {
        $whereClauses[] = "t.created_at >= ?";
        $params[] = $startDate;
        $types .= 's';
    }
    if ($endDate) {
        $whereClauses[] = "t.created_at <= ?";
        $params[] = $endDate;
        $types .= 's';
    }

    // Status filter
    if ($status && $status !== 'all') {
        $whereClauses[] = "t.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Priority filter
    if ($priority && $priority !== 'all') {
        $whereClauses[] = "t.priority = ?";
        $params[] = $priority;
        $types .= 's';
    }

    $whereSQL = implode(' AND ', $whereClauses);

    $sql = "SELECT 
                t.ticket_id,
                t.title,
                t.status,
                t.priority,
                t.created_at,
                u.name as student_name,
                d.name as division
            FROM tickets t
            LEFT JOIN users u ON u.u_id = t.u_id
            LEFT JOIN division d ON d.did = t.division
            WHERE $whereSQL
            ORDER BY t.created_at DESC";

    return $this->executeQuery($sql, $params, $types);
}
}