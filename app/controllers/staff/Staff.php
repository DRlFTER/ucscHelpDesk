<?php

class Staff extends Controller {
    public function settings()
    {
            $this->requireLogin('staff');
            $headContent = '\n        <link rel="stylesheet" href="/css/settings/settings.css"/>';
            $this->view('settings', [
                    'title' => 'Settings',
                    'head' => $headContent,
                    'role' => 'staff',
                    'roleLabel' => 'Staff',
                    'roleMessage' => 'Staff settings: update your profile and work preferences (dummy content).',
            ]);
    }

  public function staffDashboard()
{
    $this->requireLogin('staff');

    require_once __DIR__ . '/../../models/staff/Ticket.php';
    require_once __DIR__ . '/../../models/staff/Announcement.php';
    $modelTicket = new StaffTicket();
    $modelAnn = new Announcement();

    $tickets = [];
    try {
        $tickets = $modelTicket->getAllTickets();
    } catch (Throwable $e) {
        error_log('Failed to load tickets for dashboard: ' . $e->getMessage());
    }

    $pending = array_filter($tickets, fn($t) => $t['status'] === 'pending');
    $assigned = array_filter($tickets, fn($t) => $t['status'] === 'agent assigned');
    $resolved = array_filter($tickets, fn($t) => in_array($t['status'], ['agent-closed', 'closed', 'resolved'])); // Fixed: Added 'resolved'
    $total = count($tickets);

    $recentTickets = array_slice($tickets, 0, 8);

    $announcements = [];
    try {
        $allAnn = $modelAnn->getAll();
        $announcements = array_slice($allAnn, 0, 5);
    } catch (Throwable $e) {
        error_log('Failed to load announcements for dashboard: ' . $e->getMessage());
    }

    $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css"/>';
    $this->view('staff/staffDashboard', [
        'title' => 'Staff Dashboard',
        'head' => $headContent,
        'stats' => [
            'pending' => count($pending),
            'assigned' => count($assigned),
            'resolved' => count($resolved), 
            'total' => $total
        ],
        'recentTickets' => $recentTickets,
        'announcements' => $announcements,
    ]);
}

    public function anView($id = null)
    {
        $this->requireLogin('staff');
        $announcement_id = $id !== null ? (int)$id : (isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0));  // Fallback to POST['id'] from hidden field
        if (!$announcement_id) {
            header("Location: /404");
            exit;
        }
        require_once __DIR__ . '/../../models/staff/Announcement.php';
        $model = new Announcement();
        $errors = [];
        
        // Handle update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
            $new_topic = trim($_POST['topic'] ?? '');
            $new_content = trim($_POST['content'] ?? '');
            if (empty($new_topic)) {
                $errors[] = "Topic is required.";
            } elseif (strlen($new_topic) > 50) {
                $errors[] = "Topic must be 50 characters or less.";
            }
            if (empty($new_content)) {
                $errors[] = "Content is required.";
            } elseif (strlen($new_content) > 500) {
                $errors[] = "Content must be 500 characters or less.";
            }
            if (empty($errors)) {
                $ok = $model->update($announcement_id, $new_topic, $new_content);
                if ($ok) {
                   $_SESSION['success'] = 'Announcement updated successfully!'; 
            header("Location: /staff/announcements");
                    exit;
                } else {
                    $errors[] = "Failed to update announcement.";
                }
            }
        }
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ticket'])) {
            $ok = $model->delete($announcement_id);
            if ($ok) {
                header("Location: /staff/announcements");
                exit;
            } else {
                $errors[] = "Failed to delete announcement.";
            }
        }
        
        try {
            $announcement = $model->getById($announcement_id);
        } catch (Throwable $e) {
            error_log('Failed to load announcement: ' . $e->getMessage());
            $announcement = null;
        }
        if (!$announcement) {
            header("Location: /404");
            exit;
        }
        
        try {
            $files = $model->getFiles($announcement_id);
        } catch (Throwable $e) {
            error_log('Failed to load files: ' . $e->getMessage());
            $files = [];
        }
        
        $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css" />' . "\n" .
                       '<link rel="stylesheet" href="/css/staff/an_view.css" />';
        
        $this->view('staff/anView', [
            'title' => 'Announcement Details',
            'head' => $headContent,
            'announcement' => $announcement,
            'files' => $files,
            'errors' => $errors,
        ]);
    }

    public function staffTickets()
    {
        $this->requireLogin('staff');

        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $tickets = [];
        $errorMsg = null;
        try {
            $model = new StaffTicket();
            $tickets = $model->getAllTickets(); 
        } catch (Throwable $e) {
            $tickets = [];
            $errorMsg = $e->getMessage();
        }

        $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css"/>';
        $this->view('staff/staffTickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'tickets' => $tickets,
            'error' => $errorMsg,
        ]);
    }

    public function ticketDetails($id = null)
    {
        $this->ticketView($id);
    }

    public function ticketView($id = null)
    {
        $this->requireLogin('staff');

        $ticket_id = null;
        if ($id !== null) {
            $ticket_id = (int)$id;
        } elseif (isset($_GET['ticket_id'])) {
            $ticket_id = intval($_GET['ticket_id']);
        }

        if (!$ticket_id) {
            header("Location: /404");
            exit;
        }

        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $model = new StaffTicket();
        try {
            $ticket = $model->getTicketById($ticket_id);
        } catch (Throwable $e) {
            error_log('Failed to load ticket: ' . $e->getMessage());
            $ticket = null;
        }

        if (!$ticket) {
            header("Location: /404");
            exit;
        }

        $errors = [];
        $success = '';
        $current_staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'assign':
                    if ($ticket['status'] === 'pending') {
                        $ok = $model->assignToStaff($ticket_id, $current_staff_id);
                        if ($ok) {
                            $success = 'Ticket assigned to you!';
                            $ticket = $model->getTicketById($ticket_id); 
                        } else {
                            $errors[] = "Failed to assign ticket.";
                        }
                    } else {
                        $errors[] = "Ticket is not pending.";
                    }
                    break;
            
                case 'respond':
                    $response_text = trim($_POST['response'] ?? '');
                    if (!empty($response_text)) {
                        $ok = $model->addResponse($ticket_id, $current_staff_id, $response_text);
                        if ($ok) {
                            $success = 'Response added successfully!';
                        } else {
                            $errors[] = "Failed to add response.";
                        }
                    } else {
                        $errors[] = "Response cannot be empty.";
                    }
                    break;
            
                case 'forward':
                    $forward_to = (int)($_POST['forward_to'] ?? 0);
                    if ($forward_to > 0 && $forward_to != $current_staff_id) {
                        $ok = $model->forwardTicket($ticket_id, $forward_to);
                        if ($ok) {
                            $success = 'Ticket forwarded successfully!';
                            $ticket = $model->getTicketById($ticket_id);
                        } else {
                            $errors[] = "Failed to forward ticket.";
                        }
                    } else {
                        $errors[] = "Select a different staff member.";
                    }
                    break;
            
                case 'resolve':
                    $ok = $model->resolveTicket($ticket_id);
                    if ($ok) {
                        $success = 'Ticket resolved!';
                        $ticket = $model->getTicketById($ticket_id);
                    } else {
                        $errors[] = "Failed to resolve ticket. Are you assigned?";
                    }
                    break;
            
                case 'reject':
                    $ok = $model->rejectTicket($ticket_id);
                    if ($ok) {
                        $success = 'Ticket closed!';
                        $ticket = $model->getTicketById($ticket_id);
                    } else {
                        $errors[] = "Failed to close ticket. Are you assigned?";
                    }
                    break;
            }
        }

        $staff_members = $model->getStaffMembers();

        $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css" />' . "\n" .
                       '<link rel="stylesheet" href="/css/staff/global.css" />'."\n".
                       '<link rel="stylesheet" href="/css/global/components.css" />';

        $this->view('staff/ticketDetails', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'ticket' => $ticket,
            'staff_members' => $staff_members,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function announcements()
    {
        $this->requireLogin('staff');

        require_once __DIR__ . '/../../models/staff/Announcement.php';
        $ann = new Announcement();
        $announcements = [];
        try {
            $announcements = $ann->getAll();
        } catch (Throwable $e) {
            error_log('Announcement load failed: ' . $e->getMessage());
            $announcements = [];
        }

        $dbError = method_exists($ann, 'getLastError') ? $ann->getLastError() : null;

        $headContent = "<link rel=\"stylesheet\" href=\"/css/staff/staffTickets.css\" />\n";
        $headContent .= "<link rel=\"stylesheet\" href=\"/css/staff/announcements.css\" />\n";
        $headContent .= "<script src=\"/js/staff/announcements.js\" defer></script>\n";

        $this->view('staff/staffAnnoucements', [
            'title' => 'Announcements',
            'head' => $headContent,
            'announcements' => $announcements,
            'dbError' => $dbError,
        ]);
    }

    public function staffAnnoucements()
    {
        $this->announcements();
    }

    public function staffAnnCreate()
    {
        $this->requireLogin('staff');
        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
        if (!$staff_id) {
            header("Location: /staff/announcements");
            exit;
        }

        require_once __DIR__ . '/../../models/staff/Announcement.php';
        $model = new Announcement();
        $errors = [];
        $success = '';


        try {
            $divisions = $model->getStaffDivisions($staff_id);
        } catch (Throwable $e) {
            error_log('Failed to load divisions: ' . $e->getMessage());
            $divisions = [];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = trim($_POST['topic'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $division_id = (int)($_POST['division'] ?? 0);

            // Validate inputs
            if (empty($topic)) {
                $errors[] = "Topic is required.";
            } elseif (strlen($topic) > 50) {
                $errors[] = "Topic must be 50 characters or less.";
            }

            if (empty($content)) {
                $errors[] = "Content is required.";
            } elseif (strlen($content) > 500) {
                $errors[] = "Content must be 500 characters or less.";
            }

            if ($division_id <= 0) {
                $errors[] = "Please select a valid division.";
            }

            // Basic file validation (full in model)
            $file = $_FILES['file'] ?? null;
            if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE && $file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "File upload failed: " . $file['error'];
            } elseif ($file && $file['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $max_size = 5 * 1024 * 1024;
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.";
                } elseif ($file['size'] > $max_size) {
                    $errors[] = "File size exceeds 5MB limit.";
                }
            }

            if (empty($errors)) {
                $data = [
                    'staff_id' => $staff_id,
                    'topic' => $topic,
                    'content' => $content,
                    'division_id' => $division_id
                ];
                $ok = $model->create($data, $file);
                if ($ok) {
                    $_SESSION['success'] = 'Announcement created successfully!';
                    header("Location: /staff/announcements");
                    exit;
                } else {
                    $errors[] = "Failed to create announcement. Please try again.";
                }
            }

            // Repopulate on error
            $divisions = $model->getStaffDivisions($staff_id);
        }

        $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css" />' . "\n" .
                       '<link rel="stylesheet" href="/css/staff/anncreate.css" />';

        $this->view('staff/staffAnnCreate', [
            'title' => 'Create Announcement',
            'head' => $headContent,
            'divisions' => $divisions,
            'staff_id' => $staff_id,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    
// Add this method to your existing Staff.php controller class

/**
 * Show form and handle creation of new template.
 */
// In Staff.php, replace the entire createTemplate() method with this corrected version:

public function createTemplate()
{
    $this->requireLogin('staff');
    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
    if (!$staff_id) {
        header("Location: /staff/dashboard");  // Redirect if no valid staff ID
        exit;
    }

    require_once __DIR__ . '/../../models/staff/Template.php';
    $model = new Template();
    $errors = [];
    $success = "";
    
    try {
        $divisions = $model->getStaffDivisions($staff_id);
    } catch (Throwable $e) {
        error_log('Failed to load divisions: ' . $e->getMessage());
        $divisions = [];
    }

    $field_count = 1;  // Default
    $post_data = [];   // For repopulating form on error

  // In Staff.php, replace the POST handling block in createTemplate() with this:

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $selected_did = (int)($_POST['category'] ?? 0);  // Get selected did from form
    $letter_required = isset($_POST['letter_required']) ? 1 : 0;
    $fields = [];
    $field_count = (int)($_POST['field_count'] ?? 1);  // Use submitted count

    // Lookup category name from selected did
    $category_name = '';
    foreach ($divisions as $division) {
        if ((int)$division['did'] === $selected_did) {
            $category_name = $division['name'];
            break;
        }
    }

    // Collect dynamic fields
    for ($i = 1; $i <= $field_count; $i++) {
        $field_name = trim($_POST['field_' . $i] ?? '');
        if (!empty($field_name)) {
            $fields[] = $field_name;
        }
    }

    // Validate
    if (empty($name)) {
        $errors[] = "Template name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Template name must be 100 characters or less.";
    }

    if (empty($category_name)) {
        $errors[] = "Please select a valid category.";
    }

    if (empty($fields)) {
        $errors[] = "At least one field is required.";
    }

    if (empty($errors)) {
        try {
            $data = [
                'name' => $name,
                'category' => $category_name,  // Now the string name
                'fields' => $fields,
                'letter_required' => $letter_required,
                'created_by' => $staff_id,
                'division' => $selected_did
            ];
            $ok = $model->create($data);
            if ($ok) {
                $_SESSION['success'] = "Template created successfully!";
                header("Location: /staff/createTemplate");  // Redirect to self
                exit;
            } else {
                $errors[] = "Failed to create template. Please try again.";
            }
        } catch (Throwable $e) {
            error_log('Failed to create template: ' . $e->getMessage());
            $errors[] = "Database error occurred. Please try again.";
        }
    }

    // Repopulate on error
    $post_data = $_POST;
}

    $headContent = '<link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./global.css">
                    <style>
                        /* Your existing inline styles here - no changes needed */
                    </style>';

    $this->view('staff/createTemplate', [
        'title' => 'UCSC Help Desk - Create Template',
        'head' => $headContent,
        'divisions' => $divisions,
        'staff_id' => $staff_id,
        'errors' => $errors,
        'success' => $success,
        'division' => $selected_did ?? 0,
        'field_count' => $field_count,
        'post_data' => $post_data,
    ]);
}

 public function staffFAQ()
    {
        $this->requireLogin('staff');
        $headContent = '<link rel="stylesheet" href="/css/staff/staffFAQ.css" />';
        $this->view('staff/staffFAQ', [
            'title' => 'Staff FAQs',
            'head' => $headContent,
        ]);
    }

    public function createFAQ() {
    $this->requireLogin('staff');
    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
    $errors = [];
    $success = '';
    $post_data = $_POST ?? [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if (empty($question)) $errors[] = "Question is required.";
        if (strlen($question) > 500) $errors[] = "Question must be 500 characters or less.";
        if (empty($answer)) $errors[] = "Answer is required.";
        if (strlen($answer) > 2000) $errors[] = "Answer must be 2000 characters or less.";

        if (empty($errors)) {
            // Save to DB via model (implement FAQ::create())
            require_once __DIR__ . '/../../models/staff/FAQ.php';
            $model = new FAQ();
            $ok = $model->create([
                'question' => $question,
                'answer' => $answer,
                'category' => $category,
                'created_by' => $staff_id
            ]);
            if ($ok) {
                $_SESSION['faq_success'] = 'FAQ created successfully!';
                header("Location: /staff/staffFAQ");
                exit;
            } else {
                $errors[] = "Failed to create FAQ. Please try again.";
            }
        }
    }

    $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css?v=' . time() . '"/>'. "\n" .
                       '<link rel="stylesheet" href="/css/staff/anncreate.css" />';

    $this->view('staff/createFAQ', [
        'title' => 'Create FAQ',
        'head' => $headContent,
        'errors' => $errors,
        'success' => $success,
        'post_data' => $post_data,
        'staff_id' => $staff_id,
    ]);
}
    public function staffForum()
    {
        $this->requireLogin('staff');
        $headContent = '<link rel="stylesheet" href="/css/student/studentForum.css" />';
        $this->view('staff/staffForum', [
            'title' => 'Forum',
            'head' => $headContent,
        ]);
    }

    public function staffForumFull()
    {
        $this->requireLogin('staff');
        $headContent = '<link rel="stylesheet" href="/css/student/studentForumFull.css" />';
        $this->view('staff/staffForumFull', [
            'title' => 'Forum Post',
            'head' => $headContent,
        ]);
    }

    // Create new forum post
    public function staffNewForum()
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
    public function staffForumData()
    {
        $this->requireLogin('staff');
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
    public function staffForumToggleVisibility()
    {
        $this->requireLogin('staff');
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
    public function staffForumToggleStatus()
    {
        $this->requireLogin('staff');
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
    public function staffForumPostData()
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

   public function calender() {
        $this->requireLogin('staff');
        $headContent = '\n        <link rel="stylesheet" href="/css/calender/calender.css"/>';
        $this->view('calender', [
            'title' => 'Calendar',
            'head' => $headContent,
            'role' => 'staff',
            'roleLabel' => 'Staff',
            'roleMessage' => 'Staff calendar: plan your tasks and events (dummy content).',
        ]);
    }

   public function staffCalender() {
        // Backward-compatible route: delegate to unified method
        $this->calender();
    }

     public function staffKB()
    {
        $this->requireLogin('staff');
        $headContent = '<link rel="stylesheet" href="/css/staff/staffKB.css" />';
        $this->view('staff/staffKB', [
            'title' => 'Knowledge Base',
            'head' => $headContent,
        ]);
    }
    public function createKB() {
    $this->requireLogin('staff');
    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
    $errors = [];
    $success = '';
    $post_data = $_POST ?? [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $file = $_FILES['resource_file'] ?? null;

        if (empty($title)) $errors[] = "Title is required.";
        if (strlen($title) > 200) $errors[] = "Title must be 200 characters or less.";
        if (empty($description)) $errors[] = "Description is required.";
        if (strlen($description) > 5000) $errors[] = "Description must be 5000 characters or less.";

        // File validation (optional, but if uploaded)
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['.pdf', '.doc', '.docx', '.jpg', '.png', '.txt'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array('.' . $ext, $allowed)) $errors[] = "Invalid file type.";
            if ($file['size'] > 10 * 1024 * 1024) $errors[] = "File too large (max 10MB).";
        }

        if (empty($errors)) {
            // Save via model (implement KB::create() in models/staff/KB.php)
            require_once __DIR__ . '/../../models/staff/KB.php';
            $model = new KB();
            $ok = $model->create([
                'title' => $title,
                'category' => $category,
                'description' => $description,
                'tags' => $tags,
                'file_path' => $file ? $file['tmp_name'] : null, // Handle upload in model
                'created_by' => $staff_id
            ]);
            if ($ok) {
                $_SESSION['kb_success'] = 'Resource added successfully!';
                header("Location: /staff/staffKB");
                exit;
            } else {
                $errors[] = "Failed to add resource. Please try again.";
            }
        }
    }

    $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css?v=' . time() . '"/>';
                     

    $this->view('staff/createKB', [
        'title' => 'Add Knowledge Base Resource',
        'head' => $headContent,
        'errors' => $errors,
        'success' => $success,
        'post_data' => $post_data,
        'staff_id' => $staff_id,
    ]);
}

}