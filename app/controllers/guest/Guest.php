<?php

class Guest extends Controller
{
    public function dashboard()
    {
        $headContent = '
        <link rel="stylesheet" href="/css/global/components.css" />
        <link rel="stylesheet" href="/css/guest/dashboard.css"/>';
         $this->view('dashboardGuest', ['title' => 'Guest Dashboard', 'head' => $headContent]);
    }
}
