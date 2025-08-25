<?php
class App
{
    private $controller = 'Home';
    private $method = 'index';

    private function splitURL()
    {
        $URL = $_GET['url'] ?? "home";
        $URL = explode("/", trim($URL, "/"));
        return $URL;
    }

    public function loadController()
    {
        $URL = $this->splitURL();
    $controllerSegment = ucfirst($URL[0]);
    $basePath = "../app/controllers/";
    $filename = $basePath . $controllerSegment . ".php";

        /** Select Controller **/
        if (file_exists($filename)) {
            require $filename;
            $this->controller = $controllerSegment;
            unset($URL[0]);
        } else {
            // Try nested folder: e.g., controllers/student/Student.php
            $nested = $basePath . strtolower($controllerSegment) . "/" . $controllerSegment . ".php";
            if (file_exists($nested)) {
                require $nested;
                $this->controller = $controllerSegment;
                unset($URL[0]);
            } else {
                $filename = "../app/controllers/_404.php";
                require $filename;
                $this->controller = "_404";
            }
        }

        $controller = new $this->controller;

        /** Select Method **/
        if (!empty($URL[1])) {
            if (method_exists($controller, $URL[1])) {
                $this->method = $URL[1];
                unset($URL[1]);
            } else {
                // Fallback to a 404 controller
                require "../app/controllers/_404.php";
                $controller = new _404;
            }
        }

        call_user_func_array([$controller, $this->method], $URL);
    }
}
