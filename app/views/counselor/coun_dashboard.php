<?php
// --- Temporary Data (replace with actual database queries) ---
$counselor_name = "Brian Weerasinghe"; // Replace with actual counselor name from session

// Dashboard Statistics
$stats = [
    'unanswered_queries' => 5,
    'tickets_assigned' => 8,
    'resolved_this_week' => 12
];

// Recent Tickets Data
$recent_tickets = [
    [
        'id' => 'ST190847',
        'title' => 'Database Query Issue',
        'description' => 'Student needs assistance with database queries',
        'date' => 'June 28, 2025',
        'status' => 'Pending',
        'student' => 'P.A.K.N. Pethiyagoda',
        'student_id' => '230067234'
    ],
    [
        'id' => 'ST190848', 
        'title' => 'Algorithms Explanation',
        'description' => 'Binary search implementation help needed',
        'date' => 'June 25, 2025',
        'status' => 'In Progress',
        'student' => 'K.M.K.S. Altikarathna',
        'student_id' => '230053432'
    ],
    [
        'id' => 'ST190849',
        'title' => 'Lab Equipment Access',
        'description' => 'Server room permission request',
        'date' => 'June 24, 2025', 
        'status' => 'Pending',
        'student' => 'L.A.T.M. Malalasena',
        'student_id' => '230046738'
    ]
];

// Announcements
$announcements = [
    [
        'title' => 'System Maintenance',
        'description' => 'Scheduled maintenance on Dec 2%, 2:00-4:00 AM',
        'type' => 'maintenance',
        'icon' => '⚙️'
    ]
];

// Calendar Events
$calendar_events = [
    [
        'title' => 'Meeting with Mr. Prasad',
        'location' => 'at WCC1',
        'time' => '8:00 PM',
        'date' => 'June 28'
    ]
];

// Function to get status class
function getStatusClass($status) {
    $classes = [
        'Pending' => 'status-pending',
        'In Progress' => 'status-progress', 
        'Resolved' => 'status-resolved',
        'Under Review' => 'status-review'
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Header */
        .header {
            background: white;
            padding: 12px 24px;
            border-bottom: 1px solid #e1e5e9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #4285f4;
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: #4285f4;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background-color 0.2s;
        }

        .user-profile:hover {
            background-color: #f1f3f4;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ff4444;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .welcome-section {
            margin-bottom: 32px;
        }

        .welcome-section h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .welcome-section p {
            color: #666;
            margin-bottom: 4px;
        }

        .last-activity {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .stat-card:nth-child(1) .stat-number { color: #4285f4; }
        .stat-card:nth-child(2) .stat-number { color: #ff9800; }
        .stat-card:nth-child(3) .stat-number { color: #4caf50; }

        .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .action-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .action-new { background: #4285f4; color: white; }
        .action-view { background: #4caf50; color: white; }
        .action-post { background: #9c27b0; color: white; }
        .action-resources { background: #f44336; color: white; }
        .action-reports { background: #ffeb3b; color: #333; }
        .action-students { background: #e91e63; color: white; }

        .action-new .action-icon { background: rgba(255,255,255,0.2); }
        .action-view .action-icon { background: rgba(255,255,255,0.2); }
        .action-post .action-icon { background: rgba(255,255,255,0.2); }
        .action-resources .action-icon { background: rgba(255,255,255,0.2); }
        .action-reports .action-icon { background: rgba(0,0,0,0.1); }
        .action-students .action-icon { background: rgba(255,255,255,0.2); }

        /* Recent Tickets */
        .recent-tickets {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tickets-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .view-all-btn {
            background: #4285f4;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            font-weight: 500;
        }

        .ticket-item {
            display: grid;
            grid-template-columns: 80px 1fr auto auto;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-id {
            font-weight: 600;
            color: #4285f4;
        }

        .ticket-info h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .ticket-student {
            font-size: 13px;
            color: #666;
        }

        .ticket-date {
            font-size: 13px;
            color: #666;
        }

        .ticket-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-progress { background: #cce5ff; color: #0056b3; }
        .status-resolved { background: #d4edda; color: #155724; }
        .status-review { background: #f8d7da; color: #721c24; }

        .ticket-actions {
            display: flex;
            gap: 8px;
        }

        .action-link {
            color: #4285f4;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sidebar-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .announcement {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #fff8e1;
            border-radius: 8px;
            border-left: 4px solid #ff9800;
        }

        .announcement-icon {
            font-size: 20px;
        }

        .announcement-content h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .announcement-content p {
            font-size: 13px;
            color: #666;
        }

        .calendar-event {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .calendar-event:last-child {
            border-bottom: none;
        }

        .event-date {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        .event-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .event-details {
            font-size: 13px;
            color: #666;
        }

        .account-menu {
            list-style: none;
        }

        .account-menu li {
            margin-bottom: 12px;
        }

        .account-menu a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 0;
            transition: color 0.2s;
        }

        .account-menu a:hover {
            color: #4285f4;
        }

        .search-box {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .search-box:focus {
            outline: none;
            border-color: #4285f4;
        }

        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <div class="logo-icon">🎓</div>
            UCSC Help Desk
        </div>
        <div class="user-profile">
            <span>🔔</span>
            <div class="user-avatar">B</div>
            <span><?= $counselor_name ?></span>
            <span>▼</span>
        </div>
    </header>

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back, <?= explode(' ', $counselor_name)[0] ?>!</h1>
            <p>Here's what needs your attention today</p>
            <div class="last-activity">
                <span>⏰</span>
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
                        <a href="new_ticket.php" class="action-btn action-new">
                            <div class="action-icon">📝</div>
                            New Ticket
                        </a>
                        <a href="view_tickets.php" class="action-btn action-view">
                            <div class="action-icon">👁️</div>
                            View Tickets
                        </a>
                        <a href="post_announcement.php" class="action-btn action-post">
                            <div class="action-icon">📢</div>
                            Post Announcement
                        </a>
                        <a href="resources.php" class="action-btn action-resources">
                            <div class="action-icon">📚</div>
                            View Resources
                        </a>
                        <a href="reports.php" class="action-btn action-reports">
                            <div class="action-icon">📊</div>
                            Reports
                        </a>
                        <a href="students.php" class="action-btn action-students">
                            <div class="action-icon">👥</div>
                            Students
                        </a>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="recent-tickets">
                    <div class="tickets-header">
                        <h3 class="section-title">Recent Tickets</h3>
                        <a href="view_all_tickets.php" class="view-all-btn">View All</a>
                    </div>

                    <?php foreach ($recent_tickets as $ticket): ?>
                    <div class="ticket-item">
                        <div class="ticket-id"><?= $ticket['id'] ?></div>
                        <div class="ticket-info">
                            <h4><?= htmlspecialchars($ticket['title']) ?></h4>
                            <div class="ticket-student"><?= $ticket['student'] ?> • <?= $ticket['student_id'] ?></div>
                        </div>
                        <div class="ticket-date"><?= $ticket['date'] ?></div>
                        <div class="ticket-status <?= getStatusClass($ticket['status']) ?>">
                            <?= $ticket['status'] ?>
                        </div>
                        <div class="ticket-actions">
                            <a href="view_ticket.php?id=<?= $ticket['id'] ?>" class="action-link">View Details</a>
                            <?php if ($ticket['status'] === 'Pending'): ?>
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
                        <div class="announcement-icon"><?= $announcement['icon'] ?></div>
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
                    <?php foreach ($calendar_events as $event): ?>
                    <div class="calendar-event">
                        <div class="event-date"><?= $event['date'] ?></div>
                        <div class="event-title"><?= $event['title'] ?></div>
                        <div class="event-details"><?= $event['location'] ?> • <?= $event['time'] ?></div>
                    </div>
                    <?php endforeach; ?>
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
                        <li><a href="profile_settings.php">👤 Profile Settings <span>›</span></a></li>
                        <li><a href="notifications.php">🔔 Notifications <span>›</span></a></li>
                        <li><a href="ticket_history.php">📋 Ticket History <span>›</span></a></li>
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
            searchBox.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // Implement search functionality
                    alert('Search functionality will be implemented');
                }
            });

            // User profile dropdown (placeholder)
            const userProfile = document.querySelector('.user-profile');
            userProfile.addEventListener('click', function() {
                alert('Profile dropdown menu will be implemented');
            });

            // Notification bell
            const notificationBell = document.querySelector('.user-profile span:first-child');
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                alert('Notifications panel will be implemented');
            });
        });
    </script>
</body>
</html>