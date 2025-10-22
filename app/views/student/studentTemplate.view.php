<?php
// Student Template view
// Assumes controller supplies: $templates (array), $errors (array), $success (string), $generated_letter (string)
include_once(__DIR__ . "/../../views/common/navbar.php");

$templates = $templates ?? [];
$errors = $errors ?? [];
$success = $success ?? '';
$generated_letter = $generated_letter ?? '';
?>

<!-- Page-specific styles -->
<link rel="stylesheet" href="/css/student/studentTemplate.view.css">

<main id="main-content" class="main-content">
  <div class="page-header">
    <h2 class="page-title">Use Template</h2>
    <p class="page-subtitle">Select a template to submit a ticket</p>
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
              <button class="ticket-action-btn" onclick="toggleTemplate(<?php echo (int)$template['id']; ?>)">Use Template</button>
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
                <div class="detail-item">
                  <span class="detail-label">Process:</span>
                  <div class="detail-value-box">
                    <?php echo nl2br(htmlspecialchars(($template['process'] ?? ''))); ?>
                  </div>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Expected Outcome:</span>
                  <div class="detail-value-box">
                    <?php echo nl2br(htmlspecialchars(($template['outcome'] ?? ''))); ?>
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
                <button type="submit" class="ticket-action-btn">Submit</button>
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

<script src="/js/student/studentTemplate.view.js"></script>
