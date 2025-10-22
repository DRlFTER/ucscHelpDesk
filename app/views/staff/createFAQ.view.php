<?php
// Expects $divisions, $staff_id, $errors, $success from controller
?>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Create FAQ</h2>
      <p class="page-subtitle">Submit a new FAQ for your division</p>
    </div>
    <div class="tickets-container">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">New FAQ</h3>
            <div class="ticket-meta">
              <span>Staff ID: <?php echo htmlspecialchars($staff_id); ?></span>
            </div>
          </div>
        </div>
        <div class="response-section">
          <?php if (!empty($errors)): ?>
            <div class="error" style="background:#fee; border:1px solid #f44; color:#600; padding:10px; margin:10px 0;">
              <?php echo implode('<br>', $errors); ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($success)): ?>
            <div class="success" style="background:#efe; border:1px solid #4c4; color:#060; padding:10px; margin:10px 0;">
              <?php echo htmlspecialchars($success); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="" enctype="multipart/form-data" class="response-form">
            <span class="detail-label">Topic (*)</span>
            <div class="detail-value-box">
              <input type="text" id="topic" name="topic" placeholder="Enter FAQ Question " 
                     value="<?php echo htmlspecialchars($_POST['topic'] ?? ''); ?>" maxlength="50" required>
            </div>

            <span class="detail-label">Content (*)</span>
            <textarea class="response-textarea" id="content" name="content" placeholder="Enter FAQ Answer" 
                      maxlength="500" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>

            <span class="detail-label">Division (*)</span>
            <div class="detail-value-box">
              <select id="division" name="division" required>
                <option value="">Select a division</option>
                <?php foreach ($divisions as $division): ?>
                  <option value="<?php echo $division['did']; ?>" 
                          <?php echo (($_POST['division'] ?? '') == $division['did']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($division['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

          

            <button type="submit" class="submit-response-btn">Submit the FAQ</button>
          </form>
        </div>
    </div>
</main>

