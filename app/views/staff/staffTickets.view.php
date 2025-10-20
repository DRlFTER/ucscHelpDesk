<?php
// views/staff/staffTickets.view.php (updated with better structure)
?>
<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>
    
    <!-- Layout Container for Sidebar and Content -->
    <div class="layout-container">
      <!-- Sidebar Navigation -->
      <nav class="navMenu">
        <div class="sideNav">
          <a href="/staff/staffDashboard" class="nav-link">Dashboard</a>
          <a href="/staff/staffTickets" class="nav-link active">My Tickets</a>
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
        <?php if (!empty($error)): ?>
          <div class="error-alert" style="margin:10px 0 20px; padding:12px 14px; border:1px solid #ef4444; background:#fee2e2; color:#991b1b; border-radius:8px;">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="controls-bar">
          <div class="search-bar">
            <img src="/images/173_2471.svg" alt="Search Icon"> <!-- Fixed path assuming public/images -->
            <input type="text" placeholder="Search tickets, students...">
          </div>
          <div class="filters">
            <select id="status-filter" class="filter-btn">
              <option value="">All Statuses</option>
              <option value="all">All</option>
              <option value="pending">Pending</option>
              <option value="resolved">Resolved</option>
              <option value="closed">Closed</option>
              <option value="assigned">Assigned</option>
            </select>
            <select id="priority-filter" class="filter-btn">
              <option value="">All Priorities</option>
              <option value="all">All</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>
        </div>

        <div class="tickets-container"></div>
      </div>
    </div>
</main>

<style>
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
<script>
    // Inject tickets fetched by controller as a global variable
    var tickets = <?= json_encode($tickets ?? []) ?>;
    var pageError = <?= json_encode($error ?? null) ?>;
</script>
<script src="/js/staff/staffTickets.js?v=<?= time() ?>"></script>