// Student Template View interactions

function toggleTemplate(templateId) {
  const card = document.getElementById('template-' + templateId);
  if (card) {
    card.classList.toggle('active');
  }
}

// Optional: auto-scroll opened template into view
// document.addEventListener('click', (e) => {
//   if (e.target && e.target.classList.contains('ticket-action-btn') && e.target.dataset.templateId) {
//     const id = e.target.dataset.templateId;
//     const el = document.getElementById('template-' + id);
//     if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
//   }
// });
