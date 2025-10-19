<?php
// Debug endpoint to inspect announcements fetched by the Announcement model
// Access: http://kaviv1/debug/ann_test.php

require __DIR__ . '/../../app/core/init.php';
require_once __DIR__ . '/../../app/models/staff/Announcement.php';

// Ensure errors are visible in output for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$annModel = new Announcement();
$rows = $annModel->getAll();
$err = method_exists($annModel, 'getLastError') ? $annModel->getLastError() : null;

header('Content-Type: text/plain; charset=utf-8');

echo "Announcement debug\n";
echo "==================\n\n";
echo "Count: " . count($rows) . "\n";
echo "DB error: " . ($err ?: '(none)') . "\n\n";
echo "Rows:\n";
var_export($rows);

// Also show which DB constants are being used (obfuscate password)
echo "\n\nDB config (used by this app):\n";
echo "DBHOST=" . (defined('DBHOST') ? DBHOST : '(undefined)') . "\n";
echo "DBNAME=" . (defined('DBNAME') ? DBNAME : '(undefined)') . "\n";
echo "DBUSER=" . (defined('DBUSER') ? DBUSER : '(undefined)') . "\n";
