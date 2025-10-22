<?php

class Counselor extends Controller
{
    public function settings()
    {
        $this->requireLogin('counselor');
        $headContent = '\n    <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => 'counselor',
            'roleLabel' => 'Counselor',
            'roleMessage' => 'Counselor settings: configure availability and preferences (dummy content).',
        ]);
    }

    public function dashboard()
    {
        // Require counselor role
        $this->requireLogin('counselor');

        // Page head
    $headContent = '
    <link rel="stylesheet" href="/css/counselor/counselorDashboard.css"/>';

        // Current user id
        $uid = (int)($_SESSION['user']['u_id'] ?? 0);

        // Load dashboard data via model (server-side; no JS fetching)
        /** @var CounselorDashboard $dash */
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $dash = new CounselorDashboard();
        $cards = $dash->getCardsData($uid);
        $recentAssigned = $dash->getRecentAssigned($uid, 6);
        $newPending = $dash->getNewPending(6);
        $meetingTickets = $dash->getMeetingTickets(6);

        $this->view('counselor/counselorDashboard', [
            'title' => 'Counselor Dashboard',
            'head' => $headContent,
            'cards' => $cards,
            'recentAssigned' => $recentAssigned,
            'newPending' => $newPending,
            'meetingTickets' => $meetingTickets,
        ]);
    }

     public function calender() {
        $this->requireLogin('counselor');
        $headContent = '\n        <link rel="stylesheet" href="/css/calender/calender.css"/>';
        $this->view('calender', [
            'title' => 'Calendar',
            'head' => $headContent,
            'role' => 'counselor',
            'roleLabel' => 'Counselor',
            'roleMessage' => 'Counselor calendar: plan your tasks and events (dummy content).',
        ]);
    }

    public function tickets() {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/tickets/tickets.css"/>';
        $this->view('tickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'role' => 'counselor',
        ]);
    }

    /**
     * Return tickets for counselor as JSON for the unified Tickets page.
     * Counselor is restricted to Counselling division tickets only.
     * Response shape mirrors admin ticketsData.
     */
    public function ticketsData()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        $db = Database::getInstance();

        // Query params
        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $priority= isset($_GET['priority']) ? trim((string)$_GET['priority']) : '';
        // category is ignored for counselor; enforced to Counselling via WHERE

        $where = [];
        $joins = "LEFT JOIN users u ON u.u_id = t.u_id LEFT JOIN division d ON d.did = t.division";

        // Mandatory restriction: only Counselling tickets (robust lower/like match)
        $where[] = "LOWER(COALESCE(d.name,'')) LIKE 'counsel%'";

        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(t.title LIKE '%$s%' OR u.name LIKE '%$s%')";
        }

        if ($status !== '') {
            $s = strtolower($status);
            if ($s === 'open') {
                $where[] = "t.status = 'pending'";
            } elseif ($s === 'in-progress') {
                $where[] = "t.status = 'agent assigned'";
            } elseif ($s === 'resolved') {
                $where[] = "t.status IN ('resolved','closed','agent-closed')";
            } else {
                $sEsc = $db->real_escape_string($status);
                $where[] = "t.status = '$sEsc'";
            }
        }
        if ($priority !== '') {
            $p = $db->real_escape_string($priority);
            $where[] = "LOWER(t.priority) = LOWER('$p')";
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        // Count
        $total = 0;
        $countSql = "SELECT COUNT(*) AS c FROM tickets t $joins $whereSql";
        if ($res = $db->query($countSql)) {
            $row = $res->fetch_assoc();
            $total = (int)($row['c'] ?? 0);
            $res->free();
        }

        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.meeting_requested, t.u_id, u.name AS student_name
                FROM tickets t
                $joins
                $whereSql
                ORDER BY t.created_at DESC
                LIMIT $perPage OFFSET $offset";

        $rows = [];
        if ($res = $db->query($sql)) {
            while ($row = $res->fetch_assoc()) { $rows[] = $row; }
            $res->free();
        }

        $mapStatus = function ($s) {
            $s = strtolower((string)$s);
            switch ($s) {
                case 'pending': return 'open';
                case 'agent assigned': return 'in-progress';
                case 'resolved':
                case 'closed':
                case 'agent-closed': return 'resolved';
                default: return $s ?: '';
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

    /** Full ticket page for counselor using the unified view */
    public function ticket()
    {
        $this->requireLogin('counselor');
        $headContent = '\n        <link rel="stylesheet" href="/css/ticketFull/ticketFull.css"/>';
        $this->view('ticketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'role' => 'counselor',
        ]);
    }

    /** Single ticket details for counselor (restricted to Counselling division) */
    public function ticketData()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        $db = Database::getInstance();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';
        if (!$id && $code && preg_match('/TKT[-\s]?(\d+)/i', $code, $m)) {
            $id = (int)$m[1];
        }
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ticket id']);
            exit;
        }

        $idEsc = (int)$id;
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.description, t.u_id, u.name AS student_name, t.meeting_requested
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                LEFT JOIN division d ON d.did = t.division
                WHERE t.ticket_id = $idEsc AND LOWER(COALESCE(d.name,'')) LIKE 'counsel%'
                LIMIT 1";

        $row = null;
        if ($res = $db->query($sql)) { $row = $res->fetch_assoc(); $res->free(); }
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        $statusRaw = strtolower((string)($row['status'] ?? ''));
        $statusUi = ($statusRaw === 'pending' || $statusRaw === 'agent assigned') ? 'Under Review' : (in_array($statusRaw, ['resolved','closed','agent-closed']) ? 'Resolved' : ucfirst($statusRaw));

        $attachments = [];
        if ($res = $db->query("SELECT doc_name, location FROM supporting_documents WHERE ticket_id = $idEsc")) {
            while ($r = $res->fetch_assoc()) {
                $attachments[] = [ 'name' => (string)($r['doc_name'] ?? ''), 'url' => '/' . ltrim((string)($r['location'] ?? ''), '/') ];
            }
            $res->free();
        }

        $createdAt = $row['created_at'] ?? null;
        $createdPretty = '';
        if ($createdAt) { $ts = strtotime($createdAt); if ($ts !== false) $createdPretty = date('M d, Y \\a\\t g:i A', $ts); }

        $meeting = 'none';
        $mr = strtolower(trim((string)($row['meeting_requested'] ?? '')));
        if ($mr === 'requested') $meeting = 'requested';
        elseif ($mr === 'scheduled') $meeting = 'scheduled';

        echo json_encode([
            'id' => (int)$row['ticket_id'],
            'code' => 'TKT-' . (int)$row['ticket_id'],
            'title' => (string)($row['title'] ?? ''),
            'description' => (string)($row['description'] ?? ''),
            'category' => (string)($row['category'] ?? ''),
            'priority' => ucfirst((string)($row['priority'] ?? '')),
            'status' => $statusUi,
            'createdOn' => $createdPretty,
            'meeting' => $meeting,
            'student' => [ 'id' => isset($row['u_id']) ? (int)$row['u_id'] : null, 'name' => (string)($row['student_name'] ?? '') ],
            'assigned' => null,
            'attachments' => $attachments,
        ]);
        exit;
    }
}
