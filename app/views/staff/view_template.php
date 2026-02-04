<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pageCSS = "global.css";
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "support_desk_my_version";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$student_id = isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : 1; 

$sql = "SELECT id, name, category, fields, process, outcome, letter_required FROM templates ORDER BY name";
$result = $conn->query($sql);
$templates = [];
while ($row = $result->fetch_assoc()) {
    $row['fields'] = json_decode($row['fields'], true);
    $templates[] = $row;
}

$errors = [];
$success = "";
$generated_letter = "";
$ticket_id = 0;
$selected_template_id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_template_id = (int)($_POST['template_id'] ?? 0);
    $template = null;
    foreach ($templates as $temp) {
        if ($temp['id'] == $selected_template_id) {
            $template = $temp;
            break;
        }
    }

    if (!$template) {
        $errors[] = "Invalid template selected.";
    } else {
        $data = [];
        foreach ($template['fields'] as $field) {
            $value = trim($_POST[$field] ?? '');
            if (empty($value)) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            } else {
                $data[$field] = $value;
            }
        }

        if (empty($errors)) {

            $letter_path = '';
            if ($template['letter_required']) {
                $html = '<html><body><h1>' . htmlspecialchars($template['name']) . '</h1>';
                $html .= '<p>Generated on ' . date('Y-m-d H:i:s') . '</p>';
                foreach ($data as $key => $value) {
                    $html .= '<p><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . htmlspecialchars($value) . '</p>';
                }
                $html .= '</body></html>';

                $upload_dir = 'C:/xampp/htdocs/UCSC/ucscHelpDesk/public/uploads/letters/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $letter_file = time() . '_letter.html';
                $letter_path = $upload_dir . $letter_file;
                file_put_contents($letter_path, $html);
            }

            $title = $template['name'] . ' Submission';
            $category = $template['category'];
            $status = 'open';
            $priority = 'medium'; 
            $student_name = 'Student Name'; 
            $meeting_requested = 'No'; 

            $sql = "INSERT INTO tickets (created_at, title, student_name, category, status, priority, meeting_requested) VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $title, $student_name, $category, $status, $priority, $meeting_requested);
            if ($stmt->execute()) {
                $ticket_id = $conn->insert_id;

                $data_json = json_encode($data);
                $sql = "INSERT INTO template_submissions (template_id, student_id, data, generated_letter, ticket_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissi", $selected_template_id, $student_id, $data_json, $letter_path, $ticket_id);
                $stmt->execute();

                $success = "Template submitted successfully as ticket ID {$ticket_id}.";
                if ($letter_path) {
                    $generated_letter = "/public/uploads/letters/" . $letter_file; // Relative URL for download
                }
            } else {
                $errors[] = "Failed to create ticket.";
            }
        }
    }
}

$conn->close();

$pageTitle = "Use Template";
include_once("./staff_nabar.html"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Use Template</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./general.css">
  <style>
    .main-content {
      padding: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .page-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .page-title {
      font-size: 24px;
      color: #333;
      margin-bottom: 10px;
    }

    .page-subtitle {
      font-size: 16px;
      color: #666;
    }

    .tickets-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .ticket-card {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 15px;
      background: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .ticket-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .ticket-title-group {
      flex-grow: 1;
    }

    .ticket-title {
      font-size: 18px;
      color: #333;
      margin: 0;
    }

    .ticket-meta {
      font-size: 12px;
      color: #666;
    }

  

    .ticket-body {
      display: none;
      padding: 10px 0;
    }

    .details-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .detail-item {
      display: flex;
      align-items: flex-start;
    }

    .detail-label {
      font-weight: bold;
      color: #444;
      width: 120px;
      margin-top: 8px;
    }

    .detail-value-box {
      flex-grow: 1;
      padding: 8px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      background: #f9f9f9;
    }

    .detail-value-box input,
    .detail-value-box textarea {
      width: 100%;
      padding: 8px;
      border: none;
      background: transparent;
      font-size: 14px;
      box-sizing: border-box;
    }

    .detail-value-box textarea {
      resize: vertical;
      min-height: 50px;
    }

    .error {
      color: red;
      font-size: 12px;
      margin-top: 5px;
      display: block;
    }

    .success {
      color: green;
      font-size: 14px;
      margin-bottom: 15px;
      text-align: center;
    }

    .template-details {
      display: none;
    }

    .active .template-details {
      display: block;
    }

    .letter-link {
      margin-top: 10px;
      color: #4a90e2;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Use Template</h2>
      <p class="page-subtitle">Select a template to submit a ticket</p>
    </div>
    <div class="tickets-container">
      <?php if (empty($templates)): ?>
        <p>No templates available.</p>
      <?php else: ?>
        <?php foreach ($templates as $template): ?>
          <article class="ticket-card" id="template-<?php echo $template['id']; ?>">
            <div class="ticket-header">
              <div class="ticket-title-group">
                <h3 class="ticket-title"><?php echo htmlspecialchars($template['name']); ?></h3>
                <div class="ticket-meta">
                  <span>Category: <?php echo htmlspecialchars($template['category']); ?></span>
                </div>
              </div>
              <div class="ticket-action">
                <button class="ticket-action-btn" onclick="toggleTemplate(<?php echo $template['id']; ?>)">Use Template</button>
              </div>
            </div>
            <div class="template-details">
              <div class="ticket-body">
                <div class="details-group">
                  <div class="detail-item">
                    <span class="detail-label">Problem:</span>
                    <div class="detail-value-box">
                      <?php echo nl2br(htmlspecialchars($template['process'])); ?> <!-- Using process as placeholder -->
                    </div>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Required Info:</span>
                    <div class="detail-value-box">
                      <?php echo nl2br(htmlspecialchars($template['process'])); ?> <!-- Using process as placeholder -->
                    </div>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Process:</span>
                    <div class="detail-value-box">
                      <?php echo nl2br(htmlspecialchars($template['process'])); ?>
                    </div>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Expected Outcome:</span>
                    <div class="detail-value-box">
                      <?php echo nl2br(htmlspecialchars($template['outcome'])); ?>
                    </div>
                  </div>
                </div>
              </div>
              <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                <div class="details-group">
                  <?php foreach ($template['fields'] as $field): ?>
                    <div class="detail-item">
                      <span class="detail-label"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $field))); ?>:</span>
                      <div class="detail-value-box">
                        <input type="text" name="<?php echo htmlspecialchars($field); ?>" placeholder="Enter <?php echo htmlspecialchars(str_replace('_', ' ', $field)); ?>">
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <div class="detail-item">
                    <span class="detail-label">Upload File:</span>
                    <div class="detail-value-box">
                      <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                  </div>
                </div>
                <div class="ticket-action">
                  <button type="submit" class="ticket-action-btn">Submit</button>
                </div>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php if ($generated_letter): ?>
          <div class="detail-item">
            <span class="detail-label">Download Letter:</span>
            <div class="detail-value-box">
              <a href="<?php echo htmlspecialchars($generated_letter); ?>" class="letter-link" target="_blank">Click here</a>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
          <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
  <script>
    function toggleTemplate(templateId) {
      const card = document.getElementById('template-' + templateId);
      card.classList.toggle('active');
    }
  </script>
</body>
</html>