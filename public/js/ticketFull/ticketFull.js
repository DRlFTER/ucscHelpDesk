// Role-aware unified Ticket Full page logic
(function () {
  // Navbar height variable helper
  function setNavbarVar() {
    const nav = document.querySelector(".navbar");
    const h = nav ? nav.offsetHeight : 64;
    document.documentElement.style.setProperty("--navbar-height", h + "px");
  }
  let rafId;
  function onResize() {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = requestAnimationFrame(setNavbarVar);
  }
  window.addEventListener("resize", onResize);
  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", setNavbarVar);
  else setNavbarVar();
})();

let ticketData = null;

// Demo placeholders; these can be swapped for real conversation/timeline later
const conversation = [
  {
    name: "Support Agent",
    role: "Staff",
    time: "4 hours ago",
    text: "Thanks, we are looking into this now.",
    authorType: "staff",
  },
  {
    name: "You",
    role: "",
    time: "3 hours ago",
    text: "Great, thanks!",
    authorType: "student",
  },
];
const timeline = [
  { label: "Ticket created", time: "—", color: "green", pending: false },
  { label: "Assigned", time: "—", color: "blue", pending: false },
  { label: "Under review", time: "—", color: "yellow", pending: false },
  { label: "Resolved", time: "Pending", color: "gray", pending: true },
];

const CFG = window.TICKET_FULL_CONFIG || {
  role: "guest",
  apiBase: "/admin/ticketData",
  deleteEndpoint: "/admin/ticketDelete",
};
const ROLE = (CFG.role || "guest").toLowerCase();

function statusClass(status) {
  const normalized = (status || "").toLowerCase().replace(/\s+/g, "");
  switch (normalized) {
    case "underreview":
    case "pending":
      return "status underReview";
    case "resolved":
    case "completed":
    case "closed":
      return "status resolved";
    case "open":
    case "cancelled":
    case "rejected":
    case "high":
      return "status open";
    case "low":
    case "requested":
      return "status requested";
    default:
      return "status";
  }
}

function renderHeader() {
  document.getElementById("ticketTitle").textContent = ticketData.title;
  const statusEl = document.getElementById("ticketStatus");
  statusEl.className = statusClass(ticketData.status);
  statusEl.textContent = ticketData.status;

  const meta = [
    { text: ticketData.code || `TKT-${ticketData.id}` },
    { text: ticketData.createdOn || "" },
  ];
  document.getElementById("ticketMeta").innerHTML = meta
    .map((m) => `<span>${m.text}</span>`)
    .join("");
}

function renderDescription() {
  document.getElementById("ticketDescriptionText").textContent =
    ticketData.description || "";
}

function renderAttachments() {
  const c = document.getElementById("attachmentsList");
  const list = Array.isArray(ticketData.attachments)
    ? ticketData.attachments
    : [];
  c.innerHTML = list
    .map(
      (a) => `
    <div class="attachmentItem">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12 20.5a6 6 0 0 1-8.49-8.49l10-10a4 4 0 0 1 5.66 5.66l-10 10a2 2 0 1 1-2.83-2.83l9-9"/></svg>
      <a href="${a.url}" download>${a.name}</a>
      <button title="Download">⤓</button>
    </div>`
    )
    .join("");
}

function renderMessages() {
  const m = document.getElementById("messages");
  m.innerHTML = conversation
    .map((msg) => {
      const typeClass = msg.authorType === "staff" ? "staff" : "student";
      return `
      <div class="message">
        <div class="messageBubble ${typeClass}">
          <div class="messageHeader">
            <span class="name">${msg.name}</span>
            ${msg.role ? `<span class="role">${msg.role}</span>` : ""}
            <span class="time">${msg.time}</span>
          </div>
          <div class="messageText">${msg.text}</div>
        </div>
      </div>`;
    })
    .join("");
}

function renderInfo() {
  const info = [
    { label: "Ticket ID", value: ticketData.id },
    { label: "Category", value: ticketData.category },
    { label: "Priority", value: ticketData.priority },
    { label: "Assigned staff", value: ticketData.assigned || "—" },
    { label: "Created on", value: ticketData.createdOn || "—" },
  ];
  document.getElementById("ticketInfoList").innerHTML = info
    .map(
      (i) => `
    <div class="infoRow">
      <span class="label">${i.label}</span>
      <span class="value">${i.value}</span>
    </div>`
    )
    .join("");
}

function renderTimeline() {
  document.getElementById("timelineList").innerHTML = timeline
    .map(
      (t) => `
    <li class="timelineItem">
      <span class="tlDot ${t.color}"></span>
      <div class="tlText">
        <span class="label">${t.label}</span>
        <span class="time">${t.time}</span>
      </div>
    </li>`
    )
    .join("");
}

function toggleActionButtons() {
  const del = document.getElementById("deleteBtn");
  const sched = document.getElementById("scheduleBtn");
  // Defaults: show delete for admin only
  if (ROLE === "admin") {
    if (del) del.style.display = "";
    if (sched) sched.style.display = "none";
    return;
  }
  if (ROLE === "counselor") {
    // Show Schedule button only if meeting is requested
    const meeting = (ticketData.meeting || "").toLowerCase();
    if (meeting === "requested") {
      if (sched) sched.style.display = "";
    } else {
      if (sched) sched.style.display = "none";
    }
    if (del) del.style.display = "none";
  } else {
    // Other roles: hide both
    if (del) del.style.display = "none";
    if (sched) sched.style.display = "none";
  }
}

function wireActions() {
  document.getElementById("sendBtn").addEventListener("click", () => {
    const input = document.getElementById("replyInput");
    const text = input.value.trim();
    if (!text) return;
    conversation.push({
      name: "You",
      role: "",
      time: "Just now",
      text,
      authorType: "student",
    });
    input.value = "";
    renderMessages();
  });

  document.getElementById("resolveBtn").addEventListener("click", () => {
    ticketData.status = "Resolved";
    timeline[timeline.length - 1] = {
      label: "Resolved",
      time: "Just now",
      color: "green",
      pending: false,
    };
    renderHeader();
    renderTimeline();
  });

  const deleteBtn = document.getElementById("deleteBtn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", () => openDeleteModal());
  }

  const scheduleBtn = document.getElementById("scheduleBtn");
  if (scheduleBtn) {
    scheduleBtn.addEventListener("click", () => {
      // Placeholder: open scheduling flow/modal; can be wired to real endpoint later
      alert("Open scheduling dialog for ticket #" + ticketData.id);
    });
  }
}

// Cache helpers
const CACHE_TTL_MS = 5 * 60 * 1000;
function cacheKeyFor(id) {
  return `${ROLE}_ticket_${id}`;
}
function loadFromCache(id) {
  try {
    const str = localStorage.getItem(cacheKeyFor(id));
    if (!str) return null;
    const obj = JSON.parse(str);
    if (!obj || !obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
    return obj.data || null;
  } catch {
    return null;
  }
}
function saveToCache(id, data) {
  try {
    localStorage.setItem(
      cacheKeyFor(id),
      JSON.stringify({ _ts: Date.now(), data })
    );
  } catch {}
}

function getTicketIdFromUrl() {
  const u = new URL(window.location.href);
  const id = u.searchParams.get("id");
  if (id) return parseInt(id, 10);
  const code = u.searchParams.get("code");
  if (code) {
    const m = code.match(/\d+/);
    if (m) return parseInt(m[0], 10);
  }
  return null;
}

async function fetchTicket(id) {
  const res = await fetch(`${CFG.apiBase}?id=${encodeURIComponent(id)}`, {
    credentials: "include",
  });
  if (!res.ok) throw new Error("Failed to fetch ticket");
  return res.json();
}

(async function init() {
  const id = getTicketIdFromUrl();
  if (id) {
    ticketData = loadFromCache(id);
    if (!ticketData) {
      try {
        ticketData = await fetchTicket(id);
        saveToCache(id, ticketData);
      } catch (e) {
        console.error(e);
      }
    } else {
      fetchTicket(id)
        .then((fresh) => {
          saveToCache(id, fresh);
        })
        .catch(() => {});
    }
  }
  if (!ticketData) {
    ticketData = {
      id: 0,
      code: "",
      title: "Ticket",
      status: "Under Review",
      createdOn: "",
      attachments: [],
      description: "",
      category: "",
      priority: "",
      assigned: "",
      meeting: "none",
    };
  }
  try {
    const vtName = ticketData.id
      ? `ticket-${ticketData.id}`
      : ticketData.code
      ? `ticket-${ticketData.code}`
      : null;
    if (vtName) {
      const summary = document.getElementById("ticketSummaryCard");
      if (summary) summary.style.viewTransitionName = vtName;
    }
  } catch {}
  renderHeader();
  renderDescription();
  renderAttachments();
  renderMessages();
  renderInfo();
  renderTimeline();
  toggleActionButtons();
  wireActions();
})();

// Modal helpers (admin only)
function openDeleteModal() {
  if (ROLE !== "admin") return; // safety
  const overlay = document.getElementById("deleteModal");
  if (!overlay) return;
  overlay.classList.add("open");
  document.body.classList.add("modal-open");

  const cancelBtn = document.getElementById("cancelDeleteBtn");
  const confirmBtn = document.getElementById("confirmDeleteBtn");
  const backdropBtn = overlay.querySelector(".modalBackdropClose");

  const close = () => {
    overlay.classList.remove("open");
    document.body.classList.remove("modal-open");
    cancelBtn && cancelBtn.removeEventListener("click", onCancel);
    confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
    backdropBtn && backdropBtn.removeEventListener("click", onCancel);
  };
  const onCancel = (e) => {
    e && e.preventDefault();
    close();
  };
  const onConfirm = async (e) => {
    e && e.preventDefault();
    try {
      const res = await fetch(CFG.deleteEndpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });
      if (!res.ok) throw new Error("Delete failed");
      try {
        localStorage.setItem("admin_tickets_bust", String(Date.now()));
      } catch {}
      window.location.href = "/admin/tickets";
    } catch (e) {
      console.error(e);
      alert("Failed to delete the ticket.");
    } finally {
      close();
    }
  };

  cancelBtn && cancelBtn.addEventListener("click", onCancel);
  confirmBtn && confirmBtn.addEventListener("click", onConfirm);
  backdropBtn && backdropBtn.addEventListener("click", onCancel);
}
