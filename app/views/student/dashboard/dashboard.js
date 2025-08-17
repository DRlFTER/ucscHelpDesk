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

  // Dummy data population
  const openTicketsCount = document.getElementById("openTicketsCount");
  const lastActivity = document.getElementById("lastActivity");
  if (openTicketsCount) openTicketsCount.textContent = "2 Open Tickets";
  if (lastActivity) lastActivity.textContent = "Last Activity: 2 hours ago";

  const recentTickets = document.getElementById("recentTickets");
  if (recentTickets) {
    recentTickets.innerHTML = `
      <h3>Recent Tickets</h3>
      <a href="#" class="ticket">
        <div class="ticketDetails">
          <p><span class="ticketTitle">WIFI connection issue in Library</span></p>
          <div class="ticketMeta">
            <span class="ticketCategory">Technical Support</span>
            <span class="ticketTimestamp">Updated 2 hours ago</span>
          </div>
        </div>
        <span class="status inProgress">In Progress</span>
      </a>
      <a href="#" class="ticket">
        <div class="ticketDetails">
          <p><span class="ticketTitle">Password reset request</span></p>
          <div class="ticketMeta">
            <span class="ticketCategory">Account Access</span>
            <span class="ticketTimestamp">Updated 1 day ago</span>
          </div>
        </div>
        <span class="status open">Open</span>
      </a>
      <a href="#" class="ticket">
        <div class="ticketDetails">
          <p><span class="ticketTitle">Course registration problem</span></p>
          <div class="ticketMeta">
            <span class="ticketCategory">Academic</span>
            <span class="ticketTimestamp">Updated 3 days ago</span>
          </div>
        </div>
        <span class="status resolved">Resolved</span>
      </a>
    `;
  }

  const prioritySection = document.getElementById("prioritySection");
  if (prioritySection) {
    prioritySection.innerHTML = `
      <h3>Priority</h3>
      <a href="#" class="priorityItem">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="red"><path d="M200-80v-760h640l-80 200 80 200H280v360h-80Z"/></svg>
        <div>
          <span class="priorityTitle">Lecture Hall Changed</span><br>
          <span class="priorityDescription">Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00</span>
        </div>
      </a>
    `;
  }

  const announcementsSection = document.getElementById("announcementsSection");
  if (announcementsSection) {
    announcementsSection.innerHTML = `
      <h3>Announcements</h3>
      <a href="#" class="announcement warning">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#9D5226"><path d="M480-79q-16 0-30.5-6T423-102L102-423q-11-12-17-26.5T79-480q0-16 6-31t17-26l321-321q12-12 26.5-17.5T480-881q16 0 31 5.5t26 17.5l321 321q12 11 17.5 26t5.5 31q0 16-5.5 30.5T858-423L537-102q-11 11-26 17t-31 6Zm-40-361h80v-240h-80v240Zm40 120q17 0 28.5-11.5T520-360q0-17-11.5-28.5T480-400q-17 0-28.5 11.5T440-360q0 17 11.5 28.5T480-320Z"/></svg>
        <div>
          <span class="announcementTitle">System Maintenance</span><br>
          <span class="announcementDescription">Scheduled maintenance on Dec 25, 2:00-4:00 AM</span>
        </div>
      </a>
      <a href="#" class="announcement info">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#2786FF"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
        <div>
          <span class="announcementTitle">New FAQ Section</span><br>
          <span class="announcementDescription">Check out our new updated WIFI troubleshooting guide</span>
        </div>
      </a>
    `;
  }

  const calendarSection = document.getElementById("calendarSection");
  if (calendarSection) {
    calendarSection.innerHTML = `
      <h3>Calendar</h3>
      <a href="#" class="event">
        <div>June 28</div>
        <div>Meeting with Mr. Prasad at W003</div>
        <div>5:00 PM</div>
      </a>
    `;
  }

  const accountSection = document.getElementById("accountSection");
  if (accountSection) {
    accountSection.innerHTML = `
      <h3>Account</h3>
      <div class="accountItems">
        <a href="#" class="accountItem">Profile Settings</a>
        <a href="#" class="accountItem">Notifications</a>
        <a href="#" class="accountItem">Ticket History</a>
      </div>
    `;
  }
});