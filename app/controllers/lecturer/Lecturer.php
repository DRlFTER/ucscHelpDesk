<?php

class Lecturer extends Controller
{
    public function settings()
    {
        // Using lecturer role even if lecturer area is a placeholder
        $this->requireLogin('lecturer');
        $headContent = '\n        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', [
            'title' => 'Settings',
            'head' => $headContent,
            'role' => 'lecturer',
            'roleLabel' => 'Lecturer',
            'roleMessage' => 'Lecturer settings: control notifications and profile (dummy content).',
        ]);
    }

    public function dashboard()
    {
        echo '<h1>Lecturer Dashboard (placeholder)</h1>';
    }
}
