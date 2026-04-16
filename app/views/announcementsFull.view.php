<?php
  $__vt = '';
  if (isset($_GET['id']) && $_GET['id'] !== '') {
    $__vt = 'announcement-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string)$_GET['id']);
  }
  $roleName = isset($role) ? strtolower($role) : strtolower($_SESSION['user']['role'] ?? 'guest');
  $canEdit = in_array($roleName, ['staff', 'admin']);
?>
<main>
  <div class="announcementFullPage" <?= $__vt ? ('style="view-transition-name: ' . htmlspecialchars($__vt) . '"') : '' ?>>
    <!-- Page Header with back button -->
    <div class="announcementPageHeader">
      <button class="backBtn" onclick="history.back()" aria-label="Go back">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
      </button>
      <div class="pageHeaderContent">
        <h1 class="pageTitle">Announcement Details</h1>
        <p class="pageSubtitle"><?= $canEdit ? 'View and manage this announcement' : 'View this announcement' ?></p>
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

      <?php if ($canEdit): ?>
      <!-- Edit Section (Staff/Admin only) -->
      <div class="editSection">
        <h3 class="sectionTitle">Edit Announcement</h3>
        <?php if (!empty($errors)): ?>
          <div class="errorMsg">
            <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
          <div class="successMsg">
            <?php echo htmlspecialchars($success); ?>
          </div>
        <?php endif; ?>
        <form class="editForm" method="POST" action="">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
          <div class="formField">
            <label for="editTopic" class="formLabel">Topic</label>
            <input type="text" id="editTopic" name="topic" value="<?php echo htmlspecialchars($announcement['topic']); ?>" maxlength="50" required placeholder="Announcement topic">
          </div>
          <div class="formField">
            <label for="editContent" class="formLabel">Content</label>
            <textarea id="editContent" class="editTextarea" name="content" required placeholder="Announcement content"><?php echo htmlspecialchars($announcement['content']); ?></textarea>
          </div>
          <div class="formActions">
            <button type="button" class="btnPrimary" onclick="document.getElementById('updateAnnModal').classList.add('open');">
              <span class="btnPrimaryText">Update Announcement</span>
            </button>
            <button type="button" class="btnDanger" onclick="document.getElementById('deleteAnnModal').classList.add('open');">
              <span class="btnDangerText">Delete Announcement</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Custom Update Modal -->
      <div id="updateAnnModal" class="modalOverlay">
        <div class="modalBackdropClose" onclick="document.getElementById('updateAnnModal').classList.remove('open')"></div>
        <div class="msgHolder">
          <div class="msgContainer">
            <h3 class="msgTitle">Update Announcement</h3>
            <p class="msgText">Are you sure you want to update this announcement with the new details?</p>
            <div class="msgActions">
              <button type="button" class="btnSecondary" style="background:#e5e7eb; color:#374151; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;" onclick="document.getElementById('updateAnnModal').classList.remove('open')">Cancel</button>
              <button type="button" class="btnPrimary" style="background:#8c8cf9; color:#fff; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;" onclick="submitAnnForm('update_ticket')">Yes, Update</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Custom Delete Modal -->
      <div id="deleteAnnModal" class="modalOverlay">
        <div class="modalBackdropClose" onclick="document.getElementById('deleteAnnModal').classList.remove('open')"></div>
        <div class="msgHolder">
          <div class="msgContainer">
            <h3 class="msgTitle">Delete Announcement</h3>
            <p class="msgText">Are you sure you want to delete this announcement? This cannot be undone.</p>
            <div class="msgActions">
              <button type="button" class="btnSecondary" style="background:#e5e7eb; color:#374151; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;" onclick="document.getElementById('deleteAnnModal').classList.remove('open')">Cancel</button>
              <button type="button" class="btnDanger" style="background:#ef4444; color:#fff; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;" onclick="submitAnnForm('delete_ticket')">Yes, Delete</button>
            </div>
          </div>
        </div>
      </div>

      <script>
      function submitAnnForm(actionType) {
        const form = document.querySelector('.editForm');
        // Add hidden input so the controller knows which action is taken
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = actionType;
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);

        // If deleting, disable required fields to bypass HTML5 validation
        if (actionType === 'delete_ticket') {
          form.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));
          form.submit();
          return;
        }

        // Native check validity if updating
        if (form.checkValidity()) {
          form.submit();
        } else {
          document.getElementById('updateAnnModal').classList.remove('open');
          form.reportValidity(); // Show native html5 validation tooltips
        }
      }
      </script>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
window.ANNOUNCEMENT_CONFIG = {
  role: '<?= htmlspecialchars($roleName) ?>',
  canEdit: <?= $canEdit ? 'true' : 'false' ?>
};
</script>
<script src="/js/announcements/announcementFull.js" defer></script>
