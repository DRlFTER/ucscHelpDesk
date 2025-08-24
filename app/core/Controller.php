<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);

        ob_start();
        require(__DIR__ . "/../views/" . $view . ".view.php");
        $content = ob_get_clean();

        require(__DIR__ . "/../views/layouts/main.php");
    }

    public function model($model)
    {
        // Avoid re-including if the class is already loaded (prevents redeclare errors)
        if (!class_exists($model, false)) {
            $path = __DIR__ . "/../models/" . $model . ".php";
            if (file_exists($path)) {
                require_once $path;
            } else {
                throw new Exception("Model file not found: {$path}");
            }
        }
        return new $model();
    }
}

