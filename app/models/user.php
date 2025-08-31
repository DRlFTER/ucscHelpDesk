<?php

class User extends Model
{
	/**
	 * Find a user by email in the new users schema.
	 * Returns: [u_id, email, name, role, password_hash, number, year, designation]
	 */
	public function findByEmail(string $email): ?array
	{
		$sql = "SELECT u_id, email, name, role, password_hash, number, year, designation FROM users WHERE email = ? LIMIT 1";
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
}


