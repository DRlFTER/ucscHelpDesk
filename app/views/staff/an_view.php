<?php
// db.php - Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "support_desk_my_version";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Get ticket_id from URL
$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ticket_id === 0) {
    die("Invalid ticket ID.");
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    $new_topic = trim($_POST['topic'] ?? '');
    $new_content = trim($_POST['content'] ?? '');

    if (empty($new_topic)) {
        $errors[] = "Topic is required.";
    } elseif (strlen($new_topic) > 50) {
        $errors[] = "Topic must be 50 characters or less.";
    }

    if (empty($new_content)) {
        $errors[] = "Content is required.";
    } elseif (strlen($new_content) > 500) {
        $errors[] = "Content must be 500 characters or less.";
    }

    if (empty($errors)) {
        $sql = "UPDATE announcement SET topic = ?, content = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_topic, $new_content, $ticket_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to refresh the page with updated data
  header("Location: /staff/announcements?ticket_id=$ticket_id");
        exit;
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ticket'])) {
    $sql = "DELETE FROM announcement WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to a list page or home page after deletion
  header("Location: /staff/announcements"); // Adjust to your list page URL
    exit;
}

// Fetch ticket details
$sql = "SELECT id, topic, content, date_time, u.name AS staff_name, d.name AS division_name
        FROM announcement a
        JOIN users u ON a.u_id = u.u_id
        JOIN staff_division sd ON u.u_id = sd.u_id
        JOIN division d ON sd.d_id = d.did
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

$ticket = $result->fetch_assoc();

if (!$ticket) {
    die("Ticket not found.");
}

// Fetch attached files
$sql = "SELECT file_name, file_path, file_type, file_size FROM announcement_files WHERE announcement_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

$conn->close();

$pageTitle = "Support Staff - Ticket Details";
include_once("./staff_nabar.html");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Announcement Details</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="./global.css">
<link rel="stylesheet" href="./general.css">
<link rel="stylesheet" href="./an_view.css">
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Announcement Details</h2>
      <p class="page-subtitle">View and manage this announcement</p>
    </div>

    <div class="ticket-detail-card">
      <div class="ticket-header">
        <div class="ticket-title-group">
          <h3 class="ticket-title"><?php echo htmlspecialchars($ticket['topic']); ?></h3>
          <div class="ticket-meta">
            <span><?php echo htmlspecialchars($ticket['id']); ?></span>
            <span><?php echo htmlspecialchars($ticket['date_time']); ?></span>
          </div>
        </div>
      </div>
      <div class="ticket-body">
        <div class="details-group">
          <div class="detail-item">
            <span class="detail-label">Author:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['staff_name']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Division:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['division_name']); ?></span>
          </div>
        </div>
      </div>
      <h3>Description</h3>
      <p>
        <?php echo htmlspecialchars($ticket['content']); ?>
      </p>

      <!-- File Section -->
      <div class="file-section">
        <h3>Attached Files</h3>
        <?php if (empty($files)): ?>
          <p>No files attached.</p>
        <?php else: ?>
          <?php foreach ($files as $file): ?>
            <div class="file-item">
              <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-link">
                <?php echo htmlspecialchars($file['file_name']); ?>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Edit Section -->
      <div class="edit-section">
        <h3>Edit Announcement</h3>
        <?php if (!empty($errors)): ?>
          <div class="error">
            <?php echo implode('<br>', $errors); ?>
          </div>
        <?php endif; ?>
        <form class="edit-form" method="POST" action="">
          <input type="text" name="topic" value="<?php echo htmlspecialchars($ticket['topic']); ?>" maxlength="50" required>
          <textarea class="edit-textarea" name="content" required><?php echo htmlspecialchars($ticket['content']); ?></textarea>
          <button type="submit" class="update-ticket-btn" name="update_ticket">Update Ticket</button>
          <button type="submit" class="delete-ticket-btn" name="delete_ticket" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete Ticket</button>
        </form>
      </div>
    </div>
  </main>
</body>
</html>