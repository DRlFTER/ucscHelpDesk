<?php
require_once __DIR__ . '/../../core/config.php';

class StaffReport
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
     * Report 1: All Tickets by status/priority, date range.
     */
   public function getAllTicketsReport(string $start_date = '', string $end_date = '', string $status = '', string $priority = ''): array
    {
        $conn = self::getConnection();
        $staff_id = $_SESSION['user']['u_id'];
        $divisions = $this->getStaffDivisions($staff_id);
        $sql = "SELECT t.ticket_id, t.title, t.status, t.priority, t.created_at, u.name AS student_name, d.name AS category
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                LEFT JOIN division d ON d.did = t.division
                WHERE t.division = ?";
        $params = [(int)$divisions[0]['did']];  // Assuming single division for staff
        $types = 'i';

        if ($start_date) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $start_date . ' 00:00:00';
            $types .= 's';
        }
        if ($end_date) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $end_date . ' 23:59:59';
            $types .= 's';
        }
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if ($priority) {
            $sql .= " AND t.priority = ?";
            $params[] = $priority;
            $types .= 's';
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $reports;
    }

    /**
     * Get summary stats for All Tickets Report.
     */
    public function getAllTicketsSummary(string $start_date = '', string $end_date = '', string $status = '', string $priority = ''): array
    {
        $conn = self::getConnection();
        $staff_id = $_SESSION['user']['u_id'];
        $divisions = $this->getStaffDivisions($staff_id);
        $sql = "SELECT 
                    COUNT(*) AS total_tickets,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending_count,
                    COUNT(CASE WHEN status = 'resolved' THEN 1 END) AS resolved_count,
                    COUNT(CASE WHEN status = 'agent-closed' THEN 1 END) AS agent_closed_count,
                    COUNT(CASE WHEN status = 'closed' THEN 1 END) AS closed_count,
                    COUNT(CASE WHEN status = 'agent assigned' THEN 1 END) AS agent_assigned_count,
                    AVG(DATEDIFF(NOW(), created_at)) AS avg_age_days
                FROM tickets t
                WHERE t.division = ?";
        $params = [(int)$divisions[0]['did']];  // Assuming single division for staff
        $types = 'i';

        if ($start_date) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $start_date . ' 00:00:00';
            $types .= 's';
        }
        if ($end_date) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $end_date . ' 23:59:59';
            $types .= 's';
        }
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if ($priority) {
            $sql .= " AND t.priority = ?";
            $params[] = $priority;
            $types .= 's';
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc() ?: [];
        $summary['pending_pct'] = $summary['total_tickets'] ? round(($summary['pending_count'] / $summary['total_tickets']) * 100, 1) : 0;
        $summary['resolved_pct'] = $summary['total_tickets'] ? round(($summary['resolved_count'] / $summary['total_tickets']) * 100, 1) : 0;
        $summary['agent_assigned_pct'] = $summary['total_tickets'] ? round(($summary['agent_assigned_count'] / $summary['total_tickets']) * 100, 1) : 0;
        $summary['agent_closed_pct'] = $summary['total_tickets'] ? round(($summary['agent_closed_count'] / $summary['total_tickets']) * 100, 1) : 0;
        $summary['closed_pct'] = $summary['total_tickets'] ? round(($summary['closed_count'] / $summary['total_tickets']) * 100, 1) : 0;
        $stmt->close();
        $conn->close();
        return $summary;
    }

    /**
     * Report 2: Overdue Tickets (>3 days pending), by division.
     */
    public function getOverdueTicketsReport(string $division_id = ''): array
    {
        $conn = self::getConnection();
        $staff_id = $_SESSION['user']['u_id'];
        $divisions = $this->getStaffDivisions($staff_id);
        $sql = "SELECT t.ticket_id, t.title, t.created_at, DATEDIFF(NOW(), t.created_at) AS days_overdue, u.name AS student_name, d.name AS category
                FROM tickets t
                INNER JOIN users u ON t.u_id = u.u_id
                LEFT JOIN division d ON d.did = t.division
                WHERE t.status = 'pending' AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY) AND t.division = ?";
        $params = [(int)$divisions[0]['did']];  // Assuming single division for staff
        $types = 'i';

        if ($division_id) {
            $sql .= " AND t.division = ?";
            $params[] = (int)$division_id;
            $types .= 'i';
        }

        $sql .= " ORDER BY t.created_at ASC";  // Oldest first

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $reports;
    }

    /**
     * Get summary stats for Overdue Tickets Report.
     */
    /**
 * Get summary stats for Overdue Tickets Report. FIXED: Added category breakdown for pie chart.
 */
/**
 * Get summary stats for Overdue Tickets Report. FIXED: Filter by staff's division.
 */
/**
 * Get summary stats for Overdue Tickets Report - NOW BY DAYS OVERDUE
 */
public function getOverdueTicketsSummary(string $division_id = ''): array
{
    $conn = self::getConnection();
    $staff_id = $_SESSION['user']['u_id'];
    $divisions = $this->getStaffDivisions($staff_id);
    $did = (int)$divisions[0]['did'];

    // Main summary
    $sql = "SELECT 
                COUNT(*) AS total_overdue,
                AVG(DATEDIFF(NOW(), t.created_at)) AS avg_days_overdue
            FROM tickets t
            WHERE t.status = 'pending' 
              AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY) 
              AND t.division = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $did);
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();

    // === NEW: Days Overdue Breakdown ===
    $days_sql = "SELECT 
                    COUNT(CASE WHEN DATEDIFF(NOW(), t.created_at) BETWEEN 20 AND 50 THEN 1 END) AS `20-50`,
                    COUNT(CASE WHEN DATEDIFF(NOW(), t.created_at) BETWEEN 50 AND 150 THEN 1 END) AS `50-150`,
                    COUNT(CASE WHEN DATEDIFF(NOW(), t.created_at) BETWEEN 150 AND 200 THEN 1 END) AS `150-200`,
                    COUNT(CASE WHEN DATEDIFF(NOW(), t.created_at) > 200 THEN 1 END) AS `200+`
                FROM tickets t
                WHERE t.status = 'pending' 
                  AND t.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY) 
                  AND t.division = ?";

    $stmt2 = $conn->prepare($days_sql);
    $stmt2->bind_param('i', $did);
    $stmt2->execute();
    $days_row = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();

    $summary['days_breakdown'] = json_encode([
        '20-50 days'  => (int)($days_row['20-50'] ?? 0),
        '50-150 days' => (int)($days_row['50-150'] ?? 0),
        '150-200 days'=> (int)($days_row['150-200'] ?? 0),
        '200+ days'  => (int)($days_row['200+'] ?? 0)
    ]);

    $conn->close();
    return $summary;
}

    /**
     * Report 3: Tickets per Staff Member (assignments). FIXED with working JOIN logic.
     */
   /**
 * Report 3: Tickets per Staff Member (assignments).
 */
/**
 * Report 3: Currently Agent-Assigned Tickets per Staff
 */
public function getStaffAssignmentReport(string $start_date = '', string $end_date = ''): array
{
    $conn = self::getConnection();
    $staff_id = $_SESSION['user']['u_id'];
    $divisions = $this->getStaffDivisions($staff_id);
    $did_list = array_column($divisions, 'did');

    $sql = "SELECT 
                u.name AS staff_name, 
                u.email, 
                COUNT(t.ticket_id) AS ticket_count,
                'agent assigned' AS status
            FROM users u
            LEFT JOIN tickets t 
                ON t.assigned_to = u.u_id 
                AND t.status = 'agent assigned'
            WHERE u.role = 'staff' 
              AND t.division IN (" . implode(',', array_fill(0, count($did_list), '?')) . ")";

    $params = $did_list;
    $types = str_repeat('i', count($did_list));

    if ($start_date) {
        $sql .= " AND t.created_at >= ?";
        $params[] = $start_date . ' 00:00:00';
        $types .= 's';
    }
    if ($end_date) {
        $sql .= " AND t.created_at <= ?";
        $params[] = $end_date . ' 23:59:59';
        $types .= 's';
    }

    $sql .= " GROUP BY u.u_id, u.name, u.email 
              HAVING ticket_count > 0 
              ORDER BY ticket_count DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $reports;
}


    /**
     * Get summary stats for Staff Assignment Report.
     */
   public function getStaffAssignmentSummary(string $start_date = '', string $end_date = ''): array
{
    $conn = self::getConnection();
    $staff_id = $_SESSION['user']['u_id'];
    $divisions = $this->getStaffDivisions($staff_id);
    $did_list = array_column($divisions, 'did');

    $sql = "SELECT 
                COUNT(DISTINCT u.u_id) AS total_staff, 
                COUNT(t.ticket_id) AS total_assignments
            FROM users u
            LEFT JOIN tickets t 
                ON t.assigned_to = u.u_id 
                AND t.status = 'agent assigned'
            WHERE u.role = 'staff' 
              AND t.division IN (" . implode(',', array_fill(0, count($did_list), '?')) . ")";

    $params = $did_list;
    $types = str_repeat('i', count($did_list));

    if ($start_date) {
        $sql .= " AND t.created_at >= ?";
        $params[] = $start_date . ' 00:00:00';
        $types .= 's';
    }
    if ($end_date) {
        $sql .= " AND t.created_at <= ?";
        $params[] = $end_date . ' 23:59:59';
        $types .= 's';
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    $conn->close();

    $summary['avg_per_staff'] = $summary['total_staff'] 
        ? round($summary['total_assignments'] / $summary['total_staff'], 1) 
        : 0;

    return $summary;
}

    /**
     * Report 4: Escalations by Level (from ticket_timeline).
     */
    public function getEscalationReport(string $start_date = '', string $end_date = '', int $level = 0): array
    {
        $conn = self::getConnection();
        $staff_id = $_SESSION['user']['u_id'];
        $divisions = $this->getStaffDivisions($staff_id);

        $sql = "SELECT t.ticket_id, t.title, t.created_at AS ticket_date, tt.level_1, tt.level_2, tt.level_3, u.name AS student_name
                FROM tickets t
                INNER JOIN ticket_timeline tt ON t.ticket_id = tt.ticket_id
                INNER JOIN users u ON t.u_id = u.u_id
                WHERE 1=1 AND t.division IN (" . implode(',', array_map(fn($d) => $d['did'], $divisions)) . ")";
        $params = [];
        $types = '';

        if ($start_date) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $start_date . ' 00:00:00';
            $types .= 's';
        }
        if ($end_date) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $end_date . ' 23:59:59';
            $types .= 's';
        }
        if ($level > 0) {
            $column = "level_$level";
            $sql .= " AND tt.$column IS NOT NULL AND tt.$column != '0000-00-00 00:00:00'";
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $reports;
    }

    /**
     * Get summary stats for Escalation Report.
     */
    public function getEscalationSummary(string $start_date = '', string $end_date = '', int $level = 0): array
    {
        $conn = self::getConnection();
        $sql = "SELECT 
                    COUNT(*) AS total_escalations,
                    COUNT(CASE WHEN level_1 IS NOT NULL AND level_1 != '0000-00-00 00:00:00' THEN 1 END) AS level1_count,
                    COUNT(CASE WHEN level_2 IS NOT NULL AND level_2 != '0000-00-00 00:00:00' THEN 1 END) AS level2_count,
                    COUNT(CASE WHEN level_3 IS NOT NULL AND level_3 != '0000-00-00 00:00:00' THEN 1 END) AS level3_count
                FROM ticket_timeline tt
                INNER JOIN tickets t ON tt.ticket_id = t.ticket_id
                WHERE 1=1";
        $params = [];
        $types = '';

        if ($start_date) {
            $sql .= " AND t.created_at >= ?";
            $params[] = $start_date . ' 00:00:00';
            $types .= 's';
        }
        if ($end_date) {
            $sql .= " AND t.created_at <= ?";
            $params[] = $end_date . ' 23:59:59';
            $types .= 's';
        }
        if ($level > 0) {
            $column = "level_$level";
            $sql .= " AND tt.$column IS NOT NULL AND tt.$column != '0000-00-00 00:00:00'";
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc() ?: [];
        $total = $summary['total_escalations'] ?? 0;
        $summary['level1_pct'] = $total ? round(($summary['level1_count'] / $total) * 100, 1) : 0;
        $summary['level2_pct'] = $total ? round(($summary['level2_count'] / $total) * 100, 1) : 0;
        $summary['level3_pct'] = $total ? round(($summary['level3_count'] / $total) * 100, 1) : 0;
        $stmt->close();
        $conn->close();
        return $summary;
    }

    /**
     * Get divisions for report filters.
     */
    public function getDivisions(): array
    {
        $conn = self::getConnection();
        $staff_id = $_SESSION['user']['u_id'];
        $divisions = $this->getStaffDivisions($staff_id);
        $sql = "SELECT did, name FROM division WHERE did IN (" . implode(',', array_map(fn($d) => (int)$d['did'], $divisions)) . ") ORDER BY name";
        $result = $conn->query($sql);
        $divisions = [];
        while ($row = $result->fetch_assoc()) {
            $divisions[] = $row;
        }
        $conn->close();
        return $divisions;
    }


/**
 * Report: Ticket Volume & Trends (monthly/quarterly grouping)
 * 
 * Returns ticket counts grouped by time period (month or quarter) 
 * and optionally by category/division.
 * Useful for spotting seasonal peaks and trends.
 */
public function getTicketVolumeTrendsReport(
    string $start_date = '',
    string $end_date = '',
    string $group_by = 'month',      // 'month' or 'quarter'
    int $division_id = 0,             // optional specific division filter
    string $category_name = ''        // optional exact category name filter
): array {
    $conn = self::getConnection();
    $staff_id = $_SESSION['user']['u_id'];
    $divisions = $this->getStaffDivisions($staff_id);
    $allowed_dids = array_column($divisions, 'did');

    // Build safe IN clause for divisions staff has access to
    $div_in = implode(',', array_fill(0, count($allowed_dids), '?'));

    $sql = "
        SELECT 
            DATE_FORMAT(t.created_at, '%Y-%m') AS period,
            d.name AS category,
            COUNT(t.ticket_id) AS ticket_count
        FROM tickets t
        LEFT JOIN division d ON d.did = t.division
        WHERE t.division IN ($div_in)
    ";
    $params = $allowed_dids;
    $types = str_repeat('i', count($allowed_dids));

    if ($start_date) {
        $sql .= " AND t.created_at >= ?";
        $params[] = $start_date . ' 00:00:00';
        $types .= 's';
    }
    if ($end_date) {
        $sql .= " AND t.created_at <= ?";
        $params[] = $end_date . ' 23:59:59';
        $types .= 's';
    }
    if ($division_id > 0 && in_array($division_id, $allowed_dids)) {
        $sql .= " AND t.division = ?";
        $params[] = $division_id;
        $types .= 'i';
    }
    if ($category_name !== '') {
        $sql .= " AND d.name = ?";
        $params[] = $category_name;
        $types .= 's';
    }

    // Group by period (and category if not filtering to one)
    if ($group_by === 'quarter') {
        $sql .= " GROUP BY YEAR(t.created_at), QUARTER(t.created_at), d.name";
        $sql = str_replace(
            "DATE_FORMAT(t.created_at, '%Y-%m') AS period",
            "CONCAT(YEAR(t.created_at), '-Q', QUARTER(t.created_at)) AS period",
            $sql
        );
    } else {
        // default: monthly
        $sql .= " GROUP BY DATE_FORMAT(t.created_at, '%Y-%m'), d.name";
    }

    $sql .= " ORDER BY period ASC, ticket_count DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $reports;
    }
}



?>