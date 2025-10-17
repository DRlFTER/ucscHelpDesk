// Admin Dashboard client-side rendering with caching and graceful errors
(function () {
  // Defer fetch until DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  const CACHE_KEY = "admin_dashboard_data";
  const CACHE_TTL_MS = 5 * 60 * 1000; // 5 minutes

  function getCache() {
    try {
      const s = localStorage.getItem(CACHE_KEY);
      if (!s) return null;
      const obj = JSON.parse(s);
      if (!obj || !obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
      return obj.data || null;
    } catch {
      return null;
    }
  }
  function setCache(data) {
    try {
      localStorage.setItem(
        CACHE_KEY,
        JSON.stringify({ _ts: Date.now(), data })
      );
    } catch {}
  }

  async function fetchData() {
    const res = await fetch("/admin/dashboardData", { credentials: "include" });
    if (!res.ok) throw new Error("Failed to load dashboard data");
    return res.json();
  }

  function showError(msg) {
    let bar = document.getElementById("dashError");
    if (!bar) return;
    bar.textContent = msg || "Failed to load dashboard data.";
    bar.style.display = "block";
  }

  function hideError() {
    const bar = document.getElementById("dashError");
    if (bar) bar.style.display = "none";
  }

  function skeleton() {
    // Optional: add simple placeholders
    const cardContainer = document.getElementById("cardContainer");
    if (cardContainer) {
      cardContainer.innerHTML = Array.from({ length: 4 })
        .map(
          () =>
            '<div class="infoCard" aria-busy="true"><h3>&nbsp;</h3><p class="value">&nbsp;</p><p class="change">&nbsp;</p></div>'
        )
        .join("");
    }
    const platformStatus = document.getElementById("platformStatus");
    if (platformStatus) {
      platformStatus.innerHTML =
        "<h3>Platform Status</h3><ul><li>Loading…</li><li>Loading…</li><li>Loading…</li></ul>";
    }
    const recentTickets = document.getElementById("recentTickets");
    if (recentTickets) {
      recentTickets.innerHTML =
        '<h3>Recent Tickets</h3><ul><li class="ticketItem">Loading…</li></ul>';
    }
  }

  function renderMenu() {
    const menuItems = [
      {
        name: "Analytics",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M324.62-480q-12.77 0-21.39 8.62-8.61 8.61-8.61 21.38v130q0 12.77 8.61 21.38 8.62 8.62 21.39 8.62 12.76 0 21.38-8.62 8.61-8.61 8.61-21.38v-130q0-12.77-8.61-21.38-8.62-8.62-21.38-8.62Zm310.76-200q-12.76 0-21.38 8.62-8.61 8.61-8.61 21.38v330q0 12.77 8.61 21.38 8.62 8.62 21.38 8.62 12.77 0 21.39-8.62 8.61-8.61 8.61-21.38v-330q0-12.77-8.61-21.38-8.62-8.62-21.39-8.62ZM480-400q-12.77 0-21.38 8.62Q450-382.77 450-370v50q0 12.77 8.62 21.38Q467.23-290 480-290t21.38-8.62Q510-307.23 510-320v-50q0-12.77-8.62-21.38Q492.77-400 480-400ZM212.31-140Q182-140 161-161q-21-21-21-51.31v-535.38Q140-778 161-799q21-21 51.31-21h535.38Q778-820 799-799q21 21 21 51.31v535.38Q820-182 799-161q-21 21-51.31 21H212.31Zm0-60h535.38q4.62 0 8.46-3.85 3.85-3.84 3.85-8.46v-535.38q0-4.62-3.85-8.46-3.84-3.85-8.46-3.85H212.31q-4.62 0-8.46 3.85-3.85 3.84-3.85 8.46v535.38q0 4.62 3.85 8.46 3.84 3.85 8.46 3.85ZM200-760v560-560Zm280 270q12.77 0 21.38-8.62Q510-507.23 510-520t-8.62-21.38Q492.77-550 480-550t-21.38 8.62Q450-532.77 450-520t8.62 21.38Q467.23-490 480-490Z"/></svg>',
      },
      {
        name: "Tickets",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M180.31-212q-26.53 0-45.42-18.85T116-276.17v-95.87q0-8.73 5.08-15.73 5.09-7 13.65-9.61 24.35-11.62 38.81-33.21Q188-452.19 188-480.02t-14.46-49.4q-14.46-21.58-38.81-33.16-8.56-2.61-13.65-9.58-5.08-6.96-5.08-15.92v-95.8q0-26.45 18.89-45.28Q153.78-748 180.31-748h599.38q26.53 0 45.42 18.85T844-683.83v95.87q0 8.73-5.08 15.73-5.09 7-13.65 9.61-24.35 11.62-38.81 33.21Q772-507.81 772-479.98t14.46 49.4q14.46 21.58 38.81 33.16 8.56 2.61 13.65 9.58 5.08 6.96 5.08 15.92v95.8q0 26.45-18.89 45.28Q806.22-212 779.69-212H180.31Zm0-52h599.38q5.39 0 8.85-3.46t3.46-8.85V-355q-32-19-52-52t-20-73q0-40 20-73t52-52v-78.69q0-5.39-3.46-8.85t-8.85-3.46H180.31q-5.39 0-8.85 3.46t-3.46 8.85V-605q32 19 52 52t20 73q0 40-20 73t-52 52v78.69q0 5.39 3.46 8.85t8.85 3.46Zm299.49-61.85q10.97 0 18.58-7.42 7.62-7.41 7.62-18.38 0-10.97-7.42-18.58-7.42-7.62-18.38-7.62-10.97 0-18.58 7.42-7.62 7.42-7.62 18.39 0 10.96 7.42 18.58 7.42 7.61 18.38 7.61Zm0-128.15q10.97 0 18.58-7.42 7.62-7.42 7.62-18.38 0-10.97-7.42-18.58-7.42-7.62-18.38-7.62-10.97 0-18.58 7.42-7.62 7.42-7.62 18.38 0 10.97 7.42 18.58 7.42 7.62 18.38 7.62Zm0-128.15q10.97 0 18.58-7.42 7.62-7.42 7.62-18.39 0-10.96-7.42-18.58-7.42-7.61-18.38-7.61-10.97 0-18.58 7.42-7.62 7.41-7.62 18.38 0 10.97 7.42 18.58 7.42 7.62 18.38 7.62ZM480-480Z"/></svg>',
      },
      {
        name: "Users",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M127.93-288.62q0-22.7 10.96-40.08t30.63-28.87q49.02-28.89 103.17-45.51 54.16-16.61 123.23-16.61 69.08 0 123.23 16.61 54.16 16.62 103.18 45.51 19.67 11.49 30.63 28.87 10.96 17.38 10.96 40.08v16.16q0 20.85-15.39 36.81t-37.42 15.96h-430.7q-22.02 0-37.25-15.38-15.23-15.39-15.23-37.39v-16.16Zm651.38 68.93h-55.85q5.54-12.77 9-26 3.46-13.22 3.46-26.77v-13.08q0-37.38-14.28-68.57-14.29-31.18-37.72-50.73 28.23 8 55.89 19.57 27.65 11.58 54.5 27.73 17 9.54 27.38 29.16 10.38 19.62 10.38 42.84v13.08q0 22-15.38 37.39-15.38 15.38-37.38 15.38ZM395.92-492.31q-51.75 0-87.87-36.12-36.12-36.13-36.12-87.88 0-51.75 36.12-87.87 36.12-36.13 87.87-36.13 51.75 0 87.88 36.13 36.12 36.12 36.12 87.87 0 51.75-36.12 87.88-36.13 36.12-87.88 36.12Zm281.38-124q0 51.75-36.12 87.88-36.12 36.12-87.87 36.12-3.77 0-4.23.46-.47.46-4.23-.38 21.66-25.45 34.37-56.62 12.7-31.17 12.7-67.5 0-36.34-12.96-67.23-12.96-30.88-34.11-56.8 2.61-.08 4.23 0 1.61.07 4.23.07 51.75 0 87.87 36.13 36.12 36.12 36.12 87.87ZM179.92-271.69h432v-16.93q0-8-3.79-14.07-3.79-6.06-13.36-11.31-42.38-25.46-91.69-39.58-49.31-14.11-107.16-14.11-57.84 0-107.15 13.61-49.31 13.62-91.69 40.08-9.57 5.13-13.36 10.99-3.8 5.86-3.8 14.3v17.02Zm216.22-272.62q29.78 0 50.78-21.21t21-51q0-29.79-21.21-50.79t-51-21q-29.79 0-50.79 21.22-21 21.21-21 51 0 29.78 21.22 50.78 21.21 21 51 21Zm-.22 272.62Zm0-344.62Z"/></svg>',
      },
      {
        name: "Settings",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M442.69-116q-10.46 0-17.84-5.58-7.39-5.57-9.62-15.42l-15.77-86.85q-26.07-9.38-52.96-24.07-26.88-14.7-48.19-33.77L216-252.23q-8.85 2.31-18.38-.89-9.54-3.19-14.08-11.8l-38.08-66.16q-4.54-8.61-3.07-17.77 1.46-9.15 8.46-15.92l65.92-57q-2.38-13.92-3.96-28.42-1.58-14.5-1.58-29.43 0-14.53 1.58-28.84t3.96-29.77l-65.92-57q-7-6.77-8.27-16.12-1.27-9.34 3.27-17.96l37.69-65q4.54-8.23 14.08-11.61 9.53-3.39 18.38-1.08l81.92 29.08q22.47-19.46 48.39-33.96t52.77-24.27L415.23-823q2.23-9.85 9.62-15.42 7.38-5.58 17.84-5.58h74.62q10.46 0 17.84 5.58 7.39 5.57 9.62 15.42l15.77 87.23q28 10.54 52.57 24.27 24.58 13.73 47.43 33.58L744.39-707q8.84-2.31 18.38 1.08 9.54 3.38 14.07 11.61l37.7 65.39q4.54 8.61 3.07 17.77-1.46 9.15-8.46 15.92l-67.46 58.15q3.15 14.69 4.35 28.62 1.19 13.92 1.19 28.46 0 14.15-1.39 28.08-1.38 13.92-3.76 29.77l66.3 57.38q7 6.77 8.66 15.92 1.65 9.16-2.89 17.77l-37.31 65.77q-4.53 8.62-14.26 11.81-9.73 3.19-18.58.88l-83.46-29.46q-22.85 19.85-47.81 33.96-24.96 14.12-52.19 23.89L544.77-137q-2.23 9.85-9.62 15.42-7.38 5.58-17.84 5.58h-74.62ZM462-168h35.62L517-268.15q37.62-7 69.46-25.23 31.85-18.24 57.39-48.39L740.23-309l18.39-30-76.77-67.38q6-18.54 9.3-36.47 3.31-17.92 3.31-37.15 0-19.62-3.31-37.15-3.3-17.54-9.3-35.7L759.38-621 741-651l-97.54 32.38q-22.08-27.46-56.61-47.42-34.54-19.96-70.23-25.81L498-792h-36.38l-18.24 99.77q-37.61 6.23-70.03 24.65-32.43 18.43-57.97 48.96L219-651l-18.38 30L277-553.62q-6 16.24-9.5 35.12t-3.5 38.88q0 19.62 3.5 38.12 3.5 18.5 9.12 35.12l-76 67.38L219-309l96-32q24.77 29.38 57.19 47.81 32.43 18.42 70.81 25.42L462-168Zm16.46-188q51.92 0 87.96-36.04 36.04-36.04 36.04-87.96 0-51.92-36.04-87.96Q530.38-604 478.46-604q-51.54 0-87.77 36.04T354.46-480q0 51.92 36.23 87.96Q426.92-356 478.46-356ZM480-480Z"/></svg>',
      },
    ];
    const list = document.getElementById("menuList");
    if (!list) return;
    list.innerHTML = menuItems
      .map(
        (item) =>
          `\n  <li class="menuItem"><div class="menuItemDiv">${item.icon} ${item.name}</div></li>\n`
      )
      .join("");
  }

  function renderCards(cardsData) {
    const c = document.getElementById("cardContainer");
    if (!c) return;
    c.innerHTML = (cardsData || [])
      .map(
        (card) =>
          `\n  <div class="infoCard">\n    <h3>${card.title}</h3>\n    <p class="value">${card.value}</p>\n    <p class="change">${card.change}</p>\n  </div>\n`
      )
      .join("");
  }

  function renderPlatformStatus(list) {
    const c = document.getElementById("platformStatus");
    if (!c) return;
    c.innerHTML = `\n  <h3>Platform Status</h3>\n  <ul>\n    ${(list || [])
      .map(
        (p) =>
          `\n      <li>\n        ${
            p.name
          } \n        <span class="status ${String(
            p.status || ""
          ).toLowerCase()}">${p.status}</span>\n      </li>\n    `
      )
      .join("")}\n  </ul>\n`;
  }

  function renderRecentTickets(list) {
    const c = document.getElementById("recentTickets");
    if (!c) return;
    c.innerHTML = `\n  <h3>Recent Tickets</h3>\n  <ul>\n    ${(list || [])
      .map(
        (t) =>
          `\n      <li class="ticketItem" tabindex="0" role="link" aria-label="Open ticket ${
            t.title || ""
          }" data-id="${t.id ?? ""}" data-code="${
            t.code ?? ""
          }">\n        <div>\n          <strong>${
            t.title
          }</strong><br>\n          <small>${t.agent} • ${
            t.time
          }</small>\n        </div>\n        <span class="status ${String(
            t.priority || ""
          ).toLowerCase()}">${t.priority}</span>\n      </li>\n    `
      )
      .join("")}\n  </ul>\n`;

    if (!c._listenersBound) {
      const open = (el) => {
        const id = el.getAttribute("data-id");
        const code = el.getAttribute("data-code");
        if (id) {
          window.location.assign(`/admin/ticket?id=${encodeURIComponent(id)}`);
        } else if (code) {
          window.location.assign(
            `/admin/ticket?code=${encodeURIComponent(code)}`
          );
        }
      };

      c.addEventListener("click", (e) => {
        const item = e.target.closest(".ticketItem");
        if (item && c.contains(item)) open(item);
      });
      c.addEventListener("keydown", (e) => {
        const item = e.target.closest(".ticketItem");
        if (!item) return;
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          open(item);
        }
      });
      c._listenersBound = true;
    }
  }

  function renderTopAgents(topAgents) {
    const c = document.getElementById("topAgents");
    if (!c) return;
    c.innerHTML = `\n    <h3>Top Performing Agents</h3>\n    <table>\n      <tr><th>Agent</th><th>Tickets Resolved</th><th>Avg Response Time</th></tr>\n      ${(
      topAgents || []
    )
      .map(
        (a) =>
          `\n        <tr>\n          <td>${a.name}</td>\n          <td>${a.resolved}</td>\n          <td>${a.responseTime}</td>\n        </tr>\n      `
      )
      .join("")}\n    </table>\n  `;
  }

  // Keep chart instances to update without recreating
  let charts = { line: null, pie: null };

  function renderCharts(trends, categories) {
    const lineEl = document.getElementById("ticketTrendsChart");
    const pieEl = document.getElementById("ticketsByCategoryChart");
    if (!lineEl || !pieEl || typeof Chart === "undefined") return;

    const run = () => {
      const lineData = {
        labels: (trends && trends.labels) || [],
        datasets: [
          {
            label: "New Tickets",
            data: (trends && trends.new) || [],
            borderColor: "#3b82f6",
            backgroundColor: "#3b82f6",
          },
          {
            label: "Resolved tickets",
            data: (trends && trends.resolved) || [],
            borderColor: "#10b981",
            backgroundColor: "#10b981",
            fill: false,
          },
        ],
      };
      const lineOpts = {
        responsive: true,
        plugins: { legend: { position: "bottom" } },
      };

      const pieData = {
        labels: (categories && categories.labels) || [],
        datasets: [
          {
            data: (categories && categories.data) || [],
            backgroundColor: ["#3b82f6", "#f59e0b", "#10b981", "#6b7280"],
          },
        ],
      };
      const pieOpts = {
        responsive: true,
        plugins: { legend: { position: "right" } },
      };

      if (!charts.line) {
        charts.line = new Chart(lineEl.getContext("2d"), {
          type: "line",
          data: lineData,
          options: lineOpts,
        });
      } else {
        charts.line.data = lineData;
        charts.line.options = lineOpts;
        charts.line.update();
      }

      if (!charts.pie) {
        charts.pie = new Chart(pieEl.getContext("2d"), {
          type: "pie",
          data: pieData,
          options: pieOpts,
        });
      } else {
        charts.pie.data = pieData;
        charts.pie.options = pieOpts;
        charts.pie.update();
      }
    };

    // Defer heavy chart work until the browser is idle to improve TTI
    if (typeof window.requestIdleCallback === "function") {
      requestIdleCallback(run, { timeout: 300 });
    } else {
      setTimeout(run, 0);
    }
  }

  async function init() {
    renderMenu();

    // Use cache immediately if available
    const cached = getCache();
    if (cached) {
      hideError();
      // Hydrate quickly without skeletons if we already have cache
      hydrate(cached);
      // SWR: refresh in background
      fetchData()
        .then((fresh) => {
          setCache(fresh);
          hydrate(fresh, true);
        })
        .catch((e) => {
          console.warn("Background refresh failed", e);
        });
      return;
    }

    // No cache, fetch now
    try {
      // Only show skeletons when we have no cached content
      skeleton();
      const data = await fetchData();
      setCache(data);
      hideError();
      hydrate(data);
    } catch (e) {
      console.error(e);
      showError(
        "We couldn't load the latest dashboard data. Please try again."
      );
    }
  }

  function hydrate(data, isRefresh = false) {
    if (!data || typeof data !== "object") return;
    renderCards(data.cardsData);
    renderPlatformStatus(data.platformStatus);
    renderRecentTickets(data.recentTickets);
    renderTopAgents(data.topAgents);
    renderCharts(data.trends, data.categories);
  }
})();
