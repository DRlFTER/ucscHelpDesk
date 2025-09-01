<?php

class Login extends Controller
{public function index()
    {
       $headContent = '
        <link rel="stylesheet" href="/css/login/login.css"/>';
        $this->view('login/login', ['title' => 'Log in', 'head' => $headContent]);
    }
}