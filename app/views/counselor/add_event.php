<?php
// add_event.php - Save events to database
header('Content-Type: application/json');

include 'db_connect.php';

// Check if database connection exists
if (!$conn) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

try {
    // Get POST data
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $start_date = isset($_POST['start']) ? $_POST['start'] : '';
    $counselor_id = "Mrs. Nalani Perera"; // Replace with session if available
    
    // Validation
    if (empty($title)) {
        echo json_encode([
            "success" => false,
            "error" => "Event title is required"
        ]);
        exit;
    }
    
    if (empty($start_date)) {
        echo json_encode([
            "success" => false,
            "error" => "Event date is required"
        ]);
        exit;
    }
    
    // Format the date - add time if only date provided
    if (strlen($start_date) == 10) { // YYYY-MM-DD format
        $start_date = $start_date . " 10:00:00"; // Add default time
    }
    
    // Set end time to 1 hour after start
    $start_obj = new DateTime($start_date);
    $end_obj = clone $start_obj;
    $end_obj->add(new DateInterval('PT1H'));
    $end_date = $end_obj->format('Y-m-d H:i:s');
    $start_date = $start_obj->format('Y-m-d H:i:s');
    
    // Check if events table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'events'");
    if ($table_check->num_rows == 0) {
        echo json_encode([
            "success" => false,
            "error" => "Events table not found. Please create it first."
        ]);
        exit;
    }
    
    // Prepare and execute insert
    $query = "INSERT INTO events (title, start, end, counselor_id, description, color) 
              VALUES (?, ?, ?, ?, ?, '#4285f4')";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "error" => "Database prepare error: " . $conn->error
        ]);
        exit;
    }
    
    $description = "Event created by counselor";
    $stmt->bind_param("ssss", $title, $start_date, $end_date, $counselor_id, $description);
    
    if ($stmt->execute()) {
        $event_id = $stmt->insert_id;
        
        echo json_encode([
            "success" => true,
            "message" => "Event created successfully",
            "event_id" => $event_id,
            "title" => htmlspecialchars($title),
            "start" => $start_date,
            "end" => $end_date
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Database execute error: " . $stmt->error
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Exception: " . $e->getMessage()
    ]);
}