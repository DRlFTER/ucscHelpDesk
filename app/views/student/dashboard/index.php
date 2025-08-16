<?php
$pageTitle = "Student Dashboard";
$pageCSS = "./dashboard.css";
include_once(__DIR__ . "/../../common/header.php");
include_once(__DIR__ . "/../../common/navbar.php");
include_once(__DIR__ . "/../../common/footer.php");
?>

<script src="./dashboard.js"></script>

<main>
  <div class="dashboardContainer">
    <div class="navMenu"></div>
    <div class="dashboardContent">
      <div class="dashboardColumnOne">
        <div class="welcomeCard">
          <h2>Welcome Back, Brian!</h2>
          <p>Here’s what’s happening with your support requests</p>
          <div class="ticket-info">
            <span>2 Open Tickets</span>
            <span>Last Activity: 2 hours ago</span>
          </div>
        </div>
        <div class="quickActions">
          <a href="#" class="quickActionItem"><div class="icon">
            <div class="quickActionSvg">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg></div>
          </div>New Ticket</a>
          <a href="#" class="quickActionItem"><div class="icon">
            <div class="quickActionSvg">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
            </div>
            </div>View FAQs</a>
          <a href="#" class="quickActionItem"><div class="icon">
            <div class="quickActionSvg">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M880-80 720-240H320q-33 0-56.5-23.5T240-320v-40h440q33 0 56.5-23.5T760-440v-280h40q33 0 56.5 23.5T880-640v560ZM160-473l47-47h393v-280H160v327ZM80-280v-520q0-33 23.5-56.5T160-880h440q33 0 56.5 23.5T680-800v280q0 33-23.5 56.5T600-440H240L80-280Zm80-240v-280 280Z"/></svg>
            </div>
            </div>Forums</a>
          <a href="#" class="quickActionItem"><div class="icon">
            <div class="quickActionSvg">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M580-240q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-880h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
            </div>
          </div>Appoinments</a>
        </div>
        <div class="knowledgeBase sectionCard">
          <h3>Knowledge Base</h3>
          <input type="text" placeholder="Search FAQs, forums, and help articles..." />
        </div>
          <div class="recentTickets sectionCard">
            <h3>Recent Tickets</h3>
            <div class="ticket inProgress">
              <div class="ticketDetails">
                <p><span class="ticketTitle">Wifi connection issues in library</span></p>
                <span class="ticketIcon">Technical Support<span>
                <span class="clockIcon">Updated 2 hours ago<span>
              </div>
              <span class="status inProgress">In Progress</span>
            </div>
            <div class="ticket ">
              <div class="ticketDetails">
                <p><span class="ticketTitle">Password reset request</span></p>
                <span class="ticketIcon">Account Access<span>
                <span class="clockIcon">Updated 1 day ago<span>
              </div>
              <span class="status open">Open</span>
            </div>
            <div class="ticket">
              <div class="ticketDetails">
                <p><span class="ticketTitle">Course registration problem</span></p>
                <span class="ticketIcon">Academic<span>
                <span class="clockIcon">Updated 3 days ago</span>
              </div>
              <span class="status resolved">Resolved</span>
            </div>
          </div>
      </div>
      <div class="dashboardColumnTwo">
        <div class="priority sectionCard">
          <h3>Priority</h3>
          <div class="priorityItem">
            <span class="priorityTitle">Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00</span>
          </div>
        </div>
        <div class="announcements sectionCard">
          <h3>Announcements</h3>
          <div class="announcement warning">
            <span class="announcementTitle">System Maintenance</span><br>
            <span class="announcementDescription">Scheduled maintenance on Dec 25, 2:00-4:00 AM</span>
          </div>
          <div class="announcement info">
            <span class="announcementTitle">New FAQ Section</span><br>
            <span class="announcementDescription">Check out our new updated WIFI troubleshooting guide</span>
          </div>
        </div>
        <div class="calendar sectionCard">
          <h3>Calendar</h3>
          <div class="event">
            <strong>June 28</strong> Meeting with Mr. Prasad at W003 — 5:00 PM
          </div>
        </div>
        <div class="account sectionCard">
          <h3>Account</h3>
            Profile Settings<br>
            Notifications<br>
            Ticket History<br>
        </div>
      </div>
    </div>
  </div>
</main>
