<?php

class Settings extends Controller
{
    public function index()
    {
        $role = strtolower($_SESSION['user']['role'] ?? '');
        if ($role) {
            if ($role === 'counsellor') { $role = 'counselor'; }
            header('Location: /' . $role . '/settings');
            exit;
        }

        $headContent = '
        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => null,
            'roleLabel' => null,
            'roleMessage' => 'Please log in to access your settings.',
        ]);
    }
}