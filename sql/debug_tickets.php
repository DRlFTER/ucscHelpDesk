<?php
require_once __DIR__ . '/../app/core/Database.php';
$db = Database::getInstance();
$r = $db->query("SELECT * FROM ticket LIMIT 1")->fetch_assoc();
print_r($r);
