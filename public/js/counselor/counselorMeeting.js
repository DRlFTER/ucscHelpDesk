// Counselor Meeting UI interactions (no media streams)
(function () {
  const toolbar = document.querySelector('.meetingToolbar');
  if (!toolbar) return;

  function togglePressed(btn) {
    const pressed = btn.getAttribute('aria-pressed') === 'true';
    btn.setAttribute('aria-pressed', String(!pressed));
  }

  toolbar.addEventListener('click', (e) => {
    const target = e.target.closest('button');
    if (!target) return;
    const action = target.dataset.action;

    switch (action) {
      case 'mic':
      case 'camera':
      case 'screen':
        togglePressed(target);
        break;
      case 'leave':
        // Basic confirmation, then redirect back to counselor dashboard
        if (confirm('End the meeting?')) {
          window.location.href = '/counselor/dashboard';
        }
        break;
      default:
        break;
    }
  });
})();
