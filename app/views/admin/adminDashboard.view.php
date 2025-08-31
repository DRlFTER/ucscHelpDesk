<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1" crossorigin="anonymous"></script>

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
          <div class="chartHolder">
          <canvas id="ticketsByCategoryChart"></canvas></div>
        </div>
      </div>
      <div class="contentRow">
        <div class="platformStatus cardBox" id="platformStatus"></div>
        <div class="recentTickets cardBox" id="recentTickets"></div>
      </div>
    </div>
  </div>
</main>

<script>const menuItems = [
  { name: "Analytics", icon: "" },
  { name: "Tickets", icon: "" },
  { name: "Users", icon: "" },
  { name: "Settings", icon: "" },
];

// Server-provided datasets
const cardsData = <?= json_encode($cardsData ?? [], JSON_UNESCAPED_UNICODE) ?>;

const platformStatus = <?= json_encode($platformStatus ?? [], JSON_UNESCAPED_UNICODE) ?>;

const recentTickets = <?= json_encode($recentTickets ?? [], JSON_UNESCAPED_UNICODE) ?>;

const topAgents = <?= json_encode($topAgents ?? [], JSON_UNESCAPED_UNICODE) ?>;

document.getElementById("menuList").innerHTML = menuItems
  .map(
    (item) => `
  <li class="menuItem">${item.icon} ${item.name}</li>
`
  )
  .join("");

document.getElementById("cardContainer").innerHTML = cardsData
  .map(
    (card) => `
  <div class="infoCard">
    <h3>${card.title}</h3>
    <p class="value">${card.value}</p>
    <p class="change">${card.change}</p>
  </div>
`
  )
  .join("");

document.getElementById("platformStatus").innerHTML = `
  <h3>Platform Status</h3>
  <ul>
    ${platformStatus
      .map(
        (p) => `
      <li>
        ${p.name} 
        <span class="status ${p.status.toLowerCase()}">${p.status}</span>
      </li>
    `
      )
      .join("")}
  </ul>
`;

document.getElementById("recentTickets").innerHTML = `
  <h3>Recent Tickets</h3>
  <ul>
    ${recentTickets
      .map(
        (t) => `
      <li class="ticketItem">
        <div>
          <strong>${t.title}</strong><br>
          <small>${t.agent} • ${t.time}</small>
        </div>
        <span class="status ${t.priority.toLowerCase()}">${t.priority}</span>
      </li>
    `
      )
      .join("")}
  </ul>
`;

const topAgentsContainer = document.getElementById("topAgents");
if (topAgentsContainer) {
  topAgentsContainer.innerHTML = `
    <h3>Top Performing Agents</h3>
    <table>
      <tr><th>Agent</th><th>Tickets Resolved</th><th>Avg Response Time</th></tr>
      ${topAgents
        .map(
          (a) => `
        <tr>
          <td>${a.name}</td>
          <td>${a.resolved}</td>
          <td>${a.responseTime}</td>
        </tr>
      `
        )
        .join("")}
    </table>
  `;
}


const ctxLine = document.getElementById("ticketTrendsChart").getContext("2d");
const trends = <?= json_encode($trends ?? ['labels'=>[], 'new'=>[], 'resolved'=>[]], JSON_UNESCAPED_UNICODE) ?>;
new Chart(ctxLine, {
  type: "line",
  data: {
  labels: trends.labels,
    datasets: [
      {
        label: "New Tickets",
    data: trends.new,
        borderColor: "#3b82f6",
        backgroundColor: "#3b82f6",
      },
      {
        label: "Resolved tickets",
    data: trends.resolved,
        borderColor: "#10b981",
        backgroundColor: "#10b981",
        fill: false,
      },
    ],
  },
  options: { responsive: true, plugins: { legend: { position: "bottom" } } },
});

const ctxPie = document
  .getElementById("ticketsByCategoryChart")
  .getContext("2d");

const categories = <?= json_encode($categories ?? ['labels'=>[], 'data'=>[]], JSON_UNESCAPED_UNICODE) ?>;
new Chart(ctxPie, {
  type: "pie",
  data: {
  labels: categories.labels,
    datasets: [
      {
    data: categories.data,
        backgroundColor: ["#3b82f6", "#f59e0b", "#10b981", "#6b7280"],
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "right", // 👈 move legend to the left
      },
    },
  },
});

</script>