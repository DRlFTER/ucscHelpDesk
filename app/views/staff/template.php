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

$staff_id = 1; 

$errors = [];
$success = "";
$field_count = isset($_POST['field_count']) ? (int)$_POST['field_count'] : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $process = trim($_POST['process'] ?? '');
    $outcome = trim($_POST['outcome'] ?? '');
    $letter_required = isset($_POST['letter_required']) ? 1 : 0;
    $fields = [];

    // Collect dynamic fields
    for ($i = 1; $i <= $field_count; $i++) {
        $field_name = trim($_POST['field_' . $i] ?? '');
        if (!empty($field_name)) {
            $fields[] = $field_name;
        }
    }

    // Validate
    if (empty($name)) {
        $errors[] = "Template name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Template name must be 100 characters or less.";
    }

    if (empty($category)) {
        $errors[] = "Category is required.";
    } elseif (strlen($category) > 50) {
        $errors[] = "Category must be 50 characters or less.";
    }

    if (empty($process)) {
        $errors[] = "Process is required.";
    } elseif (strlen($process) > 1000) {
        $errors[] = "Process must be 1000 characters or less.";
    }

    if (empty($outcome)) {
        $errors[] = "Outcome is required.";
    } elseif (strlen($outcome) > 1000) {
        $errors[] = "Outcome must be 1000 characters or less.";
    }

    if (empty($fields)) {
        $errors[] = "At least one field is required.";
    }

    if (empty($errors)) {
        $fields_json = json_encode($fields);
        $sql = "INSERT INTO templates (name, category, fields, process, outcome, letter_required, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $category, $fields_json, $process, $outcome, $letter_required, $staff_id); // Fixed to "ssssssi"
        if ($stmt->execute()) {
            $success = "Template created successfully!";
            $name = $category = $process = $outcome = '';
            $fields = [];
            $field_count = 1;
        } else {
            $errors[] = "Failed to create template. Please try again. Error: " . $conn->error;
        }
    }
}

$conn->close();

$pageTitle = "Create Template";
include_once("./staff_nabar.html");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Create Template</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./global.css">
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

    .ticket-card {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
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
    .detail-value-box textarea,
    .detail-value-box select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
      background: transparent;
      border: none;
    }

    .detail-value-box textarea {
      resize: vertical;
      min-height: 100px;
    }

    .add-field-btn {
      background: #4a90e2;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      margin-top: 5px;
    }

    .add-field-btn:hover {
      background: #357abd;
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

    .ticket-action {
      text-align: right;
      margin-top: 15px;
    }

    .ticket-action-btn {
      background: #4a90e2;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .ticket-action-btn:hover {
      background: #357abd;
    }
  </style>
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Create Template</h2>
      <p class="page-subtitle">Define a new FAQ template for student issues</p>
    </div>
    <div class="tickets-container">
      <article class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">New Template</h3>
            <div class="ticket-meta">
              <span>Created by: <?php echo htmlspecialchars($staff_id); ?></span>
              <span>Date: <?php echo date('Y-m-d H:i:s'); ?></span>
            </div>
          </div>
        </div>
        <div class="ticket-body">
          <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
          <?php endif; ?>
          
          <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
              <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
          <?php endif; ?>

          <form method="POST" action="">
            <input type="hidden" name="field_count" value="<?php echo $field_count; ?>">
            <div class="details-group">
              <div class="detail-item">
                <span class="detail-label">Template Name:</span>
                <div class="detail-value-box">
                  <input type="text" name="name" placeholder="Enter template name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" maxlength="100">
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Category:</span>
                <div class="detail-value-box">
                  <input type="text" name="category" placeholder="Enter category (e.g., Technical Support)" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>" maxlength="50">
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Required Fields:</span>
                <div class="detail-value-box">
                  <?php for ($i = 1; $i <= $field_count; $i++): ?>
                    <input type="text" name="field_<?php echo $i; ?>" placeholder="Field name (e.g., student_id)" value="<?php echo isset($_POST['field_' . $i]) ? htmlspecialchars($_POST['field_' . $i]) : ''; ?>" style="margin-bottom: 5px;">
                  <?php endfor; ?>
                  <button type="button" class="add-field-btn" onclick="addField()">Add Another Field</button>
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Process:</span>
                <div class="detail-value-box">
                  <textarea name="process" placeholder="Enter process steps" maxlength="1000"><?php echo isset($_POST['process']) ? htmlspecialchars($_POST['process']) : ''; ?></textarea>
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Expected Outcome:</span>
                <div class="detail-value-box">
                  <textarea name="outcome" placeholder="Enter expected outcome" maxlength="1000"><?php echo isset($_POST['outcome']) ? htmlspecialchars($_POST['outcome']) : ''; ?></textarea>
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Letter Required:</span>
                <div class="detail-value-box">
                  <input type="checkbox" name="letter_required" value="1" <?php echo isset($_POST['letter_required']) ? 'checked' : ''; ?>>
                </div>
              </div>
            </div>
            <div class="ticket-action">
              <button type="submit" class="ticket-action-btn">Save Template</button>
            </div>
          </form>
        </div>
      </article>
    </div>
  </main>
  <script>
    function addField() {
      const container = document.querySelector('.detail-value-box');
      const fieldCount = container.getElementsByTagName('input').length + 1;
      const newField = document.createElement('input');
      newField.type = 'text';
      newField.name = 'field_' + fieldCount;
      newField.placeholder = 'Field name (e.g., student_id)';
      newField.style.marginBottom = '5px';
      container.insertBefore(newField, container.lastElementChild);
      document.querySelector('input[name="field_count"]').value = fieldCount;
    }
  </script>
</body>
</html>