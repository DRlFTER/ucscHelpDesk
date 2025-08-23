<?php
// --- Temporary Student List (later replace with DB query) ---
$students = [
    ["id" => 1, "name" => "Alice Perera"],
    ["id" => 2, "name" => "Nimal Silva"],
    ["id" => 3, "name" => "Kavindu Fernando"],
];

// --- Temporary Tickets (later replace with DB query, linked by student_id) ---
$tickets = [
    [
        "student_id" => 1,
        "title" => "Struggling with Exam Stress",
        "category" => "Mental Health",
        "description" => "Student feels anxious during exams and seeks coping strategies."
    ],
    [
        "student_id" => 2,
        "title" => "Homesickness Issue",
        "category" => "Personal Support",
        "description" => "First-year student struggling with being away from home."
    ],
    [
        "student_id" => 1,
        "title" => "Sleep Problems",
        "category" => "Mental Health",
        "description" => "Difficulty sleeping due to workload and stress."
    ],
    [
        "student_id" => 3,
        "title" => "Time Management",
        "category" => "Personal Support",
        "description" => "Needs guidance on balancing studies and part-time work."
    ]
];

// --- Selected Student Filter ---
$selectedStudent = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Counselor Tickets | UCSC Help Desk</title>
  <link rel="stylesheet" href="../common/css/components.css">
  <link rel="stylesheet" href="coun.css">
  
</head>
<body>
  <!-- ✅ Reuse Navbar -->
  <?php include 'coun_navbar.html'; ?>

  <!-- ✅ Student Filter -->
  <div class="filter-box">
    <form method="GET" action="">
      <label for="student">Select Student:</label>
      <select name="student_id" id="student" onchange="this.form.submit()">
        <option value="">-- All Students --</option>
        <?php foreach ($students as $stu): ?>
          <option value="<?= $stu['id'] ?>" <?= ($selectedStudent == $stu['id']) ? 'selected' : '' ?>>
            <?= $stu['name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <!-- ✅ Ticket Section -->
  <div class="ticket-container">
    <?php
    $hasTickets = false;
    foreach ($tickets as $ticket):
        if ($selectedStudent && $ticket['student_id'] != $selectedStudent) continue;
        $hasTickets = true;
    ?>
      <div class="ticket-card">
        <div class="ticket-title"><?= htmlspecialchars($ticket['title']) ?></div>
        <div class="ticket-category"><?= htmlspecialchars($ticket['category']) ?></div>
        <p><?= htmlspecialchars($ticket['description']) ?></p>
        <div class="ticket-actions">
          <button class="btn btn-secondary">View</button>
          <button class="btn btn-primary">Handle</button>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (!$hasTickets): ?>
      <p style="padding:20px;">No tickets found for this student.</p>
    <?php endif; ?>
  </div>
</body>
</html>
