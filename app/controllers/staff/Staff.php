<?php

class Staff extends Controller
{
    public function staffTickets()
    {
        $this->requireLogin('staff');

        // Load staff ticket model and fetch tickets
        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $tickets = [];
        try {
            $model = new StaffTicket();
            // If later we support assignments, pass staff id: (int)($_SESSION['user']['u_id'] ?? 0)
            $tickets = $model->getAllTickets();
        } catch (Throwable $e) {
            // In production we might log this
            $tickets = [];
        }

        $headContent = '
        <link rel="stylesheet" href="/css/staff/staffTickets.css"/>';
        $this->view('staff/staffTickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'tickets' => $tickets,
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
            $this->redirect('404');
            return;
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
            $this->redirect('404');
            return;
        }

        $headContent = '<link rel="stylesheet" href="/css/staff/staffTickets.css" />\n'
            . '<link rel="stylesheet" href="/css/staff/global.css" />';

        $this->view('staff/ticketDetails', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'ticket' => $ticket,
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
        // keep the small announcements tweaks after the main stylesheet
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
}