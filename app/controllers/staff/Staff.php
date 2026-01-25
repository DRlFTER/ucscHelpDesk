<?php

class Staff extends Controller {

private $faqModel;

// In the __construct() method (add if not present, or update existing):
public function __construct()
{
    require_once __DIR__ . '/../../models/staff/Faq.php';
    // ... existing constructor code if any ...
    $this->faqModel = new StaffFaqModel();
}
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
    unset($_SESSION['overdues_checked']);
    $this->requireLogin('staff');

    require_once __DIR__ . '/../../models/staff/Ticket.php';
    $tickets = [];
    $errorMsg = null;
    $staff_level = null;  // Initialize
    try {
        $model = new StaffTicket();
        $tickets = $model->getAllTickets(); 
        $staff_level = $model->getStaffLevel((int)$_SESSION['user']['u_id']);  // FIXED: Correct method name, cast int
    } catch (Throwable $e) {
        $tickets = [];
        $errorMsg = $e->getMessage();
        error_log('StaffTickets error: ' . $e->getMessage());  // Log for debug
    }

    $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css"/>';
    $this->view('staff/staffTickets', [
        'title' => 'Tickets',
        'head' => $headContent,
        'tickets' => $tickets,
        'error' => $errorMsg,
        'staff_level' => $staff_level ?? 0,  // Default to 0 if null (or 'staff' string if preferred)
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
                        $ok2 = $model->setTicketupdateTimeline($ticket_id);
                        if ($ok && $ok2) {
                            $success = 'Ticket assigned to you!';
                            $ticket = $model->getTicketById($ticket_id);
                            $set_level = $model->setTicketLevel($ticket_id, $current_staff_id, $model->getStaffLevel($current_staff_id));
                            error_log('Ticket level set for ticket ID ' . $ticket_id . ' to staff ID ' . $current_staff_id."ticket level: ".$model->getStaffLevel($current_staff_id));

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
                        $ok2 = $model->setTimeLineReview($ticket_id);
                        if ($ok && $ok2) {
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
                            $get_level = $model->getStaffLevel($forward_to);
                            $set_level = $model->setTicketLevel($ticket_id,$forward_to,$get_level);
                      //      echo 'Ticket level set for ticket ID ' . $ticket_id . ' to staff ID ' . $forward_to.' ticket level: '.$get_level;
                        } else {
                            $errors[] = "Failed to forward ticket.";
                        }
                    } else {
                        $errors[] = "Select a different staff member.";
                    }
                    break;
            
                case 'resolve':
                    $ok = $model->resolveTicket($ticket_id);
                    $ok2 = $model->resolveTicketTimeLine($ticket_id);
                    if ($ok && $ok2) {
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
    
public function staffTemplate()
{
    $this->requireLogin('staff');

    $templates = [];
    $errors = [];
    $success = '';

    require_once __DIR__ . '/../../models/staff/Template.php';
    require_once __DIR__ . '/../../models/student/Ticket.php';
    $tplModel = new Template();
    $ticketModel = new StudentTicket();

    // Load templates (always, for display after actions)
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u_id = (int)($_SESSION['user']['u_id'] ?? 0);

        // Handle DELETE
        if (isset($_POST['delete_ticket'])) {
            $template_id = (int)$_POST['template_id'];
            if ($template_id > 0) {
                try {
                    $ok = $tplModel->delete($template_id);
                    if ($ok) {
                        $success = 'Template deleted successfully!';
                        // Reload templates after delete to reflect changes
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
                        } catch (Throwable $reloadE) {
                            error_log('Failed to reload templates after delete: ' . $reloadE->getMessage());
                        }
                    } else {
                        $errors[] = 'Failed to delete template.';
                    }
                } catch (Throwable $e) {
                    error_log('Delete template failed: ' . $e->getMessage());
                    $errors[] = 'Delete failed: ' . $e->getMessage();
                }
            } else {
                $errors[] = 'Invalid template ID.';
            }
        }
        // Handle CREATE TICKET FROM TEMPLATE
        elseif (isset($_POST['template_id'])) {
            $template_id = (int)$_POST['template_id'];

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
    }

    $headContent = '<link rel="stylesheet" href="/css/student/studentTemplate.view.css" />';

    $this->view('staffTemplate', [
        'title' => 'Use Template',
        'head' => $headContent,
        'templates' => $templates,
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

// Replace the existing staffFAQ() method with this new faqs() method for management.
// If you want a separate view-only page, keep staffFAQ() as-is and use faqs() for management.
// I've assumed /staff/faqs routes to this for management (e.g., via routes.php).

/**
 * Staff FAQ Management Page (CRUD for FAQs)
 */
public function faqs()
{
    $this->requireLogin('staff');
    $headContent = '
        <link rel="stylesheet" href="/css/staff/staffFaqs.css"/>';
    $this->view('staff/staffFaqs', ['title' => 'Manage FAQs', 'head' => $headContent]);
}

/**
 * Return FAQs as JSON for the Staff FAQs page.
 * { data: [ { id, question, answer, createdAt } ], meta: { page, perPage, total, totalPages } }
 */
public function faqsData()
{
    $this->requireLogin('staff');
    header('Content-Type: application/json');
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['perPage']) ? max(1, min(100, (int)$_GET['perPage'])) : 10;
    $search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
    $total = $this->faqModel->getFaqCount($search);
    $totalPages = $perPage > 0 ? (int)max(1, ceil($total / $perPage)) : 1;
    if ($page > $totalPages) { $page = $totalPages; }
    $offset = ($page - 1) * $perPage;
    $rows = $this->faqModel->getFaqs($search, $perPage, $offset);
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

/**
 * Create a new FAQ (JSON response)
 */
public function faqCreate()
{
    $this->requireLogin('staff');
    header('Content-Type: application/json');
    $question = isset($_POST['question']) ? trim((string)$_POST['question']) : '';
    $answer = isset($_POST['answer']) ? trim((string)$_POST['answer']) : '';
    if ($question === '' || $answer === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Question and answer are required']);
        exit;
    }
    try {
        $id = $this->faqModel->createFaq($question, $answer);
        $row = $this->faqModel->getFaqById($id);
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

/**
 * Update an existing FAQ (JSON response)
 */
public function faqUpdate()
{
    $this->requireLogin('staff');
    header('Content-Type: application/json');
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $question = isset($_POST['question']) ? trim((string)$_POST['question']) : '';
    $answer = isset($_POST['answer']) ? trim((string)$_POST['answer']) : '';
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
        $ok = $this->faqModel->updateFaq($id, $question, $answer);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
            exit;
        }
        $row = $this->faqModel->getFaqById($id);
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

/**
 * Delete a FAQ (JSON response)
 */
public function faqDelete()
{
    $this->requireLogin('staff');
    header('Content-Type: application/json');
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        exit;
    }
    try {
        $ok = $this->faqModel->deleteFaq($id);
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

// Optional: If you want to keep a simple view-only staffFAQ(), update it to fetch data via a new read-only method.
// But for now, assuming faqs() handles management; add this if needed for viewing only.
public function staffFAQ()
{
    $this->requireLogin('staff');
    // Optionally load read-only data here if separate from management.
    $headContent = '<link rel="stylesheet" href="/css/staff/staffFaqs.css" />';
    $this->view('staff/staffFAQ', [
        'title' => 'Staff FAQs',
        'head' => $headContent,
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

    require_once __DIR__ . '/../../models/staff/KB.php';
    $kbModel = new KB();
    $kb_data = [];
    try {
        $flat_articles = $kbModel->getAllArticles();
    
        $grouped = [];   
        foreach($flat_articles as $article){
            $section = $article['section'] ?? 'Uncategorized';
            if(!isset($grouped[$section])){
                $grouped[$section] = [
                    'section' => $section,
                    'items' => []
                ];
            }

            $updated_pretty = date('F Y', strtotime($article['updated']));
            $item = [
                'id' => $article['base_id'],
                'title' => $article['topic'],
                'description' => $article['description'],
                'updated' => $updated_pretty,
                'type'=> $article['type'],
                'files' => $kbModel->getFilesByArticle($article['base_id']) // NEW: Load files for each article
            ];
            $grouped[$section]['items'][] = $item;
        }
        $kb_data = array_values($grouped);
    } catch (Throwable $e) {
            error_log('KB load failed: ' . $e->getMessage());
            $kb_data = [];
        }

        $headContent = '<link rel="stylesheet" href="/css/staff/staffKB.css" />';
        $this->view('staff/staffKB', [
            'title' => 'Knowledge Base',
            'head' => $headContent,
            'kb_data' => $kb_data,
        ]);
}

    public function createKB() {
    $this->requireLogin('staff');
    $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
    $errors = [];
    $success = '';
    $post_data = $_POST ?? [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $topic = trim($_POST['topic'] ?? '');
        $section = trim($_POST['section'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $file = $_FILES['resource_file'] ?? null;

        if (empty($topic)) $errors[] = "Topic is required.";
        if (strlen($topic) > 200) $errors[] = "Topic must be 200 characters or less.";
        if (empty($description)) $errors[] = "Description is required.";
        if (strlen($description) > 5000) $errors[] = "Description must be 5000 characters or less.";

        // File validation (optional, but if uploaded)
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            $max_size = 10 * 1024 * 1024; // 10MB
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX, TXT.";
            } elseif ($file['size'] > $max_size) {
                $errors[] = "File too large (max 10MB).";
            }
        }

        if (empty($errors)) {
            // FIXED: Pass $data and $file separately to model
            $data = [
                'staff_id' => $staff_id,
                'topic' => $topic,
                'section' => $section,
                'description' => $description,
                'type' => $type
            ];
            require_once __DIR__ . '/../../models/staff/KB.php';
            $model = new KB();
            $ok = $model->create($data, $file);
            if ($ok) {
                $_SESSION['kb_success'] = 'Resource added successfully!';
                header("Location: /staff/staffKB");
                exit;
            } else {
                $errors[] = "Failed to add resource. Please try again.";
            }
        }
    }

            

    $this->view('staff/createKB', [
        'title' => 'Add Knowledge Base Resource',
        'errors' => $errors,
        'success' => $success,
        'post_data' => $post_data,
        'staff_id' => $staff_id,
    ]);
}
public function updateKB($id = null) {
    $this->requireLogin('staff');
    require_once __DIR__ . '/../../models/staff/KB.php';
    $kbModel = new KB();
    $errors = [];
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle Delete
        if (isset($_POST['delete_ticket'])) {
            if ($kbModel->deleteArticle($id)) {
                $_SESSION['flash_success'] = 'Resource deleted successfully.';  // Flash message
                header('Location: /staff/staffKB');  // Redirect to list
                exit;
            } else {
                $errors[] = 'Delete failed (check DB permissions).';
            }
        } 
        // Handle Update
        else if (isset($_POST['title'])) {
            // Validate
            if (empty($_POST['title']) || empty($_POST['section']) || empty($_POST['type'])) {
                $errors[] = 'All fields required.';
            } else {
                $updateData = [
                    'topic' => trim($_POST['title']),
                    'description' => trim($_POST['description']),
                    'section' => $_POST['section'],
                    'type' => $_POST['type']
                ];
                if ($kbModel->updateArticle($id, $updateData)) {
                    $success = 'Resource updated successfully.';
                    
                    // Handle file upload if present
                    $file = $_FILES['resource_file'] ?? null;
                    if ($file && $file['error'] === UPLOAD_ERR_OK) {
                        // Delete old files (if any)
                        $oldFiles = $kbModel->getFilesByArticle($id);
                        foreach ($oldFiles as $oldFile) {
                            $fullOldPath = __DIR__ . '/../../../public/' . $oldFile['file_path'];
                            if (file_exists($fullOldPath)) {
                                unlink($fullOldPath);
                            }
                        }
                        // FIXED: Clear old DB entries (consistent table/column)
                        $db = Database::getInstance();
                        $stmt = $db->prepare("DELETE FROM kb_files WHERE kb_id = ?");
                        if ($stmt) {
                            $stmt->bind_param('i', $id);
                            $stmt->execute();
                            $stmt->close();
                        }
                        
                        // Add new file (NEW: Use dedicated method)
                        $staff_id = (int)($_SESSION['user']['u_id'] ?? 0);
                        if ($kbModel->addFile($id, $file, $staff_id)) {  // Pass staff_id
                            $success .= ' File added successfully.';
                        } else {
                            $errors[] = 'Article updated, but file upload failed.';
                        }
                    }
                    
                    // Redirect back to KB list
                    header('Location: /staff/staffKB');
                    exit;
                } else {
                    $errors[] = 'Update failed.';
                }
            }
        }
        // Repopulate form on error
        $post_data = $_POST;
    } else {
        // GET: Fetch existing data
        $article = $kbModel->getArticleById($id);
        if (!$article) {
            $errors[] = 'Resource not found.';
            $this->view('staff/staffKB', ['title' => 'KB Not Found', 'errors' => $errors]);  // Or 404
            return;
        }
        $post_data = [
            'title' => $article['topic'],
            'description' => $article['description'],
            'section' => $article['section'],
            'type' => $article['type']
        ];
    }

    $this->view('staff/updateKB', [
        'title' => 'Update KB Resource',
        'post_data' => $post_data ?? [],
        'errors' => $errors,
        'success' => $success,
        'staff_id' => $_SESSION['user_id'] ?? '',  // For meta
        'kb_id' => $id  // Pass ID for form
    ]);
}

/**
 * Download a specific file by file_id (matches JS href)
 * FIXED: Fetch by file_id, not article ID; direct serve
 */
public function downloadKB($file_id = null) {
    $this->requireLogin('staff');
    if (!$file_id || !is_numeric($file_id)) {
        http_response_code(400);
        echo 'Invalid file ID';
        exit;
    }
    require_once __DIR__ . '/../../models/staff/KB.php';
    $kbModel = new KB();
    $db = Database::getInstance();
    
    // FIXED: Query single file by file_id
    $sql = "SELECT file_name, file_path, file_type, file_size FROM kb_files WHERE kb_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error';
        exit;
    }
    $stmt->bind_param('i', $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();
    $stmt->close();
    
    if (!$file) {
        http_response_code(404);
        echo 'File not found';
        exit;
    }
    
    // Build server path from web-relative
    $webPath = $file['file_path'] ?? '';
    $serverPath = realpath(__DIR__ . '/../../../public/' . ltrim($webPath, '/'));
    $uploadRoot = realpath(__DIR__ . '/../../../public/uploads/kb');
    if ($serverPath === false || !$uploadRoot || strpos($serverPath, $uploadRoot) !== 0 || !is_file($serverPath) || !is_readable($serverPath)) {
        http_response_code(404);
        echo 'File not accessible';
        exit;
    }
    
    // Clear output buffer for clean headers
    if (ob_get_level()) ob_end_clean();
    
    $fileSize = $file['file_size'] ?? filesize($serverPath);
    $mime = $file['file_type'] ?? 'application/octet-stream';
    if (function_exists('mime_content_type')) {
        $detectedMime = mime_content_type($serverPath);
        if ($detectedMime) $mime = $detectedMime;
    }
    
    // Force download headers
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . $fileSize);
    readfile($serverPath);
    exit;
}

    public function chatMessages()
    {
        $this->requireLogin('staff');
        header('Content-Type: application/json');

        $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
        if ($ticketId <= 0) {
            echo json_encode(['error' => 'missing ticket_id']);
            return;
        }

        require_once __DIR__ . '/../../models/TicketChat.php';
        $chatModel = new TicketChat();
        
        $chat = $chatModel->getChatByTicketId($ticketId);
        $messages = [];
        
        if ($chat) {
            $messages = $chatModel->getMessages($chat['chat_id']);
            // Mark messages as read
            $staffId = (int)($_SESSION['user']['u_id'] ?? 0);
            $chatModel->markMessagesAsRead($chat['chat_id'], $staffId);
        }

        echo json_encode(['messages' => $messages]);
    }

    public function sendMessage()
    {
        $this->requireLogin('staff');
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
        
        $staffId = (int)($_SESSION['user']['u_id'] ?? 0);
        
        $chat = $chatModel->getChatByTicketId($ticketId);
        $chatId = 0;

        if (!$chat) {
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
                echo json_encode(['error' => 'send_failed']);
            }
        } else {
            echo json_encode(['error' => 'chat_creation_failed']);
        }
    }


        /**
     * Generate Reports Page
     */
public function staffReports()
{
    $this->requireLogin('staff');

    require_once __DIR__ . '/../../models/staff/Reports.php';
    $model = new StaffReport();
    $report_type = $_POST['report_type'] ?? ($_GET['type'] ?? 'all_tickets');
    $start_date = $_POST['start_date'] ?? ($_GET['start'] ?? '');
    $end_date = $_POST['end_date'] ?? ($_GET['end'] ?? '');
    $status = $_POST['status'] ?? ($_GET['status'] ?? '');
    $priority = $_POST['priority'] ?? ($_GET['priority'] ?? '');
    $division_id = $_POST['division_id'] ?? ($_GET['division_id'] ?? '');
    $level = (int)($_POST['level'] ?? ($_GET['level'] ?? 0));

    $reports = [];
    $summary = [];
    $error = null;

    try {
        switch ($report_type) {
            case 'all_tickets':
                $reports = $model->getAllTicketsReport($start_date, $end_date, $status, $priority);
                $summary = $model->getAllTicketsSummary($start_date, $end_date, $status, $priority);
                break;
            case 'overdue_tickets':
                $reports = $model->getOverdueTicketsReport($division_id);
                $summary = $model->getOverdueTicketsSummary($division_id);
                break;
            case 'staff_assignment':
                $reports = $model->getStaffAssignmentReport($start_date, $end_date);
                $summary = $model->getStaffAssignmentSummary($start_date, $end_date);
                break;
            case 'escalation':
                $reports = $model->getEscalationReport($start_date, $end_date, $level);
                $summary = $model->getEscalationSummary($start_date, $end_date, $level);
                break;
            default:
                $reports = $model->getAllTicketsReport();
                $summary = $model->getAllTicketsSummary();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    $divisions = $model->getDivisions();

    // Optional CSV Export (pure PHP, no libs)
    if (isset($_GET['csv'])) {
        $title = ucwords(str_replace('_', ' ', $report_type)) . '_Report_' . date('Y-m-d');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $title . '.csv"');
        $output = fopen('php://output', 'w');

        // Summary rows
        if ($report_type === 'all_tickets' && !empty($summary)) {
            fputcsv($output, ['SUMMARY STATS']);
            fputcsv($output, ['Total Tickets', $summary['total_tickets'] ?? 0]);
            fputcsv($output, ['Pending (%)', ($summary['pending_pct'] ?? 0) . '%']);
            fputcsv($output, ['Resolved (%)', ($summary['resolved_pct'] ?? 0) . '%']);
            fputcsv($output, []);  // Blank
        } elseif ($report_type === 'overdue_tickets' && !empty($summary)) {
            fputcsv($output, ['SUMMARY STATS']);
            fputcsv($output, ['Total Overdue', $summary['total_overdue'] ?? 0]);
            fputcsv($output, ['Avg Days Overdue', ($summary['avg_days_overdue'] ?? 0) . ' days']);
            fputcsv($output, []);  // Blank
        } elseif ($report_type === 'staff_assignment' && !empty($summary)) {  // NEW: Added for staff
            fputcsv($output, ['SUMMARY STATS']);
            fputcsv($output, ['Total Staff', $summary['total_staff'] ?? 0]);
            fputcsv($output, ['Total Assignments', $summary['total_assignments'] ?? 0]);
            fputcsv($output, ['Avg per Staff', ($summary['avg_per_staff'] ?? 0)]);
            fputcsv($output, []);  // Blank
        } elseif ($report_type === 'escalation' && !empty($summary)) {  // NEW: Added for escalation
            fputcsv($output, ['SUMMARY STATS']);
            fputcsv($output, ['Total Escalations', $summary['total_escalations'] ?? 0]);
            fputcsv($output, ['Level 1 (%)', ($summary['level1_pct'] ?? 0) . '%']);
            fputcsv($output, ['Level 3 (%)', ($summary['level3_pct'] ?? 0) . '%']);
            fputcsv($output, []);  // Blank
        }

        // Headers
        $headers = [];
        if ($report_type === 'all_tickets') $headers = ['Ticket ID', 'Title', 'Status', 'Priority', 'Student', 'Category', 'Created'];
        elseif ($report_type === 'overdue_tickets') $headers = ['Ticket ID', 'Title', 'Student', 'Category', 'Days Overdue', 'Created'];
        elseif ($report_type === 'staff_assignment') $headers = ['Staff Name', 'Email', 'Ticket Count', 'Status'];
        elseif ($report_type === 'escalation') $headers = ['Ticket ID', 'Title', 'Student', 'Level 1 Date', 'Level 2 Date', 'Level 3 Date', 'Created'];
        fputcsv($output, $headers);

        // Data
        foreach ($reports as $row) {
            $data = [];
            if ($report_type === 'all_tickets') {
                $data = [$row['ticket_id'], substr($row['title'], 0, 50) . '...', $row['status'], $row['priority'], $row['student_name'], $row['category'], date('Y-m-d', strtotime($row['created_at']))];
            } elseif ($report_type === 'overdue_tickets') {
                $data = [$row['ticket_id'], substr($row['title'], 0, 50) . '...', $row['student_name'], $row['category'], $row['days_overdue'] . ' days', date('Y-m-d', strtotime($row['created_at']))];
            } elseif ($report_type === 'staff_assignment') {
                $data = [$row['staff_name'], $row['email'], $row['ticket_count'], $row['status'] ?? 'N/A'];
            } elseif ($report_type === 'escalation') {
                $data = [
                    $row['ticket_id'],
                    substr($row['title'], 0, 50) . '...',
                    $row['student_name'],
                    $row['level_1'] ? date('Y-m-d H:i', strtotime($row['level_1'])) : 'N/A',
                    $row['level_2'] ? date('Y-m-d H:i', strtotime($row['level_2'])) : 'N/A',
                    $row['level_3'] ? date('Y-m-d H:i', strtotime($row['level_3'])) : 'N/A',
                    date('Y-m-d', strtotime($row['ticket_date']))
                ];
            }
            fputcsv($output, $data);
        }

        fclose($output);
        exit;
    }

    // Normal render
    $headContent = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    $this->view('staff/staffReports', [
        'title' => 'Generate Reports',
        'head' => $headContent,
        'reports' => $reports,
        'summary' => $summary,
        'report_type' => $report_type,
        'divisions' => $divisions,
        'error' => $error,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'status' => $status,
        'priority' => $priority,
        'division_id' => $division_id,
        'level' => $level
    ]);
}

}