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
        $this->requireLogin('counselor');

    $headContent = '
    <link rel="stylesheet" href="/css/counselor/counselorDashboard.css"/>';

        $uid = (int)($_SESSION['user']['u_id'] ?? 0);

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

    public function meeting()
    {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/counselor/counselorMeeting.css"/>';

        $this->view('counselor/counselorMeeting', [
            'title' => 'Counselor Meeting',
            'head' => $headContent,
            'role' => 'counselor',
        ]);
    }

    public function forum()
    {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/counselor/counselorDashboard.css"/>
        <link rel="stylesheet" href="/css/counselor/counselorForum.css"/>';

        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        $category = $_GET['category'] ?? null;
        $sortBy = $_GET['sort'] ?? 'recent';
        $search = $_GET['search'] ?? null;
        
        $forumTopics = $forumModel->getForumTopics($category, $sortBy, $search);

        $this->view('counselor/counselorForum', [
            'title' => 'Counseling Forum',
            'head' => $headContent,
            'forumTopics' => $forumTopics,
        ]);
    }

    public function forumTopic()
    {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/counselor/counselorDashboard.css"/>
        <link rel="stylesheet" href="/css/counselor/counselorForum.css"/>';

        $topicId = (int)($_GET['id'] ?? 0);
        
        if (!$topicId) {
            header('Location: /counselor/forum');
            exit;
        }

        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        // Increment view count
        $forumModel->incrementViewCount($topicId);
        
        $topic = $forumModel->getTopicById($topicId);
        $replies = $forumModel->getTopicReplies($topicId);
        $replyCount = count($replies);

        $this->view('counselor/counselorForumTopic', [
            'title' => 'Forum Discussion',
            'head' => $headContent,
            'topic' => $topic,
            'replies' => $replies,
            'replyCount' => $replyCount,
        ]);
    }

    public function forumCreateTopic()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $category = trim($data['category'] ?? '');
        $isPinned = !empty($data['is_pinned']);
        $authorId = (int)($_SESSION['user']['u_id'] ?? 0);
        
        if (empty($title) || empty($content) || empty($category) || !$authorId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        $topicId = $forumModel->createTopic($title, $content, $category, $isPinned, $authorId);
        
        if ($topicId) {
            echo json_encode([
                'success' => true,
                'message' => 'Topic created successfully',
                'topic_id' => $topicId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create topic']);
        }
        exit;
    }

    public function forumCreateReply()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $topicId = (int)($data['topic_id'] ?? 0);
        $content = trim($data['content'] ?? '');
        $authorId = (int)($_SESSION['user']['u_id'] ?? 0);
        
        if (!$topicId || empty($content) || !$authorId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        $replyId = $forumModel->createReply($topicId, $content, $authorId);
        
        if ($replyId) {
            echo json_encode([
                'success' => true,
                'message' => 'Reply posted successfully',
                'reply_id' => $replyId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to post reply']);
        }
        exit;
    }

    public function forumDeleteReply()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $replyId = (int)($data['reply_id'] ?? 0);
        $userId = (int)($_SESSION['user']['u_id'] ?? 0);
        
        if (!$replyId || !$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        $success = $forumModel->deleteReply($replyId, $userId);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Reply deleted']);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized or reply not found']);
        }
        exit;
    }

    public function forumSearch()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        $query = trim($_GET['q'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $sortBy = trim($_GET['sort'] ?? 'recent');
        
        require_once __DIR__ . '/../../models/counselor/Forum.php';
        $forumModel = new CounselorForumModel();
        
        $topics = $forumModel->getForumTopics($category ?: null, $sortBy, $query ?: null);
        
        echo json_encode(['success' => true, 'topics' => $topics]);
        exit;
    }

    public function ticketsData()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        $db = Database::getInstance();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $priority= isset($_GET['priority']) ? trim((string)$_GET['priority']) : '';

        $where = [];
        $joins = "LEFT JOIN users u ON u.u_id = t.u_id LEFT JOIN division d ON d.did = t.division";

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

    public function ticket()
    {
        $this->requireLogin('counselor');
        $headContent = '       <link rel="stylesheet" href="/css/ticketFull/ticketFull.css"/>';
        $this->view('ticketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'role' => 'counselor',
        ]);
    }

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
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.description, t.u_id, u.name AS student_name, t.meeting_requested,
                       sa.name AS staff_name, sh.position, sh.level
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                LEFT JOIN division d ON d.did = t.division
                LEFT JOIN users sa ON sa.u_id = t.assigned_to
                LEFT JOIN staff_division sd ON sd.u_id = t.assigned_to AND sd.did = t.division
                LEFT JOIN staff_hierachy sh ON sh.h_id = sd.h_id
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

        // --- Timeline Logic ---
        $timeline = [];
        $timeline[] = [ 'label' => 'Ticket created', 'time' => $createdPretty ?: '—', 'color' => 'green', 'pending' => false ];

        $staffName = $row['staff_name'] ?? null;
        $position = $row['position'] ?? null;
        $level = $row['level'] ?? null;
        
        $assignLabel = 'Assigned';
        $assignTime = 'Pending';
        $assignColor = 'gray';
        $assignPending = true;
        if (!empty($staffName)) {
            $assignLabel = "Assigned to {$staffName}";
            if ($position) $assignLabel .= " ({$position})";
            if ($level) $assignLabel .= " [Level {$level}]";
            $assignTime = 'Done';
            $assignColor = 'blue';
            $assignPending = false;
        }
        $timeline[] = [ 'label' => $assignLabel, 'time' => $assignTime, 'color' => $assignColor, 'pending' => $assignPending ];

        $reviewLabel = 'Under review';
        $reviewTime = 'Pending';
        $reviewColor = 'gray';
        $reviewPending = true;
        if (in_array($statusRaw, ['agent assigned', 'resolved', 'closed', 'agent-closed'])) {
            $reviewLabel = 'Under review';
            $reviewTime = 'In Progress';
            $reviewColor = 'yellow';
            $reviewPending = false;
            if (in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
                $reviewTime = 'Completed';
                $reviewColor = 'green';
            }
        }
        $timeline[] = [ 'label' => $reviewLabel, 'time' => $reviewTime, 'color' => $reviewColor, 'pending' => $reviewPending ];

        $resolveLabel = 'Resolved';
        $resolveTime = 'Pending';
        $resolveColor = 'gray';
        $resolvePending = true;
        if (in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
            $resolveTime = 'Completed';
            $resolveColor = 'green';
            $resolvePending = false;
        }
        $timeline[] = [ 'label' => $resolveLabel, 'time' => $resolveTime, 'color' => $resolveColor, 'pending' => $resolvePending ];
        // ----------------------

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
            'assigned' => !empty($staffName) ? ($position ? "$staffName ($position)" : $staffName) : null,
            'timeline' => $timeline,
            'attachments' => $attachments,
        ]);
        exit;
    }
}
