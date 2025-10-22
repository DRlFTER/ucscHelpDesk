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
     * Resolve counselor's relevant division ids.
     * Priority:
     * 1) staff_division mapping for this user
     * 2) users.designation numeric fallback
     * 3) division name fallback: Counseling/Counselling
     */
    private function getCounselorDivisionIds(int $uid): array
    {
        $ids = [];
        // 1) staff_division mapping
        if ($stmt = $this->db->prepare("SELECT did FROM staff_division WHERE u_id = ?")) {
            $stmt->bind_param('i', $uid);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()) {
                    $ids[] = (int)$r['did'];
                }
            }
            $stmt->close();
        }
        if (!empty($ids)) return array_values(array_unique($ids));

        // 2) designation numeric fallback (some installs store did in users.designation)
        if ($stmt = $this->db->prepare("SELECT designation FROM users WHERE u_id = ? AND role = 'counselor' LIMIT 1")) {
            $stmt->bind_param('i', $uid);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $did = (int)($row['designation'] ?? 0);
                    if ($did > 0) $ids[] = $did;
                }
            }
            $stmt->close();
        }
        if (!empty($ids)) return array_values(array_unique($ids));

        // 3) division name fallback
        $names = ['Counseling', 'Counselling'];
        foreach ($names as $nm) {
            if ($stmt = $this->db->prepare("SELECT did FROM division WHERE LOWER(name) = LOWER(?) LIMIT 1")) {
                $stmt->bind_param('s', $nm);
                if ($stmt->execute()) {
                    $res = $stmt->get_result();
                    if ($row = $res->fetch_assoc()) { $ids[] = (int)$row['did']; }
                }
                $stmt->close();
            }
            if (!empty($ids)) break;
        }
        return array_values(array_unique($ids));
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
        $dids = $this->getCounselorDivisionIds($counselorId);
        if (empty($dids)) { return [ 'open' => 0, 'assigned' => 0, 'meetings' => 0, 'resolvedByYou' => 0 ]; }

        // Open in Counseling
        $open = 0;
        $in = implode(',', array_map('intval', $dids));
        $sqlOpen = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($in) AND " . $this->statusOpenClause();
        if ($res = $this->db->query($sqlOpen)) { $open = (int)($res->fetch_assoc()['c'] ?? 0); $res->close(); }

        // Assigned to you (open)
        $assigned = 0;
        $sqlAssigned = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($in) AND t.assigned_to = ? AND " . $this->statusOpenClause();
        if ($stmt = $this->db->prepare($sqlAssigned)) {
            $stmt->bind_param('i', $counselorId);
            if ($stmt->execute()) { $assigned = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0); }
            $stmt->close();
        }

        // Meeting requests (requested or scheduled)
        $meetings = 0;
        $sqlMeet = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($in) AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')";
        if ($res = $this->db->query($sqlMeet)) { $meetings = (int)($res->fetch_assoc()['c'] ?? 0); $res->close(); }

        // Resolved by you (all time)
        $resolvedByYou = 0;
        $sqlRes = "SELECT COUNT(*) AS c FROM tickets t WHERE t.division IN ($in) AND t.assigned_to = ? AND " . $this->statusResolvedClause();
        if ($stmt = $this->db->prepare($sqlRes)) {
            $stmt->bind_param('i', $counselorId);
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
        $dids = $this->getCounselorDivisionIds($counselorId);
        if (empty($dids)) return [];
        $in = implode(',', array_map('intval', $dids));
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($in) AND t.assigned_to = ?
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('ii', $counselorId, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getNewPending(int $limit = 6): array
    {
        // Note: independent of counselor id; show latest pending in counseling divisions
        // If multiple counseling divisions exist, include all.
        // We'll find counseling-like divisions generically.
        $dids = $this->getCounselorDivisionIds((int)($_SESSION['user']['u_id'] ?? 0));
        if (empty($dids)) return [];
        $in = implode(',', array_map('intval', $dids));
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.status, t.priority,
                       CASE WHEN COALESCE(t.assigned_to, 0) = 0 THEN 0 ELSE 1 END AS is_assigned
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($in) AND t.status = 'pending'
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function getMeetingTickets(int $limit = 6): array
    {
        $dids = $this->getCounselorDivisionIds((int)($_SESSION['user']['u_id'] ?? 0));
        if (empty($dids)) return [];
        $in = implode(',', array_map('intval', $dids));
        $sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.meeting_requested, t.assigned_to
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                WHERE t.division IN ($in) AND LOWER(COALESCE(t.meeting_requested,'')) IN ('requested','scheduled')
                ORDER BY t.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $limit);
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
