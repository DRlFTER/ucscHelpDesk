<?php

class Login extends Controller
{public function index()
    {
       $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/login/login.css"/>';
        $this->view('login', ['title' => 'Log in', 'head' => $headContent]);
    }
}