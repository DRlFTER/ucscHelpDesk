<?php

class Student extends Controller
{
    public function dashboard()
    {
        $this->requireLogin('student');

        $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/student/dashboard.css"/>';
         $this->view('dashboardStudent', ['title' => 'Student Dashboard', 'head' => $headContent]);
    }
}