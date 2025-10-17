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