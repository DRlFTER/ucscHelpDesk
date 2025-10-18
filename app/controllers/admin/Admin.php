<?php

class Admin extends Controller
{
    public function dashboard()
    {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminDashboard.css"/>';

        // Build dashboard data from DB
        $db = Database::getInstance();

        // 1) Cards: totals, open, avg response time, resolution rate
        $totalTickets = 0;
        $openTickets = 0;
        $avgRespMinutes = null;
        $resolvedTickets = 0;

        // Total tickets
        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets")) {
            $row = $res->fetch_assoc();
            $totalTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        // Open tickets (pending, agent assigned)
        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('pending','agent assigned')")) {
            $row = $res->fetch_assoc();
            $openTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        // Avg first response time in minutes
        $sqlAvg = "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                   FROM tickets t
                   JOIN (
                     SELECT ticket_id, MIN(date_time) AS first_response
                     FROM ticket_response
                     GROUP BY ticket_id
                   ) tr ON tr.ticket_id = t.ticket_id";
        if ($res = $db->query($sqlAvg)) {
            $row = $res->fetch_assoc();
            $avgRespMinutes = isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
            $res->free_result();
        }

        // Resolved tickets (resolved, closed, agent-closed)
        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('resolved','closed','agent-closed')")) {
            $row = $res->fetch_assoc();
            $resolvedTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        $avgRespText = $avgRespMinutes !== null ? round($avgRespMinutes / 60, 1) . 'h' : '—';
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100) . '%' : '0%';

        $cardsData = [
            [ 'title' => 'Total Tickets', 'value' => $totalTickets, 'change' => '' ],
            [ 'title' => 'Open Tickets', 'value' => $openTickets, 'change' => '' ],
            [ 'title' => 'Average Response Time', 'value' => $avgRespText, 'change' => '' ],
            [ 'title' => 'Satisfaction Rate', 'value' => $resolutionRate, 'change' => '' ], // Using resolution rate as proxy
        ];

        // 2) Recent tickets (latest 6)
        $recentTickets = [];
        $sqlRecent = "SELECT t.ticket_id, t.title, u.name AS requester, t.created_at, t.priority
                      FROM tickets t
                      LEFT JOIN users u ON u.u_id = t.u_id
                      ORDER BY t.created_at DESC
                      LIMIT 6";
        if ($res = $db->query($sqlRecent)) {
            while ($row = $res->fetch_assoc()) {
                $recentTickets[] = [
                    'id' => (int)$row['ticket_id'],
                    'title' => (string)$row['title'],
                    'agent' => (string)($row['requester'] ?? 'Unknown'), // shown under agent label in UI
                    'time' => self::relativeTime($row['created_at']),
                    'priority' => strtoupper((string)$row['priority']),
                ];
            }
            $res->free_result();
        }

        // 3) Top agents (by number of ticket responses)
        $topAgents = [];
        $sqlTopAgents = "SELECT u.name,
                                COUNT(DISTINCT tr.ticket_id) AS resolved,
                                AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                         FROM users u
                         JOIN (
                           SELECT ticket_id, u_id, MIN(date_time) AS first_response
                           FROM ticket_response
                           GROUP BY ticket_id, u_id
                         ) tr ON tr.u_id = u.u_id
                         JOIN tickets t ON t.ticket_id = tr.ticket_id
                         WHERE u.role IN ('staff','admin','counselor','lecturer')
                         GROUP BY u.u_id, u.name
                         ORDER BY resolved DESC
                         LIMIT 5";
        if ($res = $db->query($sqlTopAgents)) {
            while ($row = $res->fetch_assoc()) {
                $avgMin = isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
                $topAgents[] = [
                    'name' => (string)$row['name'],
                    'resolved' => (int)$row['resolved'],
                    'responseTime' => $avgMin !== null ? round($avgMin / 60, 1) . 'h' : '—',
                ];
            }
            $res->free_result();
        }

        // 4) Trends (last 4 weeks: tickets created per ISO week, and those that are currently resolved)
        $trends = [ 'labels' => [], 'new' => [], 'resolved' => [] ];
        // Build 4 weekly buckets ending this week
        $now = new DateTime('now');
        // Clone to start of this week (Mon)
        $weekStart = clone $now;
        $weekStart->modify('monday this week')->setTime(0, 0, 0);
        for ($i = 3; $i >= 0; $i--) {
            $start = (clone $weekStart)->modify("-$i week");
            $end = (clone $start)->modify('+1 week');

            $label = 'Week ' . (4 - $i);
            $trends['labels'][] = $label;

            $startEsc = $db->real_escape_string($start->format('Y-m-d H:i:s'));
            $endEsc = $db->real_escape_string($end->format('Y-m-d H:i:s'));

            // New tickets in this week
            $qNew = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= '$startEsc' AND created_at < '$endEsc'";
            $countNew = 0;
            if ($res = $db->query($qNew)) { $r = $res->fetch_assoc(); $countNew = (int)($r['c'] ?? 0); $res->free_result(); }
            $trends['new'][] = $countNew;

            // Resolved tickets created this week (approximation)
            $qRes = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= '$startEsc' AND created_at < '$endEsc' AND status IN ('resolved','closed','agent-closed')";
            $countRes = 0;
            if ($res = $db->query($qRes)) { $r = $res->fetch_assoc(); $countRes = (int)($r['c'] ?? 0); $res->free_result(); }
            $trends['resolved'][] = $countRes;
        }

        // 5) Tickets by category
        $categories = [ 'labels' => [], 'data' => [] ];
        if ($res = $db->query("SELECT COALESCE(category,'Other') AS category, COUNT(*) AS c FROM tickets GROUP BY category ORDER BY c DESC")) {
            while ($row = $res->fetch_assoc()) {
                $categories['labels'][] = (string)$row['category'];
                $categories['data'][] = (int)$row['c'];
            }
            $res->free_result();
        }

        // 6) Platform status (no table yet; keep static placeholders)
        $platformStatus = [
            [ 'name' => 'Student Portal', 'status' => 'Operational' ],
            [ 'name' => 'Lecturer Portal', 'status' => 'Operational' ],
            [ 'name' => 'Email Notifications', 'status' => 'Degraded' ],
            [ 'name' => 'Ticketing System', 'status' => 'Operational' ],
        ];

        $this->view('adminDashboard', [
            'title' => 'Admin Dashboard',
            'head' => $headContent,
            // data
            'cardsData' => $cardsData,
            'recentTickets' => $recentTickets,
            'topAgents' => $topAgents,
            'trends' => $trends,
            'categories' => $categories,
            'platformStatus' => $platformStatus,
        ]);
    }

    public function tickets() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminTickets.css"/>';
        $this->view('adminTickets', ['title' => 'Tickets', 'head' => $headContent]);
    }

    public function ticket() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminTicketFull.css"/>';
        $this->view('adminTicketFull', ['title' => 'Ticket Details', 'head' => $headContent]);
    }
    public function users() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminUsers.css"/>';
        $this->view('adminUsers', ['title' => 'User Management', 'head' => $headContent]);
    }

    /**
     * Return users for admin as JSON for the Users page.
     * Supports pagination, search, and filters (type/role and designation).
     * Response shape:
     * {
     *   data: [
     *     { id, name, email, role, designation, number, year }
     *   ],
     *   meta: { page, perPage, total, totalPages, designations: string[] }
     * }
     */
    public function usersData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $db = Database::getInstance();

        // Query params
        $page       = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage    = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search     = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $type       = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
        $designation= isset($_GET['designation']) ? trim((string)$_GET['designation']) : '';

        $where = [];

        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(u.name LIKE '%$s%' OR u.email LIKE '%$s%' OR u.number LIKE '%$s%' OR u.designation LIKE '%$s%')";
        }

        if ($type !== '') {
            // Whitelist allowed roles
            $role = strtolower($type);
            $allowed = ['staff','student','lecturer','admin','counselor'];
            if (in_array($role, $allowed, true)) {
                $roleEsc = $db->real_escape_string($role);
                $where[] = "u.role = '$roleEsc'";
            }
        }

        if ($designation !== '') {
            $d = $db->real_escape_string($designation);
            $where[] = "COALESCE(u.designation,'') = '$d'";
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        // Total count
        $total = 0;
        $countSql = "SELECT COUNT(*) AS c FROM users u $whereSql";
        if ($res = $db->query($countSql)) {
            $row = $res->fetch_assoc();
            $total = (int)($row['c'] ?? 0);
            $res->free();
        }

        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        // Data query
        $sql = "SELECT u.u_id, u.name, u.email, u.role, u.designation, u.number, u.year
                FROM users u
                $whereSql
                ORDER BY u.name ASC
                LIMIT $perPage OFFSET $offset";

        $rows = [];
        if ($res = $db->query($sql)) {
            while ($r = $res->fetch_assoc()) { $rows[] = $r; }
            $res->free();
        }

        // Distinct designations for filter options (entire table, not page-limited)
        $designations = [];
        $dsql = "SELECT DISTINCT designation FROM users WHERE designation IS NOT NULL AND designation <> '' ORDER BY designation ASC";
        if ($res = $db->query($dsql)) {
            while ($r = $res->fetch_assoc()) {
                $val = (string)($r['designation'] ?? '');
                if ($val !== '') $designations[] = $val;
            }
            $res->free();
        }

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => isset($r['u_id']) ? (int)$r['u_id'] : null,
                'name' => (string)($r['name'] ?? ''),
                'email' => (string)($r['email'] ?? ''),
                'role' => (string)($r['role'] ?? ''),
                'designation' => isset($r['designation']) ? (string)$r['designation'] : null,
                'number' => isset($r['number']) ? (string)$r['number'] : null,
                'year' => isset($r['year']) ? (int)$r['year'] : null,
            ];
        }

        echo json_encode([
            'data' => $out,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'designations' => $designations,
            ],
        ]);
        exit;
    }

    /**
     * Return dashboard datasets as JSON for client-side rendering/caching.
     */
    public function dashboardData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $db = Database::getInstance();

        // 1) Cards
        $totalTickets = 0;
        $openTickets = 0;
        $avgRespMinutes = null;
        $resolvedTickets = 0;

        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets")) {
            $row = $res->fetch_assoc();
            $totalTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('pending','agent assigned')")) {
            $row = $res->fetch_assoc();
            $openTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        $sqlAvg = "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                   FROM tickets t
                   JOIN (
                     SELECT ticket_id, MIN(date_time) AS first_response
                     FROM ticket_response
                     GROUP BY ticket_id
                   ) tr ON tr.ticket_id = t.ticket_id";
        if ($res = $db->query($sqlAvg)) {
            $row = $res->fetch_assoc();
            $avgRespMinutes = isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
            $res->free_result();
        }

        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('resolved','closed','agent-closed')")) {
            $row = $res->fetch_assoc();
            $resolvedTickets = (int)($row['c'] ?? 0);
            $res->free_result();
        }

        $avgRespText = $avgRespMinutes !== null ? round($avgRespMinutes / 60, 1) . 'h' : '—';
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100) . '%' : '0%';

        $cardsData = [
            [ 'title' => 'Total Tickets', 'value' => $totalTickets, 'change' => '' ],
            [ 'title' => 'Open Tickets', 'value' => $openTickets, 'change' => '' ],
            [ 'title' => 'Average Response Time', 'value' => $avgRespText, 'change' => '' ],
            [ 'title' => 'Satisfaction Rate', 'value' => $resolutionRate, 'change' => '' ],
        ];

        // 2) Recent tickets
        $recentTickets = [];
        $sqlRecent = "SELECT t.ticket_id, t.title, u.name AS requester, t.created_at, t.priority
                      FROM tickets t
                      LEFT JOIN users u ON u.u_id = t.u_id
                      ORDER BY t.created_at DESC
                      LIMIT 6";
        if ($res = $db->query($sqlRecent)) {
            while ($row = $res->fetch_assoc()) {
                $recentTickets[] = [
                    'id' => (int)$row['ticket_id'],
                    'title' => (string)$row['title'],
                    'agent' => (string)($row['requester'] ?? 'Unknown'),
                    'time' => self::relativeTime($row['created_at']),
                    'priority' => strtoupper((string)$row['priority']),
                ];
            }
            $res->free_result();
        }

        // 3) Top agents
        $topAgents = [];
        $sqlTopAgents = "SELECT u.name,
                                COUNT(DISTINCT tr.ticket_id) AS resolved,
                                AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                         FROM users u
                         JOIN (
                           SELECT ticket_id, u_id, MIN(date_time) AS first_response
                           FROM ticket_response
                           GROUP BY ticket_id, u_id
                         ) tr ON tr.u_id = u.u_id
                         JOIN tickets t ON t.ticket_id = tr.ticket_id
                         WHERE u.role IN ('staff','admin','counselor','lecturer')
                         GROUP BY u.u_id, u.name
                         ORDER BY resolved DESC
                         LIMIT 5";
        if ($res = $db->query($sqlTopAgents)) {
            while ($row = $res->fetch_assoc()) {
                $avgMin = isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
                $topAgents[] = [
                    'name' => (string)$row['name'],
                    'resolved' => (int)$row['resolved'],
                    'responseTime' => $avgMin !== null ? round($avgMin / 60, 1) . 'h' : '—',
                ];
            }
            $res->free_result();
        }

        // 4) Trends (last 4 weeks)
        $trends = [ 'labels' => [], 'new' => [], 'resolved' => [] ];
        $now = new DateTime('now');
        $weekStart = clone $now;
        $weekStart->modify('monday this week')->setTime(0, 0, 0);
        for ($i = 3; $i >= 0; $i--) {
            $start = (clone $weekStart)->modify("-$i week");
            $end = (clone $start)->modify('+1 week');

            $label = 'Week ' . (4 - $i);
            $trends['labels'][] = $label;

            $startEsc = $db->real_escape_string($start->format('Y-m-d H:i:s'));
            $endEsc = $db->real_escape_string($end->format('Y-m-d H:i:s'));

            $qNew = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= '$startEsc' AND created_at < '$endEsc'";
            $countNew = 0;
            if ($res = $db->query($qNew)) { $r = $res->fetch_assoc(); $countNew = (int)($r['c'] ?? 0); $res->free_result(); }
            $trends['new'][] = $countNew;

            $qRes = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= '$startEsc' AND created_at < '$endEsc' AND status IN ('resolved','closed','agent-closed')";
            $countRes = 0;
            if ($res = $db->query($qRes)) { $r = $res->fetch_assoc(); $countRes = (int)($r['c'] ?? 0); $res->free_result(); }
            $trends['resolved'][] = $countRes;
        }

        // 5) Tickets by category
        $categories = [ 'labels' => [], 'data' => [] ];
        if ($res = $db->query("SELECT COALESCE(category,'Other') AS category, COUNT(*) AS c FROM tickets GROUP BY category ORDER BY c DESC")) {
            while ($row = $res->fetch_assoc()) {
                $categories['labels'][] = (string)$row['category'];
                $categories['data'][] = (int)$row['c'];
            }
            $res->free_result();
        }

        // 6) Platform status (static placeholders for now)
        $platformStatus = [
            [ 'name' => 'Student Portal', 'status' => 'Operational' ],
            [ 'name' => 'Lecturer Portal', 'status' => 'Operational' ],
            [ 'name' => 'Email Notifications', 'status' => 'Degraded' ],
            [ 'name' => 'Ticketing System', 'status' => 'Operational' ],
        ];

        echo json_encode([
            'cardsData' => $cardsData,
            'recentTickets' => $recentTickets,
            'topAgents' => $topAgents,
            'trends' => $trends,
            'categories' => $categories,
            'platformStatus' => $platformStatus,
        ]);
        exit;
    }

    /**
     * Return a single ticket's details as JSON for the full ticket view.
     * Accepts id (preferred) or code like TKT-123.
     */
    public function ticketData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');
        $db = Database::getInstance();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';
        if (!$id && $code) {
            if (preg_match('/TKT[-\s]?(\d+)/i', $code, $m)) {
                $id = (int)$m[1];
            }
        }
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ticket id']);
            exit;
        }

        $idEsc = (int)$id;
        $sql = "SELECT t.ticket_id, t.created_at, t.title, t.category, t.status, t.priority, t.description, t.u_id, u.name AS student_name
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.ticket_id = $idEsc
                LIMIT 1";

        $row = null;
        if ($res = $db->query($sql)) {
            $row = $res->fetch_assoc();
            $res->free();
        }
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        // attachments from supporting_documents
        $attachments = [];
        if ($res = $db->query("SELECT doc_name, location FROM supporting_documents WHERE ticket_id = $idEsc")) {
            while ($r = $res->fetch_assoc()) {
                $attachments[] = [
                    'name' => (string)($r['doc_name'] ?? ''),
                    'url' => '/' . ltrim((string)($r['location'] ?? ''), '/'),
                ];
            }
            $res->free();
        }

        $statusRaw = strtolower((string)($row['status'] ?? ''));
        $statusUi = ($statusRaw === 'pending' || $statusRaw === 'agent assigned') ? 'Under Review' : (in_array($statusRaw, ['resolved','closed','agent-closed']) ? 'Resolved' : ucfirst($statusRaw));

        $createdAt = $row['created_at'] ?? null;
        $createdPretty = '';
        if ($createdAt) {
            $ts = strtotime($createdAt);
            if ($ts !== false) $createdPretty = date('M d, Y \\a\\t g:i A', $ts);
        }

        echo json_encode([
            'id' => (int)$row['ticket_id'],
            'code' => 'TKT-' . (int)$row['ticket_id'],
            'title' => (string)($row['title'] ?? ''),
            'description' => (string)($row['description'] ?? ''),
            'category' => (string)($row['category'] ?? ''),
            'priority' => ucfirst((string)($row['priority'] ?? '')),
            'status' => $statusUi,
            'createdOn' => $createdPretty,
            'student' => [
                'id' => isset($row['u_id']) ? (int)$row['u_id'] : null,
                'name' => (string)($row['student_name'] ?? ''),
            ],
            'assigned' => null,
            'attachments' => $attachments,
        ]);
        exit;
    }

    /** Delete a ticket by id and return JSON */
    public function ticketDelete()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');
        $db = Database::getInstance();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }
        $idEsc = (int)$id;
        $ok = $db->query("DELETE FROM tickets WHERE ticket_id = $idEsc");
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
            exit;
        }
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Return tickets for admin as JSON for the Tickets page.
     * Shape per item:
     * {
     *   code: string,           // e.g., TKT-123
     *   createdAt: string,      // YYYY-MM-DD
     *   title: string,
     *   student: { id: int|null, name: string },
     *   category: string|null,
     *   status: 'open'|'in-progress'|'resolved'|'rejected'|string,
     *   meeting: 'none'|'requested'|'scheduled'|string,
     *   priority: 'low'|'medium'|'high'|'urgent'|string
     * }
     */
    public function ticketsData()
    {
        $this->requireLogin('admin');

        header('Content-Type: application/json');

        $db = Database::getInstance();

        // Query params for pagination and filtering
        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $category= isset($_GET['category']) ? trim((string)$_GET['category']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $priority= isset($_GET['priority']) ? trim((string)$_GET['priority']) : '';

        $where = [];
        // Search by ticket title or student name
        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(t.title LIKE '%$s%' OR u.name LIKE '%$s%')";
        }
        // Filter by category
        if ($category !== '') {
            $c = $db->real_escape_string($category);
            $where[] = "t.category = '$c'";
        }
        // Map UI status to DB statuses
        if ($status !== '') {
            $s = strtolower($status);
            if ($s === 'open') {
                $where[] = "t.status = 'pending'";
            } elseif ($s === 'in-progress') {
                $where[] = "t.status = 'agent assigned'";
            } elseif ($s === 'resolved') {
                $where[] = "t.status IN ('resolved','closed','agent-closed')";
            } else {
                // fallback to direct match
                $sEsc = $db->real_escape_string($status);
                $where[] = "t.status = '$sEsc'";
            }
        }
        // Filter by priority
        if ($priority !== '') {
            $p = $db->real_escape_string($priority);
            $where[] = "LOWER(t.priority) = LOWER('$p')";
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        // Total count for pagination
        $total = 0;
        $countSql = "SELECT COUNT(*) AS c FROM tickets t LEFT JOIN users u ON u.u_id = t.u_id $whereSql";
        if ($res = $db->query($countSql)) {
            $row = $res->fetch_assoc();
            $total = (int)($row['c'] ?? 0);
            $res->free();
        }

        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        // Data query with pagination
        $sql = "SELECT t.ticket_id, t.created_at, t.title, t.category, t.status, t.priority, t.meeting_requested, t.u_id, u.name AS student_name
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                $whereSql
                ORDER BY t.created_at DESC
                LIMIT $perPage OFFSET $offset";

        $rows = [];
        if ($res = $db->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            $res->free();
        }

        // Normalizers
        $mapStatus = function ($s) {
            $s = strtolower((string)$s);
            switch ($s) {
                case 'pending':
                    return 'open';
                case 'agent assigned':
                    return 'in-progress';
                case 'resolved':
                case 'closed':
                case 'agent-closed':
                    return 'resolved';
                default:
                    return $s ?: '';
            }
        };
        $mapMeeting = function ($m) {
            $m = strtolower(trim((string)$m));
            if ($m === 'requested') return 'requested';
            if ($m === 'scheduled') return 'scheduled';
            return 'none';
        };
        $mapDate = function ($dt) {
            if (!$dt) return '';
            $ts = strtotime($dt);
            if ($ts === false) return '';
            return date('Y-m-d', $ts);
        };

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => isset($r['ticket_id']) ? (int)$r['ticket_id'] : null,
                'code' => 'TKT-' . (string)($r['ticket_id'] ?? ''),
                'createdAt' => $mapDate($r['created_at'] ?? null),
                'title' => (string)($r['title'] ?? ''),
                'student' => [
                    'id' => isset($r['u_id']) ? (int)$r['u_id'] : null,
                    'name' => (string)($r['student_name'] ?? 'Unknown'),
                ],
                'category' => (string)($r['category'] ?? ''),
                'status' => $mapStatus($r['status'] ?? ''),
                'meeting' => $mapMeeting($r['meeting_requested'] ?? ''),
                'priority' => strtolower((string)($r['priority'] ?? '')),
            ];
        }

        echo json_encode([
            'data' => $out,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
            ],
        ]);
        exit;
    }

    /**
     * Convert a MySQL datetime string to a short relative time like "2h ago".
     */
    private static function relativeTime(?string $datetime): string
    {
        if (!$datetime) return '';
        $ts = strtotime($datetime);
        if ($ts === false) return '';
        $diff = time() - $ts;
        if ($diff < 60) return max(1, (int)floor($diff)) . 's ago';
        if ($diff < 3600) return (int)floor($diff / 60) . 'm ago';
        if ($diff < 86400) return (int)floor($diff / 3600) . 'h ago';
        return (int)floor($diff / 86400) . 'd ago';
    }
}