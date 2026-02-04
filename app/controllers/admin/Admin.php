<?php

require_once __DIR__ . '/../../models/admin/Admin.php';

class Admin extends Controller
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function settings()
    {
        $this->requireLogin('admin');
        $headContent = '\n        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => 'admin',
            'roleLabel' => 'Admin',
            'roleMessage' => 'Admin settings: manage system-wide preferences (dummy content).',
        ]);
    }

    public function dashboard()
    {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminDashboard.css"/>';
        $this->view('adminDashboard', [
            'title' => 'Admin Dashboard',
            'head' => $headContent,
        ]);
    }

    public function tickets() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/tickets/tickets.css"/>';
        $this->view('tickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'role' => 'admin',
        ]);
    }

    public function ticketFull() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/ticketFull/ticketFull.css"/>';
        $this->view('ticketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'role' => 'admin',
        ]);
    }
    public function users() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminUsers.css"/>';
        $this->view('adminUsers', ['title' => 'User Management', 'head' => $headContent]);
    }

    public function user() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminUserFull.css"/>';
        $this->view('adminUserFull', ['title' => 'User Details', 'head' => $headContent]);
    }
    public function faqs() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminFaqs.css"/>';
        $this->view('adminFaqs', ['title' => 'Manage FAQs', 'head' => $headContent]);
    }
    public function calender() {
        $this->requireLogin('admin');
        $headContent = '\n        <link rel="stylesheet" href="/css/calender/calender.css"/>';
        $this->view('calender', [
            'title' => 'Calendar',
            'head' => $headContent,
            'role' => 'admin',
            'roleLabel' => 'Admin',
            'roleMessage' => 'Admin calendar: manage organization events (dummy content).',
        ]);
    }
    public function forum() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/forum/forum.css"/>';
        $this->view('forum', ['title' => 'Forum', 'head' => $headContent]);
    }

    public function forumFull() {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/forum/forumFull.css"/>';
        $this->view('forumFull', ['title' => 'Forum Post', 'head' => $headContent]);
    }

    /**
     * Return FAQs as JSON for the Admin FAQs page.
     * { data: [ { id, question, answer, createdAt } ], meta: { page, perPage, total, totalPages } }
     */
    public function faqsData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';

        $total = $this->adminModel->getFaqCount($search);
        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        $rows = $this->adminModel->getFaqs($search, $perPage, $offset);

        $mapDate = function ($dt) {
            if (!$dt) return '';
            $ts = strtotime($dt);
            if ($ts === false) return '';
            return date('Y-m-d H:i:s', $ts);
        };

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => (int)($r['id'] ?? 0),
                'question' => (string)($r['question'] ?? ''),
                'answer' => (string)($r['answer'] ?? ''),
                'createdAt' => $mapDate($r['created_at'] ?? null),
            ];
        }

        echo json_encode([
            'data' => $data,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
            ],
        ]);
        exit;
    }

    public function faqCreate()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $question = isset($_POST['question']) ? trim((string)$_POST['question']) : '';
        $answer   = isset($_POST['answer']) ? trim((string)$_POST['answer']) : '';
        if ($question === '' || $answer === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Question and answer are required']);
            exit;
        }

        try {
            $id = $this->adminModel->createFaq($question, $answer);
            $row = $this->adminModel->getFaqById($id);

            echo json_encode([
                'id' => (int)($row['id'] ?? $id),
                'question' => (string)($row['question'] ?? $question),
                'answer' => (string)($row['answer'] ?? $answer),
                'createdAt' => isset($row['created_at']) ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Create failed']);
        }
        exit;
    }
    public function faqUpdate()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $question = isset($_POST['question']) ? trim((string)$_POST['question']) : '';
        $answer   = isset($_POST['answer']) ? trim((string)$_POST['answer']) : '';
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }
        if ($question === '' || $answer === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Question and answer are required']);
            exit;
        }

        try {
            $ok = $this->adminModel->updateFaq($id, $question, $answer);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Update failed']);
                exit;
            }

            $row = $this->adminModel->getFaqById($id);
            echo json_encode([
                'id' => (int)($row['id'] ?? $id),
                'question' => (string)($row['question'] ?? $question),
                'answer' => (string)($row['answer'] ?? $answer),
                'createdAt' => isset($row['created_at']) ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : '',
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
        }
        exit;
    }
    public function faqDelete()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $ok = $this->adminModel->deleteFaq($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Delete failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
        }
        exit;
    }

    /**
     * Forum posts data (JSON) for admin, sourced from forum_q
     */
    public function forumData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        if ($uId <= 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $category= isset($_GET['category']) ? trim((string)$_GET['category']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $sort    = isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'latest';
        $type    = isset($_GET['type']) ? trim((string)$_GET['type']) : '';

        $topicMap = [
            'general' => 'General',
            'it-support' => 'IT Support',
            'finance' => 'Finance',
            'examinations' => 'Examinations',
            'counselling' => 'Counselling',
            'other' => 'Other',
        ];
        $topicValue = '';
        if ($category !== '') {
            $key = strtolower($category);
            $topicValue = $topicMap[$key] ?? $category; 
        }

        $total = $this->adminModel->getForumCount($uId, $search, $topicValue, $status, $type);
        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        $rows = $this->adminModel->getForumPosts($uId, $search, $topicValue, $status, $type, $sort, $perPage, $offset);

        $mapDate = function ($dt) {
            if (!$dt) return '';
            $ts = strtotime($dt);
            if ($ts === false) return '';
            return date('Y-m-d H:i:s', $ts);
        };

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => isset($r['q_id']) ? (int)$r['q_id'] : null,
                'code' => 'FRM-' . (string)($r['q_id'] ?? ''),
                'createdAt' => $mapDate($r['created_at'] ?? null),
                'title' => (string)($r['title'] ?? ''),
                'student' => [ 'id' => (int)($r['u_id'] ?? 0), 'name' => (string)($r['student_name'] ?? 'Student') ],
                'topic' => (string)($r['topic'] ?? ''),
                'status' => strtolower((string)($r['status'] ?? 'open')),
                'is_Public' => isset($r['is_Public']) ? (int)$r['is_Public'] : 0,
                'votesUp' => 0,
                'votesDown' => 0,
                'comments' => 0,
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
     * Fetch a single user
     * Returns JSON shape: { id, name, email, role, designation, number, year }
     */
    public function userData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing user id']);
            exit;
        }

        $row = $this->adminModel->getUserById($id);
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        echo json_encode([
            'id' => (int)$row['u_id'],
            'name' => (string)($row['name'] ?? ''),
            'email' => (string)($row['email'] ?? ''),
            'role' => (string)($row['role'] ?? ''),
            'designation' => isset($row['designation']) ? (string)$row['designation'] : null,
            'number' => isset($row['number']) ? (string)$row['number'] : null,
            'year' => isset($row['year']) ? (int)$row['year'] : null,
            'isDeleted' => (bool)($row['is_deleted'] ?? 0),
            'deletedAt' => isset($row['deleted_at']) ? (string)$row['deleted_at'] : null,
            'isSuspended' => (bool)($row['is_suspended'] ?? 0),
            'suspendedAt' => isset($row['suspended_at']) ? (string)$row['suspended_at'] : null,
        ]);
        exit;
    }
    public function userUpdate()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $number = isset($_POST['number']) ? trim((string)$_POST['number']) : null;
        $role = isset($_POST['role']) ? trim((string)$_POST['role']) : '';
        $designation = isset($_POST['designation']) ? trim((string)$_POST['designation']) : null;
        $year = isset($_POST['year']) && $_POST['year'] !== '' ? (int)$_POST['year'] : null;

        if ($name === '' || $email === '' || $role === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email and role are required']);
            exit;
        }

        $allowed = ['staff','student','lecturer','admin','counselor'];
        if (!in_array(strtolower($role), $allowed, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role']);
            exit;
        }

        try {
            $ok = $this->adminModel->updateUser($id, $name, $email, strtolower($role), $number, $designation, $year);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Update failed']);
                exit;
            }

            $_GET['id'] = (string)$id;
            $this->userData();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
            exit;
        }
    }

    public function userDelete()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            // Soft delete: set is_deleted flag (deleted_at handled by trigger)
            $ok = $this->adminModel->softDeleteUser($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Delete failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
        }
        exit;
    }

    /**
     * Restore a soft-deleted user.
     */
    public function userRestore()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            // Restore: clear is_deleted flag (deleted_at handled by trigger)
            $ok = $this->adminModel->restoreUser($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Restore failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Restore failed']);
        }
        exit;
    }

     public function userSuspend()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            // Soft delete: set is_deleted flag (deleted_at handled by trigger)
            $ok = $this->adminModel->suspendUser($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Suspend failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Suspend failed']);
        }
        exit;
    }

    public function userUnsuspend()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $ok = $this->adminModel->unsuspendUser($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Unsuspend failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Unsuspend failed']);
        }
        exit;
    }

    /**
     * Return users for admin as JSON for the Users page.
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

        // Query params
        $page       = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage    = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search     = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $type       = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
        $designation= isset($_GET['designation']) ? trim((string)$_GET['designation']) : '';

        // Validate role
        $role = '';
        if ($type !== '') {
            $allowed = ['staff','student','lecturer','admin','counselor'];
            if (in_array(strtolower($type), $allowed, true)) {
                $role = strtolower($type);
            }
        }

        $total = $this->adminModel->getUsersCount($search, $role, $designation);
        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        $rows = $this->adminModel->getUsers($search, $role, $designation, $perPage, $offset);
        $designations = $this->adminModel->getDistinctDesignations();

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
                'isDeleted' => (bool)($r['is_deleted'] ?? 0),
                'isSuspended' => (bool)($r['is_suspended'] ?? 0),
                'suspendedAt' => isset($r['suspended_at']) ? (string)$r['suspended_at'] : null,
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

    public function dashboardData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $CACHE_TTL = 120; // seconds
        $cacheDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cache';
        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . 'admin_dashboard.json';
        $nowTs = time();

        // Serve from cache if fresh
        if (is_file($cacheFile)) {
            $mtime = @filemtime($cacheFile) ?: 0;
            if ($nowTs - $mtime < $CACHE_TTL) {
                // Optional client caching headers
                header('Cache-Control: no-store');
                echo @file_get_contents($cacheFile);
                exit;
            }
        }

        // 1) Cards
        $ticketCounts = $this->adminModel->getTicketCounts();
        $totalTickets = $ticketCounts['total'];
        $openTickets = $ticketCounts['open'];
        $resolvedTickets = $ticketCounts['resolved'];

        $avgRespMinutes = $this->adminModel->getAverageResponseTime();
        $avgRespText = $avgRespMinutes !== null ? round($avgRespMinutes / 60, 1) . 'h' : '—';
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100) . '%' : '0%';

        $cardsData = [
            [ 'title' => 'Total Tickets', 'value' => $totalTickets, 'change' => '' ],
            [ 'title' => 'Open Tickets', 'value' => $openTickets, 'change' => '' ],
            [ 'title' => 'Average Response Time', 'value' => $avgRespText, 'change' => '' ],
            [ 'title' => 'Satisfaction Rate', 'value' => $resolutionRate, 'change' => '' ],
        ];

        // 2) Recent tickets
        $recentTicketsData = $this->adminModel->getRecentTickets(6);
        $recentTickets = [];
        foreach ($recentTicketsData as $row) {
            $recentTickets[] = [
                'id' => (int)$row['ticket_id'],
                'title' => (string)$row['title'],
                'agent' => (string)($row['requester'] ?? 'Unknown'),
                'time' => self::relativeTime($row['created_at']),
                'priority' => strtoupper((string)$row['priority']),
            ];
        }

        // 3) Top agents
        $topAgentsData = $this->adminModel->getTopAgents(5);
        $topAgents = [];
        foreach ($topAgentsData as $row) {
            $avgMin = isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
            $topAgents[] = [
                'name' => (string)$row['name'],
                'resolved' => (int)$row['resolved'],
                'responseTime' => $avgMin !== null ? round($avgMin / 60, 1) . 'h' : '—',
            ];
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

            $startStr = $start->format('Y-m-d H:i:s');
            $endStr = $end->format('Y-m-d H:i:s');

            $trends['new'][] = $this->adminModel->getTicketCountByDateRange($startStr, $endStr, false);
            $trends['resolved'][] = $this->adminModel->getTicketCountByDateRange($startStr, $endStr, true);
        }

        // 5) Tickets by category (now derived from division table)
        $categoriesData = $this->adminModel->getTicketsByCategory();
        $categories = [ 'labels' => [], 'data' => [] ];
        foreach ($categoriesData as $row) {
            $categories['labels'][] = (string)$row['category'];
            $categories['data'][] = (int)$row['c'];
        }

        // 6) Platform status (static placeholders for now)
        $platformStatus = [
            [ 'name' => 'Student Portal', 'status' => 'Operational' ],
            [ 'name' => 'Lecturer Portal', 'status' => 'Operational' ],
            [ 'name' => 'Email Notifications', 'status' => 'Degraded' ],
            [ 'name' => 'Ticketing System', 'status' => 'Operational' ],
        ];

        $payload = [
            'cardsData' => $cardsData,
            'recentTickets' => $recentTickets,
            'topAgents' => $topAgents,
            'trends' => $trends,
            'categories' => $categories,
            'platformStatus' => $platformStatus,
        ];

        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }
        $json = json_encode($payload);
        if ($json !== false) {
            $tmp = $cacheFile . '.tmp';
            if (@file_put_contents($tmp, $json) !== false) {
                @rename($tmp, $cacheFile);
            }
        }

        header('Cache-Control: no-store');
        echo $json !== false ? $json : json_encode($payload);
        exit;
    }

    public function ticketData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

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

        $row = $this->adminModel->getTicketById($id);
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            exit;
        }

        $attachmentsData = $this->adminModel->getTicketAttachments($id);
        $attachments = [];
        foreach ($attachmentsData as $r) {
            $attachments[] = [
                'name' => (string)($r['doc_name'] ?? ''),
                'url' => '/' . ltrim((string)($r['location'] ?? ''), '/'),
            ];
        }

        $statusRaw = strtolower((string)($row['status'] ?? ''));
        $statusUi = ($statusRaw === 'pending' || $statusRaw === 'agent assigned') ? 'Under Review' : (in_array($statusRaw, ['resolved','closed','agent-closed']) ? 'Resolved' : ucfirst($statusRaw));

        $createdAt = $row['created_at'] ?? null;
        $createdPretty = '';
        if ($createdAt) {
            $ts = strtotime($createdAt);
            if ($ts !== false) $createdPretty = date('M d, Y \\a\\t g:i A', $ts);
        }

        // --- Timeline Logic ---
        $timeline = [];
        $timeline[] = [ 'label' => 'Ticket created', 'time' => $createdPretty ?: '—', 'color' => 'green', 'pending' => false ];

        $staffName = $row['staff_name'] ?? null;
        $position = $row['position'] ?? null;
        $level = $row['level'] ?? null;
        $assignedAt = $row['assigned_at'] ?? null;
        
        $assignLabel = 'Assigned to staff';
        $assignTime = '';
        $assignColor = 'gray';
        $assignPending = true;
        if (!empty($staffName) || in_array($statusRaw, ['agent assigned', 'resolved', 'closed', 'agent-closed'])) {
            $assignLabel = "Assigned to staff";
            if (!empty($staffName)) {
                $assignLabel = "Assigned to {$staffName}";
                if ($position) $assignLabel .= " ({$position})";
                if ($level) $assignLabel .= " [Level {$level}]";
            }
            // Use assigned_at timestamp from ticket_timeline if available
            if ($assignedAt && $assignedAt !== '0000-00-00 00:00:00') {
                $ts = strtotime($assignedAt);
                $assignTime = ($ts !== false) ? date('M d, Y \a\t g:i A', $ts) : 'Assigned';
            } else {
                $assignTime = 'Assigned'; // Fallback if no timestamp
            }
            $assignColor = 'blue';
            $assignPending = false;
        }
        $timeline[] = [ 'label' => $assignLabel, 'time' => $assignTime, 'color' => $assignColor, 'pending' => $assignPending ];

        // 3. Under Review
        $underReviewAt = $row['under_review_at'] ?? null;
        $reviewLabel = 'Under review';
        $reviewTime = 'Pending';
        $reviewColor = 'gray';
        $reviewPending = true;
        
        // Check if under_review timestamp exists in ticket_timeline
        if ($underReviewAt && $underReviewAt !== '0000-00-00 00:00:00') {
            $ts = strtotime($underReviewAt);
            $reviewTime = ($ts !== false) ? date('M d, Y \a\t g:i A', $ts) : 'In Progress';
            $reviewColor = 'yellow';
            $reviewPending = false;
            // If resolved, mark review as completed
            if (in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
                $reviewColor = 'green';
            }
        } elseif (in_array($statusRaw, ['agent assigned', 'resolved', 'closed', 'agent-closed'])) {
            // Fallback: if status indicates review but no timestamp
            $reviewTime = 'In Progress';
            $reviewColor = 'yellow';
            $reviewPending = false;
            if (in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
                $reviewTime = 'Completed';
                $reviewColor = 'green';
            }
        }
        $timeline[] = [ 'label' => $reviewLabel, 'time' => $reviewTime, 'color' => $reviewColor, 'pending' => $reviewPending ];

        // 4. Resolved
        $resolvedAt = $row['resolved_at'] ?? null;
        $resolveLabel = 'Resolved';
        $resolveTime = 'Pending';
        $resolveColor = 'gray';
        $resolvePending = true;
        
        // Check if resolved timestamp exists in ticket_timeline
        if ($resolvedAt && $resolvedAt !== '0000-00-00 00:00:00') {
            $ts = strtotime($resolvedAt);
            $resolveTime = ($ts !== false) ? date('M d, Y \a\t g:i A', $ts) : 'Completed';
            $resolveColor = 'green';
            $resolvePending = false;
        } elseif (in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
            // Fallback: if status is resolved but no timestamp
            $resolveTime = 'Completed';
            $resolveColor = 'green';
            $resolvePending = false;
        }
        $timeline[] = [ 'label' => $resolveLabel, 'time' => $resolveTime, 'color' => $resolveColor, 'pending' => $resolvePending ];
        // ----------------------

        $mr = strtolower(trim((string)($row['meeting_requested'] ?? '')));
        $meeting = 'none';
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
            'student' => [
                'id' => isset($row['u_id']) ? (int)$row['u_id'] : null,
                'name' => (string)($row['student_name'] ?? ''),
            ],
            'assigned' => !empty($staffName) ? ($position ? "$staffName ($position)" : $staffName) : null,
            'timeline' => $timeline,
            'attachments' => $attachments,
        ]);
        exit;
    }

    /** Delete a ticket by id and return JSON */
    public function ticketDelete()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $ok = $this->adminModel->deleteTicket($id);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Delete failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
        }
        exit;
    }
    public function ticketResolve()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $ok = $this->adminModel->resolveTicket($id);
            if ($ok) {
                // Also update the ticket_timeline resolved timestamp
                $this->adminModel->resolveTicketTimeline($id);
            }
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['error' => 'Mark as resolved failed']);
                exit;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Mark as resolved failed']);
        }
        exit;
    }

    /**
     * Return tickets for admin as JSON for the Tickets page.
     * Shape per item:
     * {
     *   code: string,        
     *   createdAt: string,      
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

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
        $search  = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $category= isset($_GET['category']) ? trim((string)$_GET['category']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $priority= isset($_GET['priority']) ? trim((string)$_GET['priority']) : '';

        $total = $this->adminModel->getTicketsCount($search, $category, $status, $priority);
        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        $rows = $this->adminModel->getTickets($search, $category, $status, $priority, $perPage, $offset);

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

    // Single forum post data
    public function forumPostData()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $db = Database::getInstance();
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($uId <= 0 || $id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        $idEsc = (int)$id;
        // Admin can see all posts
        $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.description, f.u_id, f.is_Public, u.name AS student_name
                FROM forum_q f
                LEFT JOIN users u ON u.u_id = f.u_id
                WHERE f.q_id = $idEsc
                LIMIT 1";

        $row = null;
        if ($res = $db->query($sql)) {
            $row = $res->fetch_assoc();
            $res->free();
        }
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'not_found']);
            return;
        }

        $createdAt = $row['created_at'] ?? null;
        $createdPretty = '';
        if ($createdAt) {
            $ts = strtotime($createdAt);
            if ($ts !== false) $createdPretty = date('M d, Y \\a\\t g:i A', $ts);
        }

        $createdAgo = '';
        if ($createdAt) {
            $ts = strtotime($createdAt);
            if ($ts !== false) {
                $diff = time() - $ts;
                if ($diff < 60) $createdAgo = $diff . ' seconds ago';
                elseif ($diff < 3600) { $m = (int)floor($diff/60); $createdAgo = $m . ' minute' . ($m>1?'s':'') . ' ago'; }
                elseif ($diff < 86400) { $h = (int)floor($diff/3600); $createdAgo = $h . ' hour' . ($h>1?'s':'') . ' ago'; }
                else { $d = (int)floor($diff/86400); $createdAgo = $d . ' day' . ($d>1?'s':'') . ' ago'; }
            }
        }

        $statusUi = strtolower((string)($row['status'] ?? 'open')) === 'answered' ? 'Answered' : 'Open';

        $payload = [
            'id' => (int)($row['q_id'] ?? 0),
            'code' => 'FRM-' . (int)($row['q_id'] ?? 0),
            'title' => (string)($row['title'] ?? 'Post'),
            'description' => (string)($row['description'] ?? ''),
            'topic' => (string)($row['topic'] ?? ''),
            'status' => $statusUi,
            'createdAt' => (string)($row['created_at'] ?? ''),
            'createdOn' => $createdPretty,
            'createdAgo' => $createdAgo,
            'is_Public' => (int)($row['is_Public'] ?? 0),
            'student' => [ 'id' => (int)($row['u_id'] ?? 0), 'name' => (string)($row['student_name'] ?? 'Student') ],
            'attachments' => [],
            'commentsCount' => 0,
            'votes' => 0,
        ];

        echo json_encode($payload);
    }

    // Toggle forum post visibility (Admin can change any post)
    public function forumToggleVisibility()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'method_not_allowed']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $state = isset($_POST['state']) ? trim((string)$_POST['state']) : '';
        if ($id <= 0 || ($state !== 'public' && $state !== 'private')) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        $isPublic = ($state === 'public') ? 1 : 0;

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE forum_q SET is_Public = ? WHERE q_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('ii', $isPublic, $id);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $stmt->close();
            echo json_encode(['ok' => true, 'is_Public' => $isPublic]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }

    // Toggle forum post status (Admin can change any post)
    public function forumToggleStatus()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'method_not_allowed']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? strtolower(trim((string)$_POST['status'])) : '';
        if ($id <= 0 || ($status !== 'open' && $status !== 'answered')) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE forum_q SET status = ? WHERE q_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('si', $status, $id);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $stmt->close();
            echo json_encode(['ok' => true, 'status' => $status]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }

    // Delete a forum post (Admin can delete any post)
    public function forumDelete()
    {
        $this->requireLogin('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'method_not_allowed';
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo 'bad_request';
            return;
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("DELETE FROM forum_q WHERE q_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $stmt->close();
            echo 'ok';
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'server_error';
        }
    }

    // Placeholder for vote endpoint
    public function forumVote()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
    }

    // ==================== Reports ====================
    
    private $reportsModel;

    private function getReportsModel()
    {
        if (!$this->reportsModel) {
            require_once __DIR__ . '/../../models/admin/Reports.php';
            $this->reportsModel = new AdminReportModel();
        }
        return $this->reportsModel;
    }

    /**
     * Reports page view
     */
    public function reports()
    {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminReports.css"/>';
        $this->view('adminReports', [
            'title' => 'Reports',
            'head' => $headContent,
        ]);
    }

    /**
     * Get filter data for reports (divisions, staff, etc.)
     */
    public function reportsFilters()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        try {
            $model = $this->getReportsModel();
            echo json_encode([
                'divisions' => $model->getDivisions(),
                'staff' => $model->getStaffMembers(),
                'statuses' => $model->getTicketStatuses(),
                'priorities' => $model->getTicketPriorities(),
                'roles' => $model->getUserRoles()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load filter data']);
        }
    }

    /**
     * Generate quick reports
     */
    public function reportsQuick()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $reportType = $_GET['report_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        $division = $_GET['division'] ?? '';
        $groupBy = $_GET['group_by'] ?? 'category';
        $period = $_GET['period'] ?? 'daily';

        try {
            $model = $this->getReportsModel();
            $data = [];
            $summary = [];
            $columns = [];
            $chartData = null;

            switch ($reportType) {
                case 'tickets-by-status':
                    $data = $model->getTicketsByStatus($startDate, $endDate, $division);
                    $summary = $model->getTicketsByStatusSummary($startDate, $endDate, $division);
                    $columns = ['status', 'count', 'division_name'];
                    $chartData = [
                        'type' => 'doughnut',
                        'labels' => array_column($data, 'status'),
                        'values' => array_map('intval', array_column($data, 'count')),
                        'label' => 'Tickets'
                    ];
                    break;

                case 'tickets-by-category':
                    $data = $model->getTicketsByCategory($startDate, $endDate);
                    $columns = ['category', 'count'];
                    $chartData = [
                        'type' => 'bar',
                        'labels' => array_column($data, 'category'),
                        'values' => array_map('intval', array_column($data, 'count')),
                        'label' => 'Tickets'
                    ];
                    break;

                case 'tickets-by-role':
                    $data = $model->getTicketsByRole($startDate, $endDate);
                    $columns = ['role', 'count'];
                    $chartData = [
                        'type' => 'pie',
                        'labels' => array_map('ucfirst', array_column($data, 'role')),
                        'values' => array_map('intval', array_column($data, 'count')),
                        'label' => 'Tickets'
                    ];
                    break;

                case 'resolution-time':
                    $data = $model->getResolutionTimeReport($startDate, $endDate, $groupBy);
                    $columns = ['group_name', 'avg_hours', 'ticket_count'];
                    $chartData = [
                        'type' => 'bar',
                        'labels' => array_column($data, 'group_name'),
                        'values' => array_map('floatval', array_column($data, 'avg_hours')),
                        'label' => 'Average Hours'
                    ];
                    break;

                case 'staff-performance':
                    $data = $model->getStaffPerformanceReport($startDate, $endDate);
                    $columns = ['staff_name', 'department', 'assigned_tickets', 'resolved_tickets', 'closed_tickets', 'avg_response_hours'];
                    $totalAssigned = array_sum(array_column($data, 'assigned_tickets'));
                    $totalResolved = array_sum(array_column($data, 'resolved_tickets'));
                    $summary = [
                        'total_staff' => count($data),
                        'total_assigned' => $totalAssigned,
                        'total_resolved' => $totalResolved,
                        'resolution_rate' => $totalAssigned > 0 ? round(($totalResolved / $totalAssigned) * 100, 1) . '%' : '0%'
                    ];
                    break;

                case 'most-active-users':
                    $data = $model->getMostActiveUsersReport($startDate, $endDate, 20);
                    $columns = ['name', 'email', 'role', 'ticket_count'];
                    $summary = [
                        'total_users' => count($data),
                        'total_tickets' => array_sum(array_column($data, 'ticket_count'))
                    ];
                    break;

                case 'ticket-volume-trend':
                    $data = $model->getTicketVolumeTrend($startDate, $endDate, $period);
                    $columns = ['period', 'count'];
                    $chartData = [
                        'type' => 'line',
                        'labels' => array_column($data, 'period'),
                        'values' => array_map('intval', array_column($data, 'count')),
                        'label' => 'Tickets'
                    ];
                    $summary = [
                        'total_tickets' => array_sum(array_column($data, 'count')),
                        'avg_per_period' => count($data) > 0 ? round(array_sum(array_column($data, 'count')) / count($data), 1) : 0,
                        'peak' => count($data) > 0 ? max(array_column($data, 'count')) : 0
                    ];
                    break;

                case 'unresolved-backlog':
                    $data = $model->getUnresolvedTicketsReport($startDate, $endDate);
                    $columns = ['date', 'unresolved_count', 'avg_days_pending'];
                    $chartData = [
                        'type' => 'line',
                        'labels' => array_column($data, 'date'),
                        'values' => array_map('intval', array_column($data, 'unresolved_count')),
                        'label' => 'Unresolved Tickets'
                    ];
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid report type']);
                    return;
            }

            echo json_encode([
                'data' => $data,
                'summary' => $summary,
                'columns' => $columns,
                'chartData' => $chartData
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate report: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate custom reports with advanced filters
     */
    public function reportsCustom()
    {
        $this->requireLogin('admin');
        header('Content-Type: application/json');

        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'category' => $_GET['category'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'assigned_to' => $_GET['assigned_to'] ?? '',
            'user_role' => $_GET['user_role'] ?? '',
            'limit' => $_GET['limit'] ?? 100
        ];

        try {
            $model = $this->getReportsModel();
            $data = $model->getCustomReport($filters);
            $summary = $model->getCustomReportSummary($filters);

            $columns = ['ticket_id', 'title', 'status', 'priority', 'category', 'submitted_by', 'user_role', 'assigned_staff', 'created_at'];

            echo json_encode([
                'data' => $data,
                'summary' => $summary,
                'columns' => $columns,
                'chartData' => null
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate custom report: ' . $e->getMessage()]);
        }
    }
}