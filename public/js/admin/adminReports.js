(function () {
  "use strict";

  let currentReportType = null;
  let currentReportData = [];
  let chartInstance = null;
  let filterData = {
    divisions: [],
    staff: [],
  };

  // DOM Elements
  const elements = {
    // Quick Report Elements
    reportCards: document.querySelectorAll(".reportCard"),
    quickReportModal: document.getElementById("quickReportModal"),
    quickReportForm: document.getElementById("quickReportFilterForm"),
    cancelQuickReport: document.getElementById("cancelQuickReport"),

    // Quick Report Filter Fields
    qrStartDate: document.getElementById("qrStartDate"),
    qrEndDate: document.getElementById("qrEndDate"),
    qrDivision: document.getElementById("qrDivision"),
    qrDivisionLabel: document.getElementById("qrDivisionLabel"),
    qrGroupBy: document.getElementById("qrGroupBy"),
    qrGroupByLabel: document.getElementById("qrGroupByLabel"),
    qrPeriod: document.getElementById("qrPeriod"),
    qrPeriodLabel: document.getElementById("qrPeriodLabel"),

    // Custom Report Elements
    customReportForm: document.getElementById("customReportForm"),
    categoryFilter: document.getElementById("categoryFilter"),
    assignedFilter: document.getElementById("assignedFilter"),

    // Results Section
    reportResults: document.getElementById("reportResults"),
    reportResultsTitle: document.getElementById("reportResultsTitle"),
    reportSummary: document.getElementById("reportSummary"),
    reportTableHead: document.getElementById("reportTableHead"),
    reportTableBody: document.getElementById("reportTableBody"),
    reportChart: document.getElementById("reportChart"),
    reportChartCanvas: document.getElementById("reportChartCanvas"),

    // Export Buttons
    exportPdf: document.getElementById("exportPdf"),
    exportCsv: document.getElementById("exportCsv"),
  };

  // Initialization
  async function init() {
    await loadFilterData();
    populateFilters();
    bindEvents();
    setDefaultDates();
  }

  function setDefaultDates() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

    const formatDate = (d) => d.toISOString().split("T")[0];

    if (elements.qrStartDate)
      elements.qrStartDate.value = formatDate(thirtyDaysAgo);
    if (elements.qrEndDate) elements.qrEndDate.value = formatDate(today);

    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");
    if (startDate) startDate.value = formatDate(thirtyDaysAgo);
    if (endDate) endDate.value = formatDate(today);
  }

  async function loadFilterData() {
    try {
      const response = await fetch("/admin/reportsFilters");
      if (!response.ok) throw new Error("Failed to load filters");
      filterData = await response.json();
    } catch (error) {
      console.error("Error loading filter data:", error);
    }
  }

  function populateFilters() {
    // Populate category/division filters
    const divisionOptions = filterData.divisions
      .map((d) => `<option value="${d.did}">${escapeHtml(d.name)}</option>`)
      .join("");

    if (elements.categoryFilter) {
      elements.categoryFilter.innerHTML =
        '<option value="">All Categories</option>' + divisionOptions;
    }
    if (elements.qrDivision) {
      elements.qrDivision.innerHTML =
        '<option value="">All Departments</option>' + divisionOptions;
    }

    // Populate staff filter
    const staffOptions = filterData.staff
      .map((s) => `<option value="${s.u_id}">${escapeHtml(s.name)}</option>`)
      .join("");

    if (elements.assignedFilter) {
      elements.assignedFilter.innerHTML =
        '<option value="">All Staff</option>' + staffOptions;
    }
  }

  function bindEvents() {
    // Quick Report Cards
    elements.reportCards.forEach((card) => {
      card.addEventListener("click", () => handleQuickReportClick(card));
    });

    // Quick Report Modal
    if (elements.cancelQuickReport) {
      elements.cancelQuickReport.addEventListener(
        "click",
        closeQuickReportModal,
      );
    }
    if (elements.quickReportModal) {
      elements.quickReportModal
        .querySelector(".modalBackdropClose")
        ?.addEventListener("click", closeQuickReportModal);
    }
    if (elements.quickReportForm) {
      elements.quickReportForm.addEventListener(
        "submit",
        handleQuickReportSubmit,
      );
    }

    // Custom Report Form
    if (elements.customReportForm) {
      elements.customReportForm.addEventListener(
        "submit",
        handleCustomReportSubmit,
      );
    }

    // Export Buttons
    if (elements.exportPdf) {
      elements.exportPdf.addEventListener("click", exportToPdf);
    }
    if (elements.exportCsv) {
      elements.exportCsv.addEventListener("click", exportToCsv);
    }
  }

  // Quick Reports
  function handleQuickReportClick(card) {
    currentReportType = card.dataset.report;

    // Reset all extra filter fields
    elements.qrDivisionLabel.style.display = "none";
    elements.qrGroupByLabel.style.display = "none";
    elements.qrPeriodLabel.style.display = "none";

    // Show relevant filters based on report type
    switch (currentReportType) {
      case "tickets-by-status":
        elements.qrDivisionLabel.style.display = "";
        break;
      case "resolution-time":
        elements.qrGroupByLabel.style.display = "";
        break;
      case "ticket-volume-trend":
        elements.qrPeriodLabel.style.display = "";
        break;
    }

    openQuickReportModal();
  }

  function openQuickReportModal() {
    if (elements.quickReportModal) {
      elements.quickReportModal.classList.add("open");
      elements.quickReportModal.setAttribute("aria-hidden", "false");
      document.body.classList.add("modal-open");
    }
  }

  function closeQuickReportModal() {
    if (elements.quickReportModal) {
      elements.quickReportModal.classList.remove("open");
      elements.quickReportModal.setAttribute("aria-hidden", "true");
      document.body.classList.remove("modal-open");
    }
  }

  async function handleQuickReportSubmit(e) {
    e.preventDefault();
    closeQuickReportModal();

    const formData = new FormData(elements.quickReportForm);
    const params = new URLSearchParams();

    params.append("report_type", currentReportType);
    for (const [key, value] of formData.entries()) {
      if (value) params.append(key, value);
    }

    await generateReport(
      `/admin/reportsQuick?${params.toString()}`,
      getReportTitle(currentReportType),
    );
  }

  // Custom Reports
  async function handleCustomReportSubmit(e) {
    e.preventDefault();
    currentReportType = "custom";

    const formData = new FormData(elements.customReportForm);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
      if (value) params.append(key, value);
    }

    await generateReport(
      `/admin/reportsCustom?${params.toString()}`,
      "Custom Report",
    );
  }

  // Report Generation
  async function generateReport(url, title) {
    showLoading();

    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error("Failed to generate report");

      const result = await response.json();
      currentReportData = result.data || [];

      renderReport(title, result);
    } catch (error) {
      console.error("Error generating report:", error);
      showError("Failed to generate report. Please try again.");
    }
  }

  function renderReport(title, result) {
    elements.reportResults.style.display = "";
    elements.reportResultsTitle.textContent = title;

    // Render summary cards
    renderSummary(result.summary || {});

    // Render table
    renderTable(result.data || [], result.columns || []);

    // Render chart if applicable
    if (result.chartData) {
      renderChart(result.chartData);
    } else {
      elements.reportChart.style.display = "none";
    }

    // Scroll to results
    elements.reportResults.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }

  function renderSummary(summary) {
    if (!summary || Object.keys(summary).length === 0) {
      elements.reportSummary.innerHTML = "";
      return;
    }

    const cards = Object.entries(summary)
      .map(([key, value]) => {
        const label = formatLabel(key);
        const formattedValue =
          typeof value === "number"
            ? Number.isInteger(value)
              ? value.toLocaleString()
              : value.toFixed(1)
            : value;

        return `
                <div class="summaryCard">
                    <span class="label">${escapeHtml(label)}</span>
                    <span class="value">${escapeHtml(String(formattedValue))}</span>
                </div>
            `;
      })
      .join("");

    elements.reportSummary.innerHTML = cards;
  }

  function renderTable(data, columns) {
    if (!data || data.length === 0) {
      elements.reportTableHead.innerHTML = "";
      elements.reportTableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="emptyState">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <p>No data found for the selected criteria</p>
                    </td>
                </tr>
            `;
      return;
    }

    // Determine columns from first row if not provided
    const cols = columns.length > 0 ? columns : Object.keys(data[0]);

    // Render header
    elements.reportTableHead.innerHTML = `
            <tr>
                ${cols.map((col) => `<th>${escapeHtml(formatLabel(col))}</th>`).join("")}
            </tr>
        `;

    // Render body
    elements.reportTableBody.innerHTML = data
      .map(
        (row) => `
            <tr>
                ${cols.map((col) => `<td>${formatCellValue(col, row[col])}</td>`).join("")}
            </tr>
        `,
      )
      .join("");
  }

  function renderChart(chartData) {
    if (!chartData || !chartData.labels || chartData.labels.length === 0) {
      elements.reportChart.style.display = "none";
      return;
    }

    elements.reportChart.style.display = "";

    // Destroy existing chart
    if (chartInstance) {
      chartInstance.destroy();
    }

    const ctx = elements.reportChartCanvas.getContext("2d");

    const chartType = chartData.type || "bar";
    const colors = generateColors(chartData.labels.length);

    chartInstance = new Chart(ctx, {
      type: chartType,
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: chartData.label || "Count",
            data: chartData.values,
            backgroundColor:
              chartType === "pie" || chartType === "doughnut"
                ? colors
                : "rgba(140, 140, 249, 0.7)",
            borderColor:
              chartType === "pie" || chartType === "doughnut"
                ? "#fff"
                : "rgba(140, 140, 249, 1)",
            borderWidth:
              chartType === "pie" || chartType === "doughnut" ? 2 : 1,
            tension: 0.3,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: chartType === "pie" || chartType === "doughnut",
            position: "right",
          },
        },
        scales:
          chartType === "pie" || chartType === "doughnut"
            ? {}
            : {
                y: {
                  beginAtZero: true,
                },
              },
      },
    });
  }

  // Export Functions
  function exportToPdf() {
    if (!currentReportData || currentReportData.length === 0) {
      alert("No data to export");
      return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Title
    const title = elements.reportResultsTitle.textContent || "Report";
    doc.setFontSize(16);
    doc.text(title, 14, 20);

    // Date
    doc.setFontSize(10);
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 28);

    // Table
    const columns = Object.keys(currentReportData[0] || {});
    const rows = currentReportData.map((row) =>
      columns.map((col) => String(row[col] ?? "")),
    );

    doc.autoTable({
      head: [columns.map(formatLabel)],
      body: rows,
      startY: 35,
      styles: { fontSize: 8 },
      headStyles: { fillColor: [140, 140, 249] },
    });

    doc.save(
      `${title.replace(/\s+/g, "_")}_${new Date().toISOString().split("T")[0]}.pdf`,
    );
  }

  function exportToCsv() {
    if (!currentReportData || currentReportData.length === 0) {
      alert("No data to export");
      return;
    }

    const columns = Object.keys(currentReportData[0] || {});
    const header = columns.map(formatLabel).join(",");
    const rows = currentReportData.map((row) =>
      columns
        .map((col) => {
          const val = row[col];
          const str = String(val ?? "");
          // Escape quotes and wrap in quotes if contains comma
          if (str.includes(",") || str.includes('"') || str.includes("\n")) {
            return `"${str.replace(/"/g, '""')}"`;
          }
          return str;
        })
        .join(","),
    );

    const csv = [header, ...rows].join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const title = elements.reportResultsTitle.textContent || "Report";

    link.href = URL.createObjectURL(blob);
    link.download = `${title.replace(/\s+/g, "_")}_${new Date().toISOString().split("T")[0]}.csv`;
    link.click();
  }

  // Utility Functions
  function showLoading() {
    elements.reportResults.style.display = "";
    elements.reportSummary.innerHTML = "";
    elements.reportTableHead.innerHTML = "";
    elements.reportTableBody.innerHTML = `
            <tr>
                <td colspan="10" class="loading">
                    <div class="spinner"></div>
                </td>
            </tr>
        `;
    elements.reportChart.style.display = "none";
  }

  function showError(message) {
    elements.reportTableBody.innerHTML = `
            <tr>
                <td colspan="10" class="emptyState">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <p>${escapeHtml(message)}</p>
                </td>
            </tr>
        `;
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return "";
    const div = document.createElement("div");
    div.textContent = String(str);
    return div.innerHTML;
  }

  function formatLabel(key) {
    return key
      .replace(/_/g, " ")
      .replace(/([a-z])([A-Z])/g, "$1 $2")
      .split(" ")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(" ");
  }

  function formatCellValue(column, value) {
    if (value === null || value === undefined) return "-";

    const col = column.toLowerCase();

    // Status badges
    if (col === "status") {
      const statusClass = String(value).toLowerCase().replace(/\s+/g, "-");
      return `<span class="status ${statusClass}">${escapeHtml(value)}</span>`;
    }

    // Priority badges
    if (col === "priority") {
      return `<span class="priority ${String(value).toLowerCase()}">${escapeHtml(value)}</span>`;
    }

    // Dates
    if (
      col.includes("date") ||
      col.includes("created_at") ||
      col.includes("at")
    ) {
      if (value) {
        const date = new Date(value);
        if (!isNaN(date)) {
          return escapeHtml(
            date.toLocaleDateString("en-US", {
              year: "numeric",
              month: "short",
              day: "numeric",
            }),
          );
        }
      }
    }

    // Numbers with decimals
    if (typeof value === "number" && !Number.isInteger(value)) {
      return escapeHtml(value.toFixed(1));
    }

    return escapeHtml(String(value));
  }

  function getReportTitle(reportType) {
    const titles = {
      "tickets-by-status": "Tickets by Status",
      "tickets-by-category": "Tickets by Category",
      "tickets-by-role": "Tickets by Role",
      "resolution-time": "Resolution Time Report",
      "staff-performance": "Staff Performance Report",
      "most-active-users": "Most Active Users",
      "ticket-volume-trend": "Ticket Volume Trend",
      "unresolved-backlog": "Unresolved Tickets Over Time",
    };
    return titles[reportType] || "Report";
  }

  function generateColors(count) {
    const baseColors = [
      "rgba(140, 140, 249, 0.8)",
      "rgba(34, 197, 94, 0.8)",
      "rgba(249, 115, 22, 0.8)",
      "rgba(236, 72, 153, 0.8)",
      "rgba(14, 165, 233, 0.8)",
      "rgba(168, 85, 247, 0.8)",
      "rgba(234, 179, 8, 0.8)",
      "rgba(239, 68, 68, 0.8)",
      "rgba(20, 184, 166, 0.8)",
      "rgba(99, 102, 241, 0.8)",
    ];

    const colors = [];
    for (let i = 0; i < count; i++) {
      colors.push(baseColors[i % baseColors.length]);
    }
    return colors;
  }

  // Initialize on DOM ready
  document.addEventListener("DOMContentLoaded", init);
})();
