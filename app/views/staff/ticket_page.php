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
$ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;

if ($ticket_id === 0) {
    die("Invalid ticket ID.");
}

// Fetch ticket details
$sql = "SELECT ticket_id, created_at, title, student_name, category, status, priority, meeting_requested
        FROM tickets
        WHERE ticket_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

$ticket = $result->fetch_assoc();

if (!$ticket) {
    die("Ticket not found.");
}

$pageTitle = "Support Staff - Ticket Details";
include_once("./staff_nabar.html");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Ticket Details</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./global.css">
  <style>
    /* Reuse styles from dashboard for consistency */
    .main-content {
        padding: 45px 84px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 35px;
        font-weight: 500;
        margin: 0 0 6px 0;
    }

    .page-subtitle {
        font-size: 25px;
        font-weight: 400;
        color: var(--color-text-body);
        margin: 0;
        letter-spacing: 0.5px;
    }

    .ticket-detail-card {
        background-color: var(--color-bg-card);
        border: 1px solid var(--color-border-card);
        border-radius: 15px;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 10px;
    }

    .ticket-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ticket-title {
        font-size: 28px;
        font-weight: 500;
        letter-spacing: 0.56px;
        margin: 0;
    }

    .ticket-meta {
        display: flex;
        gap: 36px;
        font-size: 16px;
        color: var(--color-text-light);
        letter-spacing: 0.32px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.28px;
        white-space: nowrap;
    }

    .status-review { background-color: var(--status-review-bg); color: var(--status-review-text); }
    .status-resolved { background-color: var(--status-resolved-bg); color: var(--status-resolved-text); }
    .status-rejected { background-color: var(--status-rejected-bg); color: var(--status-rejected-text); }

    .ticket-body {
        display: flex;
        gap: 30px;
        padding: 15px;
        flex-wrap: wrap;
    }

    .details-group {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .details-group.separator {
        padding-left: 30px;
        border-left: 1px solid var(--color-border-separator);
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 200px;
    }

    .detail-label {
        font-size: 20px;
        font-weight: 400;
        letter-spacing: 0.4px;
    }

    .detail-value-box {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 400;
        letter-spacing: 0.32px;
        background-color: #ececec;
        color: var(--color-text-light);
    }

    .value-requested { background-color: #d8ebff; }
    .value-priority-high { background-color: var(--priority-high-bg); color: var(--priority-high-text); }
    .value-priority-medium { background-color: var(--priority-medium-bg); color: var(--priority-medium-text); }
    .value-priority-low { background-color: var(--priority-low-bg); color: var(--priority-low-text); }

    /* Additional sections for more details */
    .ticket-description {
        margin-top: 20px;
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 10px;
    }

    .ticket-description h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .ticket-description p {
        font-size: 16px;
        line-height: 1.5;
        color: var(--color-text-body);
    }

    .actions-bar {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 20px;
    }

    .action-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.25s ease;
    }

    .resolve-btn {
        background-color: #4caf50;
        color: white;
    }

    .resolve-btn:hover {
        background-color: #388e3c;
    }

    .reject-btn {
        background-color: #f44336;
        color: white;
    }

    .reject-btn:hover {
        background-color: #d32f2f;
    }

    @media (max-width: 992px) {
        .main-content {
            padding: 30px 20px;
        }
        .ticket-body {
            flex-direction: column;
            gap: 20px;
        }
        .details-group.separator {
            border-left: none;
            padding-left: 0;
            padding-top: 20px;
            border-top: 1px solid var(--color-border-separator);
        }
    }

    @media (max-width: 768px) {
        .page-title { font-size: 28px; }
        .page-subtitle { font-size: 18px; }
        .ticket-header { flex-direction: column; align-items: flex-start; }
        .ticket-header-right { width: 100%; justify-content: flex-end; }
        .details-group { flex-direction: column; gap: 15px; }
        .actions-bar { flex-direction: column; }
    }
  </style>
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Ticket Details</h2>
      <p class="page-subtitle">View and manage this ticket</p>
    </div>

    <div class="ticket-detail-card">
      <div class="ticket-header">
        <div class="ticket-title-group">
          <h3 class="ticket-title"><?php echo htmlspecialchars($ticket['title']); ?></h3>
          <div class="ticket-meta">
            <span><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
            <span><?php echo htmlspecialchars($ticket['created_at']); ?></span>
          </div>
        </div>
        <div class="ticket-header-right">
          <span class="status-badge status-<?php echo htmlspecialchars($ticket['status']); ?>">
            <?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>
          </span>
        </div>
      </div>
      <div class="ticket-body">
        <div class="details-group">
          <div class="detail-item">
            <span class="detail-label">Student:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Category:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['category']); ?></span>
          </div>
        </div>
        <div class="details-group separator">
          <?php if ($ticket['meeting_requested']): ?>
            <div class="detail-item">
              <span class="detail-label">Meeting:</span>
              <span class="detail-value-box value-requested"><?php echo htmlspecialchars($ticket['meeting_requested']); ?></span>
            </div>
          <?php endif; ?>
          <div class="detail-item">
            <span class="detail-label">Priority:</span>
            <span class="detail-value-box value-priority-<?php echo htmlspecialchars($ticket['priority']); ?>">
              <?php echo ucfirst(htmlspecialchars($ticket['priority'])); ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Placeholder for additional details like description -->
      <!-- If your DB has a 'description' field, add it to the SQL query and display here -->
      <div class="ticket-description">
        <h3>Description</h3>
        <p>
          <!-- Replace with actual description if available in DB -->
          This is a placeholder for the ticket description. If your database has a 'description' field, update the SQL query to include it and echo it here.
        </p>
      </div>

      <!-- Actions bar for managing the ticket -->
      <div class="actions-bar">
        <button class="action-btn resolve-btn">Resolve Ticket</button>
        <button class="action-btn reject-btn">Reject Ticket</button>
      </div>
    </div>
  </main>
</body>
</html>