<?php

class Admin extends Controller
{
    public function dashboard()
    {
        $this->requireLogin('admin');
        $headContent = '
        <link rel="stylesheet" href="/css/admin/adminDashboard.css"/>';
         $this->view('adminDashboard', ['title' => 'Admin Dashboard', 'head' => $headContent]);
    }
}
