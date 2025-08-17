<?php
$pageTitle = "Admin Dashboard";
$pageCSS = "./dashboard.css";
include_once(__DIR__ . "/../../common/header.php");
include_once(__DIR__ . "/../../common/navbar.php");
?>

<main>
  <div class="dashboardContainer">
    <div class="navMenu">
      <ul id="menuList"></ul>
    </div>
    <div class="dashboardContent">
      <div class="cardContainer" id="cardContainer"></div>
      <div class="contentRow">
        <div class="ticketTrends chartBox">
          <h3>Ticket Trends</h3>
          <canvas id="ticketTrendsChart"></canvas>
        </div>
        <div class="ticketsByCategory chartBox">
          <h3>Tickets by Category</h3>
          <canvas id="ticketsByCategoryChart"></canvas>
        </div>
      </div>
      <div class="contentRow">
        <div class="platformStatus cardBox" id="platformStatus"></div>
        <div class="recentTickets cardBox" id="recentTickets"></div>
      </div>
      <div class="topAgents cardBox" id="topAgents"></div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" integrity="sha384-+QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1QwQJQw1Qw==" crossorigin="anonymous"></script>
<script src="dashboard.js"></script>

<?php include_once(__DIR__ . "/../../common/footer.php"); ?>