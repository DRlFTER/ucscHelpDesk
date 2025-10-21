document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".tickets-container");
  if (!container || !announcements) {
    console.error("Container or announcements data not found.");
    return;
  }

  container.innerHTML = announcements.map(announcement => {
    return `
      <article class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">${announcement.topic}</h3>
            <div class="ticket-meta">
              <span>${announcement.id}</span>
              <span>${announcement.date_time || 'N/A'}</span>
            </div>
          </div>
          <div class="ticket-action">
            <button class="ticket-action-btn" onclick="window.location.href='/staff/an_view?id=${announcement.id}'">
              <span>View Announcement</span>
            </button>
          </div>
        </div>
        <div class="ticket-body">
          <div class="details-group">
            <div class="detail-item">
              <span class="detail-label">Staff:</span>
              <span class="detail-value-box">${announcement.staff_name}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Division:</span>
              <span class="detail-value-box">${announcement.division_name}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Content:</span>
              <span class="detail-value-box">${announcement.content}</span>
            </div>
          </div>
        </div>
      </article>
    `;
  }).join("");
});