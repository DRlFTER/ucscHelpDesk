<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <?= $head ?>
    <style>
        :root {
            --color-primary: #8c8cf9;
            --color-text-dark: #000000;
            --color-text-body: #394353;
            --color-text-light: #454545;
            --color-text-white: #ffffff;
            --color-bg-main: #ffffff;
            --color-bg-nav: #f3f3f3;
            --color-bg-card: #fafafa;
            --color-bg-filter: #eaeaea;
            --color-border-light: #dedede;
            --color-border-medium: #171718;
            --color-border-strong: #9ca3af;
            --color-border-card: #d7d7d7;
            --color-border-separator: #c5c5c5;
            --status-pending-bg: #fff68f;
            --status-pending-text: #844d0f;
            --status-resolved-bg: #9effbc;
            --status-resolved-text: #166434;
            --status-rejected-bg: #ffd8d8;
            --status-rejected-text: #b50000;
            --priority-high-bg: #ffd8d8;
            --priority-high-text: #b50000;
            --priority-medium-bg: #fff68f;
            --priority-medium-text: #844d0f;
            --priority-low-bg: #badbff;
            --priority-low-text: #3300ff;
            --font-family-main: 'Inter', 'SF Pro', sans-serif;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: var(--font-family-main);
            background-color: var(--color-bg-main);
            color: var(--color-text-dark);
        }
        .main-content {
            padding: 45px 84px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            text-align: center;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 0;
            padding-top: 50px;
            position: relative;
        }
        .header-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin: 0 0 0 40px;
            padding-top: 50px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.2;
        }
        .page-subtitle {
            margin: 0;
            font-size: 1rem;
            color: #6b7280;
            font-weight: 400;
            margin-bottom: 2rem;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .content-area {
            padding: 20px;
            overflow-y: auto;
        }
        .error-alert {
            margin: 10px 0 20px;
            padding: 12px 14px;
            border: 1px solid #ef4444;
            background: #fff2f2;
            color: #991b1b;
            border-radius: 8px;
        }
        .report-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(140, 140, 249, 0.05);
            border-radius: 15px;
            border: 1px solid #e0e7ff;
        }
        .filter-item {
            flex: 1;
            min-width: 150px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-item label {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-text-body);
        }
        .filter-item input,
        .filter-item select {
            padding: 10px 15px;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            background: var(--color-bg-filter);
            font-size: 14px;
            color: var(--color-text-dark);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        .filter-item input:focus,
        .filter-item select:focus {
            outline: none;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            box-shadow: 0 0 0 3px rgba(140, 140, 249, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        }
        .generate-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: var(--color-primary);
            color: var(--color-text-white);
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.15s ease;
            align-self: flex-end;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .generate-btn:hover {
            background: #6a6af5;
            transform: translateY(-1px);
        }
        .filter-item input:hover {
            border: 1px solid #6a6af5;
        }
        .hidden {
            display: none !important;
        }
        .report-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border: 1px solid #e0e7ff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(140, 140, 249, 0.1);
            margin-bottom: 20px;
        }
        .report-header {
            padding: 15px 20px;
            background: var(--color-bg-card);
            border-bottom: 1px solid var(--color-border-card);
        }
        .report-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-text-dark);
        }
        .table-container {
            overflow-x: auto;
            padding: 20px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .report-table th,
        .report-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--color-border-light);
            word-break: break-word;
        }
        .report-table th {
            background: var(--color-bg-filter);
            font-weight: 600;
            color: var(--color-text-body);
            position: sticky;
            top: 0;
        }
        .report-table tr:hover {
            background: rgba(140, 140, 249, 0.05);
        }
        .status-badge,
        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            letter-spacing: 0.5px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .status-badge:hover,
        .priority-badge:hover {
            transform: scale(1.05);
        }
        .status-pending { background-color: var(--status-pending-bg); color: var(--status-pending-text); }
        .status-resolved { background-color: var(--status-resolved-bg); color: var(--status-resolved-text); }
        .status-agent-assigned { background-color: #badbff; color: #3300ff; }
        .status-agent-closed { background-color: var(--status-resolved-bg); color: var(--status-resolved-text); }
        .priority-high { background-color: var(--priority-high-bg); color: var(--priority-high-text); }
        .priority-medium { background-color: var(--priority-medium-bg); color: var(--priority-medium-text); }
        .priority-low { background-color: var(--priority-low-bg); color: var(--priority-low-text); }
        .overdue-badge {
            background-color: #ef4444;
            color: white;
        }
        .no-data-alert {
            padding: 20px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }
        /* Summary Cards */
        .summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            padding: 15px;
            background: var(--color-bg-card);
            border: 1px solid var(--color-border-card);
            border-radius: 8px;
            flex: 1;
            min-width: 150px;
        }
        .stat-card h6 {
            margin: 0 0 10px 0;
            color: var(--color-text-body);
            font-size: 14px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: var(--color-text-dark);
        }
        /* Chart Container */
        .chart-container {
            background: var(--color-bg-card);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        #statusChart, #overdueChart, #counselorChart, #escalationChart {
            max-height: 300px;
            margin: 0 auto;
        }
        /* Print Fallback Text */
        .print-fallback {
            display: none;
            font-size: 14px;
            margin-top: 10px;
            text-align: left;
        }
        /* Download Section - PDF Only */
        .download-section {
            text-align: center;
            margin-top: 20px;
        }
        .download-section .generate-btn {
            margin: 0 5px;
        }
        /* Row-wise Display Styles (UI Only) */
        .row-wise-display {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .row-wise-display h6 {
            margin: 0 0 10px 0;
        }
        .row-wise-display div {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            background: white;
        }
        /* Print-Friendly Styles - FIXED: Hide Cards/Charts/Row-Wise, Force Badges */
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            /* Hide ALL Non-Content + Unwanted Sections */
            body * { visibility: hidden !important; }
            .report-filters, .download-section, .generate-btn, .print-btn, .error-alert, .no-data-alert, .hidden,
            time, .timestamp, [class*="url"], [class*="path"],
            .summary-cards, .stat-card, .print-fallback, .row-wise-display { /* FIXED: Hide cards/row-wise/fallback, but keep chart-container */
                display: none !important;
                visibility: hidden !important;
            }
            /* Reveal Core: Just Header + Table + Charts */
            .main-content, .content-area, .report-card, .report-header, .table-container, .report-table, .report-table *, .chart-container {
                visibility: visible !important;
                display: block !important; /* Divs as blocks */
                position: static !important;
                page-break-before: avoid !important;
                margin-top: 0 !important;
                padding-top: 0 !important; /* FIXED: Zero top padding to remove blank space */
            }
            /* Chart Container Styles for Print - FIXED: Tighter margins/padding */
            .chart-container {
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                margin: 0 !important; /* FIXED: No margin */
                padding: 5px !important; /* FIXED: Minimal padding */
                width: 100% !important;
                border: none !important; /* FIXED: No border */
                background: white !important;
                text-align: center !important;
                max-width: 100% !important;
            }
            .chart-container h6 {
                font-size: 10px !important;
                font-weight: bold !important;
                margin: 0 0 2px 0 !important; /* FIXED: Minimal bottom margin */
                color: black !important;
                text-align: center !important;
                visibility: visible !important;
                padding: 0 !important;
            }
            canvas {
                max-width: 100% !important;
                max-height: 250px !important; /* FIXED: Slightly larger to fill space better */
                height: auto !important;
                width: auto !important;
                margin: 0 auto !important; /* FIXED: Center the canvas */
                display: block !important; /* FIXED: Ensure block display for centering */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                visibility: visible !important;
            }
            #statusChart, #overdueChart, #counselorChart, #escalationChart {
                max-height: 250px !important;
            }
            /* Report Card/Table - FIXED: Tighter top */
            .report-card {
                margin-top: 0 !important;
                margin-bottom: 0 !important; /* FIXED: No bottom margin */
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                overflow: visible !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
            }
            .report-header {
                padding: 2px !important; /* FIXED: Minimal */
                margin: 0 !important;
            }
            .report-header h5 {
                font-size: 11px !important;
                padding: 2px !important; /* FIXED: Minimal */
                border-bottom: 1px solid #ccc !important;
                margin: 0 !important;
            }
            .table-container {
                padding: 2px !important; /* FIXED: Minimal */
                width: 100% !important;
                overflow: visible !important;
                border: none !important;
                margin: 0 !important;
            }
            h6 { /* FIXED: For the summary view h6 in table-container */
                margin: 0 0 5px 0 !important;
                padding: 0 !important;
                font-size: 10px !important;
            }
            /* Table-Specific: Restore Layout */
            .report-table {
                display: table !important;
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 8px !important; /* FIXED: Tiny for density */
                table-layout: auto !important;
                border: 1px solid black !important;
                margin: 0 !important;
                page-break-inside: auto !important;
            }
            .report-table thead { display: table-header-group !important; }
            .report-table tbody { display: table-row-group !important; }
            .report-table tr { display: table-row !important; page-break-inside: avoid !important; }
            .report-table th, .report-table td {
                display: table-cell !important;
                border: 1px solid black !important;
                padding: 2px !important; /* FIXED: Super tight */
                word-break: break-word !important;
                hyphens: auto !important;
                overflow: visible !important;
                background: white !important;
                vertical-align: top !important;
                line-height: 1.0 !important; /* FIXED: Compress max */
                page-break-inside: avoid !important;
                width: auto !important;
                min-width: 0 !important;
                color: black !important; /* Force cell text */
            }
            .report-table thead th {
                background: #f0f0f0 !important;
                font-weight: bold !important;
                border-bottom: 2px solid black !important;
                font-size: 8px !important;
            }
            .report-table tbody tr:nth-child(even) { background: #f9f9f9 !important; }
            /* FIXED: Badges - Preserve UI Colors in Print */
            .status-badge, .priority-badge, .overdue-badge {
                visibility: visible !important;
                display: inline-block !important;
                padding: 1px 3px !important; /* Tiny for print */
                border: 1px solid #666 !important;
                margin: 0 !important;
                white-space: normal !important;
                font-size: 8px !important;
                font-weight: bold !important;
                text-shadow: 0 0 1px #000 !important; /* FIXED: Shadow for PDF crispness */
                min-width: 30px !important; /* FIXED: No collapse */
                min-height: 12px !important;
                text-align: center !important;
                line-height: 1.0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Other Tweaks */
            .main-content { padding: 0 !important; font-size: 9px !important; line-height: 1.0 !important; margin: 0 !important; } /* FIXED: Zero padding/margin */
            .content-area { padding: 0 !important; width: 100% !important; margin: 0 !important; } /* FIXED: Zero padding/margin */
            @page {
                size: A4 portrait !important;
                margin: 0.1cm !important; /* FIXED: Even tighter margins */
                @top-center { content: none !important; }
                @bottom-center { content: none !important; }
                @top-left { content: none !important; }
                @top-right { content: none !important; }
                @bottom-left { content: none !important; }
                @bottom-right { content: none !important; }
            }
        }
        @media (max-width: 992px) {
            .main-content { padding: 30px 20px; }
            .report-filters { flex-direction: column; }
            .filter-item { min-width: auto; }
            .summary-cards { flex-direction: column; }
        }
        @media (max-width: 768px) {
            .page-title { font-size: 1.5rem; }
            .page-subtitle { font-size: 0.875rem; }
            .report-table { font-size: 12px; }
            .report-table th, .report-table td { padding: 8px 6px; }
        }
        /* ============================================= */
/*              PRINT OPTIMIZATIONS              */
/* ============================================= */

@media print {
    /* Hide global navigation/sidebar elements */
    nav,
    header,
    .sidebar,
    .side-nav,
    .global-navigation,
    aside,
    .navbar,
    .topbar,
    footer,
    .sidenavWrapper,
    .no-print {
        display: none !important;
        visibility: hidden !important;
    }

    /* Ensure main content uses full page width */
    .main-content,
    .content-area,
    #main-content {
        margin: 0 !important;
        padding: 8px !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    @page {
        margin: 0.7cm 0.5cm !important;
    }
}
    </style>
</head>
<body>
    <main id="main-content" class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title">Generate Reports</h2>
                <p class="page-subtitle">Create and view detailed reports for tickets, assignments, and escalations</p>
            </div>
        </div>
       
        <div class="content-area">
            <?php if ($error): ?>
                <div class="error-alert">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <!-- Report Filter Form - HIDDEN IN PRINT BY CSS -->
            <form method="POST" class="report-filters">
                <div class="filter-item">
                    <label for="report_type">Report Type *</label>
                    <select name="report_type" id="report_type" required onchange="toggleFilters()">
                        <option value="all_tickets" <?= $report_type == 'all_tickets' ? 'selected' : '' ?>>All Tickets</option>
                        <option value="overdue_tickets" <?= $report_type == 'overdue_tickets' ? 'selected' : '' ?>>Overdue Tickets</option>
                        <option value="counselor_assignment" <?= $report_type == 'counselor_assignment' ? 'selected' : '' ?>>Counselor Assignments</option>
                        <option value="escalation" <?= $report_type == 'escalation' ? 'selected' : '' ?>>Escalations</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="filter-item">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <div class="filter-item hidden" id="status_div">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="agent assigned" <?= $status == 'agent assigned' ? 'selected' : '' ?>>Agent Assigned</option>
                        <option value="resolved" <?= $status == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="agent-closed" <?= $status == 'agent-closed' ? 'selected' : '' ?>>Agent Closed</option>
                    </select>
                </div>
                <div class="filter-item hidden" id="priority_div">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority">
                        <option value="">All</option>
                        <option value="low" <?= $priority == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $priority == 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $priority == 'high' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
                <div class="filter-item hidden" id="division_div">
                    <label for="division_id">Division</label>
                    <select name="division_id" id="division_id">
                        <option value="">All Divisions</option>
                        <?php foreach ($divisions as $div): ?>
                            <option value="<?= $div['did'] ?>" <?= $division_id == $div['did'] ? 'selected' : '' ?>><?= htmlspecialchars($div['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item hidden" id="level_div">
                    <label for="level">Escalation Level</label>
                    <select name="level" id="level">
                        <option value="0">All Levels</option>
                        <option value="1" <?= $level == 1 ? 'selected' : '' ?>>Level 1</option>
                        <option value="2" <?= $level == 2 ? 'selected' : '' ?>>Level 2</option>
                        <option value="3" <?= $level == 3 ? 'selected' : '' ?>>Level 3</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label style="visibility: hidden;">&nbsp;</label>
                    <button type="submit" class="generate-btn">Generate Report</button>
                </div>
            </form>
            <?php if (!empty($reports)): ?>
                <!-- Summary Cards - HIDDEN IN PRINT NOW -->
                <div class="summary-cards">
                    <?php if ($report_type === 'all_tickets' && !empty($summary)): ?>
                        <div class="stat-card">
                            <h6>Total Tickets</h6>
                            <p><?= $summary['total_tickets'] ?? 0 ?></p>
                        </div>
                        <div class="stat-card">
                            <h6>Pending (%)</h6>
                            <p><?= $summary['pending_pct'] ?? 0 ?>%</p>
                        </div>
                        <div class="stat-card">
                            <h6>Resolved (%)</h6>
                            <p><?= $summary['resolved_pct'] ?? 0 ?>%</p>
                        </div>
                    <?php elseif ($report_type === 'overdue_tickets' && !empty($summary)): ?>
                        <div class="stat-card">
                            <h6>Total Overdue</h6>
                            <p><?= $summary['total_overdue'] ?? 0 ?></p>
                        </div>
                        <div class="stat-card">
                            <h6>Avg Days Overdue</h6>
                            <p><?= round($summary['avg_days_overdue'] ?? 0) ?> days</p>
                        </div>
                    <?php elseif ($report_type === 'counselor_assignment' && !empty($summary)): ?>
                        <div class="stat-card">
                            <h6>Total Assignments</h6>
                            <p><?= $summary['total_assignments'] ?? 0 ?></p>
                        </div>
                        <div class="stat-card">
                            <h6>Avg per Counselor</h6>
                            <p><?= $summary['avg_per_counselor'] ?? 0 ?></p>
                        </div>
                    <?php elseif ($report_type === 'escalation' && !empty($summary)): ?>
                        <div class="stat-card">
                            <h6>Total Escalations</h6>
                            <p><?= $summary['total_escalations'] ?? 0 ?></p>
                        </div>
                        <div class="stat-card">
                            <h6>Level 1 (%)</h6>
                            <p><?= $summary['level1_pct'] ?? 0 ?>%</p>
                        </div>
                        <div class="stat-card">
                            <h6>Level 3 (%)</h6>
                            <p><?= $summary['level3_pct'] ?? 0 ?>%</p>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Charts - VISIBLE IN PRINT NOW -->
                <?php if ($report_type === 'all_tickets' && !empty($summary)): ?>
                    <?php
                    $pending_count = $summary['pending_count'] ?? 0;
                    $resolved_count = $summary['resolved_count'] ?? 0;
                    $agent_assigned_count = $summary['agent_assigned_count'] ?? 0;
                    $agent_closed_count = $summary['agent_closed_count'] ?? 0;
                    $closed_count = $summary['closed_count'] ?? 0;
                    $total = $summary['total_tickets'] ?? 0;
                    $pending_pct = $total ? round(($pending_count / $total) * 100, 1) : 0;
                    $resolved_pct = $total ? round(($resolved_count / $total) * 100, 1) : 0;
                    $agent_assigned_pct = $total ? round(($agent_assigned_count / $total) * 100, 1) : 0;
                    $agent_closed_pct = $total ? round(($agent_closed_count / $total) * 100, 1) : 0;
                    $closed_pct = $total ? round(($closed_count / $total) * 100, 1) : 0;
                    ?>
                    <div class="chart-container">
                        <h6 style="margin: 0 0 10px 0; color: var(--color-text-body);">Status Breakdown (Pie Chart)</h6>
                        <canvas id="statusChart"></canvas>
                        <div class="print-fallback">Status Breakdown: Pending <?= $pending_pct ?>% (<?= $pending_count ?> tickets), Resolved <?= $resolved_pct ?>% (<?= $resolved_count ?>), Other <?= $other_pct ?>% (<?= $other_count ?>)</div>
                    </div>
              <?php elseif ($report_type === 'overdue_tickets' && !empty($summary)): ?>
    <?php
    $days_data = json_decode($summary['days_breakdown'] ?? '{}', true);
    $labels = array_keys($days_data);
    $values = array_values($days_data);
    ?>
    <div class="chart-container">
        <h6 style="margin: 0 0 10px 0; color: var(--color-text-body);">Overdue Tickets by Days (Pie Chart)</h6>
        <canvas id="overdueChart"></canvas>
        <div class="print-fallback">
            Overdue Breakdown: 
            <?php foreach ($days_data as $range => $count): ?>
                <?= $range ?> (<?= $count ?>), 
            <?php endforeach; ?>
        </div>
    </div>
                    
                <?php elseif ($report_type === 'counselor_assignment' && !empty($reports)): ?>
    <?php
    $top_counselor = array_slice($reports, 0, 8); // Top 8 counselor
    $counselor_names = [];
    $counselor_counts = [];
    foreach ($top_counselor as $counselor) {
        $counselor_names[] = $counselor['counselor_name'];
        $counselor_counts[] = (int)$counselor['ticket_count'];
    }
    ?>
    <div class="chart-container">
        <h6 style="margin: 0 0 10px 0; color: var(--color-text-body);">Currently Agent Assigned Tickets per Counselor</h6>
        <canvas id="counselorChart"></canvas>
        <div class="print-fallback">
            Agent Assigned Tickets: <?= implode('; ', array_map(fn($n, $c) => "$n: $c", $counselor_names, $counselor_counts)) ?>
        </div>
    </div>
                <?php elseif ($report_type === 'escalation' && !empty($summary)): ?>
                    <?php
                    $total = $summary['total_escalations'] ?? 0;
                    $l1 = $total ? round(($summary['level1_count'] / $total) * 100, 1) : 0;
                    $l2 = $total ? round(($summary['level2_count'] / $total) * 100, 1) : 0;
                    $l3 = $total ? round(($summary['level3_count'] / $total) * 100, 1) : 0;
                    $other_l = 100 - $l1 - $l2 - $l3;
                    $l1_count = $summary['level1_count'] ?? 0;
                    $l2_count = $summary['level2_count'] ?? 0;
                    $l3_count = $summary['level3_count'] ?? 0;
                    $other_count = $total - $l1_count - $l2_count - $l3_count;
                    ?>
                    <div class="chart-container">
                        <h6 style="margin: 0 0 10px 0; color: var(--color-text-body);">Escalations by Level (Pie Chart)</h6>
                        <canvas id="escalationChart"></canvas>
                        <div class="print-fallback">Escalation Breakdown: Level 1 <?= $l1 ?>% (<?= $l1_count ?>), Level 2 <?= $l2 ?>% (<?= $l2_count ?>), Level 3 <?= $l3 ?>% (<?= $l3_count ?>), Other <?= $other_l ?>% (<?= $other_count ?>)</div>
                    </div>
                <?php endif; ?>
                <div class="report-card">
                    <div class="report-header">
                        <h5><?= ucwords(str_replace('_', ' ', $report_type)) ?> Report (<?= count($reports) ?> results)</h5>
                    </div>
                    <div class="table-container">
                        <?php
                        $col_count = $report_type === 'all_tickets' ? 7 : ($report_type === 'overdue_tickets' ? 6 : ($report_type === 'counselor_assignment' ? 4 : 7));
                        ?>
                        <!-- Row-wise Display - HIDDEN IN PRINT -->
                        <!-- Column-wise Display (Table) -->
                        <h6 style="margin: 0 0 10px 0;">Summary View (Column-wise)</h6>
                        <table class="report-table" data-cols="<?= $col_count ?>">
                            <thead>
                                <tr>
                                    <?php if ($report_type === 'all_tickets'): ?>
                                        <th>Ticket ID</th><th>Title</th><th>Status</th><th>Priority</th><th>Student</th><th>Category</th><th>Created</th>
                                    <?php elseif ($report_type === 'overdue_tickets'): ?>
                                        <th>Ticket ID</th><th>Title</th><th>Student</th><th>Category</th><th>Days Overdue</th><th>Created</th>
                                    <?php elseif ($report_type === 'counselor_assignment'): ?>
                                        <th>Counselor Name</th><th>Email</th><th>Ticket Count</th><th>Status</th>
                                    <?php elseif ($report_type === 'escalation'): ?>
                                        <th>Ticket ID</th><th>Title</th><th>Student</th><th>Level 1 Date</th><th>Level 2 Date</th><th>Level 3 Date</th><th>Created</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $row): ?>
                                    <tr>
                                        <?php if ($report_type === 'all_tickets'): ?>
                                            <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                            <td><span class="status-badge status-<?= htmlspecialchars(str_replace(' ', '-', $row['status'])) ?>"><?= htmlspecialchars($row['status'] ?? 'N/A') ?></span></td> <!-- FIXED: Fallback 'N/A' -->
                                            <td><span class="priority-badge priority-<?= htmlspecialchars($row['priority']) ?>"><?= htmlspecialchars($row['priority'] ?? 'N/A') ?></span></td> <!-- FIXED: Fallback 'N/A' -->
                                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td><?= htmlspecialchars($row['category']) ?></td>
                                            <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                                        <?php elseif ($report_type === 'overdue_tickets'): ?>
                                            <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td><?= htmlspecialchars($row['category']) ?></td>
                                            <td><span class="status-badge overdue-badge"><?= $row['days_overdue'] ?> days</span></td>
                                            <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                                        <?php elseif ($report_type === 'counselor_assignment'): ?>
                                            <td><?= htmlspecialchars($row['counselor_name']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><strong><?= $row['ticket_count'] ?></strong></td>
                                            <td><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                                        <?php elseif ($report_type === 'escalation'): ?>
                                            <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td><?= $row['level_1'] ? date('Y-m-d H:i', strtotime($row['level_1'])) : 'N/A' ?></td>
                                            <td><?= $row['level_2'] ? date('Y-m-d H:i', strtotime($row['level_2'])) : 'N/A' ?></td>
                                            <td><?= $row['level_3'] ? date('Y-m-d H:i', strtotime($row['level_3'])) : 'N/A' ?></td>
                                            <td><?= date('Y-m-d', strtotime($row['ticket_date'])) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Download Section - HIDDEN IN PRINT -->
                <div class="download-section">
                    <button class="generate-btn print-btn" onclick="window.print()">Download PDF (Print)</button>
                </div>
            <?php else: ?>
                <div class="no-data-alert">No data found. Adjust filters and generate again.</div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        function toggleFilters() {
            const type = document.getElementById('report_type').value;
            document.getElementById('status_div').classList.toggle('hidden', type !== 'all_tickets');
            document.getElementById('priority_div').classList.toggle('hidden', type !== 'all_tickets');
            document.getElementById('division_div').classList.toggle('hidden', type !== 'overdue_tickets');
            document.getElementById('level_div').classList.toggle('hidden', type !== 'escalation');
        }
        toggleFilters();
        // FIXED: Print Prep - Force Badge Text
        window.addEventListener('beforeprint', function() {
            // FIXED: Removed nav and sidebar hiding to keep left navigation visible
            document.querySelectorAll('time, .timestamp, [class*="url"], [class*="path"]').forEach(el => el.style.display = 'none');
           
            // FIXED: Force badge text + fallback (Preserve UI colors)
            document.querySelectorAll('.status-badge, .priority-badge, .overdue-badge').forEach(badge => {
                const text = badge.textContent.trim();
                if (!text || text === '') {
                    badge.textContent = 'N/A'; // FIXED: Fill empties
                }
                // FIXED: No forced background or color - let UI styles apply
                badge.style.fontWeight = 'bold !important';
                badge.style.padding = '1px 3px !important';
                badge.style.minWidth = '30px !important';
                badge.style.border = '1px solid #666 !important';
            });
            // Table tweaks
            document.querySelectorAll('.report-table td, .report-table th').forEach(cell => {
                if (cell.textContent.trim() === '') cell.textContent = '—';
                cell.style.border = '1px solid black !important';
                cell.style.padding = '2px !important';
                cell.style.wordBreak = 'break-word !important';
                cell.style.lFneHeight = '1.0 !important';
                cell.style.color = 'black !important';
                cell.style.fontSize = '8px !important';
            });
            // Chart tweaks for print - FIXED: Center the canvas
            document.querySelectorAll('canvas').forEach(canvas => {
                canvas.style.maxWidth = '100% !important';
                canvas.style.maxHeight = '250px !important';
                canvas.style.width = 'auto !important';
                canvas.style.height = 'auto !important';
                canvas.style.margin = '0 auto !important';
                canvas.style.display = 'block !important';
            });
            // Redraw charts if Chart.js instances exist (optional enhancement)
            if (window.statusChart) window.statusChart.resize();
            if (window.overdueChart) window.overdueChart.resize();
            if (window.counselorChart) window.counselorChart.resize();
            if (window.escalationChart) window.escalationChart.resize();
        });
        // Charts JS (unchanged, but assign to window for print resize)
        <?php if ($report_type === 'all_tickets' && !empty($summary)): ?>
            const ctxStatus = document.getElementById('statusChart')?.getContext('2d');
            if (ctxStatus) {
                window.statusChart = new Chart(ctxStatus, {
                    type: 'pie',
                    data: {
                        labels: ['Pending (<?= $pending_pct ?>%)', 'Resolved (<?= $resolved_pct ?>%)', 'Agent Assigned (<?= $agent_assigned_pct ?>%)', 'Agent Closed (<?= $agent_closed_pct ?>%)', 'Closed (<?= $closed_pct ?>%)'],
                        datasets: [{
                            data: [<?= $pending_count ?>, <?= $resolved_count ?>, <?= $agent_assigned_count ?>, <?= $agent_closed_count ?>, <?= $closed_count ?>],
                            backgroundColor: ['#78290Fbc', '#15616Dc9', '#FF7D00c9', '#C0C0C0c9', '#4B4B4Bc9']
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
      <?php elseif ($report_type === 'overdue_tickets' && !empty($summary)): ?>
    const ctxOverdue = document.getElementById('overdueChart')?.getContext('2d');
    if (ctxOverdue) {
        window.overdueChart = new Chart(ctxOverdue, {
            type: 'pie',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($values) ?>,
                    backgroundColor: ['#ff6384', '#ff9f40', '#ffc107', '#ef4444']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
        <?php elseif ($report_type === 'counselor_assignment' && !empty($reports)): ?>
    const ctxCounselor = document.getElementById('counselorChart')?.getContext('2d');
    if (ctxCounselor) {
        window.counselorChart = new Chart(ctxCounselor, {
            type: 'bar',  
            data: {
                labels: <?= json_encode($counselor_names) ?>,
                datasets: [{
                    label: 'Agent Assigned Tickets',
                    data: <?= json_encode($counselor_counts) ?>,
                    backgroundColor: '#3b82f6',
                    borderColor: '#1e40af',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    } 
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
        <?php elseif ($report_type === 'escalation' && !empty($summary)): ?>
            const ctxEscalation = document.getElementById('escalationChart')?.getContext('2d');
            if (ctxEscalation) {
                window.escalationChart = new Chart(ctxEscalation, {
                    type: 'pie',
                    data: {
                        labels: ['Level 1 (<?= $l1 ?>%)', 'Level 2 (<?= $l2 ?>%)', 'Level 3 (<?= $l3 ?>%)', 'Other (<?= $other_l ?>%)'],
                        datasets: [{
                            data: [<?= $l1_count ?>, <?= $l2_count ?>, <?= $l3_count ?>, <?= $other_count ?>],
                            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0']
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        <?php endif; ?>
    </script>
</body>
</html>