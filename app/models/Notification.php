<?php

class Notification extends Model
{
	/**
	 * Get count of unread notifications for a user.
	 */
	public function getUnreadCount(int $userId): int
	{
		$sql = "SELECT COUNT(*) AS cnt FROM notifications WHERE u_id = ? AND is_read = 0";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('i', $userId);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
		$result = $stmt->get_result();
		$row = $result ? $result->fetch_assoc() : null;
		$stmt->close();
		return (int)($row['cnt'] ?? 0);
	}

	/**
	 * Get recent notifications for a user.
	 * Returns both read and unread, ordered by newest first.
	 */
	public function getNotifications(int $userId, int $limit = 30): array
	{
		$sql = "SELECT notif_id, u_id, type, entity_type, entity_id, message, is_read, created_at
				FROM notifications
				WHERE u_id = ?
				ORDER BY created_at DESC
				LIMIT ?";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('ii', $userId, $limit);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
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
	 * Get notifications created since a given timestamp (for polling).
	 */
	public function getNewSince(int $userId, string $since): array
	{
		$sql = "SELECT notif_id, u_id, type, entity_type, entity_id, message, is_read, created_at
				FROM notifications
				WHERE u_id = ? AND created_at > ?
				ORDER BY created_at DESC";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('is', $userId, $since);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
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
	 * Mark a single notification as read (only if owned by user).
	 */
	public function markAsRead(int $notifId, int $userId): bool
	{
		$sql = "UPDATE notifications SET is_read = 1 WHERE notif_id = ? AND u_id = ?";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('ii', $notifId, $userId);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	/**
	 * Mark all notifications as read for a user.
	 */
	public function markAllRead(int $userId): bool
	{
		$sql = "UPDATE notifications SET is_read = 1 WHERE u_id = ? AND is_read = 0";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('i', $userId);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	/**
	 * Create a new notification.
	 * @return int The new notification ID
	 */
	public function create(int $userId, string $type, string $message, ?string $entityType = null, ?int $entityId = null): int
	{
		$sql = "INSERT INTO notifications (u_id, type, entity_type, entity_id, message) VALUES (?, ?, ?, ?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('issss', $userId, $type, $entityType, $entityId, $message);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
		$id = (int)$this->db->insert_id;
		$stmt->close();
		return $id;
	}
}
