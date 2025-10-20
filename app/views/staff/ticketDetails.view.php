<style>
    /* Reuse styles from dashboard for consistency */
    .main-content {
        padding: 45px 84px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 35px;
        font-weight: 500;
        margin: 0 0 6px 0;
    }

    .page-subtitle {
        font-size: 25px;
        font-weight: 400;
        color: var(--color-text-body);
        margin: 0;
        letter-spacing: 0.5px;
    }

    .ticket-detail-card {
        background-color: var(--color-bg-card);
        border: 1px solid var(--color-border-card);
        border-radius: 15px;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 10px;
    }

    .ticket-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ticket-title {
        font-size: 28px;
        font-weight: 500;
        letter-spacing: 0.56px;
        margin: 0;
    }

    .ticket-meta {
        display: flex;
        gap: 36px;
        font-size: 16px;
        color: var(--color-text-light);
        letter-spacing: 0.32px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.28px;
        white-space: nowrap;
    }

    .status-review { background-color: var(--status-review-bg); color: var(--status-review-text); }
    .status-resolved { background-color: var(--status-resolved-bg); color: var(--status-resolved-text); }
    .status-rejected { background-color: var(--status-rejected-bg); color: var(--status-rejected-text); }

    .ticket-body {
        display: flex;
        gap: 30px;
        padding: 15px;
        flex-wrap: wrap;
    }

    .details-group {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .details-group.separator {
        padding-left: 30px;
        border-left: 1px solid var(--color-border-separator);
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 200px;
    }

    .detail-label {
        font-size: 20px;
        font-weight: 400;
        letter-spacing: 0.4px;
    }

    .detail-value-box {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 400;
        letter-spacing: 0.32px;
        background-color: #ececec;
        color: var(--color-text-light);
    }

    .value-requested { background-color: #d8ebff; }
    .value-priority-high { background-color: var(--priority-high-bg); color: var(--priority-high-text); }
    .value-priority-medium { background-color: var(--priority-medium-bg); color: var(--priority-medium-text); }
    .value-priority-low { background-color: var(--priority-low-bg); color: var(--priority-low-text); }

    /* Additional sections for more details */
    .ticket-description {
        margin-top: 20px;
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 10px;
    }

    .ticket-description h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .ticket-description p {
        font-size: 16px;
        line-height: 1.5;
        color: var(--color-text-body);
    }

    .actions-bar {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 20px;
    }

    .action-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.25s ease;
    }

    .resolve-btn {
        background-color: #4caf50;
        color: white;
    }

    .resolve-btn:hover {
        background-color: #388e3c;
    }

    .reject-btn {
        background-color: #f44336;
        color: white;
    }

    .reject-btn:hover {
        background-color: #d32f2f;
    }

    /* New Styles for Assignment, Response, and Forwarding */
    .assignment-section, .response-section, .forward-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 10px;
    }

    .assignment-section h3 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .assign-btn {
        background-color: #2196F3;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .assign-btn:hover {
        background-color: #1976D2;
    }

    .response-textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: vertical;
        font-size: 14px;
    }

    .submit-response-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }

    .submit-response-btn:hover {
        background-color: #45a049;
    }

    .forward-section select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .forward-btn {
        background-color: #FF9800;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .forward-btn:hover {
        background-color: #F57C00;
    }

    @media (max-width: 992px) {
        .main-content {
            padding: 30px 20px;
        }
        .ticket-body {
            flex-direction: column;
            gap: 20px;
        }
        .details-group.separator {
            border-left: none;
            padding-left: 0;
            padding-top: 20px;
            border-top: 1px solid var(--color-border-separator);
        }
    }

    @media (max-width: 768px) {
        .page-title { font-size: 28px; }
        .page-subtitle { font-size: 18px; }
        .ticket-header { flex-direction: column; align-items: flex-start; }
        .ticket-header-right { width: 100%; justify-content: flex-end; }
        .details-group { flex-direction: column; gap: 15px; }
        .actions-bar { flex-direction: column; }
    }
</style>

<main id="main-content" class="main-content">
    <div class="page-header">
      <h2 class="page-title">Ticket Details</h2>
      <p class="page-subtitle">View and manage this ticket</p>
    </div>

    <div class="ticket-detail-card">
      <div class="ticket-header">
        <div class="ticket-title-group">
          <h3 class="ticket-title"><?php echo htmlspecialchars($ticket['title']); ?></h3>
          <div class="ticket-meta">
            <span><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
            <span><?php echo htmlspecialchars($ticket['created_at']); ?></span>
          </div>
        </div>
        <div class="ticket-header-right">
          <span class="status-badge status-<?php echo htmlspecialchars($ticket['status']); ?>">
            <?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>
          </span>
        </div>
      </div>
      <div class="ticket-body">
        <div class="details-group">
          <div class="detail-item">
            <span class="detail-label">Student:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Category:</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['category']); ?></span>
          </div>
        </div>
        <div class="details-group separator">
          <?php if ($ticket['meeting_requested']): ?>
            <div class="detail-item">
              <span class="detail-label">Meeting:</span>
              <span class="detail-value-box value-requested"><?php echo htmlspecialchars($ticket['meeting_requested']); ?></span>
            </div>
          <?php endif; ?>
          <div class="detail-item">
            <span class="detail-label">Priority:</span>
            <span class="detail-value-box value-priority-<?php echo htmlspecialchars($ticket['priority']); ?>">
              <?php echo ucfirst(htmlspecialchars($ticket['priority'])); ?>
            </span>
          </div>
        </div>
      </div>

      <div class="ticket-description">
        <h3>Description</h3>
        <p>
          <?php echo htmlspecialchars($ticket['description'] ?? 'No description provided.'); ?>
        </p>
      </div>

      <!-- Assignment Section (Only for Pending Status) -->
      <?php if ($ticket['status'] === 'pending'): ?>
        <div class="assignment-section">
          <h3>Assign to Yourself</h3>
          <p>This ticket is pending. Click to assign it to your account.</p>
          <form method="POST" action="">
            <input type="hidden" name="action" value="assign">
            <button type="submit" class="assign-btn">Get Assigned</button>
          </form>
        </div>
      <?php endif; ?>

      <!-- Response Section (Always Visible) -->
      <?php if (isset($ticket['assigned_to']) && $ticket['assigned_to'] == $_SESSION['user']['u_id']): ?>
      <div class="response-section">
        <h3>Add Response</h3>
        <p>Add a comment or update to this ticket.</p>
        <form method="POST" action="">
          <input type="hidden" name="action" value="respond">
          <textarea name="response" class="response-textarea" placeholder="Enter your response here..." required></textarea>
          <button type="submit" class="submit-response-btn">Submit Response</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Forward Section (Only if Assigned to Current Staff) -->
      <?php if (isset($ticket['assigned_to']) && $ticket['assigned_to'] == $_SESSION['user']['u_id']): ?>
        <div class="forward-section">
          <h3>Forward Ticket</h3>
          <p>Forward this ticket to another staff member.</p>
          <form method="POST" action="">
            <input type="hidden" name="action" value="forward">
            <select name="forward_to" required>
              <option value="">Select staff member</option>
              <?php foreach ($staff_members as $member): ?>
                <option value="<?php echo $member['u_id']; ?>">
                  <?php echo htmlspecialchars($member['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="forward-btn">Forward Ticket</button>
          </form>
        </div>
      <?php endif; ?>

      <!-- Actions bar for managing the ticket (Resolve/Reject only if assigned) -->
      <?php if (isset($ticket['assigned_to']) && $ticket['assigned_to'] == $_SESSION['user']['u_id']): ?>
        <div class="actions-bar">
          <form method="POST" action="" style="display: inline;">
            <input type="hidden" name="action" value="resolve">
            <button type="submit" class="action-btn resolve-btn">Resolve Ticket</button>
          </form>
          <form method="POST" action="" style="display: inline;">
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="action-btn reject-btn">Close Ticket</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </main>

<script>
  // Basic validation for response
  document.querySelector('.submit-response-btn').addEventListener('click', function(e) {
    const response = document.querySelector('.response-textarea').value.trim();
    if (!response) {
      e.preventDefault();
      alert('Response cannot be empty.');
    }
  });
</script>