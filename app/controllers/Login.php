<?php

class Login extends Controller
{public function index()
    {
       $headContent = '
        <link rel="stylesheet" href="/app/views/common/css/components.css" />
        <link rel="stylesheet" href="/app/views/login/login.css"/>';
        $this->view('login', ['title' => 'Log in', 'head' => $headContent]);
    }
}