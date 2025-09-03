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
            $res->free();
        }

        // Open tickets (pending, agent assigned)
        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('pending','agent assigned')")) {
            $row = $res->fetch_assoc();
            $openTickets = (int)($row['c'] ?? 0);
            $res->free();
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
            $res->free();
        }

        // Resolved tickets (resolved, closed, agent-closed)
        if ($res = $db->query("SELECT COUNT(*) AS c FROM tickets WHERE status IN ('resolved','closed','agent-closed')")) {
            $row = $res->fetch_assoc();
            $resolvedTickets = (int)($row['c'] ?? 0);
            $res->free();
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
        $sqlRecent = "SELECT t.title, u.name AS requester, t.created_at, t.priority
                      FROM tickets t
                      LEFT JOIN users u ON u.u_id = t.u_id
                      ORDER BY t.created_at DESC
                      LIMIT 6";
        if ($res = $db->query($sqlRecent)) {
            while ($row = $res->fetch_assoc()) {
                $recentTickets[] = [
                    'title' => (string)$row['title'],
                    'agent' => (string)($row['requester'] ?? 'Unknown'), // shown under agent label in UI
                    'time' => self::relativeTime($row['created_at']),
                    'priority' => strtoupper((string)$row['priority']),
                ];
            }
            $res->free();
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
            $res->free();
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
            if ($res = $db->query($qNew)) { $r = $res->fetch_assoc(); $countNew = (int)($r['c'] ?? 0); $res->free(); }
            $trends['new'][] = $countNew;

            // Resolved tickets created this week (approximation)
            $qRes = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= '$startEsc' AND created_at < '$endEsc' AND status IN ('resolved','closed','agent-closed')";
            $countRes = 0;
            if ($res = $db->query($qRes)) { $r = $res->fetch_assoc(); $countRes = (int)($r['c'] ?? 0); $res->free(); }
            $trends['resolved'][] = $countRes;
        }

        // 5) Tickets by category
        $categories = [ 'labels' => [], 'data' => [] ];
        if ($res = $db->query("SELECT COALESCE(category,'Other') AS category, COUNT(*) AS c FROM tickets GROUP BY category ORDER BY c DESC")) {
            while ($row = $res->fetch_assoc()) {
                $categories['labels'][] = (string)$row['category'];
                $categories['data'][] = (int)$row['c'];
            }
            $res->free();
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
