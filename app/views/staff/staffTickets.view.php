<?php
?>
<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>
    
      <div class="content-area">
        <?php if (!empty($error)): ?>
          <div class="error-alert" style="margin:10px 0 20px; padding:12px 14px; border:1px solid #ef4444; background:#fff2f2; color:#991b1b; border-radius:8px;">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="controls-bar">
          <div class="search-bar">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#070708ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="faq-search" type="text" placeholder="Search tickets..." autocomplete="off"/>
          </div>
          <div class="filters">
                        <select id="status-filter" class="filter-btn">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="agent assigned">Agent Assigned</option>
                            <option value="agent-closed">Agent Closed</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
          </div>
        </div>

        <div class="tickets-container"></div>
      </div>
    </div>
</main>

<style>
    /* New: Overdue pending colors (orange theme match) */
    .ticket-card.overdue-pending {
        border-left: 4px solid #f59e0b; /* Amber/orange border */
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
    }

    .ticket-card.overdue-pending .status-badge {
        background-color: #f59e0b;
        color: white;
    }

    /* Existing escalation styles (if you add back later) */
    .ticket-card.level-1 {
        border-left: 4px solid #dc2626; /* Red for level 1 */
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);
    }

    .ticket-card.level-2 {
        border-left: 4px solid #ea580c; /* Orange for level 2 */
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.15);
    }

    .ticket-card.escalated {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* Your existing styles (unchanged) */
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

    .status-badge:hover {
        transform: scale(1.05);
    }

    .status-pending { background-color: #fff68f; color: #844d0f; }
    .status-resolved { background-color: #9effbc; color: #166434; }
    .status-closed { background-color: #9effbc; color: #166434; }
    .status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-agent-closed { background-color: #9effbc; color: #166434; }

    .layout-container {
        display: flex;
        gap: 0;
        min-height: 70vh;
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

    .search-bar {
        flex: 1 1 400px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #8c8cf9;
        background: #f9f9f9;
        border-radius: 30px;
        padding: 11px 22px;
        box-shadow: 0 2px 4px rgba(140, 140, 249, 0.1);
    }

    .search-bar svg {
        stroke: #8c8cf9;
    }

    .search-bar input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 16px;
        width: 100%;
        color: #000;
    }

    .search-bar input::placeholder {
        color: #8c8cf9;
        opacity: 0.7;
    }

    .search-bar input:focus {
        outline: none;
    }

    .filters {
        display: flex;
        gap: 12px;
    }

    .filter-btn {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: rgba(140, 140, 249, 0.1);
        border: 1px solid #8c8cf9;
        border-radius: 30px;
        padding: 10px 18px;
        font-size: 15px;
        font-weight: 500;
        color: #000;
        cursor: pointer;
        min-width: 160px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .filter-btn:hover {
        box-shadow: 0 2px 4px rgba(140, 140, 249, 0.2);
        border-color: #6a6af5;
    }

    .filter-btn:focus {
        box-shadow: 0 0 0 3px rgba(140, 140, 249, 0.2);
        outline: none;
    }

    .filter-btn select,
    .filter-btn option {
        background: none;
        border: none;
        outline: none;
        color: #000;
    }

    .filter-btn {
        background-image: url("images/173_2475.svg");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
        padding-right: 35px;
    }

    .filter-btn option:hover {
        background: rgba(140, 140, 249, 0.1);
    }

    .tickets-container {
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid #8c8cf9;
        border-radius: 26px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    } 

    .ticket-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 15px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(140, 140, 249, 0.1);
        transition: all 0.3s ease;
    }
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid transparent;
        padding: 10px;
    }
    .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(140, 140, 249, 0.15);
            border-color: #8c8cf9;
    }
</style>
<script>
    // Inject tickets (includes is_overdue_pending and assigned_level)
    var tickets = <?= json_encode($tickets ?? []) ?>;
    var pageError = <?= json_encode($error ?? null) ?>;
    var staffLevel = <?= json_encode($staff_level ?? 0) ?>;
</script>
<script src="/js/staff/staffTickets.js?v=<?= time() ?>"></script>