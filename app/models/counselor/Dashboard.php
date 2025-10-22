<?php
require_once __DIR__ . '/../../core/Database.php';

class CounselorDashboard
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Resolve all division IDs that the counselor belongs to using staff_division mapping.
     * Falls back to users.designation if mapping is missing.
     */
    private function getDivisionIdsForCounselor(int $counselorId): array
    {
        $ids = [];
        // Primary: staff_division mapping
        if ($stmt = $this->db->prepare('SELECT did FROM staff_division WHERE u_id = ?')) {
            $stmt->bind_param('i', $counselorId);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $ids[] = (int)$row['did'];
                }
            }
            $stmt->close();
        }
        if (!empty($ids)) return $ids;

        // Fallback: users.designation
        if ($stmt = $this->db->prepare("SELECT designation FROM users WHERE u_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $counselorId);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                if ($row && !empty($row['designation'])) {
                    $did = (int)$row['designation'];
                    if ($did > 0) { $ids[] = $did; }
                }
            }
            $stmt->close();
        }
        return $ids;
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
        $dids = $this->getDivisionIdsForCounselor($counselorId);
        if (empty($dids)) { return [ 'open' => 0, 'assigned' => 0, 'meetings' => 0, 'resolvedByYou' => 0 ]; }

        $placeholders = implode(',', array_fill(0, count($dids), '?'));
        $types = str_repeat('i', count($dids));

        // Open in counselor's divisions
        $open = 0;
        $sqlOpen = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($placeholders) AND " . $this->statusOpenClause();
        if ($stmt = $this->db->prepare($sqlOpen)) {
            $stmt->bind_param($types, ...$dids);
            if ($stmt->execute()) { $open = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Assigned to you (open)
        $assigned = 0;
        $sqlAssigned = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($placeholders) AND t.assigned_to = ? AND " . $this->statusOpenClause();
        if ($stmt = $this->db->prepare($sqlAssigned)) {
            $bindTypes = $types . 'i';
            $params = array_merge($dids, [$counselorId]);
            $stmt->bind_param($bindTypes, ...$params);
            if ($stmt->execute()) { $assigned = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Meeting requests (requested or scheduled)
        $meetings = 0;
        $sqlMeet = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($placeholders) AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')";
        if ($stmt = $this->db->prepare($sqlMeet)) {
            $stmt->bind_param($types, ...$dids);
            if ($stmt->execute()) { $meetings = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Resolved by you (all time)
        $resolvedByYou = 0;
        $sqlRes = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($placeholders) AND t.assigned_to = ? AND " . $this->statusResolvedClause();
        if ($stmt = $this->db->prepare($sqlRes)) {
            $bindTypes = $types . 'i';
            $params = array_merge($dids, [$counselorId]);
            $stmt->bind_param($bindTypes, ...$params);
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
        $dids = $this->getDivisionIdsForCounselor($counselorId);
        if (empty($dids)) return [];
        $placeholders = implode(',', array_fill(0, count($dids), '?'));
        $types = str_repeat('i', count($dids)) . 'ii';
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($placeholders) AND t.assigned_to = ?
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $params = array_merge($dids, [$counselorId, $limit]);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getNewPending(int $limit = 6): array
    {
        $userId = (int)($_SESSION['user']['u_id'] ?? 0);
        $dids = $this->getDivisionIdsForCounselor($userId);
        if (empty($dids)) return [];
        $placeholders = implode(',', array_fill(0, count($dids), '?'));
        $types = str_repeat('i', count($dids)) . 'i';
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority,
                       CASE WHEN COALESCE(t.assigned_to, 0) = 0 THEN 0 ELSE 1 END AS is_assigned
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($placeholders) AND t.status = 'pending'
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $params = array_merge($dids, [$limit]);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getMeetingTickets(int $limit = 6): array
    {
        $userId = (int)($_SESSION['user']['u_id'] ?? 0);
        $dids = $this->getDivisionIdsForCounselor($userId);
        if (empty($dids)) return [];
        $placeholders = implode(',', array_fill(0, count($dids), '?'));
        $types = str_repeat('i', count($dids)) . 'i';
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.meeting_requested, t.assigned_to
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($placeholders) AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $params = array_merge($dids, [$limit]);
        $stmt->bind_param($types, ...$params);
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
