<?php

class Controller
{
    public function view($view, $data = [])
    {
        // Extract variables for the view
        extract($data);

        // Capture the view content
        ob_start();
        require(__DIR__ . "/../views/" . $view . ".view.php");
        $content = ob_get_clean();

        require(__DIR__ . "/../views/layouts/main.php");
    }
}
