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

// Conversation messages array (populated from API)
const conversation = [];

const CFG = window.TICKET_FULL_CONFIG || {
  role: "admin",
  apiBase: "/admin/ticketData",
  deleteEndpoint: "/admin/ticketDelete",
  resolveEndpoint: "/admin/ticketResolve",
};
const ROLE = (CFG.role || "admin").toLowerCase();

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

// Date/time formatting helpers for chat
function parseChatDate(dateString) {
  if (!dateString) return null;

  // MySQL DATETIME values have no timezone; treat them as UTC and convert for display.
  const mysqlDateTime = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
  const isoString = mysqlDateTime.test(dateString)
    ? dateString.replace(' ', 'T') + 'Z'
    : dateString;

  const date = new Date(isoString);
  return Number.isNaN(date.getTime()) ? null : date;
}

function formatDate(dateString) {
  const date = parseChatDate(dateString);
  if (!date) return '';

  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Today';
  } else if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  } else {
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  }
}

function formatTime(dateString) {
  const date = parseChatDate(dateString);
  if (!date) return '';

  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function formatMessageText(rawText) {
  const normalized = String(rawText || '')
    .replace(/\\r\\n/g, '\n')
    .replace(/\\n/g, '\n')
    .replace(/\r\n/g, '\n')
    .replace(/\n{2,}/g, '\n')
    .replace(/\s+(Title:|Date:|Time:|Mode:|Venue:|Meeting Link:|Notes:|\(Meeting[^\)]*ID:\s*\d+\))/g, '\n$1')
    .trim();

  if (!normalized) return '';

  return normalized
    .split('\n')
    .filter((line) => line.trim() !== '')
    .map((line) => {
      const escaped = escapeHtml(line.trim()).replace(
        /(https?:\/\/[^\s<]+)/g,
        '<a class="inlineMessageLink" href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
      );
      return `<div class="messageLine">${escaped}</div>`;
    })
    .join('');
}

function renderMessages() {
  const m = document.getElementById("messages");
  let lastDate = null;

  m.innerHTML = conversation
    .map((msg) => {
      const isStaff = msg.authorType === "staff";
      const typeClass = isStaff ? "staff" : "student";
      const division = (msg.role || "").trim();
      const headerTitle = isStaff
        ? `<span class="name"><span class="staffLabel">Staff</span>${division ? ` <span class="role">(${division})</span>` : ""}</span>`
        : `<span class="name">${msg.name || "You"}</span>`;
      
      const dateStr = formatDate(msg.time);
      const timeStr = formatTime(msg.time);
      
      let dateHeader = '';
      if (dateStr !== lastDate) {
        dateHeader = `<div class="chat-date-separator"><span>${dateStr}</span></div>`;
        lastDate = dateStr;
      }

      return `
      ${dateHeader}
      <div class="message ${typeClass}">
        <div class="messageBubble">
          <div class="messageHeader">
            ${headerTitle}
            <span class="time">${timeStr}</span>
          </div>
          <div class="messageText">${formatMessageText(msg.text)}</div>
        </div>
      </div>`;
    })
    .join("");
  
  // Auto-scroll to bottom of messages
  m.scrollTop = m.scrollHeight;
}

function renderInfo() {
  const info = [
    { label: "Ticket ID", value: ticketData.id },
    { label: "Student Name", value: ticketData.student?.name || "Unknown" },
    { label: "Ticket type", value: ticketData.type || ticketData.t_type || "—" },
    { label: "Category", value: ticketData.category },
    { label: "Priority", value: ticketData.priority },
    { label: "Assigned staff", value: ticketData.assigned || "—" },
    { label: "Created on", value: ticketData.createdOn || "—" },
  ];

  let html = info.map(i => `
    <div class="infoRow">
      <span class="label">${i.label}</span>
      <span class="value">${i.value}</span>
    </div>
  `).join("");

  // Add indicators at the bottom
  const meetingNorm = (ticketData.meeting || "").toLowerCase();
  const isMeetingRequested = meetingNorm === "requested" || meetingNorm === "true" || ticketData.meeting_requested;
  if (isMeetingRequested) {
    html += `
    <div class="infoRow mt-2">
      <span class="badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;width:100%;text-align:center;padding:8px;border-radius:10px;font-weight:500;">
        Meeting Requested
      </span>
    </div>`;
  }
  
  if (ticketData.overdue || ticketData.is_overdue_pending || ticketData.isOverdue) {
    html += `
    <div class="infoRow mt-2">
      <span class="badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;width:100%;text-align:center;padding:8px;border-radius:10px;font-weight:500;">
        Overdue (Pending)
      </span>
    </div>`;
  }

  document.getElementById("ticketInfoList").innerHTML = html;
}

function renderTimeline() {
  const tl = ticketData.timeline || [];
  const tlEl = document.getElementById("timelineList");
  if (!tlEl) return;
  tlEl.innerHTML = tl
    .map(
      (t) => `
    <li class="timelineItem">
      <span class="tlDot ${t.color}"></span>
      <div class="tlText">
        <span class="label">${t.label}</span>
        <span class="time ${t.color}">${t.time}</span>
      </div>
    </li>`
    )
    .join("");
}

function toggleActionButtons() {
  const del = document.getElementById("deleteBtn");
  const sched = document.getElementById("scheduleBtn");
  const resolveBtn = document.getElementById("resolveBtn");
  const rejectBtn = document.getElementById("rejectBtn");
  const assignBtn = document.getElementById("assignBtn");
  const forwardBtn = document.getElementById("forwardBtn");
  const statusNorm = (ticketData.status || "").toLowerCase();
  const isResolved = statusNorm === "resolved" || statusNorm === "closed" || statusNorm === "agent-closed" || statusNorm === "agent closed";

  // Admin: show delete and resolve (if not resolved)
  if (ROLE === "admin") {
    if (del) del.style.display = "";
    if (resolveBtn) resolveBtn.style.display = isResolved ? "none" : "";
    if (rejectBtn) rejectBtn.style.display = "none";
    if (sched) sched.style.display = "none";
    return;
  }

  // Student: show resolve button (if not resolved), hide delete and schedule
  if (ROLE === "student") {
    if (del) del.style.display = "none";
    if (sched) sched.style.display = "none";
    if (rejectBtn) rejectBtn.style.display = "none";
    if (resolveBtn) resolveBtn.style.display = isResolved ? "none" : "";
    return;
  }

  // Counselor: works same as staff but includes schedule option
  if (ROLE === "counselor") {
    const isPending = !!ticketData.isPending;
    const isAssignedToMe = !!ticketData.isAssignedToMe;
    const meeting = (ticketData.meeting || "").toLowerCase();
    
    if (del) del.style.display = "none";
    if (assignBtn) assignBtn.style.display = isPending ? "" : "none";
    if (forwardBtn) forwardBtn.style.display = "none"; // Hide forwarding for counselors
    if (resolveBtn) resolveBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    if (rejectBtn) rejectBtn.style.display = "none"; // Hide closing/rejecting for counselors
    if (sched) sched.style.display = isAssignedToMe && !isResolved && (meeting === "requested" || meeting === "true" || ticketData.meeting_requested) ? "" : "none";
    
    const sendBtn = document.getElementById("sendBtn");
    const replyInput = document.getElementById("replyInput");
    const attachBtn = document.getElementById("attachBtn");
    if (!isAssignedToMe || isResolved) {
      if (sendBtn) sendBtn.disabled = true;
      if (replyInput) replyInput.disabled = true;
      if (attachBtn) attachBtn.disabled = true;
    }
    return;
  }

  // Staff: assign/forward/resolve/reject rules
  if (ROLE === "staff") {
    const isPending = !!ticketData.isPending;
    const isAssignedToMe = !!ticketData.isAssignedToMe;
    
    // Hide everything by default
    if (del) del.style.display = "none";
    if (sched) sched.style.display = "none";
    
    if (assignBtn) assignBtn.style.display = isPending ? "" : "none";
    if (forwardBtn) forwardBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    if (resolveBtn) resolveBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    if (rejectBtn) rejectBtn.style.display = isAssignedToMe && !isResolved ? "" : "none";
    
    // Also disable chat input if not assigned or resolved
    const sendBtn = document.getElementById("sendBtn");
    const replyInput = document.getElementById("replyInput");
    const attachBtn = document.getElementById("attachBtn");
    if (!isAssignedToMe || isResolved) {
      if (sendBtn) sendBtn.disabled = true;
      if (replyInput) replyInput.disabled = true;
      if (attachBtn) attachBtn.disabled = true;
    }
    return;
  }

  // Other roles: hide all action buttons
  if (del) del.style.display = "none";
  if (sched) sched.style.display = "none";
  if (resolveBtn) resolveBtn.style.display = "none";
  if (rejectBtn) rejectBtn.style.display = "none";
}

function wireActions() {
  const sendBtn = document.getElementById("sendBtn");
  if (sendBtn) {
    sendBtn.addEventListener("click", async () => {
      const input = document.getElementById("replyInput");
      const text = input.value.trim();
      if (!text) return;

      const ticketId = getTicketIdFromUrl();
      if (!ticketId) return;

      // Show loading state
      sendBtn.classList.add("loading");
      sendBtn.disabled = true;

      try {
        const res = await fetch(`/${ROLE}/sendMessage`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            ticket_id: ticketId,
            message: text
          }),
          credentials: "include"
        });
        
        const data = await res.json();
        if (data.success) {
          input.value = "";
          await fetchMessages(ticketId); // Refresh messages
        } else {
          alert("Failed to send message: " + (data.error || "Unknown error"));
        }
      } catch (e) {
        console.error("Error sending message:", e);
        alert("Error sending message");
      } finally {
        // Hide loading state
        sendBtn.classList.remove("loading");
        sendBtn.disabled = false;
      }
    });
  }

  // Also allow sending with Enter key
  const replyInput = document.getElementById("replyInput");
  if (replyInput) {
    replyInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        const sendBtn = document.getElementById("sendBtn");
        if (sendBtn && !sendBtn.disabled) sendBtn.click();
      }
    });
  }

  const resolveBtn = document.getElementById("resolveBtn");
  if (resolveBtn) {
    resolveBtn.addEventListener("click", () => openResolveModal());
  }

  const deleteBtn = document.getElementById("deleteBtn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", () => openDeleteModal());
  }

<<<<<<< HEAD
  // Schedule button is handled by inline script in the view file
=======
  const rejectBtn = document.getElementById("rejectBtn");
  if (rejectBtn) {
    rejectBtn.addEventListener("click", () => openRejectModal());
  }

  const assignBtnEl = document.getElementById("assignBtn");
  const assignModal = document.getElementById("assignModal");
  if (assignBtnEl && assignModal) {
    assignBtnEl.addEventListener("click", () => {
      assignModal.classList.add("open");
      assignModal.setAttribute("aria-hidden", "false");
      document.body.classList.add("modal-open");
    });
    
    // Assign Confirm
    const confirmAssignBtn = document.getElementById("confirmAssignBtn");
    if (confirmAssignBtn) {
      confirmAssignBtn.addEventListener("click", async () => {
        try {
          const formData = new FormData();
          formData.append("id", getTicketIdFromUrl());
          const res = await fetch(`/${ROLE}/ticketAssign`, { method: "POST", body: formData });
          const data = await res.json();
          if (data.success) {
            window.location.reload();
          } else {
            alert(data.error || data.message || "Failed");
          }
        } catch(e) {
          console.error(e);
        }
      });
    }

    // Assign Cancel
    const cancelAssignBtn = document.getElementById("cancelAssignBtn");
    if (cancelAssignBtn) {
      cancelAssignBtn.addEventListener("click", () => {
        assignModal.classList.remove("open");
        assignModal.setAttribute("aria-hidden", "true");
        document.body.classList.remove("modal-open");
      });
    }
  }

  const forwardBtnEl = document.getElementById("forwardBtn");
  const forwardModal = document.getElementById("forwardModal");
  if (forwardBtnEl && forwardModal) {
    forwardBtnEl.addEventListener("click", async () => {
      forwardModal.classList.add("open");
      forwardModal.setAttribute("aria-hidden", "false");
      document.body.classList.add("modal-open");
      
      const select = document.getElementById("forwardStaffSelect");
      if (select && select.options.length <= 1) {
        try {
          const res = await fetch(`/${ROLE}/staffMembersList`);
          const data = await res.json();
          if (data.success && Array.isArray(data.data)) {
             data.data.forEach(m => {
               const opt = document.createElement("option");
               opt.value = m.u_id;
               opt.textContent = m.name;
               select.appendChild(opt);
             });
          }
        } catch(e) {}
      }
    });

    const confirmForwardBtn = document.getElementById("confirmForwardBtn");
    if (confirmForwardBtn) {
      confirmForwardBtn.addEventListener("click", async () => {
        const select = document.getElementById("forwardStaffSelect");
        if (!select || !select.value) { alert("Please select a staff member"); return; }
        
        try {
          const formData = new FormData();
          formData.append("id", getTicketIdFromUrl());
          formData.append("forward_to", select.value);
          const reason = document.getElementById("forwardReason");
          if (reason) formData.append("reason", reason.value || "");
          
          const res = await fetch(`/${ROLE}/ticketForward`, { method: "POST", body: formData });
          const data = await res.json();
          if (data.success) {
            window.location.reload();
          } else {
            alert(data.error || data.message || "Failed");
          }
        } catch(e) { console.error(e); }
      });
    }

    const cancelForwardBtn = document.getElementById("cancelForwardBtn");
    if (cancelForwardBtn) {
      cancelForwardBtn.addEventListener("click", () => {
        forwardModal.classList.remove("open");
        forwardModal.setAttribute("aria-hidden", "true");
        document.body.classList.remove("modal-open");
      });
    }
  }

  const scheduleBtn = document.getElementById("scheduleBtn");
  if (scheduleBtn) {
    scheduleBtn.addEventListener("click", () => {
      // Open the custom schedule meeting modal (UI only)
      openScheduleModal();
    });
  }
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
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

// Fetch chat messages from the server
async function fetchMessages(id) {
  try {
    const res = await fetch(`/${ROLE}/chatMessages?ticket_id=${encodeURIComponent(id)}`, { 
      credentials: "include" 
    });
    if (!res.ok) throw new Error("Failed to fetch messages");
    const data = await res.json();
    if (data.messages) {
      conversation.length = 0;
      data.messages.forEach(msg => {
        conversation.push({
          name: msg.sender_name,
          role: msg.sender_role,
          time: msg.created_at,
          text: msg.message,
          authorType: msg.sender_role === 'student' ? 'student' : 'staff'
        });
      });
      renderMessages();
    }
  } catch (e) {
    console.error("Error fetching messages:", e);
  }
}

function showMeetingDuePopup(notification) {
  const title = notification.title || "Counseling Session";
  const startTime = notification.start_time || "--:--";
  const venue = notification.venue || "";
  const link = notification.meeting_link || "";

  let text = `Meeting time has started.\n\nTitle: ${title}\nStart Time: ${startTime}`;
  if (venue) text += `\nVenue: ${venue}`;
  if (link) text += `\nMeeting Link: ${link}`;

  alert(text);
}

async function checkMeetingDueNotifications() {
  try {
    const res = await fetch('/meetingscheduler/processDueMeetingNotifications', {
      credentials: 'include'
    });
    if (!res.ok) return;

    const data = await res.json();
    if (!data || !data.success || !Array.isArray(data.notifications)) return;

    data.notifications.forEach((notification) => {
      const meetingId = notification.meeting_id;
      if (!meetingId) return;

      const key = `meeting_popup_seen_${meetingId}`;
      if (localStorage.getItem(key) === '1') return;

      showMeetingDuePopup(notification);
      localStorage.setItem(key, '1');
    });
  } catch (e) {
    console.error('Error checking due meeting notifications:', e);
  }
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
          // If data changed materially, re-render
          const oldStr = JSON.stringify(ticketData);
          const newStr = JSON.stringify(fresh);
          if (oldStr !== newStr) {
            ticketData = fresh;
            saveToCache(id, fresh);
            // Re-render components that depend on data
            renderHeader();
            renderDescription();
            renderAttachments();
            renderMessages();
            renderInfo();
            renderTimeline();
            toggleActionButtons();
          } else {
             saveToCache(id, fresh); // just update timestamp
          }
        })
        .catch(() => {});
    }
    
    // Make ticket data globally available for modal
    window.ticketData = ticketData;
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
  renderInfo();
  renderTimeline();
  toggleActionButtons();
  wireActions();

  // Interval for polling chat messages (previously 10s, now 5s to match old logic)
  if (id) {
    fetchMessages(id);
<<<<<<< HEAD
    // Poll for new messages every 10 seconds
    setInterval(() => fetchMessages(id), 10000);

    if (ROLE === 'student' || ROLE === 'counselor') {
      checkMeetingDueNotifications();
      setInterval(() => checkMeetingDueNotifications(), 30000);
    }
=======
    setInterval(() => fetchMessages(id), 5000);
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
  } else {
    renderMessages();
  }
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
      if (res.url.includes("/login")) {
        alert("Session expired. Please log in again.");
        window.location.href = "/login";
        return;
      }
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

// ... (top of file)

function openResolveModal() {
  // Allow admin, student, staff, and counselor to resolve tickets
  if (ROLE !== "admin" && ROLE !== "student" && ROLE !== "staff" && ROLE !== "counselor") return;
  const overlay = document.getElementById("resolveModal");
  if (!overlay) return;

  overlay.classList.add("open");
  // Fix the aria-hidden console warning
  overlay.removeAttribute("aria-hidden");
  document.body.classList.add("modal-open");

  const cancelBtn = document.getElementById("cancelResolveBtn");
  const confirmBtn = document.getElementById("confirmResolveBtn");
  const backdropBtn = overlay.querySelector(".modalBackdropClose");

  const close = () => {
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
    // Use {once: true} or manual removal to prevent multiple listeners
  };

  const onConfirm = async (e) => {
    e && e.preventDefault();

    // Use role-based endpoint: /student/ticketResolve or /admin/ticketResolve
    const endpoint = CFG.resolveEndpoint || `/${ROLE}/ticketResolve`;
    console.log("Resolved Endpoint Path:", endpoint);

    try {
      const res = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });

      // Capture as text first to avoid "Unexpected token <" crash
      const rawText = await res.text();

      let data;
      try {
        data = JSON.parse(rawText);
      } catch (jsonErr) {
        throw new Error(
          "Server returned non-JSON response. Check PHP error logs."
        );
      }

      if (!res.ok) throw new Error(data.error || "Mark as resolved failed");

      ticketData.status = "Resolved";

      renderHeader();
      renderInfo();
      renderTimeline();
      toggleActionButtons();

      // Clear cache so it doesn't revert on manual refresh
      try {
        localStorage.removeItem(cacheKeyFor(ticketData.id));
      } catch (e) {}

      close();
    } catch (err) {
      console.error("RESOLVE ERROR:", err);
      alert("Error: " + err.message);
    }
  };

  // Ensure listeners are only attached once
  confirmBtn.onclick = onConfirm;
  cancelBtn.onclick = (e) => {
    e.preventDefault();
    close();
  };
  if (backdropBtn) {
    backdropBtn.onclick = (e) => {
      e.preventDefault();
      close();
    };
  }
}

<<<<<<< HEAD
// Schedule modal is handled by inline script in the view file
=======
// Modal helper: counselor schedule meeting (UI only)
function openScheduleModal() {
  const overlay = document.getElementById("scheduleModal");
  if (!overlay) return;

  overlay.classList.add("open");
  document.body.classList.add("modal-open");

  const cancelBtn = document.getElementById("cancelScheduleBtn");
  const backdropBtn = overlay.querySelector(".modalBackdropClose");

  const close = (e) => {
    e && e.preventDefault();
    overlay.classList.remove("open");
    document.body.classList.remove("modal-open");
    cancelBtn && cancelBtn.removeEventListener("click", close);
    backdropBtn && backdropBtn.removeEventListener("click", close);
  };

  cancelBtn && cancelBtn.addEventListener("click", close);
  backdropBtn && backdropBtn.addEventListener("click", close);
}

// Modal helper for Reject/Close Ticket (staff only)
function openRejectModal() {
  if (ROLE !== "staff") return; 
  const overlay = document.getElementById("rejectModal");
  if (!overlay) return;

  overlay.classList.add("open");
  overlay.removeAttribute("aria-hidden");
  document.body.classList.add("modal-open");

  const cancelBtn = document.getElementById("cancelRejectBtn");
  const confirmBtn = document.getElementById("confirmRejectBtn");
  const backdropBtn = overlay.querySelector(".modalBackdropClose");

  const close = () => {
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  };

  const onConfirm = async (e) => {
    e && e.preventDefault();
    confirmBtn.disabled = true;

    try {
      const res = await fetch(`/${ROLE}/ticketReject`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });

      const rawText = await res.text();
      let data;
      try {
        data = JSON.parse(rawText);
      } catch (jsonErr) {
        throw new Error("Server returned non-JSON response.");
      }

      if (!res.ok) throw new Error(data.error || data.message || "Failed to reject via API");
      if (data.success || (!data.error && !data.message)) {
        window.location.reload();
      } else {
        alert(data.error || data.message || "Failed to reject via API");
      }
    } catch (err) {
      console.error(err);
      alert("Error rejecting ticket: " + err.message);
    } finally {
      confirmBtn.disabled = false;
      close();
    }
  };

  confirmBtn.onclick = onConfirm;
  cancelBtn.onclick = (e) => { e.preventDefault(); close(); };
  if (backdropBtn) {
    backdropBtn.onclick = (e) => { e.preventDefault(); close(); };
  }
}
>>>>>>> a9932f02c5946f38d336d8baad5864b7a3b9e935
