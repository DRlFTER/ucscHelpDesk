<?php
require_once('../../core/config.php');
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "DELETE FROM events WHERE id=$id";
$conn->query($sql);
?>
