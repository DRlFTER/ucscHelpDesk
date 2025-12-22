<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" crossorigin="anonymous" defer></script>

<main>
  <div class="fullPage">
    <div class="pageLayout">
      <aside class="adminRight">
        <div id="dashError" style="display:none;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;padding:8px 12px;border-radius:8px;">
          Error: Failed to load data.
        </div>
        <div class="dashboardContent">
          <div class="cardContainer" id="cardContainer"></div>
          <div class="contentRow">
            <div class="ticketTrends chartBox">
              <h3>Ticket Trends</h3>
              <canvas id="ticketTrendsChart"></canvas>
            </div>
            <div class="ticketsByCategory chartBox">
              <h3>Tickets by Category</h3>
              <div class="chartHolder">
                <canvas id="ticketsByCategoryChart"></canvas>
              </div>
            </div>
          </div>
          <div class="contentRow">
            <div class="platformStatus cardBox" id="platformStatus"></div>
            <div class="recentTickets cardBox" id="recentTickets"></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<div id="reportModal" class="modalOverlay" aria-hidden="true">
  <div class="msgHolder">
    <div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">
      <div class="msgContent">
        <h3 id="reportModalTitle" class="msgTitle">Generate report</h3>
        <form id="reportForm" class="formGrid">
          <label>
            <span>Start date</span>
            <input type="date" id="reportStartDate" />
          </label>
          <label>
            <span>End date</span>
            <input type="date" id="reportEndDate" />
          </label>
          <label>
            <span>Report type</span>
            <select id="reportType">
              <option value="summary">Summary</option>
              <option value="tickets-by-category">Tickets by Category</option>
              <option value="tickets-trend">Tickets Trend</option>
              <option value="agents-performance">Agents Performance</option>
            </select>
          </label>
          <div class="msgActions" style="grid-column: 1 / -1;">
            <button id="reportGenerateBtn" type="button" class="btnPrimary"><span class="btnPrimaryText">Export PDF</span></button>
            <button id="reportExportBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Save draft</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>
<script src="/js/admin/adminDashboard.js" defer></script>