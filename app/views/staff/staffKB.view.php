<main>
  <div class="fullPage kbPage">
    <div class="pageHeader">
      <h1 class="pageTitle">Knowledge Base</h1>
  <p class="pageSubtitle">Access detailed guides, policies, and technical documentation.</p>
    </div>

    <div class="ticketsFilters kbFiltersRow">
      <div class="search">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 22 21" fill="none">
          <path d="M17.65 18.375L12.1375 12.8625C11.7 13.2125 11.1969 13.4896 10.6281 13.6938C10.0594 13.8979 9.45417 14 8.8125 14C7.22292 14 5.8776 13.4495 4.77656 12.3484C3.67552 11.2474 3.125 9.90208 3.125 8.3125C3.125 6.72292 3.67552 5.3776 4.77656 4.27656C5.8776 3.17552 7.22292 2.625 8.8125 2.625C10.4021 2.625 11.7474 3.17552 12.8484 4.27656C13.9495 5.3776 14.5 6.72292 14.5 8.3125C14.5 8.95417 14.3979 9.55937 14.1938 10.1281C13.9896 10.6969 13.7125 11.2 13.3625 11.6375L18.875 17.15L17.65 18.375ZM8.8125 12.25C9.90625 12.25 10.8359 11.8672 11.6016 11.1016C12.3672 10.3359 12.75 9.40625 12.75 8.3125C12.75 7.21875 12.3672 6.28906 11.6016 5.52344C10.8359 4.75781 9.90625 4.375 8.8125 4.375C7.71875 4.375 6.78906 4.75781 6.02344 5.52344C5.25781 6.28906 4.875 7.21875 4.875 8.3125C4.875 9.40625 5.25781 10.3359 6.02344 11.1016C6.78906 11.8672 7.71875 12.25 8.8125 12.25Z" fill="#808080"/>
        </svg>
        <input id="kbSearchInput" type="text" placeholder="Search documents..." aria-label="Search knowledge base" />
      </div>
       <div class="ticket-action" style="width:250px; justify-content: center; align-items: center; display: flex; margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 20px;">
                <button class="ticket-action-btn" onclick="window.location.href='/staff/createKB';">
                    <span>Create New Resource</span>
                </button>
    </div>
      <div class="filters">
        <div class="selectWrap" id="kbCategoryWrap">
          <button class="selectButton" type="button" aria-haspopup="listbox" aria-expanded="false" aria-controls="kbCategoryList">
            <span id="kbCategoryLabel">All categories</span>
            <span class="selectChevron" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </span>
          </button>
          <ul class="selectList" id="kbCategoryList" role="listbox" tabindex="-1"></ul>
        </div>
        <select id="kbCategorySelect" class="selectHidden" aria-label="Category filter"></select>
      </div>
    </div>

    <section class="pageLayout kbLayout" aria-label="Knowledge base content">
      <div id="kbSections" class="kbSections" aria-live="polite"></div>
    </section>
  </div>
</main>
<style>
    .ticket-action {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  background: var(--CTA, #8c8cf9);
  cursor: pointer;
  transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
}

.ticket-action:hover {
  background-color: #6a6af5;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.ticket-action-btn {
  color: #fff;
  font-family: "Poppins", sans-serif;
  font-size: 14px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
  letter-spacing: 0.16px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}
    </style>
<script>
  const KB_DATA = <?php echo json_encode($kb_data ?? []); ?>;
</script>
<script src="/js/staff/staffKB.js" defer></script>
