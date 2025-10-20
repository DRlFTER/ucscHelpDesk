<?php

class Staff extends Controller {
    public function staffDashboard()
{
    $this->requireLogin('staff');

    require_once __DIR__ . '/../../models/staff/Ticket.php';
    require_once __DIR__ . '/../../models/staff/Announcement.php';
    $modelTicket = new StaffTicket();
    $modelAnn = new Announcement();

    // Fetch tickets for stats and list
    $tickets = [];
    try {
        $tickets = $modelTicket->getAllTickets();
    } catch (Throwable $e) {
        error_log('Failed to load tickets for dashboard: ' . $e->getMessage());
    }

    // Calculate stats
    $pending = array_filter($tickets, fn($t) => $t['status'] === 'pending');
    $assigned = array_filter($tickets, fn($t) => $t['status'] === 'agent assigned');
    $resolved = array_filter($tickets, fn($t) => in_array($t['status'], ['agent-closed', 'closed']));
    $total = count($tickets);

    // Recent tickets (last 5)
    $recentTickets = array_slice($tickets, 0, 5);

    // Recent announcements (last 5)
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

    /**
     * Show, update, or delete a single announcement (detail view)
     */
    public function anView($id = null)
    {
        $this->requireLogin('staff');
        $announcement_id = $id !== null ? (int)$id : (isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0));  // Fallback to POST['id'] from hidden field
        if (!$announcement_id) {
            header("Location: /404");  // Or adjust to your 404 route
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
                   $_SESSION['success'] = 'Announcement updated successfully!';  // Flash message
            header("Location: /staff/announcements");
                    exit;
                } else {
                    $errors[] = "Failed to update announcement.";
                }
            }
        }
        
        // Handle delete
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

        // Load staff ticket model and fetch tickets
        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $tickets = [];
        $errorMsg = null;
        try {
            $model = new StaffTicket();
            $tickets = $model->getAllTickets();  // No param—handles filtering internally
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
        // Keep backward-compatible wrapper that delegates to ticketView
        $this->ticketView($id);
    }

    /**
     * Show a single ticket details page.
     * Accepts an $id param or will check $_GET['ticket_id'].
     */
    public function ticketView($id = null)
    {
        $this->requireLogin('staff');

        // allow passing id via argument or GET (route helpers may pass arg)
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

        // Handle POST Actions
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
                            $ticket = $model->getTicketById($ticket_id);  // Refresh
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
                            $ticket = $model->getTicketById($ticket_id);  // Refresh
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
                        $ticket = $model->getTicketById($ticket_id);  // Refresh
                    } else {
                        $errors[] = "Failed to resolve ticket. Are you assigned?";
                    }
                    break;
            
                case 'reject':
                    $ok = $model->rejectTicket($ticket_id);
                    if ($ok) {
                        $success = 'Ticket closed!';
                        $ticket = $model->getTicketById($ticket_id);  // Refresh
                    } else {
                        $errors[] = "Failed to close ticket. Are you assigned?";
                    }
                    break;
            }
        }

        // Fetch staff members for forward dropdown
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

    /**
     * Render announcements list for staff.
     */
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

        // Capture DB error (if any) for debug display
        $dbError = method_exists($ann, 'getLastError') ? $ann->getLastError() : null;

        // Use the same stylesheet as staff tickets to reuse classes
        $headContent = "<link rel=\"stylesheet\" href=\"/css/staff/staffTickets.css\" />\n";
        // keep the small announcements tweaks after the main shelf
        $headContent .= "<link rel=\"stylesheet\" href=\"/css/staff/announcements.css\" />\n";
        $headContent .= "<script src=\"/js/staff/announcements.js\" defer></script>\n";

        $this->view('staff/staffAnnoucements', [
            'title' => 'Announcements',
            'head' => $headContent,
            'announcements' => $announcements,
            'dbError' => $dbError,
        ]);
    }

    /**
     * Backwards compatible alias for legacy URL /staff/staffAnnoucements
     */
    public function staffAnnoucements()
    {
        // Delegate to the correct method
        $this->announcements();
    }

    /**
     * Show form and handle creation of new announcement.
     */
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

        // Fetch divisions for form
        try {
            $divisions = $model->getStaffDivisions($staff_id);
        } catch (Throwable $e) {
            error_log('Failed to load divisions: ' . $e->getMessage());
            $divisions = [];
        }

        // Handle POST
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
    $field_count = isset($_POST['field_count']) ? (int)$_POST['field_count'] : 1;
    $post_data = $_POST ?? [];  // For repopulating form on error

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $process = trim($_POST['process'] ?? '');
        $outcome = trim($_POST['outcome'] ?? '');
        $letter_required = isset($_POST['letter_required']) ? 1 : 0;
        $fields = [];

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

        if (empty($category)) {
            $errors[] = "Category is required.";
        } elseif (strlen($category) > 50) {
            $errors[] = "Category must be 50 characters or less.";
        }

        if (empty($process)) {
            $errors[] = "Process is required.";
        } elseif (strlen($process) > 1000) {
            $errors[] = "Process must be 1000 characters or less.";
        }

        if (empty($outcome)) {
            $errors[] = "Outcome is required.";
        } elseif (strlen($outcome) > 1000) {
            $errors[] = "Outcome must be 1000 characters or less.";
        }

        if (empty($fields)) {
            $errors[] = "At least one field is required.";
        }

        if (empty($errors)) {
            try {
                $data = [
                    'name' => $name,
                    'category' => $category,
                    'fields' => $fields,
                    'process' => $process,
                    'outcome' => $outcome,
                    'letter_required' => $letter_required,
                    'created_by' => $staff_id
                ];
                $ok = $model->create($data);
                if ($ok) {
                    $success = "Template created successfully!";
                    $name = $category = $process = $outcome = '';
                    $fields = [];
                    $field_count = 1;
                    $post_data = [];  // Reset form
                } else {
                    $errors[] = "Failed to create template. Please try again.";
                }
            } catch (Throwable $e) {
                error_log('Failed to create template: ' . $e->getMessage());
                $errors[] = "Database error occurred. Please try again.";
            }
        }
    }

    $headContent = '<link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./global.css">
                    <style>
                        .main-content { padding: 20px; max-width: 1200px; margin: 0 auto; }
                        .page-header { text-align: center; margin-bottom: 20px; }
                        .page-title { font-size: 24px; color: #333; margin-bottom: 10px; }
                        .page-subtitle { font-size: 16px; color: #666; }
                        .ticket-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 20px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                        .ticket-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
                        .ticket-title-group { flex-grow: 1; }
                        .ticket-title { font-size: 18px; color: #333; margin: 0; }
                        .ticket-meta { font-size: 12px; color: #666; }
                        .ticket-body { padding: 10px 0; }
                        .details-group { display: flex; flex-direction: column; gap: 10px; }
                        .detail-item { display: flex; align-items: flex-start; }
                        .detail-label { font-weight: bold; color: #444; width: 120px; margin-top: 8px; }
                        .detail-value-box { flex-grow: 1; padding: 8px; border: 1px solid #e0e0e0; border-radius: 4px; background: #f9f9f9; }
                        .detail-value-box input, .detail-value-box textarea, .detail-value-box select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; box-sizing: border-box; background: transparent; border: none; }
                        .detail-value-box textarea { resize: vertical; min-height: 100px; }
                        .add-field-btn { background: #4a90e2; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 5px; }
                        .add-field-btn:hover { background: #357abd; }
                        .error { color: red; font-size: 12px; margin-top: 5px; display: block; }
                        .success { color: green; font-size: 14px; margin-bottom: 15px; text-align: center; }
                        .ticket-action { text-align: right; margin-top: 15px; }
                        .ticket-action-btn { background: #4a90e2; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; }
                        .ticket-action-btn:hover { background: #357abd; }
                    </style>';

    $this->view('staff/createTemplate', [
        'title' => 'UCSC Help Desk - Create Template',
        'head' => $headContent,
        'errors' => $errors,
        'success' => $success,
        'field_count' => $field_count,
        'post_data' => $post_data,
        'staff_id' => $staff_id,
    ]);
}
}
?>