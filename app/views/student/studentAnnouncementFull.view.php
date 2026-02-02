<?php
?>

<div class="announcementFullPage">
  <!-- Page Header with back button -->
  <div class="announcementPageHeader">
    <button class="backBtn" onclick="history.back()">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
    </button>
    <div class="pageHeaderContent">
      <h1 class="pageTitle">Announcement Details</h1>
      <p class="pageSubtitle">View this announcement</p>
    </div>
  </div>

  <!-- Main Content Card -->
  <div class="announcementLayout">
    <!-- Announcement Header -->
    <div class="announcementHeader">
      <h2 class="announcementTitle"><?php echo htmlspecialchars($announcement['topic']); ?></h2>
      <div class="announcementMeta">
        <span>
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <?php echo htmlspecialchars($announcement['date_time']); ?>
        </span>
        <span>ID: <?php echo htmlspecialchars($announcement['id']); ?></span>
      </div>
    </div>

    <!-- Details Section -->
    <div class="announcementDetails">
      <div class="detailCard">
        <span class="detailLabel">Author</span>
        <span class="detailValue"><?php echo htmlspecialchars($announcement['staff_name']); ?></span>
      </div>
      <div class="detailCard">
        <span class="detailLabel">Division</span>
        <span class="detailValue"><?php echo htmlspecialchars($announcement['division_name']); ?></span>
      </div>
    </div>

    <!-- Description Section -->
    <div class="announcementSection">
      <h3 class="sectionTitle">Description</h3>
      <p class="announcementContent"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
    </div>

    <!-- Attachments Section -->
    <div class="attachmentsSection">
      <h3 class="sectionTitle">Attached Files</h3>
      <?php if (empty($files)): ?>
        <p class="noAttachments">No files attached to this announcement.</p>
      <?php else: ?>
        <div class="attachmentsList">
          <?php foreach ($files as $file): ?>
            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download="<?php echo htmlspecialchars($file['file_name']); ?>" class="attachmentItem">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span class="attachmentName"><?php echo htmlspecialchars($file['file_name']); ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="/js/student/studentAnnouncementFull.js" defer></script>
