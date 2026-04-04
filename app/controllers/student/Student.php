<?php

class Student extends Controller
{
    public function settings()
    {
        $this->requireLogin('student');
        $headContent = '\n        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => 'student',
            'roleLabel' => 'Student',
            'roleMessage' => 'Student settings: personalize your account and preferences (dummy content).',
        ]);
    }

    public function dashboard()
    {
        $this->requireLogin('student');
        require_once __DIR__ . '/../../models/student/Ticket.php';
        $ticketModel = new StudentTicket();
        $recent = [];
        $openCount = 0;
        $lastActivity = null;
        $recentAnnouncements = [];
        $upcomingEvents = [];
        try {
            $uId = (int)($_SESSION['user']['u_id'] ?? 0);
            $dashboardData = $ticketModel->getDashboardData($uId, 3);
            $recent = $dashboardData['recent'] ?? [];
            $openCount = $dashboardData['openCount'] ?? 0;
            $lastActivity = $dashboardData['lastActivity'] ?? null;
        } catch (Throwable $e) {
            $recent = [];
        }

        try {
            require_once __DIR__ . '/../../models/student/Announcement.php';
            $annModel = new StudentAnnouncement();
            $recentAnnouncements = $annModel->getRecent(2);
        } catch (Throwable $e) {
            $recentAnnouncements = [];
        }

        try {
            require_once __DIR__ . '/../../models/CalendarEvent.php';
            $calModel = new CalendarEvent();
            $upcomingEvents = $calModel->getUpcomingEvents($uId, 3);
        } catch (Throwable $e) {
            $upcomingEvents = [];
        }

        $recentForumPosts = [];
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT f.q_id as id, f.title, f.created_at, u.name as author FROM forum_q f LEFT JOIN users u ON f.u_id = u.u_id WHERE f.is_Public = 1 ORDER BY f.created_at DESC LIMIT 3");
            if ($stmt) {
                while ($row = $stmt->fetch_assoc()) {
                    $recentForumPosts[] = $row;
                }
            }
        } catch (Throwable $e) {
            $recentForumPosts = [];
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
                'upcomingEvents' => $upcomingEvents,
                'recentForumPosts' => $recentForumPosts,
        ]);
    }

    public function ticket()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $when = trim($_POST['when'] ?? '');
            $details = trim($_POST['details'] ?? '');
            $priority = trim($_POST['priority'] ?? 'Medium');
            $t_type = trim($_POST['ticketType'] ?? 'private');
            $errors = [];
            if ($title === '') { $errors[] = 'Title is required.'; }
            if ($category === '') { $errors[] = 'Category is required.'; }
            if ($details === '') { $errors[] = 'Details are required.'; }

            if (empty($errors)) {
                try {
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
                        'type' => $t_type,
                    ]);

                    // Handle attachments
                    if ($ticketId && isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
                        require_once __DIR__ . '/../../models/Attachment.php';
                        $attachmentModel = new Attachment();
                        
                        $fileCount = count($_FILES['attachments']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            // Reconstruct the individual file array for handle_upload
                            $fileArr = [
                                'name' => $_FILES['attachments']['name'][$i],
                                'type' => $_FILES['attachments']['type'][$i],
                                'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                                'error' => $_FILES['attachments']['error'][$i],
                                'size' => $_FILES['attachments']['size'][$i]
                            ];
                            
                            if ($fileArr['error'] === UPLOAD_ERR_OK) {
                                $uploadData = handle_upload($fileArr, 'ticket');
                                if ($uploadData) {
                                    $attachmentModel->insert([
                                        'entity_type' => 'ticket',
                                        'entity_id' => $ticketId,
                                        'file_name' => $uploadData['file_name'],
                                        'file_path' => $uploadData['file_path'],
                                        'file_type' => $uploadData['file_type'],
                                        'file_size' => $uploadData['file_size'],
                                        'uploaded_by' => (int)($_SESSION['user']['u_id'] ?? 0)
                                    ]);
                                }
                            }
                        }
                    }

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

   // Updated templates() method in controllers/student.php
// Replace the existing templates() method with this:

public function templates()
{
    $this->requireLogin('student');

    $templates = [];
    $errors = [];
    $success = '';

    require_once __DIR__ . '/../../models/staff/Template.php';
    require_once __DIR__ . '/../../models/student/Ticket.php';
    $tplModel = new Template();
    $ticketModel = new StudentTicket();

    try {
        $allTemplates = $tplModel->getAll();
        foreach ($allTemplates as &$tpl) {
            if (isset($tpl['fields']) && is_string($tpl['fields'])) {
                $decoded = json_decode($tpl['fields'], true);
                $tpl['fields'] = is_array($decoded) ? $decoded : [];
            }
        }
        $templates = $allTemplates;
        unset($tpl);
    } catch (Throwable $e) {
        error_log('Failed to load templates: ' . $e->getMessage());
        $templates = [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_id'])) {
        $template_id = (int)$_POST['template_id'];
        $u_id = (int)($_SESSION['user']['u_id'] ?? 0);

        if (!$u_id || !$template_id) {
            $errors[] = 'Invalid submission.';
        } else {
            // Fetch specific template for validation and data
            $template = null;
            try {
                $template = $tplModel->getById($template_id);
                if ($template && isset($template['fields']) && is_string($template['fields'])) {
                    $decoded = json_decode($template['fields'], true);
                    $template['fields'] = is_array($decoded) ? $decoded : [];
                }
            } catch (Throwable $e) {
                error_log('Failed to load template: ' . $e->getMessage());
                $template = null;
            }

            if (!$template || empty($template['fields'])) {
                $errors[] = 'Invalid template selected.';
            } else {
                // Validate required fields
                $field_values = [];
                foreach ($template['fields'] as $field) {
                    $value = trim($_POST[$field] ?? '');
                    if (empty($value)) {
                        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                    }
                    $field_values[$field] = $value;
                }

                // Handle file upload (optional)
                $file_path = null;
                $upload_dir = __DIR__ . '/../../../uploads/tickets/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                $max_size = 5 * 1024 * 1024; // 5MB

                $file = $_FILES['file'] ?? null;
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed_types)) {
                        $errors[] = 'Invalid file type. Allowed: PDF, JPG, JPEG, PNG, DOC, DOCX.';
                    } elseif ($file['size'] > $max_size) {
                        $errors[] = 'File size exceeds 5MB limit.';
                    } else {
                        $filename = uniqid() . '.' . $ext;
                        $full_path = $upload_dir . $filename;
                        if (move_uploaded_file($file['tmp_name'], $full_path)) {
                            $file_path = '/uploads/tickets/' . $filename;
                        } else {
                            $errors[] = 'Failed to upload file.';
                        }
                    }
                } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                    $errors[] = 'File upload error: ' . $file['error'];
                }
                if (empty($errors)) {
                    // Build numbered description from fields
                    $description = '';
                    $counter = 1;
                    foreach ($field_values as $field => $value) {
                        $label = ucfirst(str_replace('_', ' ', $field));
                        $description .= '['.$counter .']'. '. ' . $label . ': ' . $value . "\n";
                        $counter++;
                    }

                    // Prepare ticket data
                    $data = [
                        'title' => $template['name'],
                        'u_id' => $u_id,
                        'category' => $template['category'], // Division name for mapping
                        'priority' => 'Medium', // Default
                        'status' => 'pending',
                        'description' => $description,
                        'meeting_requested' => null, // No request in template
                        'type' => 'template',
                    ];

                    try {
                        $ticket_id = $ticketModel->create($data);

                        // Add file if uploaded
                        if ($file_path) {
                            $ticketModel->addFile($ticket_id, $file_path, $_FILES['file']['name']);
                        }

                        $success = 'Ticket submitted successfully using template "' . htmlspecialchars($template['name']) . '". Ticket ID: TKT-' . str_pad($ticket_id, 4, '0', STR_PAD_LEFT);
                    } catch (Throwable $e) {
                        error_log('Failed to create ticket from template: ' . $e->getMessage());
                        $errors[] = 'Failed to submit ticket: ' . $e->getMessage();
                    }
                }
            }
        }
    }

    $headContent = '<link rel="stylesheet" href="/css/student/studentTemplate.view.css" />';

    $this->view('studentTemplate', [
        'title' => 'Use Template',
        'head' => $headContent,
        'templates' => $templates,
        'errors' => $errors,
        'success' => $success,
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

        $items = array_merge($found, $lost, $claimed);
        usort($items, function($a, $b){
            return (int)($b['q_id'] ?? 0) <=> (int)($a['q_id'] ?? 0);
        });

        try {
            $uIds = [];
            foreach ($items as $row) {
                if (!empty($row['u_id'])) { $uIds[] = (int)$row['u_id']; }
            }
            $uIds = array_values(array_unique(array_filter($uIds)));
            if (!empty($uIds)) {
                $db = Database::getInstance();
                $in = implode(',', array_map('intval', $uIds));
                $map = [];
                if ($res = $db->query("SELECT u_id, name FROM users WHERE u_id IN ($in)")) {
                    while ($r = $res->fetch_assoc()) {
                        $map[(int)$r['u_id']] = (string)($r['name'] ?? '');
                    }
                    $res->free();
                }
                foreach ($items as &$row) {
                    $uid = (int)($row['u_id'] ?? 0);
                    if ($uid && isset($map[$uid])) {
                        $row['owner_name'] = $map[$uid];
                    }
                }
                unset($row);
            }
        } catch (Throwable $e) {
        }

    $headContent = '<link rel="stylesheet" href="/css/student/studentTickets.css" />' . "\n" .
               '<link rel="stylesheet" href="/css/student/studentLostFound.css" />';
        $this->view('student/lostFound', [
            'title' => 'Lost & Found',
            'head' => $headContent,
            'items' => $items,
            'lostItems' => $lost,
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

        // Use globalized announcements stylesheet
        $headContent = '<link rel="stylesheet" href="/css/announcements/announcements.css" />';
        $this->view('announcements', [
            'title' => 'Announcements',
            'head' => $headContent,
            'announcements' => $announcements,
            'dbError' => $dbError,
            'role' => 'student',
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

        // Use globalized announcement full stylesheet
        $headContent = '<link rel="stylesheet" href="/css/announcements/announcementFull.css" />';
        $this->view('announcementsFull', [
            'title' => 'Announcement Details',
            'head' => $headContent,
            'announcement' => $announcement,
            'files' => $files,
            'role' => 'student',
        ]);
    }

    // Student FAQ page
    public function faq()
    {
        $this->requireLogin('student');
        
        // Fetch FAQs from database
        require_once __DIR__ . '/../../models/staff/Faq.php';
        $faqModel = new StaffFaqModel();
        // Get all FAQs (limit 100 for now)
        $faqs = $faqModel->getFaqs('', 100, 0);

        $headContent = '<link rel="stylesheet" href="/css/student/studentFAQ.css" />';
        $this->view('student/studentFAQ', [
            'title' => 'FAQs',
            'head' => $headContent,
            'faqs' => $faqs
        ]);
    }

    // Student Knowledge Base page
    public function knowledgebase()
    {
        $this->requireLogin('student');

        require_once __DIR__ . '/../../models/staff/KB.php';
        $kbModel = new KB();
        $articles = [];
        try {
            $articles = $kbModel->getAllArticles();
        } catch (Throwable $e) {
            $articles = [];
        }

        // Group by section
        $grouped = [];
        foreach ($articles as $row) {
            $sec = $row['section'] ?? 'Other';
            if (!isset($grouped[$sec])) {
                $grouped[$sec] = [
                    'section' => $sec,
                    'items' => []
                ];
            }
            
            // Format date
            $updated = $row['updated'] ?? '';
            if ($updated) {
                $ts = strtotime($updated);
                if ($ts) $updated = date('F Y', $ts);
            }

            // Determine color based on type
            $type = $row['type'] ?? 'Guide';
            $color = 'blue';
            if (stripos($type, 'schedule') !== false) $color = 'green';
            
            // Get files
            $files = $kbModel->getFilesByArticle($row['base_id']);
            $fileUrl = null;
            if (!empty($files)) {
                // Use the first file
                $fileUrl = $files[0]['file_path'] ?? null;
                // Ensure path starts with / if relative
                if ($fileUrl && $fileUrl[0] !== '/') {
                    $fileUrl = '/' . $fileUrl;
                }
            }

            $grouped[$sec]['items'][] = [
                'id' => (int)$row['base_id'],
                'title' => $row['topic'],
                'updated' => $updated,
                'type' => $type,
                'desc' => $row['description'],
                'color' => $color,
                'fileUrl' => $fileUrl
            ];
        }

        $this->view('knowledgeBase', [
            'kb_data' => array_values($grouped)
        ]);
    }

    // Student Calendar page
    public function calender()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/calender/calender.css" />';
        $this->view('calender', [
            'title' => 'Calendar',
            'head' => $headContent,
            'role' => 'student',
            'roleLabel' => 'Student',
            'roleMessage' => 'Student calendar: keep track of your schedule (dummy content).',
        ]);
    }

    // Student forum page (using global forum view)
    public function forum()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/forum/forum.css" />';
        $this->view('forum', [
            'title' => 'Forum',
            'head' => $headContent,
        ]);
    }

    public function forumFull()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/forum/forumFull.css" />';
        $this->view('forumFull', [
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
                        $forumId = $stmt->insert_id;
                        $stmt->close();
                        if ($ok) {
                            // Handle attachments
                            if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
                                require_once __DIR__ . '/../../models/Attachment.php';
                                $attachmentModel = new Attachment();
                                
                                $fileCount = count($_FILES['attachments']['name']);
                                for ($i = 0; $i < $fileCount; $i++) {
                                    $fileArr = [
                                        'name' => $_FILES['attachments']['name'][$i],
                                        'type' => $_FILES['attachments']['type'][$i],
                                        'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                                        'error' => $_FILES['attachments']['error'][$i],
                                        'size' => $_FILES['attachments']['size'][$i]
                                    ];
                                    
                                    if ($fileArr['error'] === UPLOAD_ERR_OK) {
                                        $uploadData = handle_upload($fileArr, 'forum');
                                        if ($uploadData) {
                                            $attachmentModel->insert([
                                                'entity_type' => 'forum',
                                                'entity_id' => $forumId,
                                                'file_name' => $uploadData['file_name'],
                                                'file_path' => $uploadData['file_path'],
                                                'file_type' => $uploadData['file_type'],
                                                'file_size' => $uploadData['file_size'],
                                                'uploaded_by' => $uId
                                            ]);
                                        }
                                    }
                                }
                            }

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

        $this->view('newForum', [
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
        } elseif ($srt === 'votes') {
            $orderSql = 'ORDER BY (SELECT COALESCE(SUM(vote_type), 0) FROM forum_votes WHERE post_id = f.q_id) DESC, f.created_at DESC';
        }
        // 'comments' default to created_at for now

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
            'commentsCount' => (int)($db->query("SELECT COUNT(*) as c FROM forum_comments WHERE post_id = " . (int)($row['q_id'] ?? 0))->fetch_assoc()['c'] ?? 0),
            'votes' => (int)($row['vote_count'] ?? 0),
            'voted' => (int)($row['my_vote'] ?? 0),
            'isOwner' => ((int)$row['u_id'] === $uId),
        ];

        echo json_encode($payload);
    }

    public function forumVote()
    {
        $this->requireLogin('student');
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

    public function forumComments()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        $db = Database::getInstance();
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($uId <= 0 || $id <= 0) {
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        // Check view permission
        $check = $db->query("SELECT q_id FROM forum_q WHERE q_id = $id AND (is_Public = 1 OR u_id = $uId)");
        if (!$check || $check->num_rows === 0) {
            echo json_encode([]);
            return;
        }

        $sql = "SELECT c.id, c.parent_id, c.content, c.created_at, u.name as author_name, u.role as author_role, u.u_id as author_id
                FROM forum_comments c
                LEFT JOIN users u ON u.u_id = c.u_id
                WHERE c.post_id = $id
                ORDER BY c.created_at ASC";
        
        $comments = [];
        if ($res = $db->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                // Formatting time
                $ts = strtotime($row['created_at']);
                $time = ($ts) ? date('M d, g:i A', $ts) : '';
                
                $comments[] = [
                    'id' => (int)$row['id'],
                    'parentId' => $row['parent_id'] ? (int)$row['parent_id'] : null,
                    'text' => $row['content'],
                    'time' => $time,
                    'name' => $row['author_name'] ?? 'Unknown',
                    'role' => $row['author_role'] ?? '',
                    'authorType' => $row['author_role'] ?? 'student', // simplified
                    'authorId' => (int)$row['author_id']
                ];
            }
        }
        echo json_encode($comments);
    }

    public function forumAddComment()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'method']);
            return;
        }

        $db = Database::getInstance();
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $parentId = isset($_POST['parentId']) && $_POST['parentId'] ? (int)$_POST['parentId'] : null;
        $text = isset($_POST['text']) ? trim($_POST['text']) : '';

        if ($uId <= 0 || $id <= 0 || empty($text)) {
            http_response_code(400);
            echo json_encode(['error' => 'bad_request']);
            return;
        }

        // Check permissions
        $check = $db->query("SELECT q_id FROM forum_q WHERE q_id = $id AND (is_Public = 1 OR u_id = $uId)");
        if (!$check || $check->num_rows === 0) {
            http_response_code(403);
            echo json_encode(['error' => 'forbidden']);
            return;
        }

        $stmt = $db->prepare("INSERT INTO forum_comments (post_id, u_id, parent_id, content) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiis", $id, $uId, $parentId, $text);
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                // Fetch back to return UI friendly data
                $res = $db->query("SELECT c.created_at, u.name as author_name, u.role FROM forum_comments c LEFT JOIN users u ON u.u_id = c.u_id WHERE c.id = $newId");
                $r = $res ? $res->fetch_assoc() : null;
                $time = $r ? date('M d, g:i A', strtotime($r['created_at'])) : 'Just now';
                
                echo json_encode([
                    'ok' => true, 
                    'comment' => [
                        'id' => $newId,
                        'parentId' => $parentId,
                        'text' => $text,
                        'time' => $time,
                        'name' => $r['author_name'] ?? 'Me',
                        'role' => $r['role'] ?? 'student',
                        'authorType' => $r['role'] ?? 'student'
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'db_error']);
            }
        } else {
             http_response_code(500);
             echo json_encode(['error' => 'prepare_error']);
        }
    }

    // Delete a forum post owned by the current student
    public function forumDelete()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0 || $uId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad request']);
            return;
        }

        $db = Database::getInstance();
        $idEsc = (int)$id;
        // Ensure ownership
        $ownRow = null;
        if ($res = $db->query("SELECT q_id FROM forum_q WHERE q_id = $idEsc AND u_id = $uId")) {
            $ownRow = $res->fetch_assoc();
            $res->free();
        }
        if (!$ownRow) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }

        // Delete post; replies are ON DELETE CASCADE (see schema)
        $ok = $db->query("DELETE FROM forum_q WHERE q_id = $idEsc AND u_id = $uId");
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
            return;
        }

        echo json_encode(['success' => true]);
    }



    // Student tickets list (using global tickets view)
    public function tickets()
    {
        $this->requireLogin('student');
        $headContent = '<link rel="stylesheet" href="/css/tickets/tickets.css" />';
        $this->view('tickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'role' => 'student',
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
        // Scope to current user OR public tickets
        $where[] = "(t.u_id = $uId OR t.t_type = 'public')";

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

    $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS division_name, t.status, t.priority, t.meeting_requested, t.t_type, u.name AS student_name, u.u_id AS student_id
        FROM tickets t
        LEFT JOIN division d ON d.did = t.division
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
                'student' => [ 'id' => (int)($r['student_id'] ?? 0), 'name' => (string)($r['student_name'] ?? 'Unknown') ],
                'category' => (string)($r['division_name'] ?? ''),
                'status' => $mapStatus($r['status'] ?? ''),
                'meeting' => $mapMeeting($r['meeting_requested'] ?? ''),
                'priority' => strtolower((string)($r['priority'] ?? '')),
                'visibility' => (string)($r['t_type'] ?? 'private'),
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
        $headContent = '<link rel="stylesheet" href="/css/ticketFull/ticketFull.css" />';
        $this->view('ticketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'role' => 'student',
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
    $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS division_name, t.status, t.priority, t.description, t.u_id, u.name AS student_name,
               sa.name AS staff_name, sh.position, sh.level, tl.assigned AS assigned_at, tl.under_review AS under_review_at, tl.resolved AS resolved_at
        FROM tickets t
        LEFT JOIN users u ON u.u_id = t.u_id
        LEFT JOIN division d ON d.did = t.division
        LEFT JOIN users sa ON sa.u_id = t.assigned_to
        LEFT JOIN staff_division sd ON sd.u_id = t.assigned_to AND sd.did = t.division
        LEFT JOIN staff_hierachy sh ON sh.h_id = sd.h_id
        LEFT JOIN ticket_timeline tl ON tl.ticket_id = t.ticket_id
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

        // attachments from attachments table
        $attachments = [];
        if ($res = $db->query("SELECT file_name, file_path FROM attachments WHERE entity_type = 'ticket' AND entity_id = $idEsc")) {
            while ($r = $res->fetch_assoc()) {
                $attachments[] = [
                    'name' => (string)($r['file_name'] ?? ''),
                    'url' => '/' . ltrim((string)($r['file_path'] ?? ''), '/'),
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

        // --- Timeline Logic ---
        $timeline = [];
        // 1. Created
        $timeline[] = [
            'label' => 'Ticket created',
            'time' => $createdPretty ?: '—',
            'color' => 'green',
            'pending' => false
        ];

        // 2. Assigned to staff
        $staffName = $ticket['staff_name'] ?? null;
        $position = $ticket['position'] ?? null;
        $level = $ticket['level'] ?? null;
        $assignedAt = $ticket['assigned_at'] ?? null;
        
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

        $timeline[] = [
            'label' => $assignLabel,
            'time' => $assignTime,
            'color' => $assignColor,
            'pending' => $assignPending
        ];

        // 3. Under Review
        $underReviewAt = $ticket['under_review_at'] ?? null;
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
        $timeline[] = [
            'label' => $reviewLabel,
            'time' => $reviewTime,
            'color' => $reviewColor,
            'pending' => $reviewPending
        ];

        // 4. Resolved
        $resolvedAt = $ticket['resolved_at'] ?? null;
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
        $timeline[] = [
            'label' => $resolveLabel,
            'time' => $resolveTime,
            'color' => $resolveColor,
            'pending' => $resolvePending
        ];
        // ----------------------

        // Fetch staff responses for conversation
        $messages = [];
        if ($res = $db->query("SELECT tr.response, tr.date_time, u.name AS staff_name, (
                SELECT d.name FROM staff_division sd
                JOIN division d ON d.did = sd.did
                WHERE sd.u_id = u.u_id
                LIMIT 1
            ) AS division_name
            FROM ticket_response tr
            JOIN users u ON u.u_id = tr.u_id
            WHERE tr.ticket_id = $idEsc
            ORDER BY tr.date_time ASC")) {
            while ($r = $res->fetch_assoc()) {
                $dt = (string)($r['date_time'] ?? '');
                $pretty = '';
                if ($dt) { $ts = strtotime($dt); if ($ts !== false) { $pretty = date('M d, Y \\a\\t g:i A', $ts); } }
                $messages[] = [
                    'name' => (string)($r['staff_name'] ?: 'Staff'),
                    'role' => (string)($r['division_name'] ?: 'Staff'),
                    'time' => $pretty,
                    'text' => (string)($r['response'] ?? ''),
                    'authorType' => 'staff',
                ];
            }
            $res->free();
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
            'assigned' => !empty($staffName) ? ($position ? "$staffName ($position)" : $staffName) : null,
            'timeline' => $timeline,
            'attachments' => $attachments,
            'messages' => $messages,
            'allowReply' => true,
        ];

        $feedback = null;
        if ($resFb = $db->query("SELECT rating, feedback, created_at FROM feedbacks WHERE ticket_id = $idEsc LIMIT 1")) {
            if ($rFb = $resFb->fetch_assoc()) {
                $feedback = [
                    'rating' => (int)$rFb['rating'],
                    'feedback' => (string)$rFb['feedback'],
                    'createdAt' => date('M d, Y \a\t g:i A', strtotime($rFb['created_at']))
                ];
            }
            $resFb->free();
        }
        $payload['feedback'] = $feedback;

        echo json_encode($payload);
    }

    public function submitFeedback()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) $input = $_POST;
        }

        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $ticketId = isset($input['ticket_id']) ? (int)$input['ticket_id'] : 0;
        $rating = isset($input['rating']) ? (int)$input['rating'] : 0;
        $feedbackText = isset($input['feedback']) ? (string)$input['feedback'] : '';

        if ($ticketId <= 0 || $rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid feedback data']);
            return;
        }

        $db = Database::getInstance();
        $ticketIdEsc = (int)$ticketId;
        
        $ownRow = null;
        if ($res = $db->query("SELECT status FROM tickets WHERE ticket_id = $ticketIdEsc AND u_id = $studentId")) {
            $ownRow = $res->fetch_assoc();
            $res->free();
        }
        if (!$ownRow) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found or permission denied']);
            return;
        }

        $statusRaw = strtolower((string)$ownRow['status']);
        if (!in_array($statusRaw, ['resolved', 'closed', 'agent-closed'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Ticket is not resolved yet']);
            return;
        }

        $feedbackTextEsc = $db->real_escape_string($feedbackText);

        $sql = "INSERT INTO feedbacks (ticket_id, student_id, rating, feedback) VALUES ($ticketIdEsc, $studentId, $rating, '$feedbackTextEsc')";
        
        if ($db->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save feedback']);
        }
    }

    // Delete a ticket owned by the current student
    public function ticketDelete()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad request']);
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
            echo json_encode(['error' => 'Not found']);
            return;
        }

        // Optionally delete attachments first if FK constraints exist
        $db->query("DELETE FROM attachments WHERE entity_type = 'ticket' AND entity_id = $idEsc");
        $db->query("DELETE FROM tickets WHERE ticket_id = $idEsc AND u_id = $studentId");

        echo json_encode(['success' => true]);
    }

    // Mark a ticket owned by the current student as resolved
    public function ticketResolve()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad request']);
            return;
        }

        $db = Database::getInstance();
        $idEsc = (int)$id;
        // Ensure ownership
        $ownRow = null;
        if ($res = $db->query("SELECT ticket_id FROM tickets WHERE ticket_id = $idEsc AND u_id = $studentId")) {
            $ownRow = $res->fetch_assoc();
            $res->free();
        }
        if (!$ownRow) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }

        // Update status to resolved
        $ok = $db->query("UPDATE tickets SET status = 'resolved' WHERE ticket_id = $idEsc AND u_id = $studentId");
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
            return;
        }

        // Update ticket_timeline resolved timestamp
        $db->query("UPDATE ticket_timeline SET resolved = CURRENT_TIMESTAMP WHERE ticket_id = $idEsc");

        // Trigger notification for status change
        require_once __DIR__ . '/../../lib/NotificationHelper.php';
        NotificationHelper::notifyTicketStatusChange($idEsc, 'resolved', $studentId);

        echo json_encode(['success' => true]);
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

    public function chatMessages()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
        if ($ticketId <= 0) {
            echo json_encode(['error' => 'missing ticket_id']);
            return;
        }

        require_once __DIR__ . '/../../models/TicketChat.php';
        $chatModel = new TicketChat();
        
        // Verify ticket ownership
        $db = Database::getInstance();
        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $checkSql = "SELECT u_id FROM tickets WHERE ticket_id = $ticketId AND u_id = $studentId";
        $res = $db->query($checkSql);
        if (!$res || $res->num_rows === 0) {
             echo json_encode(['error' => 'access_denied']);
             return;
        }

        $chat = $chatModel->getChatByTicketId($ticketId);
        $messages = [];
        
        if ($chat) {
            $messages = $chatModel->getMessages($chat['chat_id']);
            // Mark messages as read
            $chatModel->markMessagesAsRead($chat['chat_id'], $studentId);
        }

        echo json_encode(['messages' => $messages]);
    }

    public function sendMessage()
    {
        $this->requireLogin('student');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'invalid_method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $ticketId = isset($input['ticket_id']) ? (int)$input['ticket_id'] : 0;
        $message = isset($input['message']) ? trim($input['message']) : '';

        if ($ticketId <= 0 || empty($message)) {
            echo json_encode(['error' => 'missing_data']);
            return;
        }

        require_once __DIR__ . '/../../models/TicketChat.php';
        $chatModel = new TicketChat();
        
        // Verify ticket ownership and get assigned staff
        $db = Database::getInstance();
        $studentId = (int)($_SESSION['user']['u_id'] ?? 0);
        $checkSql = "SELECT u_id, assigned_to FROM tickets WHERE ticket_id = $ticketId AND u_id = $studentId";
        $res = $db->query($checkSql);
        if (!$res || $res->num_rows === 0) {
             echo json_encode(['error' => 'access_denied']);
             return;
        }
        $ticket = $res->fetch_assoc();
        $assignedTo = $ticket['assigned_to'];

        $chat = $chatModel->getChatByTicketId($ticketId);
        $chatId = 0;

        if (!$chat) {
            if (!$assignedTo) {
                 echo json_encode(['error' => 'no_agent_assigned']);
                 return;
            }
            $chatId = $chatModel->createChat($ticketId, $studentId, $assignedTo);
        } else {
            $chatId = $chat['chat_id'];
        }

        if ($chatId) {
            $success = $chatModel->sendMessage($chatId, $studentId, $message);
            if ($success) {
                // Trigger notification for the other party
                require_once __DIR__ . '/../../lib/NotificationHelper.php';
                NotificationHelper::notifyTicketMessage($ticketId, $studentId, $_SESSION['user']['name'] ?? 'Student');
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'send_failed']);
            }
        } else {
            echo json_encode(['error' => 'chat_creation_failed']);
        }
    }
}