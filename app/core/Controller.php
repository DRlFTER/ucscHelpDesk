<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);

        // Base views directory
        $baseDir = __DIR__ . "/../views/";

        // Replace dots or slashes in $view to allow subfolders
        $viewPath = $baseDir . str_replace(['.', '\\'], '/', $view) . ".view.php";

        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: {$viewPath}");
        }

        ob_start();
        require($viewPath);
        $content = ob_get_clean();

        require($baseDir . "layouts/main.php");
    }

    public function model($model)
    {
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
