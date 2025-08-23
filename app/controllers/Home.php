<?php

class Home extends Controller
{
    public function index()
    {
        $this->view('home', ['title' => 'UCSC Help Desk', 'head' => '']);
    }
}
