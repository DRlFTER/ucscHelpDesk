<?php
// Expects $announcement, $files, $errors, $head, $title from controller
// Optional: Check for success message from session (if your Controller has flash support)
$success = ''; // Or: $_SESSION['success'] ?? ''; unset($_SESSION['success']);
?>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Announcement Details</h2>
      <p class="page-subtitle">View and manage this announcement</p>
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
        <?php echo htmlspecialchars($announcement['content']); ?>
      </p>

      <!-- File Section -->
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

      <!-- Edit Section -->
      <div class="edit-section">
        <h3>Edit Announcement</h3>
        <?php if (!empty($errors)): ?>
          <div class="error">
            <?php echo implode('<br>', $errors); ?>
          </div>
        <?php endif; ?>
        <form class="edit-form" method="POST" action="">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($announcement['id']); ?>"> <!-- Key fix: Preserves ID on POST -->
        <input type="text" name="topic" value="<?php echo htmlspecialchars($announcement['topic']); ?>" maxlength="50" required>
        <textarea class="edit-textarea" name="content" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
        <button type="submit" class="update-ticket-btn" name="update_ticket">Update Announcement</button>
        <button type="submit" class="delete-ticket-btn" name="delete_ticket" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete Announcement</button>
      </form>
      </div>
    </div>
  </main>