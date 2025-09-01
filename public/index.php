<?php

session_start();
require '../app/core/init.php';

$timeout = 10;
$currentPath = $_SERVER['REQUEST_URI'];

if (!empty($_SESSION['user'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        header("Location: " . ROOT . "login?expired=1");
        exit;
    }

    $_SESSION['last_activity'] = time();
}

DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);
$app = new App;
$app->loadController();