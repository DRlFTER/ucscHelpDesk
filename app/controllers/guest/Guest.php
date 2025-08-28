<?php

class Guest extends Controller
{
    public function dashboard()
    {
        $headContent = '
        <link rel="stylesheet" href="/app/views/common/css/components.css" />
        <link rel="stylesheet" href="/app/views/guest/dashboard.css"/>';
         $this->view('dashboardGuest', ['title' => 'Guest Dashboard', 'head' => $headContent]);
    }
}
