<?php ?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Staff Dashboard</h2>
        <p class="page-subtitle">Overview of your tickets and recent announcements</p>
    </div>
    
    <div class="layout-container">
      
        
        <div class="content-area">
            <section class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
                    <div class="stat-label">Total Tickets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $stats['pending'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $stats['assigned'] ?? 0; ?></div>
                    <div class="stat-label">Assigned</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $stats['resolved'] ?? 0; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </section>

            <div class="dashboard-grid">
                <section class="dashboard-section">
                    <h3 class="section-header">Recent Tickets</h3>
                    <?php if (!empty($recentTickets)): ?>
                        <div class="tickets-container">
                            <?php foreach ($recentTickets as $ticket): ?>
                                <article class="ticket-card">
                                    <div class="ticket-header">
                                        <div class="ticket-title-group">
                                            <h4 class="ticket-title"><?php echo htmlspecialchars($ticket['title'] ?? 'Untitled'); ?></h4>
                                            <div class="ticket-meta">
                                                <span class="meta-item"><?php echo htmlspecialchars($ticket['student_name'] ?? 'Unknown Student'); ?></span>
                                                <span class="meta-item"><?php echo date('M j, Y', strtotime($ticket['created_at'] ?? 'now')); ?></span>
                                                <span class="status-badge status-<?php echo htmlspecialchars(str_replace(' ', '-', strtolower($ticket['status'] ?? ''))); ?>">
                                                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $ticket['status'] ?? 'Unknown'))); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="/staff/ticketDetails?ticket_id=<?php echo $ticket['ticket_id']; ?>" class="ticket-action">
                                            <span class="action-text">View Details</span>
                                            <span class="action-icon">→</span>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-content">No recent tickets found.</p>
                    <?php endif; ?>
                </section>
                <section class="dashboard-section">
                    <h3 class="section-header">Recent Announcements</h3>
                    <?php if (!empty($announcements)): ?>
                        <div class="announcements-container">
                            <?php foreach ($announcements as $ann): ?>
                                <article class="announcement-card">
                                    <div class="announcement-header">
                                        <div class="announcement-content">
                                            <h4 class="announcement-title"><?php echo htmlspecialchars($ann['topic'] ?? 'Untitled'); ?></h4>
                                            <p class="announcement-excerpt"><?php echo htmlspecialchars(substr($ann['content'] ?? '', 0, 100)) . '...'; ?></p>
                                        </div>
                                    </div>
                                    <div class="announcement-meta">
                                        <span class="meta-item announcement-author">By <?php echo htmlspecialchars($ann['staff_name'] ?? 'Unknown'); ?></span>
                                        <span class="meta-item announcement-date"><?php echo date('M j, Y', strtotime($ann['date_time'] ?? 'now')); ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-content">No recent announcements.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>

<style>
    .content-area {
        overflow: visible !important; 
        height: auto !important; 
    }

    .layout-container {
        display: flex;
        gap: 0;
        height: auto !important;
        overflow: visible !important; 
    }

    html, body {
        height: auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    .main-content {
        overflow: visible !important; /* Prevent main from clipping */
    }

    /* Enhanced Dashboard Stats */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(140, 140, 249, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #8c8cf9, #6a6af5);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(140, 140, 249, 0.2);
    }

    .stat-icon {
        font-size: 2em;
        margin-bottom: 12px;
        opacity: 0.8;
    }

    .stat-number {
        font-size: 2.8em;
        font-weight: 700;
        color: #8c8cf9;
        margin-bottom: 8px;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.95em;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
    }

    /* Polished Quick Actions */
    .quick-actions {
        display: flex;
        gap: 16px;
        margin-bottom: 40px;
    }

    .action-btn {
        flex: 1;
        padding: 16px 24px;
        background: linear-gradient(135deg, #8c8cf9 0%, #6a6af5 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1em;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(140, 140, 249, 0.3);
        position: relative;
        overflow: hidden;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(140, 140, 249, 0.4);
    }

    .btn-icon {
        font-size: 1.1em;
    }

    /* Refined Dashboard Sections */
    .dashboard-section {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .section-header {
        font-size: 1.6em;
        margin-bottom: 20px;
        color: #1f2937;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-header::before {
        content: '→';
        color: #8c8cf9;
        font-weight: bold;
    }

    /* Improved Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }

    /* Enhanced Tickets Container */
    .tickets-container {
        border: none;
        background: transparent;
        border-radius: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    } 

    .ticket-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(140, 140, 249, 0.1);
        transition: all 0.3s ease;
    }

    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(140, 140, 249, 0.15);
        border-color: #8c8cf9;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        border: none;
        padding: 0;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .ticket-title {
        font-size: 1.2em;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin: 0;
        color: #1f2937;
        line-height: 1.3;
    }

    .ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 24px;
        font-size: 0.9em;
        color: #6b7280;
        letter-spacing: 0.01em;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: #f3f4f6;
        border-radius: 6px;
        font-weight: 500;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: 600;
        letter-spacing: 0.5px;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .status-pending { background-color: #fff68f; color: #844d0f; }
    .status-resolved { background-color: #9effbc; color: #166434; }
    .status-rejected { background-color: #ffd8d8; color: #b50000; }
    .status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-agent-closed { background-color: #dcfce7; color: #155724; }
    .status-closed { background-color: #dcfce7; color: #155724; }

    .status-badge:hover {
        transform: scale(1.05);
    }

    .ticket-action {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #8c8cf9 0%, #6a6af5 100%);
        color: white;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
        font-size: 0.9em;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(140, 140, 249, 0.3);
    }

    .ticket-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(140, 140, 249, 0.4);
        background: linear-gradient(135deg, #6a6af5 0%, #5a5ae5 100%);
    }

    .action-text {
        flex: 1;
    }

    .action-icon {
        font-weight: bold;
    }

    /* Polished Announcements */
    .announcements-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .announcement-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(140, 140, 249, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        overflow: hidden;
    }

    .announcement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(140, 140, 249, 0.15);
        border-color: #8c8cf9;
    }

    .announcement-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #8c8cf9, #6a6af5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .announcement-card:hover::before {
        opacity: 1;
    }

    .announcement-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
        position: relative;
    }

    .announcement-icon {
        font-size: 1.5em;
        flex-shrink: 0;
        margin-top: 2px;
        color: #8c8cf9;
    }

    .announcement-content {
        flex: 1;
    }

    .announcement-title {
        font-size: 1.1em;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 8px 0;
        line-height: 1.4;
    }

    .announcement-excerpt {
        margin: 0 0 12px 0;
        color: #6b7280;
        font-size: 0.95em;
        line-height: 1.6;
    }

    .announcement-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85em;
        color: #9ca3af;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: #f3f4f6;
        border-radius: 6px;
        font-weight: 500;
    }

    .announcement-date {
        background: linear-gradient(135deg, #8c8cf9, #6a6af5);
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8em;
        font-weight: 600;
    }

    .no-content {
        text-align: center;
        color: #9ca3af;
        font-style: italic;
        padding: 40px 20px;
        margin: 0;
        font-size: 1em;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px dashed #d1d5db;
    }

    /* Sidebar Enhancements */
    .navMenu {
        flex: 0 0 280px;
        background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
        backdrop-filter: blur(20px);
        border-right: 1px solid rgba(140, 140, 249, 0.2);
        height: auto !important;
        overflow: visible !important;
        border-radius: 0 20px 20px 0;
        padding: 20px 0;
        box-shadow: 2px 0 12px rgba(140, 140, 249, 0.1);
    }

    .sideNav {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 0 20px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        color: #6b7280;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.95em;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-icon {
        font-size: 1.1em;
        width: 24px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background: linear-gradient(135deg, #8c8cf9, #6a6af5);
        color: white;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(140, 140, 249, 0.3);
    }

    .nav-link:hover .nav-icon,
    .nav-link.active .nav-icon {
        transform: scale(1.1);
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: white;
        border-radius: 0 2px 2px 0;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .quick-actions {
            flex-direction: column;
        }
        .navMenu {
            flex: 0 0 100%;
            order: -1;
            border-right: none;
            border-bottom: 1px solid rgba(140, 140, 249, 0.2);
            border-radius: 20px 20px 0 0;
        }
        .sideNav {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
        }
        .nav-link {
            padding: 10px 16px;
            font-size: 0.9em;
        }
        .dashboard-stats {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .stat-number {
            font-size: 2.2em;
        }
        .action-btn {
            padding: 14px 20px;
            font-size: 0.95em;
        }
        .ticket-meta {
            gap: 8px 16px;
        }
    }

    /* Global Polish */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
    }

    /* Reuse and Override Status Colors */
    .status-pending { background-color: #fff68f !important; color: #844d0f !important; }
    .status-resolved { background-color: #9effbc !important; color: #166434 !important; }
    .status-closed { background-color: #9effbc !important; color: #166434 !important; }
    .status-agent-assigned { background-color: #badbff !important; color: #3300ff !important; }
    .status-agent-closed { background-color: #9effbc !important; color: #166434 !important; }
</style>