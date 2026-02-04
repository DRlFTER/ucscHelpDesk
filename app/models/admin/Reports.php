<?php
require_once __DIR__ . '/../../core/config.php';

class AdminReportModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== Helper Methods ====================
    
    /**
     * Get all divisions for filter dropdown
     */
    public function getDivisions(): array
    {
        $sql = "SELECT did, name FROM division ORDER BY name";
        $result = $this->db->query($sql);
        $divisions = [];
        while ($row = $result->fetch_assoc()) {
            $divisions[] = $row;
        }
        return $divisions;
    }

    /**
     * Get all staff members for filter dropdown
     */
    public function getStaffMembers(): array
    {
        $sql = "SELECT u_id, name, email FROM users WHERE role = 'staff' ORDER BY name";
        $result = $this->db->query($sql);
        $staff = [];
        while ($row = $result->fetch_assoc()) {
            $staff[] = $row;
        }
        return $staff;
    }

    /**
     * Get all user roles for filter dropdown
     */
    public function getUserRoles(): array
    {
        return ['student', 'staff', 'lecturer', 'counselor', 'admin'];
    }

    /**
     * Get ticket statuses for filter dropdown
     */
    public function getTicketStatuses(): array
    {
        return ['pending', 'agent assigned', 'resolved', 'agent-closed', 'closed'];
    }

    /**
     * Get ticket priorities for filter dropdown
     */
    public function getTicketPriorities(): array
    {
        return ['high', 'medium', 'low'];
    }

    // ==================== Quick Reports ====================

    /**
     * Tickets by Status Report
     */
    public function getTicketsByStatus(string $startDate = '', string $endDate = '', string $division = ''): array
    {
        $sql = "SELECT t.status, COUNT(*) as count, d.name as division_name
                FROM tickets t
                LEFT JOIN division d ON t.division = d.did
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }
        if ($division) {
            $sql .= " AND t.division = ?";
            $params[] = (int)$division;
            $types .= 'i';
        }

        $sql .= " GROUP BY t.status, d.name ORDER BY count DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Tickets by Status Summary
     */
    public function getTicketsByStatusSummary(string $startDate = '', string $endDate = '', string $division = ''): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'agent assigned' THEN 1 END) as agent_assigned,
                    COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved,
                    COUNT(CASE WHEN status IN ('agent-closed', 'closed') THEN 1 END) as closed
                FROM tickets t WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }
        if ($division) {
            $sql .= " AND t.division = ?";
            $params[] = (int)$division;
            $types .= 'i';
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc() ?: [];
        $stmt->close();
        return $summary;
    }

    /**
     * Tickets by Category Report
     */
    public function getTicketsByCategory(string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT d.name as category, COUNT(*) as count
                FROM tickets t
                LEFT JOIN division d ON t.division = d.did
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY d.name ORDER BY count DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Tickets by Role Report
     */
    public function getTicketsByRole(string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT u.role, COUNT(*) as count
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY u.role ORDER BY count DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Resolution Time Report
     */
    public function getResolutionTimeReport(string $startDate = '', string $endDate = '', string $groupBy = 'category'): array
    {
        if ($groupBy === 'staff') {
            $sql = "SELECT u.name as group_name, 
                        AVG(TIMESTAMPDIFF(HOUR, t.created_at, tt.resolved)) as avg_hours,
                        COUNT(*) as ticket_count
                    FROM tickets t
                    INNER JOIN ticket_timeline tt ON t.ticket_id = tt.ticket_id
                    LEFT JOIN users u ON t.assigned_to = u.u_id
                    WHERE tt.resolved IS NOT NULL AND tt.resolved != '0000-00-00 00:00:00'";
        } else {
            $sql = "SELECT d.name as group_name, 
                        AVG(TIMESTAMPDIFF(HOUR, t.created_at, tt.resolved)) as avg_hours,
                        COUNT(*) as ticket_count
                    FROM tickets t
                    INNER JOIN ticket_timeline tt ON t.ticket_id = tt.ticket_id
                    LEFT JOIN division d ON t.division = d.did
                    WHERE tt.resolved IS NOT NULL AND tt.resolved != '0000-00-00 00:00:00'";
        }

        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= $groupBy === 'staff' ? " GROUP BY u.name" : " GROUP BY d.name";
        $sql .= " ORDER BY avg_hours ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Staff Performance Report
     */
    public function getStaffPerformanceReport(string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT 
                    u.u_id,
                    u.name as staff_name,
                    u.email,
                    d.name as department,
                    COUNT(t.ticket_id) as assigned_tickets,
                    COUNT(CASE WHEN t.status = 'resolved' THEN 1 END) as resolved_tickets,
                    COUNT(CASE WHEN t.status IN ('agent-closed', 'closed') THEN 1 END) as closed_tickets,
                    AVG(TIMESTAMPDIFF(HOUR, t.created_at, tt.assigned)) as avg_response_hours
                FROM users u
                LEFT JOIN tickets t ON u.u_id = t.assigned_to
                LEFT JOIN ticket_timeline tt ON t.ticket_id = tt.ticket_id
                LEFT JOIN staff_division sd ON u.u_id = sd.u_id
                LEFT JOIN division d ON sd.did = d.did
                WHERE u.role = 'staff'";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND (t.created_at IS NULL OR t.created_at >= ?)";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND (t.created_at IS NULL OR t.created_at <= ?)";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY u.u_id, u.name, u.email, d.name ORDER BY resolved_tickets DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Most Active Users Report
     */
    public function getMostActiveUsersReport(string $startDate = '', string $endDate = '', int $limit = 20): array
    {
        $sql = "SELECT 
                    u.u_id,
                    u.name,
                    u.email,
                    u.role,
                    COUNT(t.ticket_id) as ticket_count
                FROM users u
                INNER JOIN tickets t ON u.u_id = t.u_id
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY u.u_id, u.name, u.email, u.role ORDER BY ticket_count DESC LIMIT ?";
        $params[] = $limit;
        $types .= 'i';

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Ticket Volume Trend Report (Daily/Weekly/Monthly)
     */
    public function getTicketVolumeTrend(string $startDate = '', string $endDate = '', string $period = 'daily'): array
    {
        switch ($period) {
            case 'weekly':
                $groupFormat = "YEARWEEK(t.created_at, 1)";
                $dateFormat = "DATE_FORMAT(MIN(t.created_at), '%Y-W%u')";
                break;
            case 'monthly':
                $groupFormat = "DATE_FORMAT(t.created_at, '%Y-%m')";
                $dateFormat = "DATE_FORMAT(t.created_at, '%Y-%m')";
                break;
            default: // daily
                $groupFormat = "DATE(t.created_at)";
                $dateFormat = "DATE(t.created_at)";
        }

        $sql = "SELECT $dateFormat as period, COUNT(*) as count
                FROM tickets t
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY $groupFormat ORDER BY period ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Unresolved Tickets Over Time Report
     */
    public function getUnresolvedTicketsReport(string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT 
                    DATE(t.created_at) as date,
                    COUNT(*) as unresolved_count,
                    AVG(DATEDIFF(NOW(), t.created_at)) as avg_days_pending
                FROM tickets t
                WHERE t.status IN ('pending', 'agent assigned')";
        $params = [];
        $types = '';

        if ($startDate) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY DATE(t.created_at) ORDER BY date ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // ==================== Custom Report Generator ====================

    /**
     * Custom Report - Flexible query based on filters
     */
    public function getCustomReport(array $filters): array
    {
        $sql = "SELECT 
                    t.ticket_id,
                    t.title,
                    t.status,
                    t.priority,
                    t.t_type,
                    t.created_at,
                    t.description,
                    u.name as submitted_by,
                    u.email as user_email,
                    u.role as user_role,
                    d.name as category,
                    staff.name as assigned_staff
                FROM tickets t
                LEFT JOIN users u ON t.u_id = u.u_id
                LEFT JOIN division d ON t.division = d.did
                LEFT JOIN users staff ON t.assigned_to = staff.u_id
                WHERE 1=1";
        $params = [];
        $types = '';

        // Date range
        if (!empty($filters['start_date'])) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $filters['start_date'] . ' 00:00:00';
            $types .= 's';
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $filters['end_date'] . ' 23:59:59';
            $types .= 's';
        }

        // Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        // Category/Division filter
        if (!empty($filters['category'])) {
            $sql .= " AND t.division = ?";
            $params[] = (int)$filters['category'];
            $types .= 'i';
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = ?";
            $params[] = $filters['priority'];
            $types .= 's';
        }

        // Assigned staff filter
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = ?";
            $params[] = (int)$filters['assigned_to'];
            $types .= 'i';
        }

        // User role filter
        if (!empty($filters['user_role'])) {
            $sql .= " AND u.role = ?";
            $params[] = $filters['user_role'];
            $types .= 's';
        }

        $sql .= " ORDER BY t.created_at DESC";

        // Limit
        if (!empty($filters['limit']) && (int)$filters['limit'] > 0) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
            $types .= 'i';
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /**
     * Get custom report summary stats
     */
    public function getCustomReportSummary(array $filters): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_tickets,
                    COUNT(CASE WHEN t.status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN t.status = 'resolved' THEN 1 END) as resolved,
                    COUNT(CASE WHEN t.priority = 'high' THEN 1 END) as high_priority,
                    AVG(DATEDIFF(NOW(), t.created_at)) as avg_age_days
                FROM tickets t
                LEFT JOIN users u ON t.u_id = u.u_id
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['start_date'])) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $filters['start_date'] . ' 00:00:00';
            $types .= 's';
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $filters['end_date'] . ' 23:59:59';
            $types .= 's';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['category'])) {
            $sql .= " AND t.division = ?";
            $params[] = (int)$filters['category'];
            $types .= 'i';
        }
        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = ?";
            $params[] = $filters['priority'];
            $types .= 's';
        }
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = ?";
            $params[] = (int)$filters['assigned_to'];
            $types .= 'i';
        }
        if (!empty($filters['user_role'])) {
            $sql .= " AND u.role = ?";
            $params[] = $filters['user_role'];
            $types .= 's';
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $this->db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc() ?: [];
        $stmt->close();
        return $summary;
    }
}
