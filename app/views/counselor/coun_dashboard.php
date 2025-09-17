<?php
// counselor_dashboard.php
// Include database connection
include 'db_connect.php';

// --- Counselor Information ---
$counselor_name = "Mrs. Nalani Perera"; // Replace with actual counselor name from session

// Dashboard Statistics
$stats = [
    'unanswered_queries' => 5,
    'tickets_assigned' => 8,
    'resolved_this_week' => 12
];

// --- Students Data (same as your original) ---
$students = [
    ["id" => "2023cs01", "name" => "Alice Perera"],
    ["id" => "2023is02", "name" => "Nimal Silva"],
    ["id" => "2023cs03", "name" => "Kavindu Fernando"],
    ["id" => "2023cs04", "name" => "Samanthi Jayasuriya"],
    ["id" => "2023is05", "name" => "Ruwantha Kumara"]
];

// --- Tickets Data (same as your original) ---
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

// Get recent tickets (limit to 3 for dashboard)
$recent_tickets = array_slice($tickets, 0, 3);

// Function to get student name by ID
function getStudentName($student_id, $students) {
    foreach ($students as $student) {
        if ($student['id'] == $student_id) {
            return $student['name'];
        }
    }
    return "Unknown Student";
}

// Announcements
$announcements = [
    [
        'title' => 'System Maintenance',
        'description' => 'Scheduled maintenance on Dec 2%, 2:00-4:00 AM',
        'type' => 'maintenance'
    ]
];

// Get upcoming events from calendar
$upcoming_events = [];
try {
    $result = $conn->query("SELECT * FROM events WHERE start >= CURDATE() ORDER BY start ASC LIMIT 3");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $upcoming_events[] = [
                'title' => $row['title'],
                'date' => date('M j', strtotime($row['start'])),
                'time' => date('g:i A', strtotime($row['start'])),
                'location' => 'Counseling Room'
            ];
        }
    }
} catch (Exception $e) {
    // Fallback data if database query fails
    $upcoming_events = [
        [
            'title' => 'Student Counseling Session',
            'date' => 'June 28',
            'time' => '2:00 PM',
            'location' => 'Room 101'
        ]
    ];
}

// If no events from database, add fallback
if (empty($upcoming_events)) {
    $upcoming_events = [
        [
            'title' => 'Student Counseling Session',
            'date' => 'June 28',
            'time' => '2:00 PM',
            'location' => 'Room 101'
        ]
    ];
}

// Function to get status class
function getStatusClass($status) {
    $classes = [
        'Pending' => 'status-pending',
        'In Progress' => 'status-progress', 
        'Resolved' => 'status-resolved',
        'Under Review' => 'status-review',
        'Rejected' => 'status-review'
    ];
    return $classes[$status] ?? 'status-default';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCSC Help Desk - Counselor Dashboard</title>
    <link rel="stylesheet" href="counselor_dashboard.css">
    
</head>
<body>
    <!-- Header -->
    <header class="header">
  <div class="logo">
    <div class="logo-icon">
      <img src="images/logo.png" alt="UCSC Logo" class="logo-image">
    </div>
    UCSC Help Desk
  </div>
  <div class="user-profile">
    <img src="images/notification.png" alt="Notifications" class="notification-icon">
    <div class="user-avatar">N</div>
    <span><?= $counselor_name ?></span>
    <span>▼</span>
  </div>
</header>


    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back, <?= explode(' ', explode('.', $counselor_name)[1] ?? $counselor_name)[0] ?>!</h1>
            <p>Here's what needs your attention today</p>
            <div class="last-activity">
                <svg class="clock-icon" viewBox="0 0 24 24">
                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z"/>
                </svg>
                <span>Last Activity: 2 hours ago</span>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['unanswered_queries'] ?></div>
                <div class="stat-label">Unanswered Queries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['tickets_assigned'] ?></div>
                <div class="stat-label">Tickets Assigned</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['resolved_this_week'] ?></div>
                <div class="stat-label">Resolved This Week</div>
            </div>
        </div>

        <div class="main-grid">
            <!-- Left Column -->
            <div>
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3 class="section-title">Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="coun_ticket.php" class="action-btn action-view">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                </svg>
                            </div>
                            View Tickets
                        </a>
                        <a href="post_announcement.php" class="action-btn action-post">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/>
                                </svg>
                            </div>
                            Post Announcement
                        </a>
                        <a href="resources.php" class="action-btn action-resources">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19,3H5C3.9,3 3,3.9 3,5V19C3,20.1 3.9,21 5,21H19C20.1,21 21,20.1 21,19V5C21,3.9 20.1,3 19,3M5,19V5H19V19H5Z"/>
                                </svg>
                            </div>
                            View Resources
                        </a>
                        <a href="coun_reports.php" class="action-btn action-reports">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M9,17H7V10H9V17M13,17H11V7H13V17M17,17H15V13H17V17Z"/>
                                </svg>
                            </div>
                            Reports
                        </a>
                        <a href="coun_calender.php" class="action-btn action-calendar" style="grid-column: 1 / -1;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/>
                                </svg>
                            </div>
                            My Calendar
                        </a>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="recent-tickets">
                    <div class="tickets-header">
                        <h3 class="section-title">Recent Tickets</h3>
                        <a href="coun_ticket.php" class="view-all-btn">View All</a>
                    </div>

                    <?php foreach ($recent_tickets as $ticket): 
                        $studentName = getStudentName($ticket['student_id'], $students);
                    ?>
                    <div class="ticket-item">
                        <div class="ticket-id"><?= $ticket['id'] ?></div>
                        <div class="ticket-info">
                            <h4><?= htmlspecialchars($ticket['title']) ?></h4>
                            <div class="ticket-student"><?= $studentName ?> • <?= $ticket['student_id'] ?></div>
                        </div>
                        <div class="ticket-date"><?= $ticket['date'] ?></div>
                        <div class="ticket-status <?= getStatusClass($ticket['status']) ?>">
                            <?= $ticket['status'] ?>
                        </div>
                        <div class="ticket-actions">
                            <a href="coun_ticket.php?id=<?= $ticket['id'] ?>" class="action-link">View Details</a>
                            <?php if ($ticket['status'] === 'Pending' || $ticket['status'] === 'Under Review'): ?>
                            <a href="respond_ticket.php?id=<?= $ticket['id'] ?>" class="action-link">Respond</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="sidebar">
                <!-- Announcements -->
                <div class="sidebar-section">
                    <h3 class="section-title">Announcements</h3>
                    <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement">
                        <div class="announcement-icon">⚙️</div>
                        <div class="announcement-content">
                            <h4><?= $announcement['title'] ?></h4>
                            <p><?= $announcement['description'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Calendar -->
                <div class="sidebar-section">
                    <h3 class="section-title">Calendar</h3>
                    <div style="color: #666; font-size: 14px; margin-bottom: 12px;">Upcoming</div>
                    <?php foreach ($upcoming_events as $event): ?>
                    <div class="calendar-event">
                        <div class="event-date"><?= $event['date'] ?></div>
                        <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                        <div class="event-details"><?= $event['location'] ?> • <?= $event['time'] ?></div>
                    </div>
                    <?php endforeach; ?>
                    <a href="coun_calender.php" style="color: #4285f4; text-decoration: none; font-size: 13px; margin-top: 12px; display: inline-block;">View Full Calendar →</a>
                </div>

                <!-- Knowledge Base -->
                <div class="sidebar-section">
                    <h3 class="section-title">Knowledge Base</h3>
                    <input type="text" class="search-box" placeholder="🔍 Search FAQ, forums, and help articles...">
                </div>

                <!-- Account -->
                <div class="sidebar-section">
                    <h3 class="section-title">Account</h3>
                    <ul class="account-menu">
                        <li>
                            <a href="profile_settings.php">
                                <div style="display: flex; align-items: center;">
                                    <svg class="menu-icon" viewBox="0 0 24 24">
                                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z"/>
                                    </svg>
                                    Profile Settings
                                </div>
                                <span>›</span>
                            </a>
                        </li>
                        <li>
                            <a href="notifications.php">
                                <div style="display: flex; align-items: center;">
                                    <svg class="menu-icon" viewBox="0 0 24 24">
                                        <path d="M12,2C13.1,2 14,2.9 14,4C14,5.1 13.1,6 12,6C10.9,6 10,5.1 10,4C10,2.9 10.9,2 12,2ZM21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 10,4C10,2.34 11.34,1 13,1S16,2.34 16,4C16,4.1 16,4.19 16,4.29C18.97,5.17 21,7.9 21,11V17L23,19H21ZM12,22C10.9,22 10,21.1 10,20H14C14,21.1 13.1,22 12,22Z"/>
                                    </svg>
                                    Notifications
                                </div>
                                <span>›</span>
                            </a>
                        </li>
                        <li>
                            <a href="ticket_history.php">
                                <div style="display: flex; align-items: center;">
                                    <svg class="menu-icon" viewBox="0 0 24 24">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                    Ticket History
                                </div>
                                <span>›</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add click handlers for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchBox = document.querySelector('.search-box');
            if (searchBox) {
                searchBox.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        // Implement search functionality
                        alert('Search functionality will be implemented');
                    }
                });
            }

            // User profile dropdown (placeholder)
            const userProfile = document.querySelector('.user-profile');
            if (userProfile) {
                userProfile.addEventListener('click', function() {
                    alert('Profile dropdown menu will be implemented');
                });
            }

            // Notification bell
            const notificationBell = document.querySelector('.bell-icon');
            if (notificationBell) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    alert('Notifications panel will be implemented');
                });
            }
        });
    </script>
</body>
</html>