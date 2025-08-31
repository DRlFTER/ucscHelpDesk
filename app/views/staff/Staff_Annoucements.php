<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pageCSS = "global.css";
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "support_desk_my_version";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$sql = "SELECT a.id, a.topic, a.content, a.date_time, u.name AS staff_name, d.name AS division_name
        FROM announcement a
        JOIN users u ON a.u_id = u.u_id
        JOIN staff_division sd ON u.u_id = sd.u_id
        JOIN division d ON sd.d_id = d.did
        ORDER BY a.date_time DESC";
$result = $conn->query($sql);

$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();

$pageTitle = "Support Staff - Announcements";
include_once("./staff_nabar.html");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Announcements</title>
<link rel="stylesheet" href="./global.css">
<link rel="stylesheet" href="./general.css">
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Announcements</h2>
      <p class="page-subtitle">Latest updates and notices</p>
    </div>
    <div class="ticket-action" style="width:250px;justify-content: center;align-items: center;display: flex;margin-left: auto;margin-right: auto;margin-top: 20px;margin-bottom: 20px;">
        <button class="ticket-action-btn" onclick="window.location.href='./create_announcements.php'">
          <span>Create New Announcement</span>
        </button>
    </div>
    <div class="tickets-container"></div>
  </main>

  <script defer>
    const announcements = <?php echo json_encode($announcements); ?>;

    document.addEventListener("DOMContentLoaded", () => {
      const container = document.querySelector(".tickets-container");
      if (!container || !announcements) {
        console.error("Container or announcements data not found.");
        return;
      }

      container.innerHTML = announcements.map(announcement => {
        return `
          <article class="ticket-card">
            <div class="ticket-header">
              <div class="ticket-title-group">
                <h3 class="ticket-title">${(announcement.topic)}</h3>
                <div class="ticket-meta">
                  <span>${(announcement.id)}</span>
                  <span>${(announcement.date_time || 'N/A')}</span>
                </div>
              </div>
              <div class="ticket-action">
                <button class="ticket-action-btn" onclick="window.location.href='./an_view.php?id=${(announcement.id)}'">
                  <span>View Announcement</span>
                </button>
              </div>
            </div>
            <div class="ticket-body">
              <div class="details-group">
                <div class="detail-item">
                  <span class="detail-label">Staff:</span>
                  <span class="detail-value-box">${(announcement.staff_name)}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Division:</span>
                  <span class="detail-value-box">${(announcement.division_name)}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Content:</span>
                  <span class="detail-value-box">${(announcement.content)}</span>
                </div>
              </div>
            </div>
          </article>
        `;
      }).join("");
    });
  </script>
</body>
</html>