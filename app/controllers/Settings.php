<?php

class Settings extends Controller
{public function index()
    {
       $headContent = '
        <link rel="stylesheet" href="/css/settings/settings.css"/>';
        $this->view('settings', ['title' => 'Settings', 'head' => $headContent]);
    }
}