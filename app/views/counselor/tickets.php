<?php
// ================= DB CONNECTION =================
$host = "localhost";
$user = "root";   // change if needed
$pass = "1234";       // change if needed
$dbname = "counselor";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================= ADD NEW TICKET =================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ticket'])) {
    $student_id = $_POST['student_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];

    $sql = "INSERT INTO tickets (student_id, title, category, priority) 
            VALUES ('$student_id', '$title', '$category', '$priority')";
    $conn->query($sql);
    header("Location: tickets.php"); 
    exit;
}

// ================= FILTER LOGIC =================
$statusFilter = isset($_GET['status']) ? $_GET['status'] : "All";
$priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : "All";

$sql = "SELECT t.id, t.title, t.category, t.priority, t.status, t.date, s.name 
        FROM tickets t 
        JOIN students s ON t.student_id = s.id WHERE 1";

if ($statusFilter != "All") {
    $sql .= " AND t.status = '$statusFilter'";
}
if ($priorityFilter != "All") {
    $sql .= " AND t.priority = '$priorityFilter'";
}

$sql .= " ORDER BY t.date DESC";
$result = $conn->query($sql);

$tickets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

// ================= FETCH STUDENTS FOR FORM =================
$students = [];
$resStudents = $conn->query("SELECT * FROM students");
if ($resStudents->num_rows > 0) {
    while ($row = $resStudents->fetch_assoc()) {
        $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tickets</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
    <style>
        body { font-family: Arial, sans-serif; 
            margin:20px; 
        }
        .ticket-card {
            border: 1px solid #ccc; 
            border-radius: 20px; 
            padding: 15px; 
            margin: 10px ;
            background: #f9f9f9;
        }
        .filters, .new-ticket { margin-bottom: 20px; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 10px;
            background: #f1f1f1;
        }
        .filters select { padding: 10px; 
            margin-right: 10px; 
            border-radius: 5px; 
            border: 1px solid #ccc;
        }
        .new-ticket input, .new-ticket select { margin: 10px; 
            padding: 8px; 
            border-radius: 5px; 
            border: 1px solid #ccc;
        }
        .btn { padding: 6px 12px;
            background: #007BFF; 
            color: #fff;  
            border:none;
            border-radius:5px; 
            cursor:pointer; 
        }
    </style>
</head>
<body>
    <!-- ✅ Navbar -->
    <?php include 'coun_navbar.html'; ?>
    <header>
    <h2>Assigned Tickets</h2>
    <p>Manage and respond to your assigned student issues</p>
    </header>

    <!-- FILTERS -->
    <div class="filters">
        <form method="GET" action="">
            Status: 
            <select name="status">
                <option <?= ($statusFilter=="All"?"selected":"") ?>>All</option>
                <option <?= ($statusFilter=="Under Review"?"selected":"") ?>>Under Review</option>
                <option <?= ($statusFilter=="Resolved"?"selected":"") ?>>Resolved</option>
                <option <?= ($statusFilter=="Rejected"?"selected":"") ?>>Rejected</option>
            </select>

            Priority: 
            <select name="priority">
                <option <?= ($priorityFilter=="All"?"selected":"") ?>>All</option>
                <option <?= ($priorityFilter=="Low"?"selected":"") ?>>Low</option>
                <option <?= ($priorityFilter=="Medium"?"selected":"") ?>>Medium</option>
                <option <?= ($priorityFilter=="High"?"selected":"") ?>>High</option>
            </select>

            <button type="submit" class="btn">Apply</button>
        </form>
    </div>

    <!-- TICKET LIST -->
    <?php if (count($tickets) > 0): ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket-card">
                <h3><?= $ticket['title'] ?></h3>
                <p><b>Student:</b> <?= $ticket['name'] ?></p>
                <p><b>Category:</b> <?= $ticket['category'] ?></p>
                <p><b>Priority:</b> <?= $ticket['priority'] ?></p>
                <p><b>Status:</b> <?= $ticket['status'] ?></p>
                <p><b>Date:</b> <?= $ticket['date'] ?></p>
                <button class="btn">See Ticket</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tickets found.</p>
    <?php endif; ?>

    <!-- NEW TICKET FORM -->
    <div class="new-ticket">
        <h3>See New Ticket</h3>
        <form method="POST" action="">
            <select name="student_id" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?> (<?= $s['id'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="category" placeholder="Category" required>
            <select name="priority">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
            </select>
            <button type="submit" name="add_ticket" class="btn">See Ticket</button>
        </form>
    </div>

</body>
</html>
