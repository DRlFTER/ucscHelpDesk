<?php

class Student extends Controller
{
    public function dashboard()
    {
        $this->requireLogin('student');
        require_once __DIR__ . '/../../models/student/Ticket.php';
        $ticketModel = new StudentTicket();
        $recent = [];
        $openCount = 0;
        $lastActivity = null;
        $recentAnnouncements = [];
        try {
            $uId = (int)($_SESSION['user']['u_id'] ?? 0);
            $dashboardData = $ticketModel->getDashboardData($uId, 3);
            $recent = $dashboardData['recent'] ?? [];
            $openCount = $dashboardData['openCount'] ?? 0;
            $lastActivity = $dashboardData['lastActivity'] ?? null;
        } catch (Throwable $e) {
            $recent = [];
        }

        // Load latest announcements (limit 2) for dashboard sidebar
        try {
            require_once __DIR__ . '/../../models/student/Announcement.php';
            $annModel = new StudentAnnouncement();
            $recentAnnouncements = $annModel->getRecent(2);
        } catch (Throwable $e) {
            $recentAnnouncements = [];
        }

    $headContent = '
    <link rel="stylesheet" href="/css/student/studentDashboard.css"/>';
         $this->view('dashboardStudent', [
            'title' => 'Student Dashboard',
            'head' => $headContent,
            'recentTickets' => $recent,
                'openCount' => $openCount,
                'lastActivity' => $lastActivity,
                'recentAnnouncements' => $recentAnnouncements,
        ]);
    }

    public function ticket()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation and persistence
            $title = trim($_POST['title'] ?? '');
            // category is now provided via hidden input populated from subcategory selection
            $category = trim($_POST['category'] ?? '');
            $when = trim($_POST['when'] ?? '');
            $details = trim($_POST['details'] ?? '');
            // Priority defaults to 'Medium' when not provided
            $priority = trim($_POST['priority'] ?? 'Medium');

            $errors = [];
            if ($title === '') { $errors[] = 'Title is required.'; }
            if ($category === '') { $errors[] = 'Category is required.'; }
            // optional: priority defaults to 'Medium' if not provided
            if ($details === '') { $errors[] = 'Details are required.'; }

            if (empty($errors)) {
                try {
                    // Load student ticket model from organized path
                    require_once __DIR__ . '/../../models/student/Ticket.php';
                    $ticketModel = new StudentTicket();
                    $meetingRequested = isset($_POST['meeting_requested']) && $_POST['meeting_requested'] ? 'Requested' : null;
                    $ticketId = $ticketModel->create([
                        'title' => $title,
                        'u_id' => (int)($_SESSION['user']['u_id'] ?? 0),
                        'category' => $category,
                        'priority' => $priority,
                        'status' => 'pending',
                        'description' => $details,
                        'meeting_requested' => $meetingRequested,
                    ]);

                    // TODO: handle attachments in a follow-up

                    // Show success popup then redirect via JS from view
                    $flash = ['type' => 'success', 'message' => 'Ticket submitted successfully. Redirecting to your dashboard...'];
                } catch (Throwable $e) {
                    $flash = ['type' => 'error', 'message' => 'Ticket submission failed: ' . $e->getMessage()];
                }
            } else {
                $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
            }
        }

    $headContent = '
    <link rel="stylesheet" href="/css/student/studentNewTicket.css" />';

    $this->view('newTicketStudent', [
            'title' => 'New Ticket',
            'head' => $headContent,
            'flash' => $flash ?? null,
        ]);
    }

    public function details($id = null)
    {
        $this->requireLogin('student');
        $ticket = null;
        if ($id !== null) {
            require_once __DIR__ . '/../../models/student/Ticket.php';
            $model = new StudentTicket();
            try {
                $ticket = $model->getByIdForUser((int)$id, (int)($_SESSION['user']['u_id'] ?? 0));
            } catch (Throwable $e) {
                $ticket = null;
            }
        }

    $headContent = '<link rel="stylesheet" href="/css/student/studentNewTicket.css" />'
             . '<link rel="stylesheet" href="/css/student/studentTicketDetails.css" />';
        $this->view('student/ticketDetail', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'ticket' => $ticket,
        ]);
    }

    public function delete($id = null)
    {
        $this->requireLogin('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null) {
            header('Location: /student/dashboard');
            exit;
        }

        require_once __DIR__ . '/../../models/student/Ticket.php';
        $model = new StudentTicket();
        try {
            $model->deleteByIdForUser((int)$id, (int)($_SESSION['user']['u_id'] ?? 0));
        } catch (Throwable $e) {
            // swallow error and redirect; could add flash messaging later
        }
        header('Location: /student/dashboard');
        exit;
    }

    public function lostfound()
    {
        $this->requireLogin('student');
        $lost = [];
        $found = [];
        $claimed = [];
        try {
            require_once __DIR__ . '/../../models/student/LostFound.php';
            $lf = new StudentLostFound();
            $lost = $lf->getByStatus('lost', 20);
            $found = $lf->getByStatus('found', 20);
            $claimed = $lf->getByStatus('claimed', 20);
        } catch (Throwable $e) {
            $lost = [];
            $found = [];
            $claimed = [];
        }

        // Merge and sort all items by newest (q_id desc) for a single unified list
        $items = array_merge($found, $lost, $claimed);
        usort($items, function($a, $b){
            return (int)($b['q_id'] ?? 0) <=> (int)($a['q_id'] ?? 0);
        });

        $headContent = '<link rel="stylesheet" href="/css/student/studentLostFound.css" />';
        $this->view('student/lostFound', [
            'title' => 'Lost & Found',
            'head' => $headContent,
            'items' => $items,
            'lostItems' => $lost, // kept for compatibility if needed elsewhere in the view
            'foundItems' => $found,
            'claimedItems' => $claimed,
            'flash' => $_SESSION['lf_flash'] ?? null,
        ]);
        unset($_SESSION['lf_flash']);
    }

    public function newLostItem()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            // when replaced priority: read datetime-local input
            $when = trim($_POST['when'] ?? '');
            $details = trim($_POST['details'] ?? '');
            $contact_mobile = trim($_POST['contact_mobile'] ?? '');
            $contact_email = trim($_POST['contact_email'] ?? '');

            $errors = [];
            if ($title === '') $errors[] = 'Item title is required';
            if ($category === '') $errors[] = 'Category is required';
            if ($when === '') $errors[] = 'Date & time are required';
            if ($details === '') $errors[] = 'Details are required';

            if (empty($errors)) {
                try {
                    require_once __DIR__ . '/../../models/student/LostFound.php';
                    $model = new StudentLostFound();
                    $id = $model->create([
                        'u_id' => (int)($_SESSION['user']['u_id'] ?? 0),
                        'item_title' => $title,
                        'category' => $category,
                        'when' => $when,
                        'item_details' => $details,
                        'status' => 'lost',
                        'contact_mobile' => $contact_mobile !== '' ? $contact_mobile : null,
                        'contact_email' => $contact_email !== '' ? $contact_email : null,
                    ]);

                    $_SESSION['lf_flash'] = ['type' => 'success', 'message' => 'Lost item submitted successfully.'];
                    header('Location: /student/lostfound');
                    exit;
                } catch (Throwable $e) {
                    $flash = ['type' => 'error', 'message' => 'Failed to submit lost item: ' . $e->getMessage()];
                }
            } else {
                $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
            }
        }

        $headContent = '<link rel="stylesheet" href="/css/student/studentNewLostItem.css" />';
        $this->view('student/newLostItem', [
            'title' => 'Report a Lost Item',
            'head' => $headContent,
            'mode' => 'lost',
            'formAction' => '/student/newLostItem',
            'flash' => $flash ?? null,
        ]);
    }

    public function newFoundItem()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $when = trim($_POST['when'] ?? '');
            $details = trim($_POST['details'] ?? '');
            $contact_mobile = trim($_POST['contact_mobile'] ?? '');
            $contact_email = trim($_POST['contact_email'] ?? '');

            $errors = [];
            if ($title === '') $errors[] = 'Item title is required';
            if ($category === '') $errors[] = 'Category is required';
            if ($when === '') $errors[] = 'Date & time are required';
            if ($details === '') $errors[] = 'Details are required';

            if (empty($errors)) {
                try {
                    require_once __DIR__ . '/../../models/student/LostFound.php';
                    $model = new StudentLostFound();
                    $id = $model->create([
                        'u_id' => (int)($_SESSION['user']['u_id'] ?? 0),
                        'item_title' => $title,
                        'category' => $category,
                        'when' => $when,
                        'item_details' => $details,
                        'status' => 'found',
                        'contact_mobile' => $contact_mobile !== '' ? $contact_mobile : null,
                        'contact_email' => $contact_email !== '' ? $contact_email : null,
                    ]);

                    $_SESSION['lf_flash'] = ['type' => 'success', 'message' => 'Found item submitted successfully.'];
                    header('Location: /student/lostfound');
                    exit;
                } catch (Throwable $e) {
                    $flash = ['type' => 'error', 'message' => 'Failed to submit found item: ' . $e->getMessage()];
                }
            } else {
                $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
            }
        }

        $headContent = '<link rel="stylesheet" href="/css/student/studentNewLostItem.css" />';
        $this->view('student/newLostItem', [
            'title' => 'Report a Found Item',
            'head' => $headContent,
            'mode' => 'found',
            'formAction' => '/student/newFoundItem',
            'flash' => $flash ?? null,
        ]);
    }

    // Mark a Lost & Found entry as found (owner only)
    public function lostfound_markfound($id = null)
    {
        $this->requireLogin('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /student/lostfound');
            exit;
        }
        $qId = $id !== null ? (int)$id : (isset($_POST['q_id']) ? (int)$_POST['q_id'] : 0);
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        if ($qId <= 0 || $uId <= 0) {
            $_SESSION['lf_flash'] = ['type' => 'error', 'message' => 'Invalid request.'];
            header('Location: /student/lostfound');
            exit;
        }
        try {
            require_once __DIR__ . '/../../models/student/LostFound.php';
            $model = new StudentLostFound();
            $ok = $model->markFoundByIdForUser($qId, $uId);
            if ($ok) {
                $_SESSION['lf_flash'] = ['type' => 'success', 'message' => 'Marked as claimed.'];
            } else {
                $_SESSION['lf_flash'] = ['type' => 'info', 'message' => 'No change applied.'];
            }
        } catch (Throwable $e) {
            $_SESSION['lf_flash'] = ['type' => 'error', 'message' => 'Failed to update: ' . $e->getMessage()];
        }
        header('Location: /student/lostfound');
        exit;
    }

    // Claim a found item by any logged-in user
    public function lostfound_claim($id = null)
    {
        $this->requireLogin('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /student/lostfound');
            exit;
        }
        $qId = $id !== null ? (int)$id : (isset($_POST['q_id']) ? (int)$_POST['q_id'] : 0);
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        if ($qId <= 0 || $uId <= 0) {
            $_SESSION['lf_flash'] = ['type' => 'error', 'message' => 'Invalid request.'];
            header('Location: /student/lostfound');
            exit;
        }
        try {
            require_once __DIR__ . '/../../models/student/LostFound.php';
            $model = new StudentLostFound();
            $ok = $model->markClaimedById($qId);
            $_SESSION['lf_flash'] = ['type' => $ok ? 'success' : 'info', 'message' => $ok ? 'Item claimed.' : 'No change applied.'];
        } catch (Throwable $e) {
            $_SESSION['lf_flash'] = ['type' => 'error', 'message' => 'Failed to update: ' . $e->getMessage()];
        }
        header('Location: /student/lostfound');
        exit;
    }

    // Student announcements page
    public function announcements()
    {
        $this->requireLogin('student');

    // Load all announcements from student model (no dependency on staff files)
    require_once __DIR__ . '/../../models/student/Announcement.php';
    $annModel = new StudentAnnouncement();
        $announcements = [];
        try {
            $announcements = $annModel->getAll();
        } catch (Throwable $e) {
            error_log('Student announcements load failed: ' . $e->getMessage());
            $announcements = [];
        }
        $dbError = method_exists($annModel, 'getLastError') ? $annModel->getLastError() : null;

    // Only use the student announcements stylesheet (self-contained)
    $headContent = '<link rel="stylesheet" href="/css/student/studentAnnouncements.css" />';
        $this->view('student/studentAnnouncements', [
            'title' => 'Announcements',
            'head' => $headContent,
            'announcements' => $announcements,
            'dbError' => $dbError,
        ]);
    }

    // Student full announcement view
    public function announcement($id = null)
    {
        $this->requireLogin('student');
        $announcement_id = $id !== null ? (int)$id : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if ($announcement_id <= 0) {
            header('Location: /404');
            exit;
        }

        // Reuse staff announcement model for single fetch and files
        require_once __DIR__ . '/../../models/staff/Announcement.php';
        $model = new Announcement();
        $announcement = null;
        $files = [];
        try {
            $announcement = $model->getById($announcement_id);
        } catch (Throwable $e) {
            $announcement = null;
        }
        if (!$announcement) {
            header('Location: /404');
            exit;
        }
        try { $files = $model->getFiles($announcement_id); } catch (Throwable $e) { $files = []; }

    $headContent = '<link rel="stylesheet" href="/css/student/studentAnnouncements.css" />' . "\n" .
               '<link rel="stylesheet" href="/css/student/studentAnnouncementFull.css" />';
        $this->view('student/studentAnnouncementFull', [
            'title' => 'Announcement Details',
            'head' => $headContent,
            'announcement' => $announcement,
            'files' => $files,
        ]);
    }

    // Student FAQ page
    public function faq()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentFAQ.css" />';
        $this->view('student/studentFAQ', [
            'title' => 'FAQs',
            'head' => $headContent,
        ]);
    }

    // Student Knowledge Base page
    public function knowledgebase()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentKnowledgeBase.css" />';
        $this->view('student/studentKnowledgeBase', [
            'title' => 'Knowledge Base',
            'head' => $headContent,
        ]);
    }

    // Student forum page (UI identical to Tickets for now)
    public function forum()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentForum.css" />';
        $this->view('student/studentForum', [
            'title' => 'Forum',
            'head' => $headContent,
        ]);
    }

    public function forumFull()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentForumFull.css" />';
        $this->view('student/studentForumFull', [
            'title' => 'Forum Post',
            'head' => $headContent,
        ]);
    }

    // Create new forum post
    public function newForum()
    {
        $this->requireLogin('student');

        // Handle submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $topic = trim($_POST['category'] ?? ''); // hidden input synced from subcategory
            $description = trim($_POST['details'] ?? '');
            $type = trim($_POST['ticketType'] ?? 'public'); // 'public' or 'draft'

            $errors = [];
            if ($title === '') $errors[] = 'Title is required.';
            if ($topic === '') $errors[] = 'Topic is required.';
            if ($description === '') $errors[] = 'Description is required.';

            if (empty($errors)) {
                try {
                    $db = Database::getInstance();
                    $isPublic = ($type === 'public') ? 1 : 0;
                    $status = 'open'; // only 'open' | 'answered' exist for now
                    $uId = (int)($_SESSION['user']['u_id'] ?? 0);

                    $stmt = $db->prepare("INSERT INTO forum_q (is_Public, title, topic, description, u_id, status, created_at) VALUES (?,?,?,?,?,?, NOW())");
                    if ($stmt) {
                        $stmt->bind_param('isssis', $isPublic, $title, $topic, $description, $uId, $status);
                        $ok = $stmt->execute();
                        $stmt->close();
                        if ($ok) {
                            $flash = ['type' => 'success', 'message' => $isPublic ? 'Post published successfully. Redirecting to Forum…' : 'Draft saved. Redirecting to Forum…'];
                        } else {
                            $flash = ['type' => 'error', 'message' => 'Failed to save the post. Please try again.'];
                        }
                    } else {
                        $flash = ['type' => 'error', 'message' => 'Failed to prepare database statement.'];
                    }
                } catch (Throwable $e) {
                    $flash = ['type' => 'error', 'message' => 'Error creating post: ' . $e->getMessage()];
                }
            } else {
                $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
            }
        }

        $headContent = '<link rel="stylesheet" href="/css/student/studentNewForum.css" />';
        $this->view('student/studentNewForum', [
            'title' => 'New Forum Post',
            'head' => $headContent,
            'flash' => $flash ?? null,
        ]);
    }

    // Forum posts data (JSON) sourced from forum_q
    public function forumData()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        $db = Database::getInstance();
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

        // Map category slug to topic label used in DB
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
            $topicValue = $topicMap[$key] ?? $category; // allow direct match
        }

    $where = [];
        // Visibility: default show public or own. If 'my', only own posts.
        if (strtolower($type) === 'my') {
            $where[] = "f.u_id = $uId";
        } else {
            $where[] = "(f.is_Public = 1 OR f.u_id = $uId)";
        }

        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(f.title LIKE '%$s%' OR f.description LIKE '%$s%')";
        }
        if ($topicValue !== '') {
            $t = $db->real_escape_string($topicValue);
            $where[] = "f.topic = '$t'";
        }
        if ($status !== '') {
            $s = strtolower($status);
            if ($s === 'open' || $s === 'answered') {
                $where[] = "LOWER(f.status) = '$s'";
            } else {
                $sEsc = $db->real_escape_string($status);
                $where[] = "f.status = '$sEsc'";
            }
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $total = 0;
        $countSql = "SELECT COUNT(*) AS c FROM forum_q f $whereSql";
        if ($res = $db->query($countSql)) {
            $row = $res->fetch_assoc();
            $total = (int)($row['c'] ?? 0);
            $res->free();
        }

        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

        // Sorting
        $orderSql = 'ORDER BY f.created_at DESC';
        $srt = strtolower($sort);
        if ($srt === 'oldest') {
            $orderSql = 'ORDER BY f.created_at ASC';
        }
        // 'votes' and 'comments' default to created_at for now

    $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.u_id, f.is_Public, u.name AS student_name
                FROM forum_q f
                LEFT JOIN users u ON u.u_id = f.u_id
                $whereSql
                $orderSql
                LIMIT $perPage OFFSET $offset";

        $rows = [];
        if ($res = $db->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            $res->free();
        }

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

    // Toggle forum post visibility (Make Public/Private) for the owner's post
    public function forumToggleVisibility()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'method_not_allowed']);
            return;
        }

        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
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
            $stmt = $db->prepare("UPDATE forum_q SET is_Public = ? WHERE q_id = ? AND u_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('iii', $isPublic, $id, $uId);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected <= 0) {
                http_response_code(403);
                echo json_encode(['error' => 'not_allowed']);
                return;
            }
            echo json_encode(['ok' => true, 'is_Public' => $isPublic]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }

    // Toggle forum post status between 'open' and 'answered' (owner only)
    public function forumToggleStatus()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'method_not_allowed']);
            return;
        }

        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? strtolower(trim((string)$_POST['status'])) : '';
        if ($id <= 0 || ($status !== 'open' && $status !== 'answered')) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE forum_q SET status = ? WHERE q_id = ? AND u_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('sii', $status, $id, $uId);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected <= 0) {
                http_response_code(403);
                echo json_encode(['error' => 'not_allowed']);
                return;
            }
            echo json_encode(['ok' => true, 'status' => $status]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }

    // Single forum post data from forum_q
    public function forumPostData()
    {
        $this->requireLogin('student');
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
        $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.description, f.u_id, f.is_Public, u.name AS student_name
                FROM forum_q f
                LEFT JOIN users u ON u.u_id = f.u_id
                WHERE f.q_id = $idEsc AND (f.is_Public = 1 OR f.u_id = $uId)
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

        // Simple relative time description
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
    // Student tickets list (same UI as admin tickets page)
    public function tickets()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentTickets.css" />';
        $this->view('student/studentTickets', [
            'title' => 'Tickets',
            'head' => $headContent,
        ]);
    }

    /**
     * Return current student's tickets as JSON for the Tickets page.
     * Mirrors admin shape but scoped to logged-in student.
     */
    public function ticketsData()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        $db = Database::getInstance();

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
        $priority= isset($_GET['priority']) ? trim((string)$_GET['priority']) : '';

        $where = [];
        // Scope to current user
        $where[] = "t.u_id = $uId";

        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(t.title LIKE '%$s%')"; // student can search by title only
        }
        if ($category !== '') {
            $c = $db->real_escape_string($category);
            $where[] = "t.category = '$c'";
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

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $total = 0;
    $countSql = "SELECT COUNT(*) AS c FROM tickets t $whereSql";
        if ($res = $db->query($countSql)) {
            $row = $res->fetch_assoc();
            $total = (int)($row['c'] ?? 0);
            $res->free();
        }

        $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
        if ($page > $totalPages) { $page = $totalPages; }
        $offset = ($page - 1) * $perPage;

    $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS division_name, t.status, t.priority, t.meeting_requested
        FROM tickets t
        LEFT JOIN division d ON d.did = t.division
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
                'student' => [ 'id' => $uId, 'name' => $_SESSION['user']['name'] ?? 'You' ],
                'category' => (string)($r['division_name'] ?? ''),
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

    // Render full ticket view for a single student ticket
    public function ticketFull()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/student/studentTicketFull.css" />';
        $this->view('student/studentTicketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
        ]);
    }

    // Return JSON data for a single ticket owned by the current student
    public function ticketData()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        $db = Database::getInstance();
        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['error' => 'missing id']);
            return;
        }

        $idEsc = (int)$id;
    $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS division_name, t.status, t.priority, t.description, t.u_id, u.name AS student_name
        FROM tickets t
        LEFT JOIN users u ON u.u_id = t.u_id
        LEFT JOIN division d ON d.did = t.division
                WHERE t.ticket_id = $idEsc AND t.u_id = $studentId
                LIMIT 1";

        $ticket = null;
        if ($res = $db->query($sql)) {
            $ticket = $res->fetch_assoc();
            $res->free();
        }
        if (!$ticket) {
            echo json_encode(['error' => 'not_found']);
            return;
        }

        $statusRaw = strtolower((string)($ticket['status'] ?? ''));
        $statusUi = ($statusRaw === 'pending' || $statusRaw === 'agent assigned')
            ? 'Under Review'
            : (in_array($statusRaw, ['resolved','closed','agent-closed']) ? 'Resolved' : ucfirst($statusRaw));

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

        $createdAt = $ticket['created_at'] ?? null;
        $createdPretty = '';
        if ($createdAt) {
            $ts = strtotime($createdAt);
            if ($ts !== false) $createdPretty = date('M d, Y \\a\\t g:i A', $ts);
        }

        $payload = [
            'id' => (int)$ticket['ticket_id'],
            'code' => 'TKT-' . (int)$ticket['ticket_id'],
            'title' => (string)($ticket['title'] ?? 'Ticket'),
            'status' => $statusUi,
            'createdOn' => $createdPretty,
            'description' => (string)($ticket['description'] ?? ''),
            'category' => (string)($ticket['division_name'] ?? ''),
            'priority' => ucfirst((string)($ticket['priority'] ?? '')),
            'assigned' => null,
            'attachments' => $attachments,
        ];

        echo json_encode($payload);
    }

    // Delete a ticket owned by the current student
    public function ticketDelete()
    {
        $this->requireLogin('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'method_not_allowed';
            return;
        }

        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo 'bad_request';
            return;
        }

        $db = Database::getInstance();
        // Ensure ownership
        $idEsc = (int)$id;
        $ownRow = null;
        if ($res = $db->query("SELECT ticket_id FROM tickets WHERE ticket_id = $idEsc AND u_id = $studentId")) {
            $ownRow = $res->fetch_assoc();
            $res->free();
        }
        if (!$ownRow) {
            http_response_code(404);
            echo 'not_found';
            return;
        }

        // Optionally delete attachments first if FK constraints exist
        $db->query("DELETE FROM supporting_documents WHERE ticket_id = $idEsc");
        $db->query("DELETE FROM tickets WHERE ticket_id = $idEsc AND u_id = $studentId");

        echo 'ok';
    }

    public function lostfound_delete($id = null)
    {
        $this->requireLogin('student');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null) {
            header('Location: /student/lostfound');
            exit;
        }

        $q_id = (int)$id;
        $u_id = (int)($_SESSION['user']['u_id'] ?? 0);
        try {
            require_once __DIR__ . '/../../models/student/LostFound.php';
            $model = new StudentLostFound();
            $ok = $model->deleteByIdForUser($q_id, $u_id);
            $_SESSION['lf_flash'] = [
                'type' => $ok ? 'success' : 'error',
                'message' => $ok ? 'Submission deleted.' : 'Delete failed or not allowed.'
            ];
        } catch (Throwable $e) {
            $_SESSION['lf_flash'] = ['type' => 'error', 'message' => 'Delete failed: ' . $e->getMessage()];
        }

        header('Location: /student/lostfound');
        exit;
    }
}