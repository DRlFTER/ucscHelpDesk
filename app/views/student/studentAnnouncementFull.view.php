<?php
?>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Announcement Details</h2>
      <p class="page-subtitle">View this announcement</p>
    </div>

    <div class="ticket-detail-card">
      <div class="ticket-header">
        <div class="ticket-title-group">
          <h3 class="ticket-title"><?php echo htmlspecialchars($announcement['topic']); ?></h3>
          <div class="ticket-meta">
            <span><?php echo htmlspecialchars($announcement['id']); ?></span>
            <span><?php echo htmlspecialchars($announcement['date_time']); ?></span>
          </div>
        </div>
      </div>
      <div class="ticket-body">
        <div class="details-group">
          <div class="detail-item">
            <span class="detail-label">Author:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($announcement['staff_name']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Division:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($announcement['division_name']); ?></span>
          </div>
        </div>
      </div>
      <h3>Description</h3>
      <p>
        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
      </p>

      <div class="file-section">
        <h3>Attached Files</h3>
        <?php if (empty($files)): ?>
          <p>No files attached.</p>
        <?php else: ?>
          <?php foreach ($files as $file): ?>
            <div class="file-item">
              <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-link">
                <?php echo htmlspecialchars($file['file_name']); ?>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>

<script src="/js/student/studentAnnouncementFull.js" defer></script>
