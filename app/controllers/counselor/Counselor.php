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

        $headContent = '<link rel="stylesheet" href="/css/student/studentDashboard.css"/>
<link rel="stylesheet" href="/css/staff/staffDashboard.css"/>';

        $uid = (int)($_SESSION['user']['u_id'] ?? 0);

        /** @var CounselorDashboard $dash */
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $dash = new CounselorDashboard();
        $cards = $dash->getCardsData($uid);
        $recentAssigned = $dash->getRecentAssigned($uid, 6);
        $allCounseling = $dash->getAllCounselingTickets(6);
        $meetingTickets = $dash->getMeetingTickets(6);

        $upcomingEvents = [];
        try {
            require_once __DIR__ . '/../../models/CalendarEvent.php';
            $calModel = new CalendarEvent();
            $upcomingEvents = $calModel->getUpcomingEvents($uid, 3);
        } catch (Throwable $e) {
            $upcomingEvents = [];
        }

        $this->view('counselor/counselorDashboard', [
            'title' => 'Counselor Dashboard',
            'head' => $headContent,
            'cards' => $cards,
            'recentAssigned' => $recentAssigned,
            'allCounseling' => $allCounseling,
            'meetingTickets' => $meetingTickets,
            'upcomingEvents' => $upcomingEvents,
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
        <link rel="stylesheet" href="/css/forum/forum.css"/>';

        $this->view('forum', [
            'title' => 'Forum',
            'head' => $headContent,
        ]);
    }

    public function forumFull()
    {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/forum/forumFull.css"/>';

        $this->view('forumFull', [
            'title' => 'Forum Post',
            'head' => $headContent,
        ]);
    }

    public function forumTopic()
    {
        $this->requireLogin('counselor');
        $headContent = '
        <link rel="stylesheet" href="/css/student/studentDashboard.css"/>
        <link rel="stylesheet" href="/css/staff/staffDashboard.css"/>
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

public function reports()
{
    $this->requireLogin('counselor');
    
    $headContent = '
    <link rel="stylesheet" href="/css/counselor/counselorReports.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';

    $this->view('counselor/counselorReports', [
        'title' => 'Reports',
        'head' => $headContent,
    ]);
}

public function getReportData()
{
    $this->requireLogin('counselor');
    header('Content-Type: application/json');

    try {
        require_once __DIR__ . '/../../models/counselor/Reports.php';
        $reportsModel = new CounselorReports();

        $type = $_GET['type'] ?? 'overview';
        $startDate = $_GET['start_date'] ?? date('Y-m-01 00:00:00');
        $endDate = $_GET['end_date'] ?? date('Y-m-t 23:59:59');
        $counselorId = isset($_GET['counselor_id']) ? (int)$_GET['counselor_id'] : null;

        $data = null;

        switch ($type) {
            case 'overview':
                $data = $reportsModel->getOverviewStats($startDate, $endDate, $counselorId);
                break;
            
            case 'by_category':
                $data = $reportsModel->getTicketsByCategory($startDate, $endDate);
                break;
            
            case 'by_priority':
                $data = $reportsModel->getTicketsByPriority($startDate, $endDate);
                break;
            
            case 'daily_volume':
                $data = $reportsModel->getDailyVolume($startDate, $endDate);
                break;
            
            case 'performance':
                $data = $reportsModel->getCounselorPerformance($startDate, $endDate);
                break;
            
            case 'meetings':
                $data = $reportsModel->getMeetingStats($startDate, $endDate);
                break;
            
            case 'students':
                $data = $reportsModel->getStudentEngagement($startDate, $endDate);
                break;
            
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid report type']);
                exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}


/**
 * ADD THIS METHOD TO: controllers/Counselor.php
 * Add after the existing reports() method
 */

/**
 * Generate filtered report with statistics
 */
public function generateReport()
{
    $this->requireLogin('counselor');
    header('Content-Type: application/json');

    try {
        require_once __DIR__ . '/../../models/counselor/Reports.php';
        $reportsModel = new CounselorReports();

        // Get filter parameters
        $reportType = $_GET['report_type'] ?? 'all';
        $startDate = $_GET['start_date'] ?? date('Y-m-01 00:00:00');
        $endDate = $_GET['end_date'] ?? date('Y-m-t 23:59:59');
        $status = isset($_GET['status']) && $_GET['status'] !== 'all' ? $_GET['status'] : null;
        $priority = isset($_GET['priority']) && $_GET['priority'] !== 'all' ? $_GET['priority'] : null;

        // Get filtered tickets
        $tickets = $reportsModel->getFilteredTickets($startDate, $endDate, $status, $priority);
        
        // Calculate statistics
        $stats = $this->calculateReportStatistics($tickets);
        
        // Return response
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $stats['total'],
                'pending' => $stats['pending'],
                'assigned' => $stats['assigned'],
                'resolved' => $stats['resolved'],
                'closed' => $stats['closed'],
                'tickets' => $tickets
            ]
        ]);

    } catch (Exception $e) {
        error_log('Generate Report Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

/**
 * Calculate statistics from ticket array
 */
private function calculateReportStatistics($tickets)
{
    $stats = [
        'total' => count($tickets),
        'pending' => 0,
        'assigned' => 0,
        'resolved' => 0,
        'closed' => 0
    ];

    foreach ($tickets as $ticket) {
        $status = strtolower($ticket['status'] ?? '');
        
        if ($status === 'pending') {
            $stats['pending']++;
        } elseif ($status === 'agent assigned') {
            $stats['assigned']++;
        } elseif ($status === 'resolved') {
            $stats['resolved']++;
        } elseif (in_array($status, ['closed', 'agent-closed'])) {
            $stats['closed']++;
        }
    }

    return $stats;
}

/**
 * Export report as PDF
 */
public function exportReport()
{
    $this->requireLogin('counselor');

    try {
        require_once __DIR__ . '/../../models/counselor/Reports.php';
        $reportsModel = new CounselorReports();

                $startDateInput = trim((string)($_GET['start_date'] ?? date('Y-m-01')));
                $endDateInput = trim((string)($_GET['end_date'] ?? date('Y-m-t')));
                $status = isset($_GET['status']) && $_GET['status'] !== 'all' ? trim((string)$_GET['status']) : null;
                $priority = isset($_GET['priority']) && $_GET['priority'] !== 'all' ? trim((string)$_GET['priority']) : null;

                $startDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', substr($startDateInput, 0, 10)) ? substr($startDateInput, 0, 10) : date('Y-m-01');
                $endDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', substr($endDateInput, 0, 10)) ? substr($endDateInput, 0, 10) : date('Y-m-t');

                $tickets = $reportsModel->getFilteredTickets(
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59',
                        $status,
                        $priority
                );
                $stats = $this->calculateReportStatistics($tickets);

                $payload = [
                        'meta' => [
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                                'status' => $status ?: 'All',
                                'priority' => $priority ?: 'All',
                                'generatedAt' => date('Y-m-d H:i:s'),
                        ],
                        'stats' => $stats,
                        'tickets' => $tickets,
                ];

                $payloadJson = json_encode(
                        $payload,
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                );

                if ($payloadJson === false) {
                        throw new Exception('Failed to prepare report data');
                }

                header('Content-Type: text/html; charset=UTF-8');

                echo '<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generating Counselor PDF Report</title>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
</head>
<body style="font-family: Arial, sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; color:#111827;">
    <div style="text-align:center;">
        <h2 style="margin-bottom:8px;">Preparing PDF report...</h2>
        <p style="margin-top:0; color:#6b7280;">Your download will start automatically.</p>
    </div>

    <script>
        const payload = ' . $payloadJson . ';

        function createPdfReport() {
            if (!window.jspdf || !window.jspdf.jsPDF) {
                throw new Error("PDF library failed to load");
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "a4" });

            doc.setFontSize(16);
            doc.text("Counselor Report", 40, 40);

            doc.setFontSize(10);
            doc.text(`Generated: ${payload.meta.generatedAt || "N/A"}`, 40, 58);
            doc.text(`Date Range: ${payload.meta.startDate || "N/A"} to ${payload.meta.endDate || "N/A"}`, 40, 74);
            doc.text(`Status: ${payload.meta.status || "All"}`, 40, 88);
            doc.text(`Priority: ${payload.meta.priority || "All"}`, 200, 88);

            doc.setFontSize(11);
            doc.text(`Total: ${payload.stats.total || 0}`, 40, 108);
            doc.text(`Pending: ${payload.stats.pending || 0}`, 130, 108);
            doc.text(`Assigned: ${payload.stats.assigned || 0}`, 230, 108);
            doc.text(`Resolved: ${payload.stats.resolved || 0}`, 340, 108);
            doc.text(`Closed: ${payload.stats.closed || 0}`, 450, 108);

            const rows = (payload.tickets || []).map(ticket => [
                ticket.ticket_id || "",
                ticket.title || "",
                ticket.status || "",
                ticket.priority || "",
                ticket.student_name || "N/A",
                ticket.division || "Counselling",
                ticket.created_at ? new Date(ticket.created_at).toLocaleDateString("en-US") : "N/A"
            ]);

            if (typeof doc.autoTable !== "function") {
                throw new Error("PDF table plugin failed to load");
            }

            doc.autoTable({
                startY: 124,
                head: [["ID", "Title", "Status", "Priority", "Student", "Division", "Date"]],
                body: rows,
                styles: { fontSize: 9, cellPadding: 5, overflow: "linebreak" },
                headStyles: { fillColor: [76, 29, 149], textColor: [255, 255, 255], fontStyle: "bold" },
                columnStyles: {
                    0: { cellWidth: 48 },
                    1: { cellWidth: 260 },
                    2: { cellWidth: 90 },
                    3: { cellWidth: 70 },
                    4: { cellWidth: 120 },
                    5: { cellWidth: 90 },
                    6: { cellWidth: 70 }
                },
                margin: { left: 30, right: 30 }
            });

            const safeStart = String(payload.meta.startDate || "start").replace(/[^0-9-]/g, "");
            const safeEnd = String(payload.meta.endDate || "end").replace(/[^0-9-]/g, "");
            doc.save(`counselor_report_${safeStart}_to_${safeEnd}.pdf`);

            setTimeout(() => {
                window.location.href = "/counselor/reports";
            }, 600);
        }

        try {
            createPdfReport();
        } catch (error) {
            document.body.innerHTML = `<div style="font-family: Arial, sans-serif; padding: 24px; color: #b91c1c;">
                <h3>Failed to generate PDF</h3>
                <p>${error.message}</p>
                <p><a href="/counselor/reports">Back to Reports</a></p>
            </div>`;
        }
    </script>
</body>
</html>';

        exit;

    } catch (Exception $e) {
        http_response_code(500);
                header('Content-Type: text/plain; charset=UTF-8');
                echo 'Failed to generate report PDF: ' . $e->getMessage();
        exit;
    }
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

    public function ticketFull()
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
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.description, t.u_id, t.assigned_to, u.name AS student_name, t.meeting_requested,
                       sa.name AS staff_name, sh.position, sh.level, tl.assigned AS assigned_at, tl.under_review AS under_review_at, tl.resolved AS resolved_at
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                LEFT JOIN division d ON d.did = t.division
                LEFT JOIN users sa ON sa.u_id = t.assigned_to
                LEFT JOIN staff_division sd ON sd.u_id = t.assigned_to AND sd.did = t.division
                LEFT JOIN staff_hierachy sh ON sh.h_id = sd.h_id
                LEFT JOIN ticket_timeline tl ON tl.ticket_id = t.ticket_id
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
        if ($res = $db->query("SELECT file_name, file_path FROM attachments WHERE entity_type = 'ticket' AND entity_id = $idEsc")) {
            while ($r = $res->fetch_assoc()) {
                $attachments[] = [ 'name' => (string)($r['file_name'] ?? ''), 'url' => '/' . ltrim((string)($r['file_path'] ?? ''), '/') ];
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
            'isPending' => $statusRaw === 'pending',
            'isAssignedToMe' => (int)($row['assigned_to'] ?? 0) === (int)($_SESSION['user']['u_id'] ?? 0),
            'timeline' => $timeline,
            'attachments' => $attachments,
        ]);
        exit;
    }

<<<<<<< HEAD
=======
    public function ticketAssign()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            http_response_code(400); echo json_encode(['error' => 'Missing id']); exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $model = new CounselorDashboard();
        $current_staff_id = (int)$_SESSION['user']['u_id'];
        
        try {
            $ticket = $model->getCounselorTicketById($id, $current_staff_id);
            if ($ticket && strtolower($ticket['status']) === 'pending') {
                $ok = $model->assignToCounselor($id, $current_staff_id);
                $ok2 = $model->setTicketupdateTimeline($id);
                if ($ok && $ok2) {
                    $model->setTicketLevel($id, 1); // Counselors act as level 1
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to assign']);
                }
            } else {
                http_response_code(400); echo json_encode(['error' => 'Ticket not pending']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function ticketForward()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = isset($data['id']) ? (int)$data['id'] : ($_GET['id'] ?? 0);
        $forward_to = isset($data['forward_to']) ? (int)$data['forward_to'] : 0;
        
        if (!$id || !$forward_to) {
            http_response_code(400); echo json_encode(['error' => 'Missing id or target counselor']); exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $model = new CounselorDashboard();
        $current_staff_id = (int)$_SESSION['user']['u_id'];
        
        try {
            $ticket = $model->getCounselorTicketById($id, $current_staff_id);
            if ($ticket && $ticket['assigned_to'] == $current_staff_id) {
                $ok = $model->forwardTicket($id, $current_staff_id, $forward_to);
                if ($ok) {
                    $model->setTicketLevel($id, 1);
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to forward']);
                }
            } else {
                http_response_code(400); echo json_encode(['error' => 'Not assigned to you']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function staffMembersList()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        $db = Database::getInstance();
        $sql = "SELECT u_id, name FROM users WHERE role = 'counselor' ORDER BY name";
        $members = [];
        if ($res = $db->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $members[] = $row;
            }
            $res->free();
        }
        echo json_encode(['success' => true, 'data' => $members]);
        exit;
    }

    public function ticketResolve()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = isset($data['id']) ? (int)$data['id'] : ($_GET['id'] ?? 0);
        
        if (!$id) {
            http_response_code(400); echo json_encode(['error' => 'Missing id']); exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $model = new CounselorDashboard();
        $current_staff_id = (int)$_SESSION['user']['u_id'];
        
        try {
            $ticket = $model->getCounselorTicketById($id, $current_staff_id);
            if ($ticket && $ticket['assigned_to'] == $current_staff_id) {
                $ok = $model->resolveTicket($id, $current_staff_id);
                $ok2 = $model->resolveTicketTimeLine($id);
                if ($ok && $ok2) {
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to resolve']);
                }
            } else {
                http_response_code(400); echo json_encode(['error' => 'Not assigned to you']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function ticketReject()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = isset($data['id']) ? (int)$data['id'] : ($_GET['id'] ?? 0);
        
        if (!$id) {
            http_response_code(400); echo json_encode(['error' => 'Missing id']); exit;
        }
        
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $model = new CounselorDashboard();
        $current_staff_id = (int)$_SESSION['user']['u_id'];
        
        try {
            $ticket = $model->getCounselorTicketById($id, $current_staff_id);
            if ($ticket && $ticket['assigned_to'] == $current_staff_id) {
                $ok = $model->rejectTicket($id, $current_staff_id);
                if ($ok) {
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to reject']);
                }
            } else {
                http_response_code(400); echo json_encode(['error' => 'Not assigned to you']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
    public function chatMessages()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
        if ($ticketId <= 0) {
<<<<<<< HEAD
            echo json_encode(['error' => 'missing_ticket_id']);
=======
            echo json_encode(['error' => 'missing ticket_id']);
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
            return;
        }

        require_once __DIR__ . '/../../models/TicketChat.php';
        $chatModel = new TicketChat();
<<<<<<< HEAD

        $messages = [];
        $chat = $chatModel->getChatByTicketId($ticketId);
        if ($chat) {
            $messages = $chatModel->getMessages((int)$chat['chat_id']);
            $counselorId = (int)($_SESSION['user']['u_id'] ?? 0);
            $chatModel->markMessagesAsRead((int)$chat['chat_id'], $counselorId);
=======
        
        $chat = $chatModel->getChatByTicketId($ticketId);
        $messages = [];
        
        if ($chat) {
            $messages = $chatModel->getMessages($chat['chat_id']);
            $staffId = (int)($_SESSION['user']['u_id'] ?? 0);
            $chatModel->markMessagesAsRead($chat['chat_id'], $staffId);
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
        }

        echo json_encode(['messages' => $messages]);
    }

    public function sendMessage()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'invalid_method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ticketId = isset($input['ticket_id']) ? (int)$input['ticket_id'] : 0;
<<<<<<< HEAD
        $message = isset($input['message']) ? trim((string)$input['message']) : '';

        if ($ticketId <= 0 || $message === '') {
=======
        $message = isset($input['message']) ? trim($input['message']) : '';

        if ($ticketId <= 0 || empty($message)) {
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
            echo json_encode(['error' => 'missing_data']);
            return;
        }

        require_once __DIR__ . '/../../models/TicketChat.php';
        $chatModel = new TicketChat();
<<<<<<< HEAD

        $db = Database::getInstance();
        $counselorId = (int)($_SESSION['user']['u_id'] ?? 0);

        $ticketQuery = "SELECT t.u_id FROM tickets t
                        LEFT JOIN division d ON d.did = t.division
                        WHERE t.ticket_id = $ticketId
                          AND LOWER(COALESCE(d.name,'')) LIKE 'counsel%'
                        LIMIT 1";
        $ticketResult = $db->query($ticketQuery);
        if (!$ticketResult || $ticketResult->num_rows === 0) {
            echo json_encode(['error' => 'ticket_not_found']);
            return;
        }

        $ticket = $ticketResult->fetch_assoc();
        $studentId = !empty($ticket['u_id']) ? (int)$ticket['u_id'] : 0;
        if ($studentId <= 0) {
            echo json_encode(['error' => 'student_not_found']);
            return;
        }

=======
        
        $staffId = (int)($_SESSION['user']['u_id'] ?? 0);
        
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
        $chat = $chatModel->getChatByTicketId($ticketId);
        $chatId = 0;

        if (!$chat) {
<<<<<<< HEAD
            $chatId = (int)$chatModel->createChat($ticketId, $studentId, $counselorId);
        } else {
            $chatId = (int)$chat['chat_id'];
        }

        if ($chatId <= 0) {
            echo json_encode(['error' => 'chat_creation_failed']);
            return;
        }

        $ok = $chatModel->sendMessage($chatId, $counselorId, $message, 'text', null);
        if (!$ok) {
            echo json_encode(['error' => 'send_failed']);
            return;
        }

        echo json_encode(['success' => true]);
=======
            require_once __DIR__ . '/../../models/staff/Ticket.php';
            $ticketModel = new StaffTicket();
            $ticket = $ticketModel->getTicketById($ticketId);
            
            if (!$ticket) {
                echo json_encode(['error' => 'ticket_not_found']);
                return;
            }
            
            $studentId = $ticket['u_id'];
            $chatId = $chatModel->createChat($ticketId, $studentId, $staffId);
        } else {
            $chatId = $chat['chat_id'];
        }

        if ($chatId) {
            $success = $chatModel->sendMessage($chatId, $staffId, $message);
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'failed_to_send']);
            }
        } else {
            echo json_encode(['error' => 'failed_to_create_chat']);
        }
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
    }

    // Forum posts data (JSON) for counselor
    public function forumData()
    {
        $this->requireLogin('counselor');
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

        $where = [];
        // Counselor can see all public posts or their own
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

        $orderSql = 'ORDER BY f.created_at DESC';
        $srt = strtolower($sort);
        if ($srt === 'oldest') {
            $orderSql = 'ORDER BY f.created_at ASC';
        } elseif ($srt === 'votes') {
            $orderSql = 'ORDER BY (SELECT COALESCE(SUM(vote_type), 0) FROM forum_votes WHERE post_id = f.q_id) DESC, f.created_at DESC';
        }

        $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.u_id, f.is_Public, u.name AS student_name,
                (SELECT COALESCE(SUM(vote_type), 0) FROM forum_votes WHERE post_id = f.q_id) as vote_count,
                (SELECT vote_type FROM forum_votes WHERE post_id = f.q_id AND u_id = $uId LIMIT 1) as my_vote
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
                'votes' => (int)($r['vote_count'] ?? 0),
                'voted' => (int)($r['my_vote'] ?? 0),
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

    // Single forum post data
    public function forumPostData()
    {
        $this->requireLogin('counselor');
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
        // Counselor can see public posts or own posts
        $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.description, f.u_id, f.is_Public, u.name AS student_name,
                (SELECT COALESCE(SUM(vote_type), 0) FROM forum_votes WHERE post_id = f.q_id) as vote_count,
                (SELECT vote_type FROM forum_votes WHERE post_id = f.q_id AND u_id = $uId LIMIT 1) as my_vote
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

        // attachments from attachments table
        $attachments = [];
        if ($res = $db->query("SELECT file_name, file_path FROM attachments WHERE entity_type = 'forum' AND entity_id = $idEsc")) {
            while ($r = $res->fetch_assoc()) {
                $attachments[] = [
                    'name' => (string)($r['file_name'] ?? ''),
                    'url' => '/' . ltrim((string)($r['file_path'] ?? ''), '/'),
                ];
            }
            $res->free();
        }

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
            'attachments' => $attachments,
            'commentsCount' => 0,
            'votes' => (int)($row['vote_count'] ?? 0),
            'voted' => (int)($row['my_vote'] ?? 0),
            'isOwner' => ((int)$row['u_id'] === $uId),
        ];

        echo json_encode($payload);
    }

    public function forumVote()
    {
        $this->requireLogin('counselor');
        header('Content-Type: application/json');

        $db = Database::getInstance();
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : '';

        if ($uId <= 0 || $id <= 0 || !in_array($type, ['up', 'down'])) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        $voteVal = ($type === 'up') ? 1 : -1;

        // Check if post exists & accessible
        $check = $db->query("SELECT q_id FROM forum_q WHERE q_id = $id AND (is_Public = 1 OR u_id = $uId)");
        if (!$check || $check->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'not_found']);
            return;
        }

        // Check existing vote
        $existing = 0;
        $checkVote = $db->query("SELECT vote_type FROM forum_votes WHERE post_id = $id AND u_id = $uId");
        if ($checkVote && $row = $checkVote->fetch_assoc()) {
            $existing = (int)$row['vote_type'];
        }

        if ($existing === $voteVal) {
            // Toggle off (remove vote)
            $db->query("DELETE FROM forum_votes WHERE post_id = $id AND u_id = $uId");
            $newVote = 0;
        } else {
            // Insert or Update
            $stmt = $db->prepare("INSERT INTO forum_votes (post_id, u_id, vote_type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE vote_type = ?");
            if ($stmt) {
                $stmt->bind_param("iiii", $id, $uId, $voteVal, $voteVal);
                $stmt->execute();
            }
            $newVote = $voteVal;
        }

        // Get new total
        $totalRes = $db->query("SELECT COALESCE(SUM(vote_type), 0) as cnt FROM forum_votes WHERE post_id = $id");
        $total = 0;
        if ($totalRes && $r = $totalRes->fetch_assoc()) {
            $total = (int)$r['cnt'];
        }

        echo json_encode(['ok' => true, 'votes' => $total, 'voted' => $newVote]);
    }

    // Toggle forum post visibility (Counselor can change own posts only)
    public function forumToggleVisibility()
    {
        $this->requireLogin('counselor');
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

    // Toggle forum post status (Counselor can change own posts only)
    public function forumToggleStatus()
    {
        $this->requireLogin('counselor');
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

    // Delete a forum post (Counselor can delete own posts only)
    public function forumDelete()
    {
        $this->requireLogin('counselor');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'method_not_allowed';
            return;
        }

        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0 || $uId <= 0) {
            http_response_code(400);
            echo 'bad_request';
            return;
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("DELETE FROM forum_q WHERE q_id = ? AND u_id = ? LIMIT 1");
            if (!$stmt) throw new Exception('prepare_failed');
            $stmt->bind_param('ii', $id, $uId);
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception('execute_failed: ' . $err);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected <= 0) {
                http_response_code(403);
                echo 'not_allowed';
                return;
            }
            echo 'ok';
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'server_error';
        }
    }


}
