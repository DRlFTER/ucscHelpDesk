<?php

class AdminModel extends Model
{
    // ==================== FAQ Methods ====================

    /**
     * Get total count of FAQs with optional search filter
     */
    public function getFaqCount(string $search = ''): int
    {
        $whereSql = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $whereSql = "WHERE (question LIKE ? OR answer LIKE ?)";
            $params = [$searchPattern, $searchPattern];
            $types = 'ss';
        }

        $sql = "SELECT COUNT(*) AS c FROM faq $whereSql";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)($row['c'] ?? 0);
    }

    /**
     * Get paginated FAQs with optional search filter
     */
    public function getFaqs(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $whereSql = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $whereSql = "WHERE (question LIKE ? OR answer LIKE ?)";
            $params = [$searchPattern, $searchPattern];
            $types = 'ss';
        }

        $sql = "SELECT id, question, answer, created_at FROM faq $whereSql ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Get a single FAQ by ID
     */
    public function getFaqById(int $id): ?array
    {
        $sql = "SELECT id, question, answer, created_at FROM faq WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Create a new FAQ
     */
    public function createFaq(string $question, string $answer): int
    {
        $sql = "INSERT INTO faq (question, answer) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('ss', $question, $answer);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $id = (int)$this->db->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Update an existing FAQ
     */
    public function updateFaq(int $id, string $question, string $answer): bool
    {
        $sql = "UPDATE faq SET question = ?, answer = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('ssi', $question, $answer, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Delete a FAQ
     */
    public function deleteFaq(int $id): bool
    {
        $sql = "DELETE FROM faq WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // ==================== Forum Methods ====================

    /**
     * Get forum posts count with filters
     */
    public function getForumCount(int $userId = 0, string $search = '', string $topic = '', string $status = '', string $type = ''): int
    {
        $where = [];
        $params = [];
        $types = '';

        if (strtolower($type) === 'my' && $userId > 0) {
            $where[] = "f.u_id = ?";
            $params[] = $userId;
            $types .= 'i';
        }

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(f.title LIKE ? OR f.description LIKE ?)";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= 'ss';
        }

        if ($topic !== '') {
            $where[] = "f.topic = ?";
            $params[] = $topic;
            $types .= 's';
        }

        if ($status !== '') {
            $where[] = "LOWER(f.status) = LOWER(?)";
            $params[] = $status;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) AS c FROM forum_q f $whereSql";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)($row['c'] ?? 0);
    }

    /**
     * Get paginated forum posts with filters
     */
    public function getForumPosts(int $userId = 0, string $search = '', string $topic = '', string $status = '', string $type = '', string $sort = 'latest', int $limit = 10, int $offset = 0): array
    {
        $where = [];
        $params = [];
        $types = '';

        if (strtolower($type) === 'my' && $userId > 0) {
            $where[] = "f.u_id = ?";
            $params[] = $userId;
            $types .= 'i';
        }

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(f.title LIKE ? OR f.description LIKE ?)";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= 'ss';
        }

        if ($topic !== '') {
            $where[] = "f.topic = ?";
            $params[] = $topic;
            $types .= 's';
        }

        if ($status !== '') {
            $where[] = "LOWER(f.status) = LOWER(?)";
            $params[] = $status;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $orderSql = strtolower($sort) === 'oldest' ? 'ORDER BY f.created_at ASC' : 'ORDER BY f.created_at DESC';

        $sql = "SELECT f.q_id, f.created_at, f.title, f.topic, f.status, f.u_id, f.is_Public, u.name AS student_name
                FROM forum_q f
                LEFT JOIN users u ON u.u_id = f.u_id
                $whereSql
                $orderSql
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    // ==================== User Methods ====================

    /**
     * Get a single user by ID
     */
    public function getUserById(int $id): ?array
    {
        $sql = "SELECT u_id, name, email, role, designation, number, year, is_deleted, deleted_at, is_suspended, suspended_at FROM users WHERE u_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Update a user
     */
    public function updateUser(int $id, string $name, string $email, string $role, ?string $number = null, ?string $designation = null, ?int $year = null): bool
    {
        $sql = "UPDATE users SET name = ?, email = ?, role = ?, number = ?, designation = ?, year = ? WHERE u_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('sssssii', $name, $email, $role, $number, $designation, $year, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Soft delete a user
     */
    public function softDeleteUser(int $id): bool
    {
        $sql = "UPDATE users SET is_deleted = 1 WHERE u_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function suspendUser(int $id): bool
    {
        $sql = "UPDATE users SET is_suspended = 1, suspended_at = NOW() WHERE u_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Unsuspend a suspended user
     */
    public function unsuspendUser(int $id): bool
    {
        $sql = "UPDATE users SET is_suspended = 0, suspended_at = NULL WHERE u_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Restore a soft-deleted user
     */
    public function restoreUser(int $id): bool
    {
        $sql = "UPDATE users SET is_deleted = 0 WHERE u_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get users count with filters
     */
    public function getUsersCount(string $search = '', string $role = '', string $designation = ''): int
    {
        $where = [];
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(u.name LIKE ? OR u.email LIKE ? OR u.number LIKE ? OR u.designation LIKE ?)";
            $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
            $types .= 'ssss';
        }

        if ($role !== '') {
            $where[] = "u.role = ?";
            $params[] = strtolower($role);
            $types .= 's';
        }

        if ($designation !== '') {
            $where[] = "COALESCE(u.designation,'') = ?";
            $params[] = $designation;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) AS c FROM users u $whereSql";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)($row['c'] ?? 0);
    }

    /**
     * Get paginated users with filters
     */
    public function getUsers(string $search = '', string $role = '', string $designation = '', int $limit = 10, int $offset = 0): array
    {
        $where = [];
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(u.name LIKE ? OR u.email LIKE ? OR u.number LIKE ? OR u.designation LIKE ?)";
            $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
            $types .= 'ssss';
        }

        if ($role !== '') {
            $where[] = "u.role = ?";
            $params[] = strtolower($role);
            $types .= 's';
        }

        if ($designation !== '') {
            $where[] = "COALESCE(u.designation,'') = ?";
            $params[] = $designation;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT u.u_id, u.name, u.email, u.role, u.designation, u.number, u.year, u.is_deleted, u.is_suspended, u.suspended_at
                FROM users u
                $whereSql
                ORDER BY u.name ASC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Get all distinct designations
     */
    public function getDistinctDesignations(): array
    {
        $sql = "SELECT DISTINCT designation FROM users WHERE designation IS NOT NULL AND designation <> '' ORDER BY designation ASC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $designations = [];
        while ($row = $result->fetch_assoc()) {
            $val = (string)($row['designation'] ?? '');
            if ($val !== '') {
                $designations[] = $val;
            }
        }
        $stmt->close();

        return $designations;
    }

    // ==================== Ticket Methods ====================

    /**
     * Get a single ticket by ID with related data
     */
    public function getTicketById(int $id): ?array
    {
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.description, t.u_id, u.name AS student_name, t.meeting_requested
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                LEFT JOIN division d ON d.did = t.division
                WHERE t.ticket_id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Get ticket attachments
     */
    public function getTicketAttachments(int $ticketId): array
    {
        $sql = "SELECT doc_name, location FROM supporting_documents WHERE ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $ticketId);
        $stmt->execute();
        $result = $stmt->get_result();

        $attachments = [];
        while ($row = $result->fetch_assoc()) {
            $attachments[] = $row;
        }
        $stmt->close();

        return $attachments;
    }

    /**
     * Delete a ticket
     */
    public function deleteTicket(int $id): bool
    {
        $sql = "DELETE FROM tickets WHERE ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
    public function resolveTicket(int $id): bool
    {
        $sql = "UPDATE tickets 
                SET status = 'resolved' 
                WHERE ticket_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get tickets count with filters
     */
    public function getTicketsCount(string $search = '', string $category = '', string $status = '', string $priority = ''): int
    {
        $where = [];
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(t.title LIKE ? OR u.name LIKE ?)";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= 'ss';
        }

        if ($category !== '') {
            $catKey = strtolower($category);
            $groupMap = $this->getCategoryGroupMap();

            if (isset($groupMap[$catKey])) {
                $likes = [];
                foreach ($groupMap[$catKey] as $kw) {
                    $likes[] = "LOWER(COALESCE(d.name,'')) LIKE ?";
                    $params[] = '%' . strtolower($kw) . '%';
                    $types .= 's';
                }
                if (!empty($likes)) {
                    $where[] = '(' . implode(' OR ', $likes) . ')';
                }
            } else {
                $where[] = "COALESCE(d.name,'') = ?";
                $params[] = $category;
                $types .= 's';
            }
        }

        if ($status !== '') {
            $s = strtolower($status);
            if ($s === 'open') {
                $where[] = "t.status = 'pending'";
            } elseif ($s === 'in-progress') {
                $where[] = "t.status = 'agent assigned'";
            } elseif ($s === 'resolved') {
                $where[] = "t.status IN ('resolved','closed','agent-closed')";
            } else {
                $where[] = "t.status = ?";
                $params[] = $status;
                $types .= 's';
            }
        }

        if ($priority !== '') {
            $where[] = "LOWER(t.priority) = LOWER(?)";
            $params[] = $priority;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) AS c FROM tickets t LEFT JOIN users u ON u.u_id = t.u_id LEFT JOIN division d ON d.did = t.division $whereSql";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)($row['c'] ?? 0);
    }

    /**
     * Get paginated tickets with filters
     */
    public function getTickets(string $search = '', string $category = '', string $status = '', string $priority = '', int $limit = 10, int $offset = 0): array
    {
        $where = [];
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';
            $where[] = "(t.title LIKE ? OR u.name LIKE ?)";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= 'ss';
        }

        if ($category !== '') {
            $catKey = strtolower($category);
            $groupMap = $this->getCategoryGroupMap();

            if (isset($groupMap[$catKey])) {
                $likes = [];
                foreach ($groupMap[$catKey] as $kw) {
                    $likes[] = "LOWER(COALESCE(d.name,'')) LIKE ?";
                    $params[] = '%' . strtolower($kw) . '%';
                    $types .= 's';
                }
                if (!empty($likes)) {
                    $where[] = '(' . implode(' OR ', $likes) . ')';
                }
            } else {
                $where[] = "COALESCE(d.name,'') = ?";
                $params[] = $category;
                $types .= 's';
            }
        }

        if ($status !== '') {
            $s = strtolower($status);
            if ($s === 'open') {
                $where[] = "t.status = 'pending'";
            } elseif ($s === 'in-progress') {
                $where[] = "t.status = 'agent assigned'";
            } elseif ($s === 'resolved') {
                $where[] = "t.status IN ('resolved','closed','agent-closed')";
            } else {
                $where[] = "t.status = ?";
                $params[] = $status;
                $types .= 's';
            }
        }

        if ($priority !== '') {
            $where[] = "LOWER(t.priority) = LOWER(?)";
            $params[] = $priority;
            $types .= 's';
        }

        $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT t.ticket_id, t.created_at, t.title, d.name AS category, t.status, t.priority, t.meeting_requested, t.u_id, u.name AS student_name
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                LEFT JOIN division d ON d.did = t.division
                $whereSql
                ORDER BY t.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Get category group map for ticket filtering
     */
    private function getCategoryGroupMap(): array
    {
        return [
            'it-access' => [
                'it','tech','technical','account','login','password','email','network','wifi','wi-fi','internet','software','hardware','device','computer','system','server','bug','error','website','portal','moodle','lms','printer','printing','access'
            ],
            'facilities-equipment' => [
                'facility','facilities','maintenance','repair','clean','electric','electrical','power','water','plumb','leak','aircon','air conditioning','ac','furniture','equipment','lab','laboratory','room','classroom','projector','door','building','lighting','light','security'
            ],
            'academic-services' => [
                'academic','course','courses','class','classes','lecture','lecturer','timetable','schedule','exam','exams','grade','grades','registration','enrollment','admission','advis','library','scholarship','student id','id card','transcript','certificate','attendance'
            ],
            'administrative-other' => [
                'finance','payment','payments','fee','fees','billing','hr','human resources','leave','policy','procurement','procure','purchase','general','other','misc','miscellaneous','event','events','parking','transport','bus','lost','found','complaint','complaints','canteen','food','cafeteria','hostel','residence','housing','staff','admin','administration'
            ],
        ];
    }

    // ==================== Dashboard Methods ====================

    /**
     * Get ticket counts for dashboard
     */
    public function getTicketCounts(): array
    {
        $sql = "SELECT
            COUNT(*) AS total_count,
            SUM(CASE WHEN status IN ('pending','agent assigned') THEN 1 ELSE 0 END) AS open_count,
            SUM(CASE WHEN status IN ('resolved','closed','agent-closed') THEN 1 ELSE 0 END) AS resolved_count
        FROM tickets";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return [
            'total' => (int)($row['total_count'] ?? 0),
            'open' => (int)($row['open_count'] ?? 0),
            'resolved' => (int)($row['resolved_count'] ?? 0),
        ];
    }

    /**
     * Get average response time in minutes
     */
    public function getAverageResponseTime(): ?float
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                FROM tickets t
                JOIN (
                    SELECT ticket_id, MIN(date_time) AS first_response
                    FROM ticket_response
                    GROUP BY ticket_id
                ) tr ON tr.ticket_id = t.ticket_id";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return isset($row['avg_minutes']) ? (float)$row['avg_minutes'] : null;
    }

    /**
     * Get recent tickets for dashboard
     */
    public function getRecentTickets(int $limit = 6): array
    {
        $sql = "SELECT t.ticket_id, t.title, u.name AS requester, t.created_at, t.priority
                FROM tickets t
                LEFT JOIN users u ON u.u_id = t.u_id
                ORDER BY t.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Get top agents for dashboard
     */
    public function getTopAgents(int $limit = 5): array
    {
        $sql = "SELECT u.name,
                       COUNT(DISTINCT tr.ticket_id) AS resolved,
                       AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tr.first_response)) AS avg_minutes
                FROM users u
                JOIN (
                    SELECT ticket_id, u_id, MIN(date_time) AS first_response
                    FROM ticket_response
                    GROUP BY ticket_id, u_id
                ) tr ON tr.u_id = u.u_id
                JOIN tickets t ON t.ticket_id = tr.ticket_id
                WHERE u.role IN ('staff','admin','counselor','lecturer')
                GROUP BY u.u_id, u.name
                ORDER BY resolved DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Get ticket count for a date range
     */
    public function getTicketCountByDateRange(string $startDate, string $endDate, bool $resolvedOnly = false): int
    {
        $statusFilter = $resolvedOnly ? "AND status IN ('resolved','closed','agent-closed')" : "";
        $sql = "SELECT COUNT(*) AS c FROM tickets WHERE created_at >= ? AND created_at < ? $statusFilter";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)($row['c'] ?? 0);
    }

    /**
     * Get tickets grouped by category/division
     */
    public function getTicketsByCategory(): array
    {
        $sql = "SELECT COALESCE(d.name,'Other') AS category, COUNT(*) AS c
                FROM tickets t
                LEFT JOIN division d ON d.did = t.division
                GROUP BY COALESCE(d.name,'Other')
                ORDER BY c DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }
}
