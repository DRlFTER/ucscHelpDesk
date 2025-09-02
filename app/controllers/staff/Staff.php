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
}
