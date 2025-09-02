<?php
// Include the config.php file
require_once('../../core/config.php');

// Database connection using settings from config.php
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Fetch tickets (e.g., only raised by students)
session_start();
$staff_id = isset($_SESSION['u_id']) ? (int)$_SESSION['u_id'] : 0; // Assuming staff is logged in with u_id

$sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, t.category, t.status, t.priority, t.meeting_requested
        FROM tickets t
        INNER JOIN users u ON t.u_id = u.u_id
        ORDER BY t.created_at DESC";

$result = $conn->query($sql);

$tickets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
} else {
    // Debug: Log if no tickets are found
    error_log("No tickets found at " . date('Y-m-d H:i:s') . " for staff_id: " . $staff_id . ". Query: " . $sql);
}

$pageTitle = "Support Staff - Ticket Dashboard";
include_once("./staff_nabar.html");
?>