<?php 
// coun_calender.php - Updated with event modal
require_once('../../core/config.php');
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Counselor Calendar</title>
  <link rel="stylesheet" href="../common/css/components.css">
  <link rel="stylesheet" href="coun.css">
    <link rel="stylesheet" href="counselor_dashboard.css">
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      margin: 20px;
      background: #f5f7fa;
    }

    .main-container {
      display: flex;
      gap: 1rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    .sidebar {
      flex: 0 0 280px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      height: fit-content;
    }

    .calendar-section {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .filters h3 {
      margin-bottom: 15px;
      font-size: 18px;
      font-weight: 600;
      color: #333;
    }

    .filters button {
      display: block;
      width: 100%;
      text-align: left;
      padding: 10px 15px;
      margin-bottom: 8px;
      border: none;
      background: #f3f4f6;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 14px;
    }

    .filters button:hover {
      background: #e5e7eb;
    }

    .filters button.active {
      background: #4285f4;
      color: white;
    }

    .upcoming {
      margin-top: 25px;
    }

    .upcoming h3 {
      margin-bottom: 15px;
      font-size: 18px;
      font-weight: 600;
      color: #333;
    }

    #upcomingEventsList {
      list-style: none;
    }

    #upcomingEventsList li {
      padding: 12px;
      background: #f8f9fa;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 13px;
      border-left: 4px solid #4285f4;
    }

    #upcomingEventsList li strong {
      display: block;
      color: #333;
      margin-bottom: 4px;
    }

    #upcomingEventsList li small {
      color: #666;
    }

    #eventFormBox {
      display: none;
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    #eventFormBox.show {
      display: block;
    }

    #eventFormBox h4 {
      margin-bottom: 15px;
      color: #333;
      font-size: 16px;
    }

    #eventFormBox .form-group {
      margin-bottom: 12px;
    }

    #eventFormBox label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #555;
      font-size: 13px;
    }

    #eventFormBox input {
      width: 100%;
      padding: 10px;
      border: 2px solid #e1e5e9;
      border-radius: 6px;
      font-size: 13px;
    }

    #eventFormBox input:focus {
      outline: none;
      border-color: #4285f4;
    }

    #eventFormBox button {
      padding: 10px 15px;
      margin-right: 8px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 13px;
      transition: all 0.2s;
    }

    #eventFormBox .btn-save {
      background: #4285f4;
      color: white;
    }

    #eventFormBox .btn-save:hover {
      background: #3367d6;
    }

    #eventFormBox .btn-cancel {
      background: #e5e7eb;
      color: #333;
    }

    #eventFormBox .btn-cancel:hover {
      background: #d1d5db;
    }

    #calendar {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      flex: 1;
    }

    .fc-button-primary {
      background: #4285f4 !important;
      border-color: #4285f4 !important;
    }

    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
      background: #3367d6 !important;
      border-color: #3367d6 !important;
    }

    .fc-button-primary:not(:disabled):hover {
      background: #3367d6 !important;
      border-color: #3367d6 !important;
    }

    /* Event Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      animation: fadeIn 0.3s;
    }

    .modal.show {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .modal-content {
      background-color: white;
      margin: 50px auto;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      animation: slideDown 0.3s;
    }

    @keyframes slideDown {
      from {
        transform: translateY(-30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 15px;
    }

    .modal-header h2 {
      font-size: 22px;
      color: #333;
      margin: 0;
    }

    .close-btn {
      background: none;
      border: none;
      font-size: 28px;
      cursor: pointer;
      color: #666;
      transition: color 0.2s;
    }

    .close-btn:hover {
      color: #333;
    }

    .modal-body {
      margin-bottom: 20px;
    }

    .event-detail {
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f0f0f0;
    }

    .event-detail:last-child {
      border-bottom: none;
    }

    .event-label {
      font-weight: 600;
      color: #555;
      font-size: 12px;
      text-transform: uppercase;
      margin-bottom: 5px;
    }

    .event-value {
      color: #333;
      font-size: 14px;
    }

    .modal-actions {
      display: flex;
      gap: 10px;
    }

    .modal-actions button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-edit {
      background: #4285f4;
      color: white;
    }

    .btn-edit:hover {
      background: #3367d6;
    }

    .btn-delete {
      background: #dc3545;
      color: white;
    }

    .btn-delete:hover {
      background: #c82333;
    }

    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
      }

      .sidebar {
        flex: 1;
      }

      .modal-content {
        width: 95%;
        margin: 100px auto;
      }
    }
  </style>
</head>
<body>
    <?php include 'coun_navbar.html'; ?>

    <header>
      <h2>🗓️ Calendar - My Counseling Events</h2>
      <p>View, create and manage your events</p>
    </header>

    <div class="main-container">
      <!-- Sidebar -->
      <aside class="sidebar">
        <nav class="filters">
          <h3>Calendar Filters</h3>
          <button onclick="goToToday()">Today</button>
          <button onclick="goToPrevMonth()">Previous Month</button>
          <button onclick="goToNextMonth()">Next Month</button>
        </nav>

        <div class="upcoming">
          <h3>Upcoming Events</h3>
          <ul id="upcomingEventsList">
            <?php
            $result = $conn->query("SELECT * FROM events WHERE start >= CURDATE() ORDER BY start ASC LIMIT 5");
            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $start_time = date('M j, g:i A', strtotime($row['start']));
                echo "<li><strong>" . htmlspecialchars($row['title']) . "</strong><small>" . $start_time . "</small></li>";
              }
            } else {
              echo "<li style='color: #999;'>No upcoming events</li>";
            }
            ?>
          </ul>
        </div>
      </aside>

      <!-- Calendar Section -->
      <div class="calendar-section">
        <!-- Event Form -->
        <div id="eventFormBox">
          <h4>Create New Event</h4>
          <form id="eventForm">
            <div class="form-group">
              <label>Event Title:</label>
              <input type="text" id="eventTitle" required placeholder="Enter event title">
            </div>
            <div class="form-group">
              <label>Date:</label>
              <input type="date" id="eventDate" required>
            </div>
            <button type="submit" class="btn-save">Save Event</button>
            <button type="button" class="btn-cancel" onclick="hideForm()">Cancel</button>
          </form>
        </div>

        <!-- Calendar -->
        <div id="calendar"></div>
      </div>
    </div>

    <!-- Event Details Modal -->
    <div id="eventModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 id="modalTitle">Event Details</h2>
          <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
          <div class="event-detail">
            <div class="event-label">Date & Time</div>
            <div class="event-value" id="modalDateTime"></div>
          </div>
          <div class="event-detail">
            <div class="event-label">Duration</div>
            <div class="event-value" id="modalDuration"></div>
          </div>
          <div class="event-detail">
            <div class="event-label">Description</div>
            <div class="event-value" id="modalDescription"></div>
          </div>
        </div>
        <div class="modal-actions">
          <button class="btn-delete" onclick="deleteCurrentEvent()">Delete Event</button>
          <button class="btn-edit" onclick="closeModal()">Close</button>
        </div>
      </div>
    </div>

    <script>
      let calendar;
      let currentEventId = null;

      document.addEventListener("DOMContentLoaded", function () {
        var calendarEl = document.getElementById("calendar");
        calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: "dayGridMonth",
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: "load_events.php",

          dateClick: function(info) {
            // Show form when date clicked
            document.getElementById("eventFormBox").classList.add("show");
            document.getElementById("eventDate").value = info.dateStr;
            document.getElementById("eventTitle").focus();
          },

          eventClick: function(info) {
            // Show modal when event clicked
            showEventModal(info.event);
          }
        });

        calendar.render();

        // Handle form submit
        document.getElementById("eventForm").onsubmit = function(e) {
          e.preventDefault();
          var title = document.getElementById("eventTitle").value;
          var date = document.getElementById("eventDate").value;

          if (title && date) {
            fetch("add_event.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: "title=" + encodeURIComponent(title) + "&start=" + encodeURIComponent(date)
            }).then(response => response.json())
              .then(data => {
                if (data.success) {
                  calendar.refetchEvents();
                  document.getElementById("eventForm").reset();
                  document.getElementById("eventFormBox").classList.remove("show");
                  alert("Event created successfully!");
                }
              });
          }
        };
      });

      function hideForm() {
        document.getElementById("eventFormBox").classList.remove("show");
      }

      function showEventModal(event) {
        currentEventId = event.id;
        document.getElementById("modalTitle").textContent = event.title;
        
        const startTime = event.start ? new Date(event.start).toLocaleString() : "Not set";
        const endTime = event.end ? new Date(event.end).toLocaleString() : "No end time";
        
        document.getElementById("modalDateTime").textContent = startTime;
        
        if (event.end) {
          const duration = (new Date(event.end) - new Date(event.start)) / 1000 / 60;
          document.getElementById("modalDuration").textContent = duration + " minutes";
        } else {
          document.getElementById("modalDuration").textContent = "No duration set";
        }
        
        document.getElementById("modalDescription").textContent = event.extendedProps?.description || "No description";
        
        document.getElementById("eventModal").classList.add("show");
      }

      function closeModal() {
        document.getElementById("eventModal").classList.remove("show");
        currentEventId = null;
      }

      function deleteCurrentEvent() {
        if (!currentEventId) return;
        
        if (confirm("Are you sure you want to delete this event?")) {
          fetch("delete_event.php?id=" + currentEventId)
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                calendar.refetchEvents();
                closeModal();
                alert("Event deleted successfully!");
              }
            });
        }
      }

      function goToToday() {
        calendar.today();
      }

      function goToPrevMonth() {
        calendar.prev();
      }

      function goToNextMonth() {
        calendar.next();
      }

      // Close modal when clicking outside
      window.onclick = function(event) {
        var modal = document.getElementById("eventModal");
        if (event.target === modal) {
          closeModal();
        }
      }
    </script>
</body>
</html>