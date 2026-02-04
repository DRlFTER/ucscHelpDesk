<?php

class _404 extends Controller
{
    public function index()
    {
        // If user is not logged in, redirect to login
        if (!isset($_SESSION['user'])) {
            header("Location: " . ROOT . "login");
            exit();
        }
        
        // Set proper HTTP 404 status code
        http_response_code(404);
        
        // Determine homepage based on user role
        $role = strtolower($_SESSION['user']['role'] ?? 'guest');
        $homePages = [
            'admin' => '/admin/dashboard',
            'student' => '/student/dashboard',
            'staff' => '/staff/staffDashboard',
            'counselor' => '/counselor/dashboard',
            'lecturer' => '/lecturer/dashboard',
            'guest' => '/guest/dashboard',
        ];
        $homePage = $homePages[$role] ?? '/login';
        
        $head = '<link rel="stylesheet" href="/css/404/404.css">';
        $this->view('404', [
            'title' => 'Page Not Found | UCSC Help Desk', 
            'head' => $head,
            'homePage' => $homePage
        ]);
    }
}
