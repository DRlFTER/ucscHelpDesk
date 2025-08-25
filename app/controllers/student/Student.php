<?php

class Student extends Controller
{
    public function dashboard()
    {
        $headContent = '
        <link rel="stylesheet" href="/app/views/common/css/components.css" />
        <link rel="stylesheet" href="/app/views/dashboard.css"/>';
         $this->view('dashboardStudent', ['title' => 'Student Dashboard', 'head' => $headContent]);
    }
}
