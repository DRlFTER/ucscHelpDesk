<?php

class Staff extends Controller
{
    public function staffTickets()
    {
         $this->requireLogin('staff');
        $headContent = '
        <link rel="stylesheet" href="/css/staff/staffTickets.css"/>';
         $this->view('staffTickets', [
            'title' => 'Tickets',
            'head' => $headContent,
            'recentTickets' => $recent,
        ]);
    }
}
