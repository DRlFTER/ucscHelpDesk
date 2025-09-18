<?php
include 'db_connect.php';

$title = $_POST['title'];
$start = $_POST['start'];

$sql = "INSERT INTO events (title, start) VALUES ('$title', '$start')";
$conn->query($sql);
?>
