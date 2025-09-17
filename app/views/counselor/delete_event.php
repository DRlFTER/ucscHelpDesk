<?php
include 'db_connect.php';

$id = $_GET['id'];
$sql = "DELETE FROM events WHERE id=$id";
$conn->query($sql);
?>
