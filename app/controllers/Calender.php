<?php

class Calender extends Controller
{
	public function index()
	{
		$role = strtolower($_SESSION['user']['role'] ?? '');
		if ($role) {
			if ($role === 'counsellor') { $role = 'counselor'; }
			header('Location: /' . $role . '/calender');
			exit;
		}

		// Fallback when not logged in
		$headContent = '\n        <link rel="stylesheet" href="/css/calender/calender.css"/>';
		$this->view('calender', [
			'title' => 'Calendar',
			'head' => $headContent,
			'role' => null,
			'roleLabel' => null,
			'roleMessage' => 'Please log in to access your calendar.',
		]);
	}
}
