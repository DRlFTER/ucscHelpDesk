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
        try {
            $uId = (int)($_SESSION['user']['u_id'] ?? 0);
            $dashboardData = $ticketModel->getDashboardData($uId, 5);
            $recent = $dashboardData['recent'] ?? [];
            $openCount = $dashboardData['openCount'] ?? 0;
            $lastActivity = $dashboardData['lastActivity'] ?? null;
        } catch (Throwable $e) {
            $recent = [];
        }

    $headContent = '
    <link rel="stylesheet" href="/css/student/studentDashboard.css"/>';
         $this->view('dashboardStudent', [
            'title' => 'Student Dashboard',
            'head' => $headContent,
            'recentTickets' => $recent,
                'openCount' => $openCount,
                'lastActivity' => $lastActivity,
        ]);
    }

    public function ticket()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation and persistence
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $when = trim($_POST['when'] ?? '');
            $details = trim($_POST['details'] ?? '');

            $errors = [];
            if ($title === '') { $errors[] = 'Title is required.'; }
            if ($category === '') { $errors[] = 'Category is required.'; }
            if ($priority === '') { $errors[] = 'Priority is required.'; }
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
        try {
            require_once __DIR__ . '/../../models/student/LostFound.php';
            $lf = new StudentLostFound();
            $lost = $lf->getByStatus('lost', 20);
            $found = $lf->getByStatus('found', 20);
        } catch (Throwable $e) {
            $lost = [];
            $found = [];
        }

        // Merge and sort all items by newest (q_id desc) for a single unified list
        $items = array_merge($found, $lost);
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

        $sql = "SELECT t.ticket_id, t.created_at, t.title, t.category, t.status, t.priority, t.meeting_requested
                FROM tickets t
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
        $sql = "SELECT t.ticket_id, t.created_at, t.title, t.category, t.status, t.priority, t.description, t.u_id, u.name AS student_name
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
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
            'category' => (string)($ticket['category'] ?? ''),
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