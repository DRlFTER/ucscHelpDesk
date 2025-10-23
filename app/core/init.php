<?php
spl_autoload_register(function ($classname) {
    $filename = __DIR__ . '/../models/' . ucfirst($classname) . '.php';
    if (is_file($filename)) {
        require_once $filename;
    }
});

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/Database.php';
require __DIR__ . '/Model.php';
require __DIR__ . '/Controller.php';
require __DIR__ . '/App.php';
