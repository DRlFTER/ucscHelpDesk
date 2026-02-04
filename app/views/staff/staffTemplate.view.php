<link rel="stylesheet" href="/css/student/studentTemplate.view.css">

<main id="main-content" class="main-content">
  <div class="page-header">
    <div class="header-content">
      <h2 class="page-title">View Template</h2>
      <p class="page-subtitle">View a template to Delete</p>
    </div>
    <button id="staffNewTemplateBtn" class="btnWSvg" type="button" onclick="window.location.href='/staff/createTemplate';">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span class="btnPrimaryText">New Template</span>
    </button>
  </div>

  <div class="tickets-container">
    <?php if (empty($templates)): ?>
      <p>No templates available.</p>
    <?php else: ?>
      <?php foreach ($templates as $template): ?>
        <article class="ticket-card" id="template-<?php echo (int)$template['id']; ?>">
          <div class="ticket-header">
            <div class="ticket-title-group">
              <h3 class="ticket-title"><?php echo htmlspecialchars($template['name'] ?? ''); ?></h3>
              <div class="ticket-meta">
                <span>Category: <?php echo htmlspecialchars($template['category'] ?? ''); ?></span>
              </div>
            </div>
            <div class="ticket-action">
              <button class="ticket-action-btn" onclick="toggleTemplate(<?php echo (int)$template['id']; ?>)">View Template</button>
            </div>
          </div>
          <div class="template-details">
            <div class="ticket-body">
              <div class="details-group">
                <div class="detail-item">
                  <span class="detail-label">Problem:</span>
                  <div class="detail-value-box">
                    <?php echo nl2br(htmlspecialchars(($template['process'] ?? ''))); ?>
                  </div>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Required Info:</span>
                  <div class="detail-value-box">
                    <?php echo nl2br(htmlspecialchars(($template['process'] ?? ''))); ?>
                  </div>
                </div>
              </div>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
              <input type="hidden" name="template_id" value="<?php echo (int)($template['id'] ?? 0); ?>">
              <div class="details-group">
                <?php if (!empty($template['fields']) && is_array($template['fields'])): ?>
                  <?php foreach ($template['fields'] as $field): ?>
                    <div class="detail-item">
                      <span class="detail-label"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $field))); ?>:</span>
                      <div class="detail-value-box">
                        <input type="text" name="<?php echo htmlspecialchars($field); ?>" placeholder="Enter <?php echo htmlspecialchars(str_replace('_', ' ', $field)); ?>">
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
                <div class="detail-item">
                  <span class="detail-label">Upload File:</span>
                  <div class="detail-value-box">
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                  </div>
                </div>
              </div>
              <div class="ticket-action">
                  <button type="submit" class="delete-ticket-btn" name="delete_ticket" onclick="return confirm('Are you sure you want to delete this Template?');">Delete Template</button>
              </div>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
      <?php if (!empty($generated_letter)): ?>
        <div class="detail-item">
          <span class="detail-label">Download Letter:</span>
          <div class="detail-value-box">
            <a href="<?php echo htmlspecialchars($generated_letter); ?>" class="letter-link" target="_blank">Click here</a>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <?php foreach ($errors as $error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>
<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e5e7eb; /* Light border for separation */
  }

  .header-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .page-title {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
    color: #111827; /* Dark gray for professional look */
    line-height: 1.2;
  }

  .page-subtitle {
    margin: 0;
    font-size: 1rem;
    color: #6b7280; /* Medium gray for subtitle */
    font-weight: 400;
  }

  .btnWSvg {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btnPrimaryText {
    font-size: 0.875rem;
  }

  .delete-ticket-btn {
    align-self: flex-end;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: var(--status-rejected-bg, #ffd8d8);  /* Reuse rejected color for consistency */
    color: var(--status-rejected-text, #b50000);
    font-family: "Poppins", sans-serif;
    font-size: 14px;
    font-weight: 400;
    cursor: pointer;
    transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
    border: 1px solid var(--status-rejected-bg);
}

.delete-ticket-btn:hover {
    background: #f56565;  /* Deeper red on hover */
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
}
.tickets-container {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid #8c8cf9;
    border-radius: 26px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  /* Template Card - Adapted from Ticket Card Theme */
  .ticket-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid #e0e7ff;
    border-radius: 15px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(140, 140, 249, 0.1);
    transition: all 0.3s ease;
  }

  .ticket-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(140, 140, 249, 0.15);
    border-color: #8c8cf9;
  }

  /* Header - Matching Ticket Header */
  .ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    border: 1px solid transparent;
    padding: 10px;
  }

  .ticket-title-group {
    flex: 1;
  }

  .ticket-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    line-height: 1.3;
  }

  .ticket-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .meta-category,
  .meta-date {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 400;
  }
</style>

<script src="/js/student/studentTemplate.view.js"></script>