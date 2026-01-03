<?php
// models/staff/Faq.php
// Note: This model reuses the exact same FAQ CRUD logic as AdminModel, since FAQs are shared across roles.
// It extends Model and focuses only on FAQ methods for cleanliness.

class StaffFaqModel extends Model
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
}