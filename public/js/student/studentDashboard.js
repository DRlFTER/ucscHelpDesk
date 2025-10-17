document.addEventListener("DOMContentLoaded", () => {
  // Defer non-critical UI work to idle time to improve perceived load
  function scheduleIdle(fn) {
    if ('requestIdleCallback' in window) {
      requestIdleCallback(fn, { timeout: 500 });
    } else {
      setTimeout(fn, 200);
    }
  }

  scheduleIdle(() => {
    // Fade-in animation (non-blocking)
    const fadeElements = document.querySelectorAll(
      ".welcomeCard, .quickActions, .knowledgeBase, .recentTickets, .priority, .announcements, .calendar, .account"
    );
    fadeElements.forEach((el, index) => {
      el.style.opacity = 0;
      el.style.transform = "translateY(10px)";
      setTimeout(() => {
        el.style.transition = "opacity 0.45s ease, transform 0.45s ease";
        el.style.opacity = 1;
        el.style.transform = "translateY(0)";
      }, index * 80);
    });

    // Quick actions button hover effects
    const quickButtons = document.querySelectorAll(".quickActions button, .quickActions .quickActionItem");
    quickButtons.forEach((btn) => {
      btn.addEventListener("mouseover", () => {
        btn.style.filter = "brightness(90%)";
      });
      btn.addEventListener("mouseout", () => {
        btn.style.filter = "brightness(100%)";
      });
      btn.addEventListener("click", () => {
        // Keep click lightweight — avoid expensive DOM work here
      });
    });

    // Tooltip for ticket status
    const statusBadges = document.querySelectorAll(".status");
    statusBadges.forEach((badge) => {
      badge.setAttribute("title", badge.textContent.trim());
    });

    // Recent Tickets: hover/focus class toggles
    const recentTicketLinks = document.querySelectorAll('.recentTickets .ticket');
    recentTicketLinks.forEach((a) => {
      a.addEventListener('mouseenter', () => a.classList.add('isHover'));
      a.addEventListener('mouseleave', () => a.classList.remove('isHover'));
      a.addEventListener('focus', () => a.classList.add('isHover'));
      a.addEventListener('blur', () => a.classList.remove('isHover'));
    });
  });

  // Lightweight immediate behavior: knowledge base search input logging (cheap)
  const searchInput = document.querySelector(".knowledgeBase input");
  if (searchInput) {
    let t = null;
    searchInput.addEventListener("input", () => {
      clearTimeout(t);
      t = setTimeout(() => {
        // Replace this console.log with integration to your KB search endpoint later
        console.log(`Searching for: ${searchInput.value}`);
      }, 250);
    });
  }
});
