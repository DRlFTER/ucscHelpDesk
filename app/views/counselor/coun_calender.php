<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Counselor Calendar</title>
  <link rel="stylesheet" href="../common/css/components.css">
  <link rel="stylesheet" href="coun.css">

  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    .main-container {
      display: flex;           /* side-by-side layout */
      gap: 1rem;               /* space between sidebar & calendar */
    }

    .sidebar {
      flex: 0 0 260px;          /* fixed width for sidebar */
    }

    .calendar-section {
      flex: 1;                  /* take remaining space */
      display: flex;
      flex-direction: column;
    }
    #eventFormBox {
      display: none; /* Hidden until date clicked */
      background: #f9f9f9;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    #eventFormBox input {
      margin: 5px;
      padding: 8px;
    }
    #eventFormBox button {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      background: #007bff;
      color: white;
      cursor: pointer;
    }
    #eventFormBox button:hover {
      background: #0056b3;
    }
    
    .filters h3 {
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }

    .filters button {
      display: block;
      width: 100%;
      text-align: left;
      padding: 0.5rem 0.75rem;
      margin-bottom: 0.4rem;
      border: none;
      background: #f3f4f6;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.2s;
    }
    .filters button:hover {
      background: #e5e7eb;
    }

    .filters button.active {
      background: red;
      color: #fff;
    }
    .upcoming {
      margin-top: 2rem;
      font-size: 1rem;
    }
    #calendar {
      flex: 1;
      background: #fff;
      border-radius: 8px;
      padding: 1rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

  </style>
</head>
<body>
    <?php include 'coun_navbar.html'; ?>
    <?php include 'db_connect.php'; ?>  
  <header>
    <h2>My Counseling Calendar</h2>
    <p>View, create and manage your events</p>
  </header>
  <div class="main-container">
  <aside class="sidebar">
      <nav class="filters">
        <h2>Calendar</h2>
        <button>Today</button>
        <button>Yesterday</button>
        <button>Last week</button>
        <button>Last 7 days</button>
        <button>This month</button>
        <button>Last 30 days</button>
        <button>Custom range</button>
      </nav>
      <div class="upcoming">
        <h2>Upcoming Events</h2>
        <ul id="upcomingEventsList">
          <?php
          $result = $conn->query("SELECT * FROM events WHERE start >= CURDATE() ORDER BY start ASC LIMIT 5");
          while ($row = $result->fetch_assoc()) {
              echo "<li>" . htmlspecialchars($row['title']) . " - " . htmlspecialchars($row['start']) . "</li>";
          }
          ?>
        </ul>
      </div>
    </aside>
    <div class="calendar-section">
  <!-- 📌 Inline Form -->
  <div id="eventFormBox">
    <form id="eventForm">
      <label>Event Title:</label>
      <input type="text" id="eventTitle" required>
      <label>Date:</label>
      <input type="date" id="eventDate" required>
      <button type="submit">Save Event</button>
      <button type="button" id="cancelForm">Cancel</button>
    </form>
  </div>

  <!-- 📌 Calendar -->
  <div id="calendar"></div>
    </div>
  </div>  
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var calendarEl = document.getElementById("calendar");
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        events: "load_events.php",

        dateClick: function(info) {
          // Show the inline form
          document.getElementById("eventFormBox").style.display = "block";
          document.getElementById("eventDate").value = info.dateStr;
          document.getElementById("eventTitle").focus();
        },

        eventClick: function(info) {
          if (confirm("Delete this event?")) {
            fetch("delete_event.php?id=" + info.event.id)
              .then(() => calendar.refetchEvents());
          }
        }
      });

      calendar.render();
      // Handle Form Submit
      const form = document.getElementById("eventForm");
      form.onsubmit = function(e) {
        e.preventDefault();
        var title = document.getElementById("eventTitle").value;
        var date  = document.getElementById("eventDate").value;

        if (title && date) {
          fetch("add_event.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "title=" + encodeURIComponent(title) + "&start=" + encodeURIComponent(date)
          }).then(() => {
            calendar.refetchEvents();
            form.reset();
            document.getElementById("eventFormBox").style.display = "none";
          });
        }
      };

      // Cancel Button
      document.getElementById("cancelForm").onclick = function() {
        form.reset();
        document.getElementById("eventFormBox").style.display = "none";
      };
    });
  </script>
</body>
</html>
