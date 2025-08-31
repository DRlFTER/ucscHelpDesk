<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "support_desk_my_version";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Fetch tickets
$sql = "SELECT ticket_id, created_at, title, student_name, category, status, priority, meeting_requested
        FROM tickets
        ORDER BY created_at DESC";
$result = $conn->query($sql);

$tickets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

$conn->close();

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
  <link rel="stylesheet" href="./global.css">
  <script>
    // Pass tickets to JavaScript
    window.tickets = <?php echo json_encode($tickets); ?>;
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
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="closed">Closed</option>
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