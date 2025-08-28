<?php

class Admin extends Controller
{
    public function dashboard()
    {
        $headContent = '
        <link rel="stylesheet" href="/app/views/common/css/components.css" />
        <link rel="stylesheet" href="/app/views/admin/adminDashboard.css"/>';
         $this->view('adminDashboard', ['title' => 'Admin Dashboard', 'head' => $headContent]);
    }
}
