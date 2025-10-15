<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>

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
  <script>
    // Inject tickets fetched by controller as a global variable
    var tickets = <?= json_encode($tickets ?? []) ?>;
  </script>
  <script src="/js/staff/staffTickets.js?v=<?= time() ?>"></script>
