<main>
  <div class="fullPage">
    <div class="pageLayout">

      <!-- Quick Reports Section -->
      <section class="reportsSection">
        <div class="sectionHeader">
          <h2 class="sectionTitle">Quick Reports</h2>
          <p class="sectionSubtitle">One-click predefined reports for common use cases</p>
        </div>

        <div class="quickReportsGrid">
          <!-- Ticket-Based Reports -->
          <div class="reportCategory">
            <h3 class="categoryTitle">Category-Based Reports</h3>
            <div class="reportCards">
              <div class="reportCard" data-report="tickets-by-status">
                <div class="reportInfo">
                  <h4>Tickets by Status</h4>
                  <p>Open / In Progress / Resolved / Closed breakdown</p>
                </div>
              </div>

              <div class="reportCard" data-report="tickets-by-category">
                <div class="reportInfo">
                  <h4>Tickets by Category</h4>
                  <p>Academic, ID Card, Wi-Fi, Hostel, Counseling distribution</p>
                </div>
              </div>

              <div class="reportCard" data-report="tickets-by-role">
                <div class="reportInfo">
                  <h4>Tickets by Role</h4>
                  <p>Student / Staff / Lecturer submission analysis</p>
                </div>
              </div>

              <div class="reportCard" data-report="resolution-time">
                <div class="reportInfo">
                  <h4>Resolution Time Report</h4>
                  <p>Average resolution time per category or staff</p>
                </div>
              </div>
            </div>
          </div>

          <!-- User, Staff & Time-Based Reports -->
          <div class="reportCategory">
            <h3 class="categoryTitle">User, Staff & Time-Based Reports</h3>
            <div class="reportCards">
              <div class="reportCard" data-report="staff-performance">
                <div class="reportInfo">
                  <h4>Staff Performance Report</h4>
                  <p>Tickets assigned vs resolved, response time</p>
                </div>
              </div>

              <div class="reportCard" data-report="most-active-users">
                <div class="reportInfo">
                  <h4>Most Active Users</h4>
                  <p>Users who submit the most tickets</p>
                </div>
              </div>

              <div class="reportCard" data-report="ticket-volume-trend">
                <div class="reportInfo">
                  <h4>Ticket Volume Trend</h4>
                  <p>Daily / Weekly / Monthly ticket trends</p>
                </div>
              </div>

              <div class="reportCard" data-report="unresolved-backlog">
                <div class="reportInfo">
                  <h4>Unresolved Tickets Over Time</h4>
                  <p>Backlog tracking and aging analysis</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Custom Report Builder Section -->
      <section class="reportsSection">
        <div class="sectionHeader">
          <h2 class="sectionTitle">Custom Report Builder</h2>
          <p class="sectionSubtitle">Build your own reports with advanced filters</p>
        </div>

        <div class="customReportBuilder">
          <form id="customReportForm" class="customReportForm">
            <div class="filterRow">
              <div class="filterGroup">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="start_date">
              </div>
              <div class="filterGroup">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="end_date">
              </div>
              <div class="filterGroup">
                <label for="statusFilter">Ticket Status</label>
                <select id="statusFilter" name="status">
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="agent assigned">Agent Assigned</option>
                  <option value="resolved">Resolved</option>
                  <option value="agent-closed">Agent Closed</option>
                  <option value="closed">Closed</option>
                </select>
              </div>
              <div class="filterGroup">
                <label for="categoryFilter">Category</label>
                <select id="categoryFilter" name="category">
                  <option value="">All Categories</option>
                </select>
              </div>
            </div>
            <div class="filterRow">
              <div class="filterGroup">
                <label for="priorityFilter">Priority</label>
                <select id="priorityFilter" name="priority">
                  <option value="">All Priorities</option>
                  <option value="high">High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>
              <div class="filterGroup">
                <label for="assignedFilter">Assigned Staff</label>
                <select id="assignedFilter" name="assigned_to">
                  <option value="">All Staff</option>
                </select>
              </div>
              <div class="filterGroup">
                <label for="userRoleFilter">User Role</label>
                <select id="userRoleFilter" name="user_role">
                  <option value="">All Roles</option>
                  <option value="student">Student</option>
                  <option value="staff">Staff</option>
                  <option value="lecturer">Lecturer</option>
                  <option value="counselor">Counselor</option>
                </select>
              </div>
              <div class="filterGroup">
                <label for="limitFilter">Max Results</label>
                <select id="limitFilter" name="limit">
                  <option value="50">50 Records</option>
                  <option value="100">100 Records</option>
                  <option value="250">250 Records</option>
                  <option value="500">500 Records</option>
                  <option value="">All Records</option>
                </select>
              </div>
            </div>
            <div class="filterActions">
              <button type="submit" class="btnPrimary">
                <span class="btnPrimaryText">Generate Report</span>
              </button>
              <button type="reset" class="btnSecondary">
                <span class="btnSecondaryText">Clear Filters</span>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Report Results Section -->
      <section id="reportResults" class="reportsSection reportResultsSection" style="display: none;">
        <div class="sectionHeader">
          <h2 class="sectionTitle" id="reportResultsTitle">Report Results</h2>
          <div class="exportActions">
            <button type="button" id="exportPdf" class="btnAttach">
              <span class="btnAttachText">Export PDF</span>
            </button>
            <button type="button" id="exportCsv" class="btnAttach">
              <span class="btnAttachText">Export CSV</span>
            </button>
          </div>
        </div>

        <!-- Summary Cards -->
        <div id="reportSummary" class="reportSummary"></div>

        <!-- Results Table -->
        <div class="tableContainer">
          <table id="reportTable" class="reportTable">
            <thead id="reportTableHead"></thead>
            <tbody id="reportTableBody"></tbody>
          </table>
        </div>

        <!-- Chart Container -->
        <div id="reportChart" class="reportChartContainer" style="display: none;">
          <canvas id="reportChartCanvas"></canvas>
        </div>
      </section>

    </div>
  </div>
</main>

<!-- Quick Report Filter Modal -->
<div id="quickReportModal" class="modalOverlay" aria-hidden="true">
  <div class="msgHolder">
    <div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="quickReportModalTitle">
      <div class="msgContent">
        <h3 id="quickReportModalTitle" class="msgTitle">Filter Report</h3>
        <form id="quickReportFilterForm" class="formGrid">
          <label>
            <span>Start Date</span>
            <input type="date" id="qrStartDate" name="start_date">
          </label>
          <label>
            <span>End Date</span>
            <input type="date" id="qrEndDate" name="end_date">
          </label>
          <label id="qrDivisionLabel" style="display: none;">
            <span>Department</span>
            <select id="qrDivision" name="division">
              <option value="">All Departments</option>
            </select>
          </label>
          <label id="qrGroupByLabel" style="display: none;">
            <span>Group By</span>
            <select id="qrGroupBy" name="group_by">
              <option value="category">Category</option>
              <option value="staff">Staff Member</option>
            </select>
          </label>
          <label id="qrPeriodLabel" style="display: none;">
            <span>Period</span>
            <select id="qrPeriod" name="period">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </label>
          <div class="msgActions" style="grid-column: 1 / -1;">
            <button type="button" id="cancelQuickReport" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
            <button type="submit" class="btnPrimary"><span class="btnPrimaryText">Generate</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" crossorigin="anonymous" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js" crossorigin="anonymous" defer></script>
<script src="/js/admin/adminReports.js" defer></script>
