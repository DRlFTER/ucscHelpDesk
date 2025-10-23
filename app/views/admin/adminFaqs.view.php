<main>
  <div class="fullPage">
    <div class="pageHeader">
      <h2 class="pageTitle">FAQs</h2>
      <p class="pageSubtitle">Create, edit, and organize frequently asked questions</p>
    </div>
    <div class="ticketsFilters">
      <div class="search">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 22 21" fill="none" aria-hidden="true">
          <path d="M17.65 18.375L12.1375 12.8625C11.7 13.2125 11.1969 13.4896 10.6281 13.6938C10.0594 13.8979 9.45417 14 8.8125 14C7.22292 14 5.8776 13.4495 4.77656 12.3484C3.67552 11.2474 3.125 9.90208 3.125 8.3125C3.125 6.72292 3.67552 5.3776 4.77656 4.27656C5.8776 3.17552 7.22292 2.625 8.8125 2.625C10.4021 2.625 11.7474 3.17552 12.8484 4.27656C13.9495 5.3776 14.5 6.72292 14.5 8.3125C14.5 8.95417 14.3979 9.55937 14.1938 10.1281C13.9896 10.6969 13.7125 11.2 13.3625 11.6375L18.875 17.15L17.65 18.375ZM8.8125 12.25C9.90625 12.25 10.8359 11.8672 11.6016 11.1016C12.3672 10.3359 12.75 9.40625 12.75 8.3125C12.75 7.21875 12.3672 6.28906 11.6016 5.52344C10.8359 4.75781 9.90625 4.375 8.8125 4.375C7.71875 4.375 6.78906 4.75781 6.02344 5.52344C5.25781 6.28906 4.875 7.21875 4.875 8.3125C4.875 9.40625 5.25781 10.3359 6.02344 11.1016C6.78906 11.8672 7.71875 12.25 8.8125 12.25Z" fill="#808080"/>
        </svg>
        <input id="faqSearch" type="text" placeholder="Search FAQs..." aria-label="Search FAQs">
      </div>
      <div class="filters">
        <button id="newFaqBtn" class="btnWSvg" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span class="btnPrimaryText">New FAQ</span>
        </button>
      </div>
    </div>
    <div id="faqsList" class="tickets" aria-live="polite"></div>
      <div class="ticketsPagination">
      <div class="ticketsPageHolder" id="faqsPagination"></div>
      </div>
    </div>
  </div>
</main>

<div id="deleteFaqModal" class="modalOverlay" aria-hidden="true">
  <div class="msgHolder">
    <div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="deleteFaqModalTitle">
      <div class="msgContent">
        <h3 id="deleteFaqModalTitle" class="msgTitle">Delete this FAQ?</h3>
        <p class="msgText">This action is permanent and cannot be undone.</p>
        <div class="msgActions">
          <button id="cancelDeleteFaqBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
          <button id="confirmDeleteFaqBtn" type="button" class="btnPrimary btnDanger"><span class="btnPrimaryText">Delete</span></button>
        </div>
      </div>
    </div>
  </div>
  <button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<div id="editFaqModal" class="modalOverlay" aria-hidden="true">
  <div class="msgHolder">
    <div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="editFaqModalTitle">
      <div class="msgContent">
        <h3 id="editFaqModalTitle" class="msgTitle">New FAQ</h3>
        <form id="faqForm" class="formGrid">
          <label>
            <span>Question</span>
            <input type="text" id="faqQuestion" name="question" required />
          </label>
          <label>
            <span>Answer</span>
            <textarea id="faqAnswer" name="answer" rows="5" required></textarea>
          </label>
          <div class="msgActions">
            <button type="button" id="cancelFaqEditBtn" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
            <button type="submit" class="btnPrimary"><span class="btnPrimaryText">Save</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<script src="/js/admin/adminFaqs.js"></script>