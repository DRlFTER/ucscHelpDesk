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

const cardsData = [
  { title: "Total Tickets", value: 194, change: "+12% from last month" },
  { title: "Open Tickets", value: 38, change: "-5% from yesterday" },
  { title: "Average Response Time", value: "2.4h", change: "+15% improvement" },
  { title: "Satisfaction Rate", value: "94%", change: "+2% from last week" },
];

const platformStatus = [
  { name: "Student Portal", status: "Operational" },
  { name: "Lecturer Portal", status: "Operational" },
  { name: "Email Notifications", status: "Degraded" },
  { name: "Ticketing System", status: "Operational" },
];

const recentTickets = [
  {
    title: "Unable to reset password",
    agent: "Kaweesha",
    time: "2h ago",
    priority: "HIGH",
  },
  {
    title: "Library book renewal",
    agent: "Kavindu",
    time: "5h ago",
    priority: "LOW",
  },
  {
    title: "Power sockets isn’t working in S104",
    agent: "Tharushi",
    time: "19h ago",
    priority: "MEDIUM",
  },
  {
    title: "Library book renewal",
    agent: "Kavindu",
    time: "5h ago",
    priority: "LOW",
  },
  {
    title: "Library book renewal",
    agent: "Kavindu",
    time: "5h ago",
    priority: "LOW",
  },
  {
    title: "Power sockets isn’t working in S104",
    agent: "Tharushi",
    time: "19h ago",
    priority: "MEDIUM",
  },
];

const topAgents = [
  { name: "Kaweesha Pathirana", resolved: 127, responseTime: "1.8h" },
  { name: "Kavindu Attanayake", resolved: 96, responseTime: "2.4h" },
];

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
new Chart(ctxLine, {
  type: "line",
  data: {
    labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
    datasets: [
      {
        label: "New Tickets",
        data: [45, 55, 38, 67],
        borderColor: "#3b82f6",
        backgroundColor: "#3b82f6",
      },
      {
        label: "Resolved tickets",
        data: [37, 50, 41, 60],
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

new Chart(ctxPie, {
  type: "pie",
  data: {
    labels: ["Technical", "Academic", "Course", "Other"],
    datasets: [
      {
        data: [40, 25, 20, 15],
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