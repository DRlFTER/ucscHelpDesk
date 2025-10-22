<?php
require_once __DIR__ . '/../../core/Database.php';

class CounselorDashboard
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function getDivisionId(string $name = 'Counseling'): ?int
    {
        $sql = "SELECT did FROM division WHERE LOWER(name) = LOWER(?) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ? (int)$row['did'] : null;
    }

    private function statusOpenClause(): string
    {
        return "t.status IN ('pending','agent assigned')";
    }

    private function statusResolvedClause(): string
    {
        return "t.status IN ('resolved','closed','agent-closed')";
    }

    public function getCardsData(int $counselorId): array
    {
        $did = $this->getDivisionId('Counseling');
        if (!$did) { return [ 'open' => 0, 'assigned' => 0, 'meetings' => 0, 'resolvedByYou' => 0 ]; }

        // Open in Counseling
        $open = 0;
        $sqlOpen = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division = ? AND " . $this->statusOpenClause();
        if ($stmt = $this->db->prepare($sqlOpen)) {
            $stmt->bind_param('i', $did);
            if ($stmt->execute()) { $open = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Assigned to you (open)
        $assigned = 0;
        $sqlAssigned = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division = ? AND t.assigned_to = ? AND " . $this->statusOpenClause();
        if ($stmt = $this->db->prepare($sqlAssigned)) {
            $stmt->bind_param('ii', $did, $counselorId);
            if ($stmt->execute()) { $assigned = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Meeting requests (requested or scheduled)
        $meetings = 0;
        $sqlMeet = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division = ? AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')";
        if ($stmt = $this->db->prepare($sqlMeet)) {
            $stmt->bind_param('i', $did);
            if ($stmt->execute()) { $meetings = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Resolved by you (all time)
        $resolvedByYou = 0;
        $sqlRes = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division = ? AND t.assigned_to = ? AND " . $this->statusResolvedClause();
        if ($stmt = $this->db->prepare($sqlRes)) {
            $stmt->bind_param('ii', $did, $counselorId);
            if ($stmt->execute()) { $resolvedByYou = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        return [
            'open' => $open,
            'assigned' => $assigned,
            'meetings' => $meetings,
            'resolvedByYou' => $resolvedByYou,
        ];
    }

    public function getRecentAssigned(int $counselorId, int $limit = 6): array
    {
        $did = $this->getDivisionId('Counseling');
        if (!$did) return [];
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division = ? AND t.assigned_to = ?
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('iii', $did, $counselorId, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getNewPending(int $limit = 6): array
    {
        $did = $this->getDivisionId('Counseling');
        if (!$did) return [];
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority,
                       CASE WHEN COALESCE(t.assigned_to, 0) = 0 THEN 0 ELSE 1 END AS is_assigned
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division = ? AND t.status = 'pending'
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('ii', $did, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getMeetingTickets(int $limit = 6): array
    {
        $did = $this->getDivisionId('Counseling');
        if (!$did) return [];
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.meeting_requested, t.assigned_to
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division = ? AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('ii', $did, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public static function relativeTime(?string $datetime): string
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
