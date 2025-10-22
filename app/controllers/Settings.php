<?php

class Settings extends Controller
{
    public function index()
    {
        // If logged in, route to the role-based URL pattern: /{role}/settings
        $role = strtolower($_SESSION['user']['role'] ?? '');
        if ($role) {
            // Normalize counselor spelling
            if ($role === 'counsellor') { $role = 'counselor'; }
            header('Location: /' . $role . '/settings');
            exit;
        }

        // Fallback: unauthenticated users see generic settings (unlikely)
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