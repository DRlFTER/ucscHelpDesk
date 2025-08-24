<?php

class User extends Model
{
	public function createUser(string $role, string $fullName, ?string $username, string $email, string $password): int
	{
		$sql = "INSERT INTO users (role, full_name, username, email, password_hash) VALUES (?, ?, ?, ?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt->bind_param('sssss', $role, $fullName, $username, $email, $hash);
	if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
	$userId = $this->db->insert_id;
		$stmt->close();
		return $userId;
	}

	public function createStudent(int $userId, string $regNumber): int
	{
		$sql = "INSERT INTO students (user_id, reg_number) VALUES (?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('is', $userId, $regNumber);
	if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
	$id = $this->db->insert_id;
		$stmt->close();
		return $id;
	}

	public function createLecturer(int $userId, string $department): int
	{
		$sql = "INSERT INTO lecturers (user_id, department) VALUES (?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('is', $userId, $department);
	if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
	$id = $this->db->insert_id;
		$stmt->close();
		return $id;
	}

	public function createStaff(int $userId, string $staffId): int
	{
		$sql = "INSERT INTO staff (user_id, staff_id) VALUES (?, ?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('is', $userId, $staffId);
	if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
	$id = $this->db->insert_id;
		$stmt->close();
		return $id;
	}

	public function createCounselor(int $userId): int
	{
		$sql = "INSERT INTO counselors (user_id) VALUES (?)";
		$stmt = $this->db->prepare($sql);
		if (!$stmt) {
			throw new Exception('Prepare failed: ' . $this->db->error);
		}
		$stmt->bind_param('i', $userId);
		if (!$stmt->execute()) {
			throw new Exception('Execute failed: ' . $stmt->error);
		}
		$id = $stmt->insert_id;
		$stmt->close();
		return $id;
	}
}

