<?php

class Register extends Controller
{
    public function index()
    {
        // If POST, attempt registration into consolidated users table
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $role = $_POST['role'] ?? '';
                $fullName = trim($_POST['fullName'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirmPassword'] ?? '';

                // optional by role
                $number = $_POST['number'] ?? null; // phone
                $year = null; // students may pass regNumber/year
                $designation = null; // staff/counselor/admin designation

                // map UI-specific fields
                if ($role === 'student') {
                    $year = $_POST['regNumber'] ?? ($_POST['year'] ?? null);
                } elseif ($role === 'lecturer') {
                    $designation = $_POST['department'] ?? null;
                } elseif ($role === 'staff') {
                    // Expect integer code 1-9 from dropdown
                    $designation = $_POST['designation'] ?? null;
                    if ($designation !== null) {
                        $designation = trim($designation);
                        if (!preg_match('/^[1-9]$/', $designation)) {
                            throw new Exception('Please select a valid department.');
                        }
                    }
                }

                if (!$role || !$fullName || !$email || !$password) {
                    throw new Exception('Missing required fields.');
                }
                if ($confirmPassword !== '' && $password !== $confirmPassword) {
                    throw new Exception('Passwords do not match.');
                }

                $userModel = $this->model('User');
                if ($userModel->findByEmail($email)) {
                    throw new Exception('An account with this email already exists.');
                }

                $userId = $userModel->createUser($role, $fullName, $email, $password, $number, $year, $designation);

                // If staff, also create staff_division mapping (u_id, did)
                if ($role === 'staff' && $designation !== null && preg_match('/^[1-9]$/', $designation)) {
                    $db = Database::getInstance();
                    $stmt = $db->prepare('INSERT INTO staff_division (u_id, did) VALUES (?, ?)');
                    if ($stmt) {
                        $uid = (int)$userId; $did = (int)$designation;
                        $stmt->bind_param('ii', $uid, $did);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

                $flash = ['type' => 'success', 'message' => 'Registration successful. You can now sign in.'];
            } catch (Throwable $e) {
                $flash = ['type' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $flash = null;
        }

        $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/register/register.css"/>';

        $this->view('register', [
            'title' => 'UCSC Help Desk - Register',
            'head'  => $headContent,
            'flash' => $flash,
        ]);
    }
}
