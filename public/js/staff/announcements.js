document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('announcements-root');
  if (!root) return;

  const raw = root.getAttribute('data-announcements') || '[]';
  let announcements = [];
  try { announcements = JSON.parse(raw); } catch (e) { console.error('Invalid announcements JSON', e); }

  if (!Array.isArray(announcements) || announcements.length === 0) {
    root.innerHTML = '<p>No announcements found.</p>';
    return;
  }

  function esc(s) {
    if (!s && s !== 0) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  function fmtDate(dt) {
    try { return new Date(dt).toLocaleString(); } catch (e) { return dt || ''; }
  }

  const html = announcements.map(a => {
    return `
      <article class="ticket-card">
        <div class="ticket-header">
          <div class="ticket-title-group">
            <h3 class="ticket-title">${esc(a.topic)}</h3>
            <div class="ticket-meta">
              <span>Announcement #${esc(a.id)}</span>
              <span>${fmtDate(a.date_time)}</span>
            </div>
          </div>
          <div class="ticket-action">
            <button class="ticket-action-btn" onclick="window.location.href='/staff/anView?id=${encodeURIComponent(a.id)}'">
              <span>View Announcement</span>
            </button>
          </div>
        </div>
        <div class="ticket-body">
          <div class="details-group">
            <div class="detail-item">
              <span class="detail-label">Staff:</span>
              <span class="detail-value-box">${esc(a.staff_name)}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Division:</span>
              <span class="detail-value-box">${esc(a.division_name)}</span>
            </div>
          </div>
        </div>
      </article>
    `;
  }).join("");

  root.innerHTML = html;
});
