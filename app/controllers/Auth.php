<?php

class Auth extends Controller
{
	public function index()
	{
		// show login form
		$this->view('login', [
			'title' => 'UCSC Help Desk',
		]);
	}

	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: ' . ROOT . 'login');
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
				// keep both for backward compatibility
				'u_id' => (int)$user['u_id'],
				'id'   => (int)$user['u_id'],
				'email' => $user['email'],
				'role'  => $user['role'],
				'name'  => $user['name'],
			];

			// legacy session keys some views rely on
			$_SESSION['u_id'] = (int)$user['u_id'];
			$_SESSION['role'] = $user['role'];
			$_SESSION['name'] = $user['name'];

			// route by role (placeholder dashboards)
				switch ($user['role']) {
					case 'student':
						header('Location: ' . ROOT . 'student/dashboard');
					break;
				case 'lecturer':
					header('Location: ' . ROOT . 'lecturer/dashboard');
					break;
				case 'staff':
					header('Location: ' . ROOT . 'staff/dashboard');
					break;
				case 'counselor':
					header('Location: ' . ROOT . 'counselor/dashboard');
					break;
				case 'admin':
					header('Location: ' . ROOT . 'admin/dashboard');
					break;
				default:
					header('Location: ' . ROOT . '');
			}
			exit;
		} catch (Throwable $e) {
			header('Location: ' . ROOT . 'login?invalid=1');
			exit;
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
			header('Location: ' . ROOT . 'login');
		exit;
	}
}

