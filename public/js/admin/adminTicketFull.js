// Navbar height variable helper to keep page height correct under the navbar
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

// Ticket data will be fetched and cached per ID
let ticketData = null;

const conversation = [
  {
    name: "Dr Dinuni Fernando",
    role: "Lecturer",
    time: "4 hours ago",
    text: "Hi! I've received your ticket and I'm looking into the LMS access issue. I can see that your account permissions were affected during the maintenance. I'm working with the IT team to restore your access. You should be able to log in within the next 2 hours.",
    authorType: "staff",
  },
  {
    name: "You",
    role: "",
    time: "4 hours ago",
    text: "Thank you for the quick response! Just to confirm, will I be able to access all my course materials including the assignment submission portal?",
    authorType: "student",
  },
  {
    name: "Dr Dinuni Fernando",
    role: "Lecturer",
    time: "4 hours ago",
    text: "Yes, absolutely! Once your access is restored, you'll have full access to all course materials, discussion forums, and the assignment submission portal. I'll update you as soon as it's fixed.",
    authorType: "staff",
  },
];

const timeline = [
  {
    label: "Ticket created",
    time: "Jul 15, 2025 at 2:30 PM",
    color: "green",
    pending: false,
  },
  {
    label: "Assigned to staff",
    time: "Jul 15, 2025 at 3:30 PM",
    color: "blue",
    pending: false,
  },
  {
    label: "Under review",
    time: "Jul 15, 2025 at 5:30 PM",
    color: "yellow",
    pending: false,
  },
  { label: "Resolved", time: "Pending", color: "gray", pending: true },
];

// Utility to map status text to classes defined in components.css
function statusClass(status) {
  const normalized = status.toLowerCase().replace(/\s+/g, "");
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
    { icon: "TKT", text: ticketData.code || `TKT-${ticketData.id}` },
    { icon: "Created", text: ticketData.createdOn || "" },
  ];
  document.getElementById("ticketMeta").innerHTML = meta
    .map((m) => `<span>${m.text}</span>`)
    .join("");
}

function renderDescription() {
  document.getElementById("ticketDescriptionText").textContent =
    ticketData.description;
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
    deleteBtn.addEventListener("click", async () => {
      if (!ticketData || !ticketData.id) return;
      openDeleteModal();
    });
  }
}

// Cache helpers (simple TTL cache)
const CACHE_TTL_MS = 5 * 60 * 1000; // 5 minutes
function cacheKeyFor(id) {
  return `admin_ticket_${id}`;
}
function loadFromCache(id) {
  try {
    const str = localStorage.getItem(cacheKeyFor(id));
    if (!str) return null;
    const obj = JSON.parse(str);
    if (!obj || typeof obj !== "object") return null;
    if (!obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
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
  const res = await fetch(`/admin/ticketData?id=${encodeURIComponent(id)}`, {
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
      // Optional refresh in background (stale-while-revalidate)
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
    };
  }
  // Set destination transition names to match the origin card
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
  wireActions();
})();

// Modal helpers
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
    // Remove listeners after animation
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
      const res = await fetch("/admin/ticketDelete", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });
      if (!res.ok) throw new Error("Delete failed");
      localStorage.removeItem(cacheKeyFor(ticketData.id));
      // Signal tickets page to bypass cache once
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
