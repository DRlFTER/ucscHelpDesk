// Guest Dashboard: public-only UI with a full-width welcome card and login prompt on interactions
(function () {
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  function init() {
    skeleton();
    fetch("/guest/publicData", { credentials: "include" })
      .then((r) => (r.ok ? r.json() : Promise.reject(new Error("Load failed"))))
      .then((data) => hydrate(data))
      .catch(() =>
        showError(
          "We couldn't load the latest dashboard data. Please try again."
        )
      );
    bindLoginPrompt();
  }

  // Show a non-blocking error bar at the top of the dashboard
  function showError(msg) {
    const bar = document.getElementById("dashError");
    if (bar) {
      bar.textContent = msg || "An error occurred.";
      bar.style.display = "block";
    } else {
      // Fallback if the error bar isn't present
      console.error(msg);
    }
  }

  function skeleton() {
    const welcome = document.getElementById("guestWelcome");
    if (welcome) {
      welcome.innerHTML = `
        <h2 style="margin:0 0 8px 0;">Welcome, Guest</h2>
        <p class="mutedText">Loading personalized suggestions…</p>
      `;
    }

    const cards = document.getElementById("cardContainer");
    if (cards) {
      cards.innerHTML = Array.from({ length: 3 })
        .map(
          () =>
            '<div class="infoCard" aria-busy="true"><h3>&nbsp;</h3><p class="value">&nbsp;</p><p class="change">&nbsp;</p></div>'
        )
        .join("");
    }

    const ra = document.getElementById("recentAnnouncements");
    if (ra) ra.innerHTML = "<h3>Announcements</h3><ul><li>Loading…</li></ul>";

    const rt = document.getElementById("recentTickets");
    if (rt) rt.innerHTML = "<h3>Recent Tickets</h3><ul><li>Loading…</li></ul>";
  }

  function hydrate(data) {
    try {
      renderWelcomeCard();
      renderCards(data.cardsData || []);
      renderAnnouncements(data.recentAnnouncements || []);
      renderTickets(data.recentTickets || []);
    } catch (e) {
      console.error(e);
    }
  }

  function renderWelcomeCard() {
    const c = document.getElementById("guestWelcome");
    if (!c) return;
    c.innerHTML = `
      <h2 style="margin:0 0 8px 0;">Welcome, Guest</h2>
      <p class="mutedText">Browse public updates. To submit tickets, track progress, and access more features, please log in.</p>
      <div style="margin-top:12px;">
        <button class="btnWSvg btnPrimaryText" id="welcomeLoginBtn">Log in</button>
      </div>
    `;

    const btn = document.getElementById("welcomeLoginBtn");
    if (btn) btn.addEventListener("click", () => window.location.href = "/login");
  }

  function renderCards(cards) {
    const c = document.getElementById("cardContainer");
    if (!c) return;
    if (!cards.length) {
      c.innerHTML = "";
      return;
    }
    c.innerHTML = cards
      .map(
        (card) =>
          `<div class="infoCard"><h3>${escapeHtml(
            card.title || ""
          )}</h3><p class="value">${escapeHtml(
            String(card.value ?? "")
          )}</p><p class="change">${escapeHtml(card.change || "")}</p></div>`
      )
      .join("");
  }

  function renderAnnouncements(list) {
    const c = document.getElementById("recentAnnouncements");
    if (!c) return;
    if (!list.length) {
      c.innerHTML = `<h3>Announcements</h3><p class="mutedText">No announcements yet.</p>`;
      return;
    }
    c.innerHTML = `<h3>Announcements</h3><ul class="annList">${list
      .map(
        (a) =>
          `<li class="annItem" role="button" tabindex="0"><div><strong>${escapeHtml(
            a.topic || ""
          )}</strong><br><small>${escapeHtml(a.author || "")} • ${escapeHtml(
            a.date || ""
          )}${
            a.division ? " • " + escapeHtml(a.division) : ""
          }</small><p class="mutedText" style="margin-top:6px;">${escapeHtml(
            a.snippet || ""
          )}</p></div><span class="status info">View</span></li>`
      )
      .join("")}</ul>`;

    bindInteractive(c);
  }

  function renderTickets(list) {
    const c = document.getElementById("recentTickets");
    if (!c) return;
    if (!list.length) {
      c.innerHTML = `<h3>Recent Tickets</h3><p class="mutedText">No tickets yet.</p>`;
      return;
    }
    c.innerHTML = `<h3>Recent Tickets</h3><ul>${list
      .map(
        (t) =>
          `<li class="ticketItem" role="button" tabindex="0"><div><strong>${escapeHtml(
            t.title || ""
          )}</strong><br><small>${escapeHtml(t.category || "")} • ${escapeHtml(
            t.time || ""
          )}</small></div><span class="status ${String(
            t.priority || ""
          ).toLowerCase()}">${escapeHtml(t.priority || "")}</span></li>`
      )
      .join("")}</ul>`;

    bindInteractive(c);
  }

  function bindInteractive(scope) {
    const container = scope || document;
    const open = () => showLoginPrompt();
    container.addEventListener("click", (e) => {
      const item = e.target.closest(".ticketItem, .annItem");
      if (item && container.contains(item)) open();
    });
    container.addEventListener("keydown", (e) => {
      const item = e.target.closest(".ticketItem, .annItem");
      if (!item) return;
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        open();
      }
    });
  }

  function bindLoginPrompt() {
    const overlay = document.getElementById("loginPromptOverlay");
    const go = document.getElementById("loginGoBtn");
    const cancel = document.getElementById("loginCancelBtn");
    if (!overlay || !go || !cancel) return;
    go.addEventListener("click", () => {
      window.location.href = "/login";
    });
    cancel.addEventListener("click", () => {
      overlay.style.display = "none";
    });
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) overlay.style.display = "none";
    });
  }

  function showLoginPrompt() {
    const overlay = document.getElementById("loginPromptOverlay");
    if (overlay) overlay.style.display = "flex";
  }

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }
})();
