<main>
    <div class="fullPage">
        <div class="pageLayout">
            <div class="ticketsFilters">
                <div class="search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 22 21" fill="none">
                        <path d="M17.65 18.375L12.1375 12.8625C11.7 13.2125 11.1969 13.4896 10.6281 13.6938C10.0594 13.8979 9.45417 14 8.8125 14C7.22292 14 5.8776 13.4495 4.77656 12.3484C3.67552 11.2474 3.125 9.90208 3.125 8.3125C3.125 6.72292 3.67552 5.3776 4.77656 4.27656C5.8776 3.17552 7.22292 2.625 8.8125 2.625C10.4021 2.625 11.7474 3.17552 12.8484 4.27656C13.9495 5.3776 14.5 6.72292 14.5 8.3125C14.5 8.95417 14.3979 9.55937 14.1938 10.1281C13.9896 10.6969 13.7125 11.2 13.3625 11.6375L18.875 17.15L17.65 18.375ZM8.8125 12.25C9.90625 12.25 10.8359 11.8672 11.6016 11.1016C12.3672 10.3359 12.75 9.40625 12.75 8.3125C12.75 7.21875 12.3672 6.28906 11.6016 5.52344C10.8359 4.75781 9.90625 4.375 8.8125 4.375C7.71875 4.375 6.78906 4.75781 6.02344 5.52344C5.25781 6.28906 4.875 7.21875 4.875 8.3125C4.875 9.40625 5.25781 10.3359 6.02344 11.1016C6.78906 11.8672 7.71875 12.25 8.8125 12.25Z" fill="#808080"/>
                    </svg>
                    <input id="ticketSearch" type="text" placeholder="Search announcements...">
                </div>
                <div class="filters">
                    <select id="divisionFilter" aria-label="Division filter"></select>
                    <select id="sortFilter" aria-label="Sort filter"></select>
                </div>
            </div>
            <div class="tickets" id="announcements-root" data-announcements='<?php echo json_encode($announcements ?? []); ?>'></div>
            <div class="ticketsPagination">
                <div class="ticketsPageHolder"></div>
            </div>

            <?php if (defined('DEBUG') && DEBUG && !empty($dbError)): ?>
                <div class="debug" style="max-width:900px; margin:12px auto; padding:10px; background:#fee; border:1px solid #fbb; color:#600;">
                    <strong>DB Error:</strong> <?php echo htmlspecialchars($dbError); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="/js/student/studentAnnouncements.js"></script>
