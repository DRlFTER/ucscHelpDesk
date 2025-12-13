<?php
?>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Create Announcement</h2>
      <p class="page-subtitle">Submit a new announcement for your division</p>
    </div>
  <div class="content-area">
    <div class="tickets-container">
       <article class="ticket-card">
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
                </article>
        </div>
    </div>
</main>
<style>

    /* Main Layout */
    .main-content {
        padding: 40px 20px;
        max-width: 900px;
        margin: 0 auto;
        min-height: 100vh;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 8px 0;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        font-size: 18px;
        color: #718096;
        margin: 0;
        font-weight: 400;
    }

    .content-area {
        padding: 0;
    }

    /* Card Styling */
    .tickets-container {
        padding: 0;
        border: 1px solid #8c8cf9;
    }

    .ticket-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 0;
    }

    .ticket-header {
        background: #f7fafc;
        color: #4a5568;
        padding: 24px 32px;
        margin: 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .ticket-title-group {
        flex-grow: 1;
    }

    .ticket-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 4px 0;
        letter-spacing: -0.01em;
        color: #2d3748;
    }

    .ticket-meta {
        display: flex;
        gap: 16px;
        font-size: 14px;
        color: #a0aec0;
    }

    .ticket-body {
        padding: 32px;
    }

    /* Success/Error Messages */
    .success, .error {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .success {
        background: #f0fff4;
        color: #38a169;
        border-left: 4px solid #48bb78;
    }

    .error {
        background: #fff5f5;
        color: #e53e3e;
        border-left: 4px solid #f56565;
    }

    /* Form Details Group */
    .details-group {
        display: flex;
        flex-direction: column;
        gap: 28px;
        margin-bottom: 0;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-label {
        font-size: 15px;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    .detail-value-box {
        padding: 24px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        min-height: 80px;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .detail-value-box:hover {
        background: #fafbfc;
        border-color: #cbd5e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .detail-value-box input,
    .detail-value-box select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        background: transparent;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }

    .detail-value-box input:focus,
    .detail-value-box select:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    
.response-section {
  margin-top: 20px;
  padding: 15px;
  border: transparent;
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

