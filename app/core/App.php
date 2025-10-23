<?php
class App
{
    private $controller = 'Login'; 
    private $method = 'index'; 

    private function splitURL()
    {
        $URL = $_GET['url'] ?? "login";
        $URL = explode("/", trim($URL, "/"));
        return $URL;
    }

    private function load404()
    {
        require "../app/controllers/_404.php";
        $this->controller = "_404";
        return new _404;
    }

    public function loadController()
    {
        $URL = $this->splitURL();
        $controllerSegment = ucfirst($URL[0]);
        $basePath = "../app/controllers/";
        $filename = $basePath . $controllerSegment . ".php";

        if (file_exists($filename)) {
            require $filename;
            $this->controller = $controllerSegment;
            unset($URL[0]);
        } else {
            $nested = $basePath . strtolower($controllerSegment) . "/" . $controllerSegment . ".php";
            if (file_exists($nested)) {
                require $nested;
                $this->controller = $controllerSegment;
                unset($URL[0]);
            } else {
                $controller = $this->load404();
                call_user_func_array([$controller, $this->method], $URL);
                return;
            }
        }

        $controller = new $this->controller;

        if (!empty($URL[1])) {
            if (method_exists($controller, $URL[1])) {
                $this->method = $URL[1];
                unset($URL[1]);
            } else {
                $controller = $this->load404();
            }
        }

        call_user_func_array([$controller, $this->method], $URL);
    }
}
