(() => {
  const search = document.getElementById('faq-search');
  const list = document.getElementById('faq-list');
  const contact = document.getElementById('contact-support-link');
  if (!list) return;

  const items = Array.from(list.querySelectorAll('.faq-item'));
  const getText = (el) => (el.textContent || '').toLowerCase();

  function filter(q) {
    const query = q.trim().toLowerCase();
    let any = false;
    items.forEach(d => {
      const hay = getText(d);
      const match = query === '' || hay.includes(query);
      d.style.display = match ? '' : 'none';
      if (match) any = true;
    });
    list.dataset.empty = any ? '0' : '1';
  }

  search?.addEventListener('input', (e) => filter(e.target.value));
  filter('');

  contact?.addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = '/student/ticket';
  });
})();
