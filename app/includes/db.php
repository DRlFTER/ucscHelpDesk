<?php 
 $host = 'localhost';
 $user = 'root';
 $pass = '';
 $dbName = 'ucscHelpDesk';

 $conn = new mysqli($host, $user, $pass, $dbName);
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
 }
?>