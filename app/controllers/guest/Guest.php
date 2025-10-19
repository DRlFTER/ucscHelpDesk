<?php

class Guest extends Controller
{
    public function dashboard()
    {
        $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/guest/guestDashboard.css"/>';
         $this->view('dashboardGuest', ['title' => 'Guest Dashboard', 'head' => $headContent]);
    }

    /**
     * Return public-only data for the guest dashboard as JSON.
     * - Announcements (all) via StudentAnnouncement model
     * - Recent tickets (anonymized, limited fields)
     * - Simple cards and platform status
     */
    public function publicData()
    {
        header('Content-Type: application/json');

        // Announcements via student model
        require_once __DIR__ . '/../../models/student/Announcement.php';
        $annModel = new StudentAnnouncement();
        $announcements = [];
        try {
            $announcements = $annModel->getAll();
        } catch (Throwable $e) {
            $announcements = [];
        }

        // Recent tickets (anonymized): show last 6 by created_at with only title/category/priority/time
        $db = Database::getInstance();
        $recentTickets = [];
        $sqlRecent = "SELECT t.ticket_id, t.title, t.created_at, t.priority, t.category FROM tickets t ORDER BY t.created_at DESC LIMIT 6";
        if ($res = $db->query($sqlRecent)) {
            while ($row = $res->fetch_assoc()) {
                $recentTickets[] = [
                    'id' => (int)$row['ticket_id'],
                    'title' => (string)($row['title'] ?? ''),
                    'time' => self::relativeTime($row['created_at'] ?? null),
                    'priority' => ucfirst((string)($row['priority'] ?? '')),
                    'category' => (string)($row['category'] ?? ''),
                ];
            }
            $res->free_result();
        }

        // Cards: summarize counts only
        $totalTickets = 0;
        $openTickets = 0;
        $annCount = is_array($announcements) ? count($announcements) : 0;
        $sqlCounts = "SELECT COUNT(*) AS total_count, SUM(CASE WHEN status IN ('pending','agent assigned') THEN 1 ELSE 0 END) AS open_count FROM tickets";
        if ($res = $db->query($sqlCounts)) {
            $row = $res->fetch_assoc();
            $totalTickets = (int)($row['total_count'] ?? 0);
            $openTickets = (int)($row['open_count'] ?? 0);
            $res->free_result();
        }

        $cardsData = [
            [ 'title' => 'Total Public Tickets', 'value' => $totalTickets, 'change' => '' ],
            [ 'title' => 'Open Tickets', 'value' => $openTickets, 'change' => '' ],
            [ 'title' => 'Announcements', 'value' => $annCount, 'change' => '' ],
            [ 'title' => 'Get Full Access', 'value' => 'Log in', 'change' => '' ],
        ];

        $platformStatus = [
            [ 'name' => 'Help Desk Availability', 'status' => 'Operational' ],
            [ 'name' => 'Ticket Submission', 'status' => 'Login Required' ],
            [ 'name' => 'Announcements', 'status' => 'Operational' ],
        ];

        // Project a minimal, safe announcement list for guests (trim content)
        $recentAnnouncements = [];
        foreach (array_slice($announcements, 0, 6) as $a) {
            $recentAnnouncements[] = [
                'id' => (int)($a['id'] ?? 0),
                'topic' => (string)($a['topic'] ?? ''),
                'snippet' => self::truncate((string)($a['content'] ?? ''), 140),
                'date' => self::prettyDate($a['date_time'] ?? null),
                'author' => (string)($a['staff_name'] ?? ''),
                'division' => (string)($a['division_name'] ?? ''),
            ];
        }

        echo json_encode([
            'cardsData' => $cardsData,
            'platformStatus' => $platformStatus,
            'recentAnnouncements' => $recentAnnouncements,
            'recentTickets' => $recentTickets,
        ]);
        exit;
    }

    private static function truncate(string $text, int $max = 140): string
    {
        $t = trim(strip_tags($text));
        if (mb_strlen($t) <= $max) return $t;
        return mb_substr($t, 0, $max - 1) . '…';
    }

    private static function prettyDate(?string $datetime): string
    {
        if (!$datetime) return '';
        $ts = strtotime($datetime);
        if ($ts === false) return '';
        return date('M d, Y', $ts);
    }

    private static function relativeTime(?string $datetime): string
    {
        if (!$datetime) return '';
        $ts = strtotime($datetime);
        if ($ts === false) return '';
        $diff = time() - $ts;
        if ($diff < 60) return max(1, (int)floor($diff)) . 's ago';
        if ($diff < 3600) return (int)floor($diff / 60) . 'm ago';
        if ($diff < 86400) return (int)floor($diff / 3600) . 'h ago';
        return (int)floor($diff / 86400) . 'd ago';
    }
}
