<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Counselor Reports & Analytics</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #f7f9fc;
    }
    .charts {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .card {
      flex: 1;
      min-width: 350px;
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    canvas {
      width: 100% !important;
      height: 300px !important;
    }
  </style>
</head>
<body>
    <?php include 'coun_navbar.html'; ?>
    <?php include 'db_connect.php'; ?>
<header>
  <h2>📊 Counselor Reports & Analytics</h2>
</header>

  <div class="charts">
    <!-- Line Chart -->
    <div class="card">
      <h3>Session Trends</h3>
      <canvas id="sessionTrend"></canvas>
    </div>

    <!-- Pie Chart -->
    <div class="card">
      <h3>Sessions by Category</h3>
      <canvas id="categoryChart"></canvas>
    </div>
  </div>

  <script>
    // Line chart (Trends)
    const ctx1 = document.getElementById('sessionTrend').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
          {
            label: 'Scheduled Sessions',
            data: [30, 45, 40, 60],
            borderColor: 'blue',
            fill: false,
            tension: 0.3
          },
          {
            label: 'Completed Sessions',
            data: [25, 40, 38, 55],
            borderColor: 'green',
            fill: false,
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Sessions' }
          }
        }
      }
    });

    // Pie chart (Categories)
    const ctx2 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx2, {
      type: 'pie',
      data: {
        labels: ['Stress Management', 'Academic Issues', 'Career Guidance', 'Personal Issues'],
        datasets: [{
          data: [40, 25, 20, 15],
          backgroundColor: ['#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'right' }
        }
      }
    });
  </script>
</body>
</html>
