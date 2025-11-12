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

  function renderTickets(list) {
    if (!Array.isArray(list) || list.length === 0) {
      container.innerHTML = `<p style="color:#6b7280;">No tickets found for your division(s).</p>`;
      return;
    }

    container.innerHTML = list.map(ticket => {
      const status = ticket.status || "pending";
      const displayStatus = typeof status === "string" ? status.charAt(0).toUpperCase() + status.slice(1) : "Pending";
      const priority = ticket.priority || "medium";
      const statusClass = status.toLowerCase().replace(/\s+/g, '-');

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
          </div>
          <div class="details-group">
            <div class="detail-item">
              <span class="status-badge status-${statusClass}">
            ${displayStatus}
          </span>
            </div>
          </div>
        </div>
      </article>
    `;
    }).join("");
  }

  // Initial render
  renderTickets(tickets);

  // Wire up status filter
  const statusSelect = document.getElementById('status-filter');
  if (statusSelect) {
    statusSelect.addEventListener('change', () => {
      const sel = statusSelect.value || '';
      const normSel = sel.toString().toLowerCase().trim().replace(/\s+/g, '-');
      if (!normSel) {
        renderTickets(tickets);
        return;
      }
      const filtered = tickets.filter(t => {
        const st = (t.status || 'pending').toString().toLowerCase().trim().replace(/\s+/g, '-');
        return st === normSel;
      });
      renderTickets(filtered);
    });
  }

  const searchInput = document.getElementById('faq-search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const searchInput = e.target.value.toLowerCase().trim()
      if(!searchInput){
        currentTickets = [...tickets];
        renderTickets(currentTickets);  
        return;
      }
      const filtered = tickets.filter(t => {
        const title = (t.title || '').toLowerCase().trim();
        return title.includes(searchInput);
      });
      currentTickets = filtered;
      renderTickets(currentTickets);
    });
  }

  // Debug: Log tickets to console
  console.log("Tickets:", tickets);
});