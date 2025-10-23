<?php
?>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Create Announcement</h2>
      <p class="page-subtitle">Submit a new announcement for your division</p>
    </div>
    <div class="tickets-container">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">New Announcement</h3>
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
              <input type="text" id="topic" name="topic" placeholder="Enter announcement topic" 
                     value="<?php echo htmlspecialchars($_POST['topic'] ?? ''); ?>" maxlength="50" required>
            </div>

            <span class="detail-label">Content (*)</span>
            <textarea class="response-textarea" id="content" name="content" placeholder="Enter announcement content" 
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

            <span class="detail-label">File (Optional)</span>
            <div class="detail-value-box">
              <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            </div>

            <button type="submit" class="submit-response-btn">Submit the Announcement</button>
          </form>
        </div>
    </div>
</main>
<style>
        .tickets-container {
           background: rgba(255, 255, 255, 0.5);
    border: 1px solid #8c8cf9;
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
        border: 1px solid transparent;
        border-radius: 15px;
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

    
.response-section {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #8c8cf9;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.response-section h3 {
  font-size: 22px;
  margin-bottom: 10px;
  font-family: "Inter", sans-serif;
}

.response-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 15px;
}

.response-textarea {
  width: 100%;
  min-height: 100px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  font-family: "Inter", sans-serif;
  resize: vertical;
  outline: none;
}

.response-textarea:focus {
  border-color: #8c8cf9;
}

.submit-response-btn {
  align-self: flex-end;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  background: #8c8cf9;
  color: #fff;
  font-family: "Poppins", sans-serif;
  font-size: 14px;
  font-weight: 400;
  cursor: pointer;
  transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
}

.submit-response-btn:hover {
  background-color: #6a6af5;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
  </style>

