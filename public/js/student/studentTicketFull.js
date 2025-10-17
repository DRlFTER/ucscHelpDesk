// Student version of adminTicketFull.js; endpoints adjusted to /student
(function () {
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
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", setNavbarVar);
  } else {
    setNavbarVar();
  }
})();

let ticketData = null;

const conversation = [
  // same placeholders as admin; can be replaced with real data later
];

const timeline = [
  { label: "Ticket created", time: "", color: "green", pending: false },
  { label: "Assigned to staff", time: "", color: "blue", pending: false },
  { label: "Under review", time: "", color: "yellow", pending: false },
  { label: "Resolved", time: "Pending", color: "gray", pending: true },
];

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
  document.getElementById("ticketTitle").textContent = ticketData.title || "Ticket";
  const statusEl = document.getElementById("ticketStatus");
  statusEl.className = statusClass(ticketData.status || "");
  statusEl.textContent = ticketData.status || "";

  const meta = [
    { icon: "TKT", text: ticketData.code || `TKT-${ticketData.id}` },
    { icon: "Created", text: ticketData.createdOn || "" },
  ];
  document.getElementById("ticketMeta").innerHTML = meta
    .map((m) => `<span>${m.text}</span>`)
    .join("");
}

function renderDescription() {
  document.getElementById("ticketDescriptionText").textContent = ticketData.description || "";
}

function renderAttachments() {
  const c = document.getElementById("attachmentsList");
  const list = Array.isArray(ticketData.attachments) ? ticketData.attachments : [];
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
    { label: "Assigned staff", value: ticketData.assigned },
    { label: "Created on", value: ticketData.createdOn },
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

function wireActions() {
  const sendBtn = document.getElementById("sendBtn");
  if (sendBtn) {
    sendBtn.addEventListener("click", () => {
      const input = document.getElementById("replyInput");
      const text = input.value.trim();
      if (!text) return;
      conversation.push({ name: "You", role: "", time: "Just now", text, authorType: "student" });
      input.value = "";
      renderMessages();
    });
  }

  const deleteBtn = document.getElementById("deleteBtn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", () => {
      if (!ticketData || !ticketData.id) return;
      openDeleteModal();
    });
  }
}

const CACHE_TTL_MS = 5 * 60 * 1000;
function cacheKeyFor(id) { return `student_ticket_${id}`; }
function loadFromCache(id) {
  try {
    const str = localStorage.getItem(cacheKeyFor(id));
    if (!str) return null;
    const obj = JSON.parse(str);
    if (!obj || typeof obj !== "object") return null;
    if (!obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
    return obj.data || null;
  } catch { return null; }
}
function saveToCache(id, data) {
  try { localStorage.setItem(cacheKeyFor(id), JSON.stringify({ _ts: Date.now(), data })); } catch {}
}

function getTicketIdFromUrl() {
  const u = new URL(window.location.href);
  const id = u.searchParams.get("id");
  if (id) return parseInt(id, 10);
  const code = u.searchParams.get("code");
  if (code) { const m = code.match(/\d+/); if (m) return parseInt(m[0], 10); }
  return null;
}

async function fetchTicket(id) {
  const res = await fetch(`/student/ticketData?id=${encodeURIComponent(id)}`, { credentials: "include" });
  if (!res.ok) throw new Error("Failed to fetch ticket");
  return res.json();
}

(async function init() {
  const id = getTicketIdFromUrl();
  if (id) {
    ticketData = loadFromCache(id);
    if (!ticketData) {
      try { ticketData = await fetchTicket(id); saveToCache(id, ticketData); }
      catch (e) { console.error(e); }
    } else {
      fetchTicket(id).then((fresh) => { saveToCache(id, fresh); }).catch(() => {});
    }
  }
  if (!ticketData) {
    ticketData = { id: 0, code: "", title: "Ticket", status: "Under Review", createdOn: "", attachments: [], description: "", category: "", priority: "", assigned: "" };
  }
  renderHeader();
  renderDescription();
  renderAttachments();
  renderMessages();
  renderInfo();
  renderTimeline();
  wireActions();
})();

function openDeleteModal() {
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

  const onCancel = (e) => { e && e.preventDefault(); close(); };

  const onConfirm = async (e) => {
    e && e.preventDefault();
    try {
      const res = await fetch("/student/ticketDelete", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });
      if (!res.ok) throw new Error("Delete failed");
      localStorage.removeItem(cacheKeyFor(ticketData.id));
      try { localStorage.setItem("student_tickets_bust", String(Date.now())); } catch {}
      window.location.href = "/student/tickets";
    } catch (e) {
      console.error(e);
      alert("Failed to delete the ticket.");
    } finally { close(); }
  };

  cancelBtn && cancelBtn.addEventListener("click", onCancel);
  confirmBtn && confirmBtn.addEventListener("click", onConfirm);
  backdropBtn && backdropBtn.addEventListener("click", onCancel);
}
