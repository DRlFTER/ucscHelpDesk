<?php
// Example tickets array (later you can fetch from DB)
$tickets = [
    [
        "id" => 1,
        "title" => "Mental Health Support",
        "description" => "Student experiencing anxiety before exams.",
        "status" => "Pending",
        "type" => "Mental Health"
    ],
    [
        "id" => 2,
        "title" => "Personal Issue",
        "description" => "Difficulty managing time between studies and part-time job.",
        "status" => "In Progress",
        "type" => "Personal Support"
    ],
    [
        "id" => 3,
        "title" => "Counseling Request",
        "description" => "Needs regular sessions for stress management.",
        "status" => "Resolved",
        "type" => "Mental Health"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Counselor Tickets | UCSC Help Desk</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background:#f9f9f9; }
        .page-container { padding: 20px; }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'coun_navbar.html'; ?>

    <!-- PAGE CONTENT -->
    <div class="page-container">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Counselor Support Tickets</h1>
        
        <!-- Cards Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($tickets as $ticket): ?>
                <div class="bg-white rounded-xl shadow-md p-5 border-l-4 
                    <?= $ticket['type'] === "Mental Health" ? "border-blue-500" : "border-green-500" ?>">
                    
                    <h2 class="text-lg font-semibold text-gray-900"><?= $ticket['title'] ?></h2>
                    <p class="text-gray-600 mt-2"><?= $ticket['description'] ?></p>
                    
                    <span class="mt-3 inline-block px-3 py-1 text-sm rounded-full 
                        <?= $ticket['status'] === "Pending" ? "bg-yellow-200 text-yellow-800" : 
                            ($ticket['status'] === "In Progress" ? "bg-blue-200 text-blue-800" : "bg-green-200 text-green-800") ?>">
                        <?= $ticket['status'] ?>
                    </span>

                    <div class="mt-4 flex justify-end gap-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Handle</button>
                        <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">View</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
