<?php

class Login extends Controller
{public function index()
    {
       $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/login/login.css"/>';
        // Explicitly use the login view under views/login/
        $this->view('login/login', ['title' => 'Log in', 'head' => $headContent]);
    }
}