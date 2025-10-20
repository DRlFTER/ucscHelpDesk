<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="margin:10px 0 20px; padding:12px 14px; border:1px solid #ef4444; background:#fee2e2; color:#991b1b; border-radius:8px;">
        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="controls-bar">
      <div class="search-bar">
        <img src="images/173_2471.svg" alt="Search Icon">
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
  </main>
  <style>
    /* Inline Status Colors for List Badges (overrides conflicts) */
    .status-badge {
        padding: 5px 10px;
        border-radius: 17px;
        font-size: 12px;  /* Keep small font */
        font-weight: 500;
        letter-spacing: 0.24px;
        white-space: nowrap;
    }

    .status-badge.status-pending { background-color: #fff68f; color: #844d0f; }
    .status-badge.status-resolved { background-color: #9effbc; color: #166434; }
    .status-badge.status-closed { background-color: #9effbc; color: #166434; }
    .status-badge.status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-badge.status-agent-closed { background-color: #9effbc; color: #166434; }
  </style>
  <script>
    // Inject tickets fetched by controller as a global variable
    var tickets = <?= json_encode($tickets ?? []) ?>;
    var pageError = <?= json_encode($error ?? null) ?>;
  </script>
  <script src="/js/staff/staffTickets.js?v=<?= time() ?>"></script>