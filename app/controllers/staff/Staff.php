<?php

class Staff extends Controller {

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

}
?>