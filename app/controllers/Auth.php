<?php

class Auth extends Controller
{
	public function index()
	{
		// show login form
		$this->view('login', [
			'title' => 'UCSC Help Desk - Login',
		]);
	}

	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: ' . ROOT . '/public/auth');
			exit;
		}

		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';

		try {
			$userModel = $this->model('User');
			$user = $userModel->findByEmail($email);
			if (!$user || !password_verify($password, $user['password_hash'])) {
				throw new Exception('Invalid credentials.');
			}

			// minimal session
			$_SESSION['user'] = [
				'id' => (int)$user['id'],
				'email' => $user['email'],
				'role' => $user['role'],
				'name' => $user['full_name'],
			];

			// route by role (placeholder dashboards)
				switch ($user['role']) {
					case 'student':
						header('Location: ' . ROOT . '/public/student/dashboard');
					break;
				case 'lecturer':
					header('Location: ' . ROOT . '/public/lecturer/dashboard');
					break;
				case 'staff':
					header('Location: ' . ROOT . '/public/staff/dashboard');
					break;
				case 'counselor':
					header('Location: ' . ROOT . '/public/counselor/dashboard');
					break;
				case 'admin':
					header('Location: ' . ROOT . '/public/admin/dashboard');
					break;
				default:
					header('Location: ' . ROOT . '/public');
			}
			exit;
		} catch (Throwable $e) {
			$this->view('login', [
				'title' => 'UCSC Help Desk - Login',
				'flash' => ['type' => 'error', 'message' => 'Login failed: ' . $e->getMessage()],
			]);
		}
	}

	public function logout()
	{
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
			header('Location: ' . ROOT . '/public/auth');
		exit;
	}
}

