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

$sql = "SELECT t.ticket_id, t.created_at, t.title, u.name AS student_name, d.name AS category, t.status, t.priority, t.meeting_requested
  FROM tickets t
  INNER JOIN users u ON t.u_id = u.u_id
  LEFT JOIN division d ON d.did = t.division
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Assigned Tickets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./general.css">
  <script>
    // Pass tickets to JavaScript
    
  </script>
  <script src="./dashboard.js" defer></script>
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>

    <div class="controls-bar">
      <div class="search-bar">
        <img src="images/173_2471.svg" alt="Search Icon">
        <input type="text" placeholder="Search tickets, students...">
      </div>
      <div class="filters">
        <select id="status-filter" class="filter-btn">
          <option value="">All Statuses</option>
          <option value="all">All</option>
          <option value="pending">Pending</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
          <option value="assigned">Assigned</option>
        </select>
        <select id="priority-filter" class="filter-btn">
          <option value="">All Priorities</option>
          <option value="all">All</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>
      </div>
    </div>

    <div class="tickets-container"></div>
  </main>
</body>
</html>