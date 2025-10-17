<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" crossorigin="anonymous" defer></script>

<main>
  <div class="dashboardContainer">
    <div class="navMenu">
      <ul id="menuList"></ul>
    </div>
    <div class="dashboardContent">
      <div id="dashError" style="display:none;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;padding:8px 12px;border-radius:8px;">
        Error: Failed to load data.
      </div>
      <div class="cardContainer" id="cardContainer"></div>
      <div class="contentRow">
        <div class="ticketTrends chartBox">
          <h3>Ticket Trends</h3>
          <canvas id="ticketTrendsChart"></canvas>
        </div>
        <div class="ticketsByCategory chartBox">
          <h3>Tickets by Category</h3>
          <div class="chartHolder">
          <canvas id="ticketsByCategoryChart"></canvas></div>
        </div>
      </div>
      <div class="contentRow">
        <div class="platformStatus cardBox" id="platformStatus"></div>
        <div class="recentTickets cardBox" id="recentTickets"></div>
      </div>
    </div>
  </div>
</main>
<script src="/js/admin/adminDashboard.js" defer></script>