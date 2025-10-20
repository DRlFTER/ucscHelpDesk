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
          <a href="/staff/staffTickets" class="nav-link ">My Tickets</a>
          <a href="/staff/staffAnnoucements" class="nav-link">Announcement</a>
          <a href="/staff/createTemplate" class="nav-link">Template</a>
          <a href="/staff/forum" class="nav-link">Forums</a>
          <a href="/staff/calendar" class="nav-link">Calendar</a>
          <a href="/staff/lostfound" class="nav-link">Lost &amp; Found</a>
          <a href="/staff/settings" class="nav-link">Settings</a>
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
                <section class="dashboard-section">
                    <h3>Recent Announcements</h3>
                    <?php if (!empty($announcements)): ?>
                        <ul class="recent-announcements">
                            <?php foreach ($announcements as $ann): ?>
                                <li class="recent-announcement">
                                    <h4><?php echo htmlspecialchars($ann['topic'] ?? 'Untitled'); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($ann['content'] ?? '', 0, 100)) . '...'; ?></p>
                                    <small>By <?php echo htmlspecialchars($ann['staff_name'] ?? 'Unknown'); ?> on <?php echo date('M j, Y', strtotime($ann['date_time'] ?? 'now')); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No recent announcements.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>

<style>
    /* Dashboard-specific styles */
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
        background: var(--color-bg-card);
        border: 1px solid var(--color-border-card);
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
        border: 1px solid var(--color-border-medium);
        border-radius: 26px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .ticket-card {
        background-color: var(--color-bg-card);
        border: 1px solid var(--color-border-card);
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
        border: 1px solid var(--color-border-light);
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

    /* Temporary inline styles for debugging - remove once CSS file works */
    .layout-container {
        display: flex;
        gap: 0;
        min-height: 70vh; /* Adjust based on viewport */
    }
    .navMenu {
        flex: 0 0 250px;
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        padding: 20px 0;
    }
    .sideNav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 0 15px;
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
        background: #007bff;
        color: white;
    }
    .content-area {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }

</style>