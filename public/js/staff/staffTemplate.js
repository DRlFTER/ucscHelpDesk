function toggleTemplate(templateId) {
  const card = document.getElementById('template-' + templateId);
  if (card) {
    card.classList.toggle('active');
  }
}
