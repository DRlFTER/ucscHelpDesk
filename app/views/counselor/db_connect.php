<?php
// /app/config/db_connect.php
$servername = "localhost";
$username   = "root";
$password   = "1234";
$dbname     = "counselor";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
?>
