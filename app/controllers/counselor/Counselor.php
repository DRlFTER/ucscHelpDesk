<?php

class Counselor extends Controller
{
    public function dashboard()
    {
        // Require counselor role
        $this->requireLogin('counselor');

        // Page head
    $headContent = '
    <link rel="stylesheet" href="/css/counselor/counselorDashboard.css"/>';

        // Current user id
        $uid = (int)($_SESSION['user']['u_id'] ?? 0);

        // Load dashboard data via model (server-side; no JS fetching)
        /** @var CounselorDashboard $dash */
        require_once __DIR__ . '/../../models/counselor/Dashboard.php';
        $dash = new CounselorDashboard();
        $cards = $dash->getCardsData($uid);
        $recentAssigned = $dash->getRecentAssigned($uid, 6);
        $newPending = $dash->getNewPending(6);
        $meetingTickets = $dash->getMeetingTickets(6);

        $this->view('counselor/counselorDashboard', [
            'title' => 'Counselor Dashboard',
            'head' => $headContent,
            'cards' => $cards,
            'recentAssigned' => $recentAssigned,
            'newPending' => $newPending,
            'meetingTickets' => $meetingTickets,
        ]);
    }
}
