<?php
// --- Temporary Students (replace later with DB query) ---
$students = [
    ["id" => "2023cs01", "name" => "Alice Perera"],
    ["id" => "2023is02", "name" => "Nimal Silva"],
    ["id" => "2023cs03", "name" => "Kavindu Fernando"],
    ["id" => "2023cs04", "name" => "Samanthi Jayasuriya"],
    ["id" => "2023is05", "name" => "Ruwantha Kumara"]
];

// --- Temporary Tickets (replace later with DB query) ---
$tickets = [
    [
        "id" => "1",
        "student_id" => "2023cs01",
        "title" => "Struggling with Exam Stress",
        "category" => "Mental Health",
        "priority" => "High",
        "status" => "Under Review",
        "date" => "Jan 12, 2024"
    ],
    [
        "id" => "2",
        "student_id" => "2023is02",
        "title" => "Homesickness Issue",
        "category" => "Personal Support",
        "priority" => "Medium",
        "status" => "Resolved",
        "date" => "Jan 10, 2024"
    ],
    [
        "id" => "3",
        "student_id" => "2023cs03",
        "title" => "Sleep Problems",
        "category" => "Mental Health",
        "priority" => "Low",
        "status" => "Rejected",
        "date" => "Jan 9, 2024"
    ],
    [
        "id" => "4",
        "student_id" => "2023cs04",
        "title" => "Time Management",
        "category" => "Personal Support",
        "priority" => "Medium",
        "status" => "Resolved",
        "date" => "Jan 8, 2024"
    ],
    [
        "id" => "5",
        "student_id" => "2023is05",
        "title" => "Anxiety Issues",
        "category" => "Mental Health",
        "priority" => "High",
        "status" => "Under Review",
        "date" => "Jan 7, 2024"
    ]
];

// --- Status Colors ---
function getStatusClass($status) {
    $map = [
        "Under Review" => "status underReview",
        "Resolved" => "status resolved",
        "Rejected" => "status rejected",
        "Pending" => "status pending",
        "In Progress" => "status progress",
        "Closed" => "status closed",
        "Open" => "status open",
        "Cancelled" => "status cancelled",
        "Completed" => "status completed",
        "Requested" => "status requested"
    ];
    return $map[$status] ?? "status default";
}

// --- Priority Colors ---
function getPriorityClass($priority) {
    $map = [
        "High" => "priority high",
        "Medium" => "priority medium",
        "Low" => "priority low"
    ];
    return $map[$priority] ?? "priority default";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Counselor Assigned Tickets</title>
  <link rel="stylesheet" href="../common/css/components.css">
  <link rel="stylesheet" href="coun.css">

</head>
<body>

  <!-- ✅ Navbar -->
  <?php include 'coun_navbar.html'; ?>

  <header>
    <h2>Assigned Student Issues</h2>
    <p>These are the tickets assigned to you for counseling.</p>
  </header>

  <!-- ✅ Ticket Cards -->
  <div class="ticket-container">
    <?php foreach ($tickets as $ticket): 
      $studentName = "";
      foreach ($students as $stu) {
        if ($stu['id'] == $ticket['student_id']) {
          $studentName = $stu['name']; break;
        }
      }
    ?>
      <div class="ticket-card">
        <div class="ticket-header">
          <div>
            <div class="ticket-title"><?= htmlspecialchars($ticket['title']) ?></div>
            <div class="ticket-sub"><?= $ticket['id'] ?> | <?= $ticket['date'] ?></div>
          </div>
          <div class="<?= getStatusClass($ticket['status']) ?>">
            <?= $ticket['status'] ?>
          </div>
        </div>
        
        <div><strong>Student:</strong> <?= $studentName ?></div>
        <div><strong>Category:</strong> <?= htmlspecialchars($ticket['category']) ?></div>
        <div><strong>Priority:</strong> 
          <span class="<?= getPriorityClass($ticket['priority']) ?>">
            <?= $ticket['priority'] ?>
          </span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>
</html>
