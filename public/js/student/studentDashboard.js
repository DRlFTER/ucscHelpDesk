document.addEventListener("DOMContentLoaded", () => {
  // Fade-in animation
  const fadeElements = document.querySelectorAll(
    ".welcomeCard, .quickActions, .knowledgeBase, .recentTickets, .priority, .announcements, .calendar, .account"
  );
  fadeElements.forEach((el, index) => {
    el.style.opacity = 0;
    el.style.transform = "translateY(10px)";
    setTimeout(() => {
      el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
      el.style.opacity = 1;
      el.style.transform = "translateY(0)";
    }, index * 100);
  });

  // Quick actions button events
  const quickButtons = document.querySelectorAll(".quickActions button");
  quickButtons.forEach((btn) => {
    btn.addEventListener("mouseover", () => {
      btn.style.filter = "brightness(90%)";
    });
    btn.addEventListener("mouseout", () => {
      btn.style.filter = "brightness(100%)";
    });
    btn.addEventListener("click", () => {
      console.log(`Button clicked: ${btn.textContent}`);
    });
  });

  // Search box listener
  const searchInput = document.querySelector(".knowledgeBase input");
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      console.log(`Searching for: ${searchInput.value}`);
    });
  }

  // Tooltip for ticket status
  const statusBadges = document.querySelectorAll(".status");
  statusBadges.forEach((badge) => {
    badge.setAttribute("title", badge.textContent.trim());
  });

  // Recent Tickets: mirror admin hover effect with class toggle for consistency
  const recentTicketLinks = document.querySelectorAll('.recentTickets .ticket');
  recentTicketLinks.forEach((a) => {
    a.addEventListener('mouseenter', () => a.classList.add('isHover'));
    a.addEventListener('mouseleave', () => a.classList.remove('isHover'));
    a.addEventListener('focus', () => a.classList.add('isHover'));
    a.addEventListener('blur', () => a.classList.remove('isHover'));
  });
});
