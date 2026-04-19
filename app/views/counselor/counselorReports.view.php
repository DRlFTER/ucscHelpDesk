<main>
  <div class="fullPage">
    <div class="pageLayout">
      <aside class="adminRight">
        <div class="dashboardContent">
          
          <!-- Header Section -->
          <div style="text-align: center; padding: 24px 0 16px 0;">
            <h2 style="margin: 0; font-size: 24px; font-weight: 600;">Generate Reports</h2>
            <p style="color: #6b7280; margin: 8px 0 0 0;">Create and view detailed reports for counseling tickets, assignments, and escalations</p>
          </div>

          <!-- Filter Section -->
          <div class="cardBox">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 12px; align-items: end;">
              
              <!-- Report Type -->
              <div class="filterGroup">
                <label for="report-type" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">Report Type *</label>
                <select id="report-type" class="filterInput" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                  <option value="all">All Tickets</option>
                  <option value="by-status">By Status</option>
                  <option value="by-priority">By Priority</option>
                  <option value="by-counselor">By Counselor</option>
                  <option value="by-student">By Student</option>
                </select>
              </div>

              <!-- Start Date -->
              <div class="filterGroup">
                <label for="start-date" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">Start Date</label>
                <input type="date" id="start-date" class="filterInput" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
              </div>

              <!-- End Date -->
              <div class="filterGroup">
                <label for="end-date" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">End Date</label>
                <input type="date" id="end-date" class="filterInput" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
              </div>

              <!-- Status -->
              <div class="filterGroup">
                <label for="status-filter" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">Status</label>
                <select id="status-filter" class="filterInput" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                  <option value="all">All</option>
                  <option value="pending">Pending</option>
                  <option value="agent assigned">Agent Assigned</option>
                  <option value="resolved">Resolved</option>
                  <option value="agent-closed">Agent Closed</option>
                  <option value="closed">Closed</option>
                </select>
              </div>

              <!-- Priority -->
              <div class="filterGroup">
                <label for="priority-filter" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">Priority</label>
                <select id="priority-filter" class="filterInput" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                  <option value="all">All</option>
                  <option value="high">High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>
            </div>

            <!-- Generate Button -->
            <div style="margin-top: 16px; text-align: center;">
              <button id="generate-report-btn" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 12px 48px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
                Generate Report
              </button>
            </div>
          </div>

          <!-- Statistics Cards -->
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 16px;">
            <div class="cardBox" style="text-align: center;">
              <div style="color: #6b7280; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Total Tickets</div>
              <div id="stat-total" style="font-size: 48px; font-weight: 700; color: #111827;">0</div>
            </div>
            <div class="cardBox" style="text-align: center;">
              <div style="color: #6b7280; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Pending (%)</div>
              <div id="stat-pending-percent" style="font-size: 48px; font-weight: 700; color: #111827;">0%</div>
            </div>
            <div class="cardBox" style="text-align: center;">
              <div style="color: #6b7280; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Resolved (%)</div>
              <div id="stat-resolved-percent" style="font-size: 48px; font-weight: 700; color: #111827;">0%</div>
            </div>
          </div>

          <!-- Pie Chart -->
          <div class="cardBox" style="margin-top: 16px;">
            <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600; text-align: center;">Status Breakdown (Pie Chart)</h3>
            <div style="max-width: 600px; margin: 0 auto;">
              <canvas id="statusPieChart"></canvas>
            </div>
          </div>

          <!-- Tickets Table -->
          <div class="cardBox" style="margin-top: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
              <h3 style="margin: 0; font-size: 18px; font-weight: 600;">
                <span id="report-title">All Tickets Report</span> (<span id="result-count">0</span> results)
              </h3>
              <button id="export-csv-btn" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 20px; font-size: 14px; font-weight: 500; cursor: pointer;">
                Export PDF
              </button>
            </div>

            <div style="overflow-x: auto;">
              <table style="width: 100%; border-collapse: collapse;">
                <thead>
                  <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">ID</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Title</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Status</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Priority</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Student</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Division</th>
                    <th style="padding: 12px; text-align: left; font-size: 13px; font-weight: 600; color: #374151;">Date</th>
                  </tr>
                </thead>
                <tbody id="tickets-table-body">
                  <tr>
                    <td colspan="7" style="padding: 24px; text-align: center; color: #6b7280;">Click "Generate Report" to view results</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </aside>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script src="/js/counselor/counselorReports.js?v=20260305"></script>