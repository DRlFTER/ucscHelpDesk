<?php
  $__vt = '';
  if (isset($_GET['id']) && $_GET['id'] !== '') {
    $__vt = 'ticket-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string)$_GET['id']);
  } elseif (isset($_GET['code']) && $_GET['code'] !== '') {
    $__vt = 'ticket-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string)$_GET['code']);
  }
  $roleName = isset($role) ? strtolower($role) : strtolower($_SESSION['user']['role'] ?? 'guest');
?>
<main>
  <div class="fullPage">
    <div class="pageHeader">
      <h1 class="pageTitle">Ticket status</h1>
      <p class="pageSubtitle">Track the progress and responses for this ticket</p>
    </div>

    <div class="pageLayout">
      <section class="ticketLeft">
        <div class="card ticketSummary" id="ticketSummaryCard" <?= $__vt ? ('style="view-transition-name: ' . htmlspecialchars($__vt) . '"') : '' ?> >
          <div class="ticketHeader">
            <h2 id="ticketTitle" class="ticketTitle"></h2>
            <span id="ticketStatus" class="status"></span>
          </div>
          <div class="ticketMeta" id="ticketMeta"></div>
          <div class="summaryBody">
            <div class="summarySection">
              <h3 class="sectionTitle">Description</h3>
              <p id="ticketDescriptionText" class="descriptionText"></p>
            </div>
            <div class="summarySection">
              <h3 class="sectionTitle">Attachments</h3>
              <div id="attachmentsList" class="attachmentsList"></div>
            </div>
          </div>
        </div>

        <div class="card conversation">
          <div class="sectionTitleRow">
            <h3 class="sectionTitle">Conversation</h3>
          </div>
          <div id="messages" class="messages"></div>
          <div class="replyBox">
            <input id="replyInput" type="text" placeholder="Type your reply here" aria-label="Reply" />
            <div class="replyActions">
              <button id="attachBtn" class="btnAttachRound" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12 20.5a6 6 0 0 1-8.49-8.49l10-10a4 4 0 0 1 5.66 5.66l-10 10a2 2 0 1 1-2.83-2.83l9-9"/></svg>
              </button>
              <button id="sendBtn" class="btnSvg" type="button" aria-label="Send">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <aside class="ticketRight">
        <div class="card ticketInfo">
          <h3 class="sectionTitle">Ticket information</h3>
          <div id="ticketInfoList" class="infoList"></div>
        </div>

        <div class="card ticketTimeline">
          <h3 class="sectionTitle">Timeline</h3>
          <ul id="timelineList" class="timelineList"></ul>
        </div>

        <div class="card ticketActions">
          <h3 class="visuallyHidden">Actions</h3>
          <div class="btnHolder">
            <button id="resolveBtn" class="btnPrimary" type="button"><span class="btnPrimaryText">Mark as resolved</span></button>
          </div>
          <div class="btnHolder">
            <!-- Delete is default for admin; hidden for counselor -->
            <button id="deleteBtn" class="btnSecondary" style="background-color: #ff7b7bff; display:none;" type="button"><span class="btnSecondaryText" style="color: white;">Delete ticket</span></button>
            <!-- Counselor schedule meeting button (shown when meeting requested) -->
            <button id="scheduleBtn" class="btnSecondary" style="display:none;" type="button"><span class="btnSecondaryText">Schedule meeting</span></button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<!-- Delete confirmation modal (admin only; hidden for others) -->
<div id="deleteModal" class="modalOverlay" aria-hidden="true">
  <div class="msgHolder">
    <div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
      <div class="msgContent">
        <h3 id="deleteModalTitle" class="msgTitle">Delete this ticket?</h3>
        <p class="msgText">This action is permanent and cannot be undone.</p>
        <div class="msgActions">
          <button id="cancelDeleteBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
          <button id="confirmDeleteBtn" type="button" class="btnPrimary btnDanger"><span class="btnPrimaryText">Delete</span></button>
        </div>
      </div>
    </div>
  </div>
  <button type="button" class="modalBackdropClose" aria-label="Close"></button>
  
</div>

<script>
  window.TICKET_FULL_CONFIG = {
    role: '<?= htmlspecialchars($roleName) ?>',
    apiBase: '/<?= htmlspecialchars($roleName) ?>/ticketData',
    deleteEndpoint: '/admin/ticketDelete'
  };
</script>
<script src="/js/ticketFull/ticketFull.js"></script>
