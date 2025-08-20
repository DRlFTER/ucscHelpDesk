<?php
function component($name, $data = [])
{
    // Build the path to the component file
    $path = __DIR__ . "/../views/components/{$name}.component.php";

    if (file_exists($path)) {
        // Extract variables for use inside the component
        extract($data);
        require $path;
    } else {
        throw new Exception("Component '{$name}' not found at {$path}");
    }
}
