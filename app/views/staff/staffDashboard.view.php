<?php
// views/staff/staffDashboard.view.php
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Staff Dashboard</h2>
        <p class="page-subtitle">Overview of your tickets and recent announcements</p>
    </div>
    
    <!-- Layout Container for Sidebar and Content -->
    <div class="layout-container">
        <!-- Sidebar Navigation -->
        <nav class="navMenu">
            <div class="sideNav">
                <a href="/staff/staffDashboard" class="nav-link active">Dashboard</a>
                <a href="/staff/staffTickets" class="nav-link">My Tickets</a>
                <a href="/staff/staffAnnoucements" class="nav-link">Announcement</a>
                <a href="/staff/createTemplate" class="nav-link">Template</a>
                <a href="/staff/staffFAQ" class="nav-link">FAQs</a>
                <a href="/staff/staffForum" class="nav-link">Forum</a>
                <a href="/staff/staffCalender" class="nav-link">Calendar</a>
                <a href="/staff/staffKB" class="nav-link">Knowledge Base</a>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <div class="content-area">
            <!-- Stats Overview -->
            <section class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
                    <div class="stat-label">Total Tickets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['pending'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['assigned'] ?? 0; ?></div>
                    <div class="stat-label">Assigned</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['resolved'] ?? 0; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="quick-actions">
                <button onclick="window.location.href='/staff/staffTickets'">View All Tickets</button>
                <button onclick="window.location.href='/staff/staffAnnCreate'">Create Announcement</button>
                <button onclick="window.location.href='/staff/createTemplate'">Create Template</button>
            </section>

            <div class="dashboard-grid">
                <!-- Recent Tickets -->
                <section class="dashboard-section">
                    <h3>Recent Tickets</h3>
                    <?php if (!empty($recentTickets)): ?>
                        <div class="tickets-container">
                            <?php foreach ($recentTickets as $ticket): ?>
                                <article class="ticket-card">
                                    <div class="ticket-header">
                                        <div class="ticket-title-group">
                                            <h4 class="ticket-title"><?php echo htmlspecialchars($ticket['title'] ?? 'Untitled'); ?></h4>
                                            <div class="ticket-meta">
                                                <span><?php echo htmlspecialchars($ticket['student_name'] ?? 'Unknown Student'); ?></span>
                                                <span><?php echo date('M j, Y', strtotime($ticket['created_at'] ?? 'now')); ?></span>
                                                <span class="status-badge status-<?php echo htmlspecialchars(str_replace(' ', '-', strtolower($ticket['status'] ?? ''))); ?>">
                                                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $ticket['status'] ?? 'Unknown'))); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="/staff/ticketDetails?ticket_id=<?php echo $ticket['ticket_id']; ?>" class="ticket-action">View Details</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No recent tickets found.</p>
                    <?php endif; ?>
                </section>

                <!-- Recent Announcements -->
                <!-- Recent Announcements - Enhanced Design -->
                <section class="dashboard-section">
                    <h3>Recent Announcements</h3>
                    <?php if (!empty($announcements)): ?>
                        <div class="announcements-container">
                            <?php foreach ($announcements as $ann): ?>
                                <article class="announcement-card">
                                    <div class="announcement-header">
                                        <div class="announcement-content">
                                            <h4 class="announcement-title"><?php echo htmlspecialchars($ann['topic'] ?? 'Untitled'); ?></h4>
                                            <p class="announcement-excerpt"><?php echo htmlspecialchars(substr($ann['content'] ?? '', 0, 100)) . '...'; ?></p>
                                        </div>
                                        <div class="announcement-meta">
                                            <small class="announcement-author">By <?php echo htmlspecialchars($ann['staff_name'] ?? 'Unknown'); ?></small>
                                            <small class="announcement-date"><?php echo date('M j, Y', strtotime($ann['date_time'] ?? 'now')); ?></small>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-announcements">No recent announcements.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>

<style>
    /* Override from staffTickets.css to fix scrolling - add this at the end of inline styles */
    .content-area {
        overflow: visible !important; /* Force no internal scroll - full page only */
        height: auto !important; /* Allow natural height */
    }

    .layout-container {
        display: flex;
        gap: 0;
        height: auto !important; /* No fixed height - page grows */
        overflow: visible !important; /* No hidden overflow */
    }

    /* Ensure full page scrolling */
    html, body {
        height: auto !important;
        overflow-y: auto !important; /* Whole page scrolls smoothly */
        overflow-x: hidden !important;
    }

    .main-content {
        overflow: visible !important; /* Prevent main from clipping */
    }

    /* Rest of your dashboard styles (unchanged) */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--color-bg-card);
        border: 1px solid var(--color-border-card);
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2.5em;
        font-weight: bold;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 1em;
        color: var(--color-text-body);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-section {
        background: #f9f9f9;
        border: 1px solid #8c8cf9;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .dashboard-section h3 {
        font-size: 1.5em;
        margin-bottom: 15px;
        color: var(--color-text-dark);
    }

    .quick-actions {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .quick-actions button {
        flex: 1;
        padding: 12px;
        background: var(--color-primary);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.25s ease;
    }

    .quick-actions button:hover {
        background: #6a6af5;
    }

    .recent-announcements {
        list-style: none;
        padding: 0;
    }

    .recent-announcement {
        padding: 10px 0;
        border-bottom: 1px solid var(--color-border-light);
    }

    .recent-announcement:last-child {
        border-bottom: none;
    }

    .recent-announcement h4 {
        margin: 0 0 5px 0;
        font-size: 1em;
        color: var(--color-text-dark);
    }

    .recent-announcement p {
        margin: 0;
        color: var(--color-text-light);
        font-size: 0.9em;
    }

    /* Dashboard Grid for Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .quick-actions {
            flex-direction: column;
        }
    }

    /* Reuse existing ticket styles for consistency */
       .tickets-container {
    border: 1px solid #f9f9f9;
    border-radius: 26px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 15px;
} 

.ticket-card {
    background-color: #f9f9f9; 
    border: 1px solid #8c8cf9;
    border-radius: 15px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid #f9f9f9;
        padding: 10px;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ticket-title {
        font-size: 21px;
        font-weight: 400;
        letter-spacing: 0.42px;
        margin: 0;
    }

    .ticket-meta {
        display: flex;
        gap: 36px;
        font-size: 13px;
        color: var(--color-text-light);
        letter-spacing: 0.26px;
        flex-wrap: wrap;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 17px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.24px;
        white-space: nowrap;
    }

    .status-pending { background-color: var(--status-pending-bg); color: var(--status-pending-text); }
    .status-resolved { background-color: var(--status-resolved-bg); color: var(--status-resolved-text); }
    .status-rejected { background-color: var(--status-rejected-bg); color: var(--status-rejected-text); }
    .status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-agent-closed { background-color: #dcfce7; color: #155724; }
    .status-closed { background-color: #dcfce7; color: #155724; }

    .ticket-action {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        background: var(--color-primary);
        color: white;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.25s ease;
    }

    .ticket-action:hover {
        background-color: #6a6af5;
    }
    
    /* Inline Status Colors for List Badges (overrides conflicts) */
    .status-badge {
        padding: 5px 10px;
        border-radius: 17px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.24px;
        white-space: nowrap;
    }

    .status-badge.status-pending { background-color: #fff68f; color: #844d0f; }
    .status-badge.status-resolved { background-color: #9effbc; color: #166434; }
    .status-badge.status-closed { background-color: #9effbc; color: #166434; }
    .status-badge.status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-badge.status-agent-closed { background-color: #9effbc; color: #166434; }

    /* Layout Styles - Fixed for Full Page Scrolling */
    .layout-container {
        display: flex;
        gap: 0;
        height: auto !important; /* No fixed height - full page flow */
        background: rgba(255, 255, 255, 0.5);
    }

    .navMenu {
        flex: 0 0 300px;
        background: rgba(255, 255, 255, 0.8); /* White with 80% opacity */
        backdrop-filter: blur(10px); /* Smooth blur blend */
        border-right: 1px solid rgba(222, 226, 230, 0.5); /* Semi-transparent border */
        height: auto !important; /* No fixed height */
        overflow: visible !important; /* No internal scroll */
        border-radius: 0 15px 15px 0; /* Rounded right corners */
    }

    .sideNav {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10 20px;
    }

    .nav-link {
        display: block;
        padding: 12px 15px;
        color: #495057;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(0, 123, 255, 0.8); /* Semi-transparent blue */
        color: white;
        backdrop-filter: blur(5px);
    }

    .content-area {
        flex: 1;
        padding: 20px;
        overflow: visible !important; /* Override staffTickets.css - no internal scroll */
        height: auto !important; /* Natural height */
    }

    /* Global overrides for full page scroll (beats staffTickets.css) */
    html, body, .main-content, .layout-container {
        height: auto !important;
        overflow-y: visible !important;
        overflow-x: hidden !important;
    }

    body {
        overflow-y: auto !important; /* Smooth full-page vertical scroll */
    }

    .announcements-container {
        display: flex;
        flex-direction: column;
        gap: 15px; /* Space between cards */
    }

    .announcement-card {
        background: #ffffff; /* Clean white background */
        border: 1px solid #e0e0e0; /* Light border */
        border-radius: 12px; /* Rounded corners */
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); /* Soft shadow for depth */
        transition: all 0.2s ease; /* Smooth hover */
        cursor: pointer; /* Indicates interactivity */
    }

    .announcement-card:hover {
        box-shadow: 0 4px 12px rgba(140, 140, 249, 0.2); /* Purple-tinted hover shadow */
        transform: translateY(-1px); /* Subtle lift */
        border-color: #8c8cf9; /* Purple border on hover */
    }

    .announcement-header {
        display: flex;
        align-items: flex-start;
        gap: 12px; /* Space between icon and content */
    }

    .announcement-icon {
        font-size: 24px; /* Larger icon for visual pop */
        flex-shrink: 0; /* Icon doesn't shrink */
        margin-top: 2px;
    }

    .announcement-content {
        flex: 1; /* Takes remaining space */
    }

    .announcement-title {
        font-size: 1.1em; /* Slightly larger for emphasis */
        font-weight: 600; /* Bolder title */
        color: var(--color-text-dark);
        margin: 0 0 6px 0; /* Bottom margin for spacing */
        line-height: 1.3;
    }

    .announcement-excerpt {
        margin: 0 0 8px 0; /* Space below excerpt */
        color: var(--color-text-light);
        font-size: 0.95em;
        line-height: 1.5; /* Better readability */
    }

    .announcement-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85em;
        color: #6b7280; /* Muted gray for meta */
        border-top: 1px solid #f0f0f0; /* Subtle separator */
        padding-top: 8px;
        margin-top: 8px;
    }

    .announcement-author {
        font-weight: 500; /* Slightly bold author */
        padding-right: 10px;
    }

    .announcement-date {
        background: #f0f0f0; /* Light badge background */
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
    }

    .no-announcements {
        text-align: center;
        color: var(--color-text-light);
        font-style: italic;
        padding: 20px;
        margin: 0;
    }
</style>