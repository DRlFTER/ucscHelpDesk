<?php
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Announcements</h2>
        <p class="page-subtitle">Latest updates and notices</p>
    </div>

        <div class="content-area">

            <div class="ticket-action" style="width:250px; justify-content: center; align-items: center; display: flex; margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 20px;">
                <button class="ticket-action-btn" onclick="window.location.href='/staff/staffAnnCreate';">
                    <span>Create New Announcement</span>
                </button>
            </div>


            <div class="tickets-container" id="announcements-root" data-announcements='<?php echo json_encode($announcements ?? []); ?>'>

            </div>

            <?php if (defined('DEBUG') && DEBUG && !empty($dbError)): ?>
                <div class="debug" style="max-width:900px; margin:12px auto; padding:10px; background:#fee; border:1px solid #fbb; color:#600;">
                    <strong>DB Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
    echo "<div id='success-toast' style='position:fixed; top:20px; right:20px; background:#4c4; color:white; padding:10px; border-radius:5px; z-index:9999;'>
            $successMsg
          </div>
          <script>
            setTimeout(function() {
                document.getElementById('success-toast').style.display = 'none';
            }, 3000);  // Auto-hide after 3s
          </script>";
}
?>

<style>
    * {
        box-sizing: border-box;
    }


    .status-badge {
        padding: 5px 10px;
        border-radius: 17px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.24px;
        white-space: nowrap;
    }

    .status-badge.status-pending { background-color: #fff68f; color: #844d0f; }
    .status-badge.status-resolved { background-color: #9effbc; color: #166434; }
    .status-badge.status-closed { background-color: #9effbc; color: #166434; }
    .status-badge.status-agent-assigned { background-color: #badbff; color: #3300ff; }
    .status-badge.status-agent-closed { background-color: #9effbc; color: #166434; }

    /* Layout Styles - Optimized for Full Page Scrolling */
    .layout-container {
        display: flex;
        gap: 0;
        width: 100%; /* Ensure full width without overflow */
        position: relative; /* Relative positioning for better flow control */
    }

    .navMenu {
        flex: 0 0 250px; /* Fixed sidebar width */
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        /* No fixed height or overflow - sidebar grows/shrinks with content */
    }

    .sideNav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 20px 15px;
        /* Allows sidebar to expand naturally */
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
        min-width: 0; /* Prevents flex item from overflowing */
        /* No overflow property - relies on body scroll for the whole page */
    }

    /* Ensure Full Page Scrolling */
    html {
        height: auto; /* Natural height */
        overflow-x: hidden; /* No horizontal scroll */
    }

    body {
        margin: 0;
        padding: 0;
        height: auto; /* Allows body to grow with content */
        overflow-x: hidden; /* Prevent horizontal overflow */
        overflow-y: auto; /* Vertical scroll on body for whole page */
    }
          .tickets-container {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid #f9f9f9;
    border-radius: 26px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 15px;
} 

.ticket-card {
    background-color: #f9f9f9; 
    border: 1px solid #8c8cf9;
    border-radius: 15px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid #f9f9f9;
        padding: 10px;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ticket-title {
        font-size: 21px;
        font-weight: 400;
        letter-spacing: 0.42px;
        margin: 0;
    }

    .ticket-meta {
        display: flex;
        gap: 36px;
        font-size: 13px;
        color: var(--color-text-light);
        letter-spacing: 0.26px;
        flex-wrap: wrap;
    }

    /* Announcement Card Styles (if JS doesn't handle; fallback for static rendering) */
    .announcement-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .announcement-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .announcement-title {
        font-size: 18px;
        font-weight: 500;
        margin: 0;
    }

    .announcement-meta {
        font-size: 12px;
        color: #666;
    }

    .announcement-body {
        margin-bottom: 10px;
        color: #444;
    }

    .view-btn {
        background: #4a90e2;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
    }

    .view-btn:hover {
        background: #357abd;
    }

    /* Responsive - Stack on smaller screens to avoid issues */
    @media (max-width: 992px) {
        .layout-container {
            flex-direction: column;
            width: 100%;
        }
        .navMenu {
            flex: none;
            border-right: none;
            border-bottom: 1px solid #dee2e6;
            order: -1; /* Sidebar on top on mobile */
        }
        .content-area {
            padding: 10px;
            order: 1;
        }
    }
</style>

<script>
// Fallback rendering if JS file fails to load
document.addEventListener('DOMContentLoaded', function() {
    const root = document.getElementById('announcements-root');
    const announcementsData = JSON.parse(root.dataset.announcements || '[]');
    
    if (announcementsData.length === 0) {
        root.innerHTML = '<p style="text-align: center; color: #666; padding: 40px;">No announcements available.</p>';
        return;
    }

    let html = '';
    announcementsData.forEach(function(ann) {
        html += `
            <article class="announcement-card">
                <div class="announcement-header">
                    <div>
                        <h3 class="announcement-title">${ann.topic || 'Untitled'}</h3>
                        <div class="announcement-meta">
                            By ${ann.staff_name || 'Unknown'} • ${ann.division_name || 'Unknown Division'} • ${new Date(ann.date_time).toLocaleString()}
                        </div>
                    </div>
                    <a href="/staff/anView?id=${ann.id}" class="view-btn">View Announcement</a>
                </div>
                <div class="announcement-body">
                    ${ann.content ? (ann.content.length > 200 ? ann.content.substring(0, 200) + '...' : ann.content) : 'No content available.'}
                </div>
            </article>
        `;
    });
    root.innerHTML = html;
});
</script>