<?php
require_once('../../core/config.php');
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM events";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end'   => $row['end']
    ];
}
echo json_encode($events);
?>
