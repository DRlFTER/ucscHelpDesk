<?php

class User extends Model
{
	/**
	 * Find a user by email in the new users schema.
	 * Returns: [u_id, email, name, role, password_hash, number, year, designation, profile_url, is_deleted, is_suspended]
	 * Only returns non-deleted users (suspended users are returned so we can show proper message)
	 */
	public function findByEmail(string $email): ?array
	{
		$sql = "SELECT u_id, email, name, role, password_hash, number, year, designation, profile_url, is_deleted, is_suspended FROM users WHERE email = ? AND is_deleted = 0 LIMIT 1";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('s', $email);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
		$result = $stmt->get_result();
		$row = $result ? $result->fetch_assoc() : null;
		$stmt->close();
		return $row ?: null;
	}

	/**
	 * Get user status (is_deleted, is_suspended) by user ID.
	 * Used to check if user status changed mid-session.
	 */
	public function getUserStatus(int $userId): ?array
	{
		$sql = "SELECT is_deleted, is_suspended FROM users WHERE u_id = ? LIMIT 1";
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
		return $row ?: null;
	}

	/**
	 * Create user in the new users schema. Optional fields are nullable.
	 * @return int inserted u_id
	 */
	public function createUser(string $role, string $name, string $email, string $password, ?string $number = null, ?string $year = null, ?string $designation = null): int
	{
		$sql = "INSERT INTO users (role, name, email, password_hash, number, year, designation) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt->bind_param('sssssss', $role, $name, $email, $hash, $number, $year, $designation);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
		$userId = (int)$this->db->insert_id;
		$stmt->close();
		return $userId;
	}

	/**
	 * Update user profile (name, phone number)
	 * @param int $userId User ID
	 * @param string $name New name
	 * @param string|null $number New phone number
	 * @return bool Success status
	 */
	public function updateProfile(int $userId, string $name, ?string $number = null): bool
	{
		$sql = "UPDATE users SET name = ?, number = ? WHERE u_id = ?";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('ssi', $name, $number, $userId);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	/**
	 * Get user by ID
	 * @param int $userId User ID
	 * @return array|null User data or null if not found
	 */
	public function findById(int $userId): ?array
	{
		$sql = "SELECT u_id, email, name, role, number, year, designation, profile_url, is_deleted, is_suspended FROM users WHERE u_id = ? AND is_deleted = 0 LIMIT 1";
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
		return $row ?: null;
	}

	/**
	 * Update user profile photo URL
	 * @param int $userId User ID
	 * @param string|null $profileUrl New profile photo URL (null to remove)
	 * @return bool Success status
	 */
	public function updateProfilePhoto(int $userId, ?string $profileUrl): bool
	{
		$sql = "UPDATE users SET profile_url = ? WHERE u_id = ?";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('si', $profileUrl, $userId);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	/**
	 * Get user's current profile photo URL
	 * @param int $userId User ID
	 * @return string|null Profile URL or null if not set
	 */
	public function getProfilePhotoUrl(int $userId): ?string
	{
		$sql = "SELECT profile_url FROM users WHERE u_id = ? LIMIT 1";
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
		return $row['profile_url'] ?? null;
	}

	/**
	 * Update user password
	 * @param int $userId User ID
	 * @param string $passwordHash New password hash
	 * @return bool Success status
	 */
	public function updatePassword(int $userId, string $passwordHash): bool
	{
		$sql = "UPDATE users SET password_hash = ? WHERE u_id = ?";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('si', $passwordHash, $userId);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
}


