<?php

class StudentLostFound extends Model
{
    /**
     * Create a new Lost & Found record.
     * Expected keys in $data: u_id, item_title, category, when, item_details, status, contact_mobile, contact_email
     * Returns inserted q_id
     */
    public function create(array $data): int
    {
    $sql = "INSERT INTO lost_found (u_id, item_title, category, `when`, item_details, created_at, status, contact_mobile, contact_email)
        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $u_id = (int)($data['u_id'] ?? 0);
        $item_title = (string)($data['item_title'] ?? '');
        $category = (string)($data['category'] ?? null);
        $when = (string)($data['when'] ?? null); // expected format YYYY-MM-DD
        $item_details = (string)($data['item_details'] ?? '');
        $status = (string)($data['status'] ?? 'lost');
        $contact_mobile = isset($data['contact_mobile']) && $data['contact_mobile'] !== '' ? (string)$data['contact_mobile'] : null;
        $contact_email = isset($data['contact_email']) && $data['contact_email'] !== '' ? (string)$data['contact_email'] : null;

        $stmt->bind_param(
            'isssssss',
            $u_id,
            $item_title,
            $category,
            $when,
            $item_details,
            $status,
            $contact_mobile,
            $contact_email
        );

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception('Execute failed: ' . $err);
        }

        $id = (int)$this->db->insert_id;
        $stmt->close();
        return $id;
    }

    /**
     * Get records by status (e.g., 'lost', 'found', 'claimed').
     * Returns an array of associative rows.
     */
    public function getByStatus(string $status, int $limit = 20): array
    {
    $sql = "SELECT q_id, u_id, item_title, category, `when`, item_details, status, contact_mobile, contact_email, created_at
        FROM lost_found
        WHERE status = ?
        ORDER BY created_at DESC
        LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }
        $stmt->bind_param('si', $status, $limit);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception('Execute failed: ' . $err);
        }
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Delete a Lost & Found record owned by the given user.
     * Returns true if a row was deleted.
     */
    public function deleteByIdForUser(int $q_id, int $u_id): bool
    {
        $sql = "DELETE FROM lost_found WHERE q_id = ? AND u_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }
        $stmt->bind_param('ii', $q_id, $u_id);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception('Execute failed: ' . $err);
        }
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
}
