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
          <a href="#" class="quickActionItem">New Ticket</a>
          <a href="#" class="quickActionItem">View FAQs</a>
          <a href="#" class="quickActionItem">Forums</a>
          <a href="#" class="quickActionItem">Appointments</a>
        </div>
        <div class="knowledgeBase sectionCard">
          <h3>Knowledge Base</h3>
          <input type="text" placeholder="Search FAQs, forums, and help articles..." />
        </div>
          <div class="recentTickets sectionCard">
            <h3>Recent Tickets</h3>
            <div class="ticket inProgress">
              <div class="ticketDetails">
                <p>Wifi connection issues in library</p>
                <span class="ticketIcon">Technical Support<span>
                <span class="clockIcon">Updated 2 hours ago<span>
              </div>
              <span class="status inProgress">In Progress</span>
            </div>
            <div class="ticket ">
              <div class="ticketDetails">
                <p>Password reset request</p>
                <span class="ticketIcon">Account Access<span>
                <span class="clockIcon">Updated 1 day ago<span>
              </div>
              <span class="status open">Open</span>
            </div>
            <div class="ticket">
              <div class="ticketDetails">
                <p>Course registration problem</p>
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
            Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00
          </div>
        </div>
        <div class="announcements sectionCard">
          <h3>Announcements</h3>
          <div class="announcement warning">
            <strong>System Maintenance</strong><br>
            Scheduled maintenance on Dec 25, 2:00-4:00 AM
          </div>
          <div class="announcement info">
            <strong>New FAQ Section</strong><br>
            Check out our new updated WIFI troubleshooting guide
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
