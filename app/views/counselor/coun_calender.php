<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Counselor Calendar</title>
  <link rel="stylesheet" href="../common/css/components.css">
  <link rel="stylesheet" href="coun.css">

  <!-- ✅ FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <style>
    #calendar {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  
  <!-- ✅ Navbar -->
  <?php include 'coun_navbar.html'; ?>
  <?php include 'db_connect.php'; ?>

  <header>
    <h2>My Counseling Calendar</h2>
    <p>View, create and manage your events</p>
  </header>

  <!-- ✅ Calendar Container -->
  <div id="calendar"></div>

  <!-- ✅ FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        editable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // ✅ Example events
        events: [
          { title: 'Session with Alice', start: '2025-08-24T10:00:00' },
          { title: 'Team Meeting', start: '2025-08-25', end: '2025-08-26' }
        ],

        // ✅ Click to create new event
        dateClick: function(info) {
          var title = prompt('Enter Event Title:');
          if (title) {
            calendar.addEvent({
              title: title,
              start: info.date,
              allDay: info.allDay
            });
          }
        }
      });
      calendar.render();
    });
  </script>

</body>
</html>
