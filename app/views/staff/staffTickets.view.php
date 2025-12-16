<?php
?>
<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Assigned Tickets</h2>
      <p class="page-subtitle">Manage and respond to your assigned student issues</p>
    </div>
    
      <div class="content-area">
        <?php if (!empty($error)): ?>
          <div class="error-alert" style="margin:10px 0 20px; padding:12px 14px; border:1px solid #ef4444; background:#fee2e2; color:#991b1b; border-radius:8px;">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="controls-bar">
          <div class="search-bar">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#070708ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="faq-search" type="text" placeholder="Search FAQs..." autocomplete="off"/>
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
    /* Scrolling Fix (full page) *//* Search Bar Theme Match - More Visible */
    .search-bar {
        flex: 1 1 400px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #8c8cf9; /* Purple theme border for visibility */
        background: #f9f9f9; /* Light grey background for contrast */
        border-radius: 30px;
        padding: 11px 22px;
        box-shadow: 0 2px 4px rgba(140, 140, 249, 0.1); /* Subtle purple shadow */
    }

    .search-bar svg {
        stroke: #8c8cf9; /* Purple icon for theme match */
    }

    .search-bar input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 16px;
        width: 100%;
        color: #000; /* Dark text for visibility */
    }

    .search-bar input::placeholder {
        color: #8c8cf9; /* Purple placeholder for theme */
        opacity: 0.7; /* Slight fade for subtlety */
    }

    .search-bar input:focus {
        outline: none; /* No focus ring */
    }

    /* Filters Theme Match - More Visible */
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
        background: rgba(140, 140, 249, 0.1); /* Light purple background for visibility */
        border: 1px solid #8c8cf9; /* Purple border */
        border-radius: 30px;
        padding: 10px 18px;
        font-size: 15px;
        font-weight: 500;
        color: #000; /* Dark text */
        cursor: pointer;
        min-width: 160px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease; /* Smooth effects */
    }

    .filter-btn:hover {
        box-shadow: 0 2px 4px rgba(140, 140, 249, 0.2); /* Purple hover shadow */
        border-color: #6a6af5; /* Darker purple on hover */
    }

    .filter-btn:focus {
        box-shadow: 0 0 0 3px rgba(140, 140, 249, 0.2); /* Purple focus ring */
        outline: none;
    }

    /* Remove default select dropdown arrow */
    .filter-btn select,
    .filter-btn option {
        background: none;
        border: none;
        outline: none;
        color: #000; /* Dark text in dropdown */
    }

    /* Add a custom dropdown arrow */
    .filter-btn {
        background-image: url("images/173_2475.svg");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
        padding-right: 35px; /* space for arrow */
    }

    .filter-btn option:hover {
        background: rgba(140, 140, 249, 0.1); /* Purple hover in dropdown */
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
    // Inject tickets fetched by controller as a global variable
    var tickets = <?= json_encode($tickets ?? []) ?>;
    var pageError = <?= json_encode($error ?? null) ?>;
</script>
<script src="/js/staff/staffTickets.js?v=<?= time() ?>"></script>