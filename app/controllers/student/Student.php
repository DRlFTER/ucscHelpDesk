<?php

class Student extends Controller
{
    public function dashboard()
    {
        $this->requireLogin('student');

        $headContent = '
        <link rel="stylesheet" href="/css/student/dashboard.css"/>';
         $this->view('dashboardStudent', ['title' => 'Student Dashboard', 'head' => $headContent]);
    }

    public function ticket()
    {
        $this->requireLogin('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation and persistence
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $priority = trim($_POST['priority'] ?? '');
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
        <link rel="stylesheet" href="/css/student/ticket.css" />';

        $this->view('ticketStudent', [
            'title' => 'New Ticket',
            'head' => $headContent,
            'flash' => $flash ?? null,
        ]);
    }
}