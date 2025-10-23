<?php

class Auth extends Controller
{
	public function index()
	{
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


			$_SESSION['user'] = [
				'u_id' => (int)$user['u_id'],
				'id'   => (int)$user['u_id'],
				'email' => $user['email'],
				'role'  => $user['role'],
				'name'  => $user['name'],
			];

			$_SESSION['u_id'] = (int)$user['u_id'];
			$_SESSION['role'] = $user['role'];
			$_SESSION['name'] = $user['name'];

			if ($user['role'] === 'staff') {
				try {
					$db = Database::getInstance();
					$uid = (int)$user['u_id'];
					$divs = [];
					if ($stmt = $db->prepare('SELECT did FROM staff_division WHERE u_id = ?')) {
						$stmt->bind_param('i', $uid);
						if ($stmt->execute()) {
							$res = $stmt->get_result();
							while ($row = $res->fetch_assoc()) { $divs[] = (int)$row['did']; }
						}
						$stmt->close();
					}
					$_SESSION['user']['division_ids'] = $divs;
				} catch (Throwable $e) {
					$_SESSION['user']['division_ids'] = [];
				}
			}

				switch ($user['role']) {
					case 'student':
						header('Location: ' . ROOT . 'student/dashboard');
					break;
				case 'lecturer':
					header('Location: ' . ROOT . 'lecturer/dashboard');
					break;
				case 'staff':
					header('Location: ' . ROOT . 'staff/staffDashboard');
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

