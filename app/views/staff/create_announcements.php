<?php

/*
with sessions 

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

// Get logged-in staff ID (replace with actual session logic)
$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : 1; // Placeholder

// Fetch divisions for the logged-in staff
$sql = "SELECT d.did, d.name 
        FROM division d
        JOIN staff_division sd ON d.did = sd.d_id
        WHERE sd.u_id = ?
        ORDER BY d.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$divisions = [];
while ($row = $result->fetch_assoc()) {
    $divisions[] = $row;
}

// Handle form submission
$errors = [];
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $division_id = (int)($_POST['division'] ?? 0);
    
    // Validate inputs
    if (empty($topic)) {
        $errors[] = "Topic is required.";
    } elseif (strlen($topic) > 50) {
        $errors[] = "Topic must be 50 characters or less.";
    }
    
    if (empty($content)) {
        $errors[] = "Content is required.";
    } elseif (strlen($content) > 500) {
        $errors[] = "Content must be 500 characters or less.";
    }
    
    if ($division_id <= 0) {
        $errors[] = "Please select a valid division.";
    }
    
    // Validate file upload
    $file = $_FILES['file'] ?? null;
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $base_upload_dir = 'C:/xampp/htdocs/UCSC/ucscHelpDesk/app/public/uploads/announcements/';
    $staff_upload_dir = $base_upload_dir . $staff_id . '/';
    $file_path = '';
    
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "File upload failed: " . $file['error'];
        } elseif (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "File size exceeds 5MB limit.";
        } else {
            if (!is_dir($staff_upload_dir)) {
                mkdir($staff_upload_dir, 0777, true); // Create staff-specific folder if it doesn't exist
            }
            $file_name = time() . '_' . basename($file['name']);
            $file_path = $staff_upload_dir . $file_name;
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    }
    
    if (empty($errors)) {
        $sql = "SELECT 1 FROM staff_division WHERE u_id = ? AND d_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $staff_id, $division_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $errors[] = "Selected division is not associated with your account.";
        } else {
            $sql = "INSERT INTO announcement (topic, content, staff_id, date_time) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $topic, $content, $staff_id);
            if ($stmt->execute()) {
                $announcement_id = $conn->insert_id;
                
                if ($file_path) {
                    $sql = "INSERT INTO announcement_files (announcement_id, file_name, file_path, file_type, file_size) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssi", $announcement_id, $file['name'], $file_path, $file['type'], $file['size']);
                    $stmt->execute();
                }
                
                $success = "Announcement created successfully!";
                $topic = $content = $division_id = '';
            } else {
                $errors[] = "Failed to create announcement. Please try again.";
            }
        }
    }
}

$conn->close();

$pageTitle = "Create Announcement";
include_once("./staff_nabar.html");
?>

*/
$pageCSS = "global.css";
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "support_desk_my_version";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 1;

$sql = "SELECT d.did, d.name 
        FROM division d
        JOIN staff_division sd ON d.did = sd.d_id
        WHERE sd.u_id = ?
        ORDER BY d.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$divisions = [];
while ($row = $result->fetch_assoc()) {
    $divisions[] = $row;
}

$errors = [];
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $division_id = (int)($_POST['division'] ?? 0);
    
    if (empty($topic)) {
        $errors[] = "Topic is required.";
    } elseif (strlen($topic) > 50) {
        $errors[] = "Topic must be 50 characters or less.";
    }
    
    if (empty($content)) {
        $errors[] = "Content is required.";
    } elseif (strlen($content) > 500) {
        $errors[] = "Content must be 500 characters or less.";
    }
    
    if ($division_id <= 0) {
        $errors[] = "Please select a valid division.";
    }
    
    $file = $_FILES['file'] ?? null;
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $base_upload_dir = 'C:/xampp/htdocs/UCSC/ucscHelpDesk/app/public/uploads/announcements/';
    $staff_upload_dir = $base_upload_dir . $staff_id . '/';
    $file_path = '';
    
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "File upload failed: " . $file['error'];
        } elseif (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "File size exceeds 5MB limit.";
        } else {
            if (!is_dir($staff_upload_dir)) {
                mkdir($staff_upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($file['name']);
            $file_path = $staff_upload_dir . $file_name;
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    }
    
    if (empty($errors)) {
        $sql = "SELECT 1 FROM staff_division WHERE u_id = ? AND d_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $staff_id, $division_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $errors[] = "Selected division is not associated with your account.";
        } else {
            $sql = "INSERT INTO announcement (topic, content, u_id, date_time) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $topic, $content, $staff_id);
            if ($stmt->execute()) {
                $announcement_id = $conn->insert_id;
                
                if ($file_path) {
                    $sql = "INSERT INTO announcement_files (announcement_id, file_name, file_path, file_type, file_size) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssi", $announcement_id, $file['name'], $file_path, $file['type'], $file['size']);
                    $stmt->execute();
                }
                echo '<script> 
        alert("Announcement created successfully");
    window.location.href = "/staff/announcements"; // Redirect after alert is closed
      </script>';
                $success = "Announcement created successfully!";
                $topic = $content = $division_id = '';


            } else {
                $errors[] = "Failed to create announcement. Please try again.";
            }
        }
    }
}

$conn->close();

$pageTitle = "Create Announcement";
include_once("./staff_nabar.html");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UCSC Help Desk - Create Announcement</title>
  <link rel="stylesheet" href="./global.css">
  <link rel="stylesheet" href="./general.css">
  <style>

   

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
      justify-content: center;
      align-items: center;
      box-sizing: border-box;
      background: transparent;
      border: none;
    }
        .response-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .response-section h3 {
        font-size: 22px;
        margin-bottom: 10px;
        font-family: "Inter", sans-serif;
    }

    .responses-list {
        max-height: 300px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding-right: 10px;
    }

    .response-item {
        background-color: var(--color-bg-card);
        border: 1px solid var(--color-border-light);
        border-radius: 8px;
        padding: 15px;
        font-family: "Inter", sans-serif;
    }

    .response-meta {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: var(--color-text-light);
        margin-bottom: 10px;
    }

    .response-text {
        font-size: 16px;
        line-height: 1.5;
        color: var(--color-text-body);
    }

.response-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }

    .response-textarea {
        width: 100%;
        min-height: 100px;
        padding: 12px;
        border: 1px solid var(--color-border-medium);
        border-radius: 8px;
        font-size: 16px;
        font-family: "Inter", sans-serif;
        resize: vertical;
        outline: none;
    }

    .response-textarea:focus {
        border-color: var(--color-primary);
    }

    .submit-response-btn {
        align-self: flex-end;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        background: var(--color-primary, #8c8cf9);
        color: #fff;
        font-family: "Poppins", sans-serif;
        font-size: 14px;
        font-weight: 400;
        cursor: pointer;
        transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
    }

    .submit-response-btn:hover {
        background-color: #6a6af5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }



  </style>
</head>
<body>
  <main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Create Announcement</h2>
      <p class="page-subtitle">Submit a new announcement for your division</p>
    </div>
    <div class="tickets-container">
      
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">New Announcement</h3>
            <div class="ticket-meta">
              <span>Staff ID: <?php echo htmlspecialchars($staff_id); ?> </span>
            </div>
          </div>
        </div>
        <div class="response-section">

          <form method="POST" action="" enctype="multipart/form-data" class="response-form">
            
                <span class="detail-label">Topic (*)</span>
                <div class="detail-value-box">
                  <input type="text" id="topic" name="topic" placeholder="Enter announcement topic" value="<?php echo isset($_POST['topic']) ? htmlspecialchars($_POST['topic']) : ''; ?>" maxlength="50">
                </div>
              
                <span class="detail-label">Content (*)</span>
                  <textarea class="response-textarea" id="content" name="content" placeholder="Enter announcement content" maxlength="500"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
  
                <span class="detail-label">Division (*) </span>
                <div class="detail-value-box">
                  <select id="division" name="division">
                    <option value="">Select a division</option>
                    <?php foreach ($divisions as $division): ?>
                      <option value="<?php echo $division['did']; ?>" <?php echo (isset($_POST['division']) && $_POST['division'] == $division['did']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($division['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              
             
                <span class="detail-label">File (*) </span>
                <div class="detail-value-box">
                  <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
              
            </div>
           
              <button type="submit" class="submit-response-btn">Submit the Announcement</button>
            
          </form>
        </div>
      </article>
    </div>
  </main>
</body>
</html>