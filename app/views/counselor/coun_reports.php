<?php
// coun_reports.php
session_start();
require_once('../../core/config.php');
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$counselor_name = "Mrs. Nalani Perera"; // Replace with session data

// Date range for reports
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today
$report_type = $_GET['report_type'] ?? 'overview';

// Sample data (replace with actual database queries)
$students = [
    ["id" => "2023cs01", "name" => "Alice Perera"],
    ["id" => "2023is02", "name" => "Nimal Silva"],
    ["id" => "2023cs03", "name" => "Kavindu Fernando"],
    ["id" => "2023cs04", "name" => "Samanthi Jayasuriya"],
    ["id" => "2023is05", "name" => "Ruwantha Kumara"]
];

$tickets = [
    [
        "id" => "1",
        "student_id" => "2023cs01",
        "title" => "Struggling with Exam Stress",
        "category" => "Mental Health",
        "priority" => "High",
        "status" => "Under Review",
        "date" => "2024-01-12",
        "resolved_date" => null
    ],
    [
        "id" => "2",
        "student_id" => "2023is02",
        "title" => "Homesickness Issue",
        "category" => "Personal Support",
        "priority" => "Medium",
        "status" => "Resolved",
        "date" => "2024-01-10",
        "resolved_date" => "2024-01-15"
    ],
    [
        "id" => "3",
        "student_id" => "2023cs03",
        "title" => "Sleep Problems",
        "category" => "Mental Health",
        "priority" => "Low",
        "status" => "Rejected",
        "date" => "2024-01-09",
        "resolved_date" => "2024-01-14"
    ],
    [
        "id" => "4",
        "student_id" => "2023cs04",
        "title" => "Time Management",
        "category" => "Personal Support",
        "priority" => "Medium",
        "status" => "Resolved",
        "date" => "2024-01-08",
        "resolved_date" => "2024-01-13"
    ],
    [
        "id" => "5",
        "student_id" => "2023is05",
        "title" => "Anxiety Issues",
        "category" => "Mental Health",
        "priority" => "High",
        "status" => "Under Review",
        "date" => "2024-01-07",
        "resolved_date" => null
    ]
];

// Calculate statistics
$total_tickets = count($tickets);
$resolved_tickets = count(array_filter($tickets, fn($t) => $t['status'] === 'Resolved'));
$pending_tickets = count(array_filter($tickets, fn($t) => in_array($t['status'], ['Under Review', 'Pending'])));
$rejected_tickets = count(array_filter($tickets, fn($t) => $t['status'] === 'Rejected'));

// Category statistics
$category_stats = [];
foreach ($tickets as $ticket) {
    $category = $ticket['category'];
    if (!isset($category_stats[$category])) {
        $category_stats[$category] = 0;
    }
    $category_stats[$category]++;
}

// Priority statistics
$priority_stats = [];
foreach ($tickets as $ticket) {
    $priority = $ticket['priority'];
    if (!isset($priority_stats[$priority])) {
        $priority_stats[$priority] = 0;
    }
    $priority_stats[$priority]++;
}

// Monthly trend data
$monthly_data = [
    'September' => ['total' => 12, 'resolved' => 8, 'pending' => 4],
    'October' => ['total' => 18, 'resolved' => 14, 'pending' => 4],
    'November' => ['total' => 22, 'resolved' => 16, 'pending' => 6],
    'December' => ['total' => 15, 'resolved' => 10, 'pending' => 5],
    'January' => ['total' => count($tickets), 'resolved' => $resolved_tickets, 'pending' => $pending_tickets]
];

// Response time analysis
$avg_response_time = 2.5; // days
$avg_resolution_time = 5.8; // days

// Student engagement stats
$student_engagement = [
    'active_students' => 45,
    'new_students' => 12,
    'returning_students' => 28,
    'satisfaction_rate' => 87
];

function getStudentName($student_id, $students) {
    foreach ($students as $student) {
        if ($student['id'] == $student_id) {
            return $student['name'];
        }
    }
    return "Unknown Student";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counseling Reports - UCSC Help Desk</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
    <link rel="stylesheet" href="counselor_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .reports-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .filter-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .filter-input, .filter-select {
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-btn {
            background: #4285f4;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .export-btn {
            background: #34a853;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4285f4, #34a853);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .positive { background: #e8f5e8; color: #2e7d32; }
        .negative { background: #ffebee; color: #c62828; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .detailed-reports {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .report-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e1e5e9;
        }

        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .table-content {
            max-height: 400px;
            overflow-y: auto;
        }

        .report-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .report-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .report-table td {
            font-size: 14px;
            color: #333;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-resolved { background: #e8f5e8; color: #2e7d32; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-review { background: #cce5ff; color: #0056b3; }
        .status-rejected { background: #ffebee; color: #c62828; }

        .priority-high { color: #d32f2f; font-weight: 600; }
        .priority-medium { color: #f57c00; font-weight: 600; }
        .priority-low { color: #388e3c; font-weight: 600; }

        .insights-panel {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .insights-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .insight-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #4285f4;
        }

        .insight-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .insight-description {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .trend-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .trend-up { color: #34a853; }
        .trend-down { color: #ea4335; }

        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .detailed-reports {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'coun_navbar.html'; ?>

    <header>
        <h2>📊 Counseling Reports & Analytics</h2>
        <p>Track performance, analyze trends, and generate insights</p>
    </header>

    <div class="reports-container">
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="filter-input" value="<?= $start_date ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="filter-input" value="<?= $end_date ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="report_type">Report Type</label>
                        <select id="report_type" name="report_type" class="filter-select">
                            <option value="overview" <?= $report_type === 'overview' ? 'selected' : '' ?>>Overview</option>
                            <option value="detailed" <?= $report_type === 'detailed' ? 'selected' : '' ?>>Detailed Analysis</option>
                            <option value="trends" <?= $report_type === 'trends' ? 'selected' : '' ?>>Trend Analysis</option>
                            <option value="performance" <?= $report_type === 'performance' ? 'selected' : '' ?>>Performance Metrics</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="filter-btn">📊 Generate Report</button>
                    </div>
                    
                    <div class="filter-group">
                        <a href="export_report.php?type=<?= $report_type ?>&start=<?= $start_date ?>&end=<?= $end_date ?>" class="export-btn">
                            📥 Export PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" style="color: #4285f4;"><?= $total_tickets ?></div>
                <div class="stat-label">Total Tickets</div>
                <div class="stat-change positive">+12% from last month</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: #34a853;"><?= $resolved_tickets ?></div>
                <div class="stat-label">Resolved Cases</div>
                <div class="stat-change positive">+8% resolution rate</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: #ff9800;"><?= $pending_tickets ?></div>
                <div class="stat-label">Pending Cases</div>
                <div class="stat-change negative">-5% from last week</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: #9c27b0;"><?= number_format($avg_response_time, 1) ?></div>
                <div class="stat-label">Avg Response Time (days)</div>
                <div class="stat-change positive">-0.3 days improved</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: #f44336;"><?= $rejected_tickets ?></div>
                <div class="stat-label">Rejected Cases</div>
                <div class="stat-change positive">-2% rejection rate</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: #607d8b;"><?= $student_engagement['satisfaction_rate'] ?>%</div>
                <div class="stat-label">Satisfaction Rate</div>
                <div class="stat-change positive">+3% this month</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">Monthly Ticket Trends</div>
                <div class="chart-container">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Categories Distribution</div>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Priority Levels</div>
                <div class="chart-container">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Resolution Status</div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="detailed-reports">
            <div class="report-table">
                <div class="table-header">
                    <div class="table-title">Recent Tickets Overview</div>
                </div>
                <div class="table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Issue</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= $ticket['id'] ?></td>
                                <td><?= getStudentName($ticket['student_id'], $students) ?></td>
                                <td><?= htmlspecialchars($ticket['title']) ?></td>
                                <td><?= $ticket['category'] ?></td>
                                <td><span class="priority-<?= strtolower($ticket['priority']) ?>"><?= $ticket['priority'] ?></span></td>
                                <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $ticket['status'])) ?>"><?= $ticket['status'] ?></span></td>
                                <td><?= date('M j, Y', strtotime($ticket['date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="insights-panel">
                <div class="insights-title">📈 Key Insights</div>
                
                <div class="insight-item">
                    <div class="insight-title">Mental Health Cases Rising</div>
                    <div class="insight-description">Mental health related tickets have increased by 15% this month, indicating growing student stress levels.</div>
                    <div class="trend-indicator trend-up">↗️ Trending Up</div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-title">Faster Response Times</div>
                    <div class="insight-description">Average response time has improved from 2.8 to 2.5 days, showing better efficiency.</div>
                    <div class="trend-indicator trend-down">⏱️ Improved</div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-title">High Priority Focus Needed</div>
                    <div class="insight-description">40% of tickets are high priority, suggesting need for proactive intervention strategies.</div>
                    <div class="trend-indicator trend-up">⚠️ Attention Required</div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-title">Student Satisfaction</div>
                    <div class="insight-description">87% satisfaction rate indicates good service quality with room for improvement.</div>
                    <div class="trend-indicator trend-up">👍 Good Performance</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Monthly Trend Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(monthlyTrendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($monthly_data)) ?>,
                datasets: [{
                    label: 'Total Tickets',
                    data: <?= json_encode(array_column($monthly_data, 'total')) ?>,
                    borderColor: '#4285f4',
                    backgroundColor: 'rgba(66, 133, 244, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Resolved',
                    data: <?= json_encode(array_column($monthly_data, 'resolved')) ?>,
                    borderColor: '#34a853',
                    backgroundColor: 'rgba(52, 168, 83, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($category_stats)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($category_stats)) ?>,
                    backgroundColor: [
                        '#4285f4',
                        '#34a853',
                        '#fbbc04',
                        '#ea4335',
                        '#9c27b0',
                        '#ff9800'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Priority Levels Chart
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($priority_stats)) ?>,
                datasets: [{
                    label: 'Number of Tickets',
                    data: <?= json_encode(array_values($priority_stats)) ?>,
                    backgroundColor: ['#ea4335', '#fbbc04', '#34a853']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Resolved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [<?= $resolved_tickets ?>, <?= $pending_tickets ?>, <?= $rejected_tickets ?>],
                    backgroundColor: ['#34a853', '#fbbc04', '#ea4335']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Auto-refresh functionality
        function refreshData() {
            // Implementation for real-time data refresh
            console.log('Refreshing report data...');
        }

        // Refresh every 5 minutes
        setInterval(refreshData, 300000);

        // Print functionality
        function printReport() {
            window.print();
        }

        // Add print button dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('header');
            const printBtn = document.createElement('button');
            printBtn.innerHTML = '🖨️ Print Report';
            printBtn.className = 'export-btn';
            printBtn.onclick = printReport;
            printBtn.style.marginLeft = '10px';
            header.appendChild(printBtn);
        });
    </script>
</body>
</html>