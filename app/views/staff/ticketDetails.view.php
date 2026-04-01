<?php
?>
<style>
    .main-content {
        padding: 45px 84px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 35px;
        font-weight: 500;
        margin: 0 0 6px 0;
        color: #1f2937;
    }

    .page-subtitle {
        font-size: 25px;
        font-weight: 400;
        color: #6b7280;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .ticket-detail-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 32px;
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
    }

    .ticket-detail-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #8c8cf9, #6a6af5);
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding: 0;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 20px;
    }

    .ticket-header-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .ticket-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .ticket-title {
        font-size: 32px;
        font-weight: 600;
        letter-spacing: -0.01em;
        margin: 0;
        color: #1f2937;
        line-height: 1.2;
    }

    .ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 14px;
        color: #9ca3af;
        letter-spacing: 0.02em;
        font-weight: 500;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    /* Status Badge Enhancement */
    .status {
        padding: 10px 18px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .status.pending { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
    .status.agent-assigned { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .status.agent-closed { background: #d1fae5; color: #065f46; border: 1px solid #86efac; }
    .status.closed { background: #d1fae5; color: #065f46; border: 1px solid #86efac; }
    .status.resolved { background: #d1fae5; color: #065f46; border: 1px solid #86efac; }

    .ticket-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        padding: 0;
    }

    .details-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .details-group:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #d1d5db;
    }

    .details-group.separator {
        border-left: none;
        padding-left: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .detail-label {
        font-size: 14px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .detail-value-box {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 500;
        letter-spacing: 0.02em;
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .detail-value-box:hover {
        background: #f9fafb;
        transform: translateX(2px);
        border-color: #9ca3af;
    }

    .value-requested { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    .value-priority-high { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }
    .value-priority-medium { background: #fefce8; color: #ca8a04; border-color: #fde047; }
    .value-priority-low { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }

    /* Enhanced Ticket Description */
    .ticket-description {
        grid-column: 1 / -1;
        margin: 0;
        padding: 24px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }

    .ticket-description:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    .ticket-description h3 {
        font-size: 24px;
        margin: 0 0 16px 0;
        color: #1f2937;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 12px;
    }

    .ticket-description p {
        font-size: 16px;
        line-height: 1.7;
        color: #4b5563;
        margin: 0;
    }

    /* Assignment Section */
    .assignment-section {
        grid-column: 1 / -1;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.08);
        transition: all 0.3s ease;
    }

    .assignment-section h3 {
        font-size: 20px;
        margin: 0 0 12px 0;
        color: #166534;
        font-weight: 600;
    }

    .assignment-section p {
        margin: 0 0 16px 0;
        color: #4b5563;
        font-size: 15px;
    }

    .assign-btn {
        background: #22c55e;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
    }

    .assign-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* Response Section */
    .response-section {
        grid-column: 1 / -1;
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);
    }

    .response-section h3 {
        font-size: 20px;
        margin: 0 0 12px 0;
        color: #92400e;
        font-weight: 600;
    }

    .response-section p {
        margin: 0 0 16px 0;
        color: #92400e;
        font-size: 15px;
    }

    .response-textarea {
        width: 100%;
        min-height: 120px;
        padding: 16px;
        border: 1px solid #d97706;
        border-radius: 12px;
        resize: vertical;
        font-size: 15px;
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        background: white;
        transition: border-color 0.2s ease;
    }

    .response-textarea:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.08);
    }

    .submit-response-btn {
        background: #eab308;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(234, 179, 8, 0.2);
        margin-top: 12px;
    }

    .submit-response-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(234, 179, 8, 0.3);
    }

    /* Forward Section */
    .forward-section {
        grid-column: 1 / -1;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);
    }

    .forward-section h3 {
        font-size: 20px;
        margin: 0 0 12px 0;
        color: #1d4ed8;
        font-weight: 600;
    }

    .forward-section p {
        margin: 0 0 16px 0;
        color: #4b5563;
        font-size: 15px;
    }

    .forward-section select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #93c5fd;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 15px;
        background: white;
        transition: border-color 0.2s ease;
    }

    .forward-section select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
    }

    .forward-btn {
        background: #3b82f6;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
    }

    .forward-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Actions Bar */
    .actions-bar {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 16px;
        padding-top: 20px;
        border-top: 1px solid #f3f4f6;
    }

    .action-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .resolve-btn {
        background: #22c55e;
        color: white;
    }

    .resolve-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .reject-btn {
        background: #ef4444;
        color: white;
    }

    .reject-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .main-content {
            padding: 30px 20px;
        }
        .ticket-body {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .details-group.separator {
            border-top: 1px solid #e5e7eb;
            margin-top: 16px;
            padding-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .page-title { font-size: 28px; }
        .page-subtitle { font-size: 18px; }
        .ticket-header { flex-direction: column; align-items: flex-start; gap: 16px; }
        .ticket-header-right { width: 100%; justify-content: flex-end; }
        .ticket-title { font-size: 26px; }
        .details-group { padding: 16px; }
        .actions-bar { flex-direction: column-reverse; width: 100%; }
        .action-btn { justify-content: center; width: 100%; }
    }

    /* Global Polish */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
    }

    /* Chat Styles */
    .chat-messages {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        height: 300px;
        overflow-y: auto;
        margin-bottom: 16px;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .message {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
    }
    .message.staff {
        align-self: flex-end;
        background-color: #3b82f6;
        color: white;
        border-bottom-right-radius: 2px;
    }
    .message.student {
        align-self: flex-start;
        background-color: #e5e7eb;
        color: #1f2937;
        border-bottom-left-radius: 2px;
    }
    .message-meta {
        font-size: 11px;
        margin-bottom: 4px;
        opacity: 0.8;
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    /* Loading spinner */
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid #fff;
        width: 18px;
        height: 18px;
        animation: spin 1s linear infinite;
        display: none;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .submit-response-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .submit-response-btn.loading .btn-text {
        display: none;
    }

    .submit-response-btn.loading .spinner {
        display: block;
    }
    .chat-date-separator {
        text-align: center;
        margin: 20px 0;
        position: relative;
        display: flex;
        justify-content: center;
    }
    .chat-date-separator span {
        background-color: #e5e7eb;
        color: #4b5563;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
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
            <span class="meta-item">Ticket #<?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
            <span class="meta-item">Created <?php echo htmlspecialchars($ticket['created_at']); ?></span>
          </div>
        </div>
        <div class="ticket-header-right">
          <span class="status <?php echo str_replace(' ', '-', strtolower($ticket['status'])); ?>">
            <?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>
          </span>
        </div>
      </div>
      <div class="ticket-body">
        <div class="details-group">
          <div class="detail-item">
            <span class="detail-label">Ticket type</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['t_type']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Student Name</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Category</span>
            <span class="detail-value-box"><?php echo htmlspecialchars($ticket['category']); ?></span>
          </div>
        </div>
        <div class="details-group separator">
          <?php if ($ticket['meeting_requested']): ?>
            <div class="detail-item">
              <span class="detail-label">Meeting Requested</span>
              <span class="detail-value-box value-requested"><?php echo htmlspecialchars($ticket['meeting_requested']); ?></span>
            </div>
          <?php endif; ?>
            <div class="detail-item">
              <span class="detail-label">Assigned Agent ID</span>
              <span class="detail-value-box"><?php echo htmlspecialchars($ticket['assigned_to']) ?? 'Unassigned'; ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Assigned Agent Name</span>
              <span class="detail-value-box"><?php echo htmlspecialchars($ticket['staff_name']) ?? 'Unassigned'; ?></span>
            </div>
        </div>
      </div>

      <div class="ticket-description">
        <h3>Description</h3>
        <p>
          <?php echo htmlspecialchars($ticket['description'] ?? 'No description provided.'); ?>
        </p>
      </div>

      <?php if (!empty($attachments)): ?>
      <div class="ticket-description" style="margin-top: 20px;">
        <h3>Attachments</h3>
        <ul style="list-style: none; padding-left: 0;">
          <?php foreach ($attachments as $att): ?>
            <li>
              <a href="<?php echo htmlspecialchars($att['url']); ?>" target="_blank" style="color: #4f46e5; text-decoration: none; font-weight: 500;">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#4f46e5" style="vertical-align: middle; margin-right: 5px;"><path d="M260-160q-91 0-155.5-64.5T40-380q0-89 61.5-153.5T250-600h50v80h-50q-58 0-99 41t-41 99q0 58 41 99t99 41h220q58 0 99-41t41-99q0-58-41-99t-99-41h-70v-80h70q91 0 155.5 64.5T800-380q0 89-61.5 153.5T600-160H260Zm220-160L320-480l56-56 64 64v-368h80v368l64-64 56 56-160 160Z"/></svg>
                <?php echo htmlspecialchars($att['name']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

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

      <!-- Chat Section -->
      <?php if ($ticket['status'] !== 'Agent-closed' && isset($ticket['assigned_to']) && $ticket['assigned_to'] == $_SESSION['user']['u_id']): ?>
      <div class="chat-section">
        <h3>Conversation</h3>
        <div id="chat-messages" class="chat-messages">
            <!-- Messages will be loaded here -->
        </div>
        <div class="chat-input-area">
            <textarea id="chat-input" class="response-textarea" placeholder="Type your message here..."></textarea>
            <button id="send-chat-btn" class="submit-response-btn" type="button">
                <span class="btn-text">Send</span>
                <div class="spinner"></div>
            </button>
        </div>
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
  document.querySelector('.submit-response-btn')?.addEventListener('click', function(e) {
    // This is for the old response form if it still exists somewhere, but we replaced it.
    // However, we reused the class .submit-response-btn for the chat send button.
    // So we should handle it specifically.
  });

  const ticketId = <?php echo json_encode($ticket['ticket_id']); ?>;
  const chatMessages = document.getElementById('chat-messages');
  const chatInput = document.getElementById('chat-input');
  const sendBtn = document.getElementById('send-chat-btn');

  if (chatMessages && sendBtn) {
      function formatDate(dateString) {
          const date = new Date(dateString.replace(/-/g, '/')); // Replace - with / for better browser support if needed, though modern browsers handle ISO
          const today = new Date();
          const yesterday = new Date(today);
          yesterday.setDate(yesterday.getDate() - 1);

          if (date.toDateString() === today.toDateString()) {
              return 'Today';
          } else if (date.toDateString() === yesterday.toDateString()) {
              return 'Yesterday';
          } else {
              return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
          }
      }

      function formatTime(dateString) {
          const date = new Date(dateString.replace(/-/g, '/'));
          return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
      }

      async function fetchMessages() {
          try {
              const res = await fetch(`/staff/chatMessages?ticket_id=${ticketId}`);
              const data = await res.json();
              if (data.messages) {
                  let lastDate = null;
                  chatMessages.innerHTML = data.messages.map(msg => {
                      const isStaff = msg.sender_role === 'staff';
                      const typeClass = isStaff ? 'staff' : 'student';
                      
                      const dateStr = formatDate(msg.created_at);
                      const timeStr = formatTime(msg.created_at);
                      
                      let dateHeader = '';
                      if (dateStr !== lastDate) {
                          dateHeader = `<div class="chat-date-separator"><span>${dateStr}</span></div>`;
                          lastDate = dateStr;
                      }
                      
                      return `
                          ${dateHeader}
                          <div class="message ${typeClass}">
                              <div class="message-meta">
                                  <span>${msg.sender_name}</span>
                                  <span>${timeStr}</span>
                              </div>
                              <div class="message-text">${msg.message}</div>
                          </div>
                      `;
                  }).join('');
                  chatMessages.scrollTop = chatMessages.scrollHeight;
              }
          } catch (err) {
              console.error(err);
          }
      }

      sendBtn.addEventListener('click', async () => {
          const text = chatInput.value.trim();
          if (!text) return;

          // Show loading state
          sendBtn.classList.add('loading');
          sendBtn.disabled = true;

          try {
              const res = await fetch('/staff/sendMessage', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ ticket_id: ticketId, message: text })
              });
              const data = await res.json();
              
              if (data.success) {
                  chatInput.value = '';
                  await fetchMessages(); // Wait for messages to reload
              } else {
                  alert('Failed to send message');
              }
          } catch (err) {
              console.error(err);
              alert('Error sending message');
          } finally {
              // Hide loading state
              sendBtn.classList.remove('loading');
              sendBtn.disabled = false;
          }
      });

      // Initial fetch and poll
      fetchMessages();
      setInterval(fetchMessages, 5000);
  }
</script>