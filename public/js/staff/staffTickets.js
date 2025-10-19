document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".tickets-container");

  if (pageError) {
    container.innerHTML = `<div style="padding:12px 14px; border:1px solid #f59e0b; background:#fffbeb; color:#92400e; border-radius:8px;">${pageError}</div>`;
    return;
  }

  if (!Array.isArray(tickets) || tickets.length === 0) {
    container.innerHTML = `<p style="color:#6b7280;">No tickets found for your division(s).</p>`;
    return;
  }

  // Map over tickets and create HTML for each
  container.innerHTML = tickets.map(ticket => {
    // Default status to "Pending" if null or undefined, and ensure it's a string
    const status = ticket.status || "pending";
    const displayStatus = typeof status === "string" ? status.charAt(0).toUpperCase() + status.slice(1) : "Pending";
    const priority = ticket.priority || "medium"; // Default priority if null

    return `
      <article class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">${ticket.title || "No Title"}</h3>
            <div class="ticket-meta">
              <span>Ticket #${ticket.ticket_id || "N/A"}</span>
              <span>${ticket.created_at ? new Date(ticket.created_at).toLocaleString() : "N/A"}</span>
            </div>
          </div>
          <span class="status-badge status-${status.toLowerCase()}">
            ${displayStatus}
          </span>
          <div class="ticket-action">
            <button class="ticket-action-btn" onclick="window.location.href='/index.php?url=staff/ticketDetails&ticket_id=${ticket.ticket_id || 0}'">
              <span>See Ticket</span>
            </button>
          </div>
        </div>
        <div class="ticket-body">
          <div class="details-group">
            <div class="detail-item">
              <span class="detail-label">Student:</span>
              <span class="detail-value-box">${ticket.student_name || "Unknown"}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Category:</span>
              <span class="detail-value-box">${ticket.category || "N/A"}</span>
            </div>
          </div>
          <div class="details-group separator">
            ${ticket.meeting_requested ? `
              <div class="detail-item">
                <span class="detail-label">Meeting:</span>
                <span class="detail-value-box value-requested">${ticket.meeting_requested}</span>
              </div>` : ""}
            <div class="detail-item">
              <span class="detail-label">Priority:</span>
              <span class="detail-value-box value-priority-${priority.toLowerCase()}">
                ${priority.charAt(0).toUpperCase() + priority.slice(1)}
              </span>
            </div>
          </div>
        </div>
      </article>
    `;
  }).join("");

  // Debug: Log tickets to console
  console.log("Tickets:", tickets);
});