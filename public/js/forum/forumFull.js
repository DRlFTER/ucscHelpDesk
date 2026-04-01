// Global Forum Full view JS - works for all roles (student, admin, staff, counselor)
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

// Detect current role from URL path
function getCurrentRole() {
  const path = window.location.pathname;
  const parts = path.split('/');
  if (parts.includes('admin')) return 'admin';
  if (parts.includes('staff')) return 'staff';
  if (parts.includes('counselor')) return 'counselor';
  if (parts.includes('student')) return 'student';
  return 'student'; // default
}

const currentRole = getCurrentRole();
let ticketData = null;

// Forum interactions
let voteState = { voted: false, count: 0 };
let conversation = [];

async function loadComments() {
    if (!ticketData || !ticketData.id) return;
    try {
        const res = await fetch(`forumComments?id=${ticketData.id}`, { credentials: 'include' });
        if (res.ok) {
            conversation = await res.json();
            renderMessages();
            // update header count
            ticketData.commentsCount = conversation.length;
            renderHeader();
        }
    } catch (e) { console.error(e); }
}

function statusClass(status) {
  const normalized = (status || "").toLowerCase().replace(/\s+/g, "");
  switch (normalized) {
    case "answered": // forum answered
      return "status resolved";
    case "open": // forum open
      return "status underReview";
    default:
      return "status";
  }
}

function applyPermissions() {
  const isOwner = Boolean(ticketData.isOwner);
  const editBtn = document.getElementById("editPostBtn");
  const delBtn = document.getElementById("deleteBtn");
  const visBtn = document.getElementById("toggleVisibilityBtn");
  const statusBtn = document.getElementById("toggleStatusBtn");
  
  if (editBtn) {
     const holder = editBtn.closest('.btnHolder');
     if (holder) holder.style.display = isOwner ? '' : 'none';
  }
  if (delBtn) {
     const holder = delBtn.closest('.btnHolder');
     if (holder) holder.style.display = isOwner ? '' : 'none';
  }
  if (visBtn) {
     const holder = visBtn.closest('.btnHolder');
     if (holder) holder.style.display = isOwner ? '' : 'none';
  }
  if (statusBtn) {
     const holder = statusBtn.closest('.btnHolder');
     if (holder) holder.style.display = isOwner ? '' : 'none';
  }
}

function renderHeader() {
  document.getElementById("ticketTitle").textContent = ticketData.title || "Post";
  const statusEl = document.getElementById("ticketStatus");
  statusEl.className = statusClass(ticketData.status || "");
  statusEl.textContent = ticketData.status || "";
  // Append visibility badge next to status
  const vis = (typeof ticketData.is_Public !== 'undefined' && !ticketData.is_Public) ? 'Private' : 'Public';
  const wrapper = statusEl.parentElement;
  if (wrapper) {
    let visEl = wrapper.querySelector('.visibilityBadge');
    if (!visEl) {
      visEl = document.createElement('span');
      visEl.className = 'status visibilityBadge';
      wrapper.appendChild(visEl);
    }
    visEl.classList.remove('underReview','inProgress','resolved','rejected');
    visEl.classList.add(vis === 'Public' ? 'inProgress' : 'rejected');
    visEl.textContent = vis;
  }

  const meta = [
    { icon: "POST", text: ticketData.code || `FRM-${ticketData.id}` },
    { icon: "Created", text: ticketData.createdOn || "" },
    { icon: "When", text: ticketData.createdAgo ? `${ticketData.createdAgo}` : "" },
    { icon: "Comments", text: `${(ticketData.commentsCount ?? conversation.length)} comments` },
  ];
  document.getElementById("ticketMeta").innerHTML = meta
    .map((m) => `<span>${m.text}</span>`)
    .join("");

  // votes
  voteState.count = Number(ticketData.votes || 0);
  voteState.voted = Number(ticketData.voted || 0);
  const voteCountEl = document.getElementById("voteCount");
  if (voteCountEl) voteCountEl.textContent = String(voteState.count);
  const voteBtn = document.getElementById("voteBtn");
  if (voteBtn) {
    const isUp = voteState.voted === 1;
    voteBtn.setAttribute("aria-pressed", isUp ? "true" : "false");
    voteBtn.classList.toggle("isActive", isUp);
  }
  const voteDownBtn = document.getElementById("voteDownBtn");
  if (voteDownBtn) {
    const isDown = voteState.voted === -1;
    voteDownBtn.setAttribute("aria-pressed", isDown ? "true" : "false");
    voteDownBtn.classList.toggle("isActive", isDown);
  }
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
  // Build tree
  const map = {};
  const roots = [];
  conversation.forEach(c => {
      c.children = [];
      map[c.id] = c;
      if (c.parentId) {
          if (map[c.parentId]) map[c.parentId].children.push(c);
          else roots.push(c); // Orphan fallback
      } else {
          roots.push(c);
      }
  });

  // Recursive render
  const renderList = (list, depth = 0) => {
      return list.map(msg => {
          const typeClass = msg.authorType === "staff" ? "staff" : "student";
          // Indentation for replies
          const style = depth > 0 ? `margin-left: ${Math.min(depth * 30, 90)}px; border-left: 2px solid #e5e7eb; padding-left: 10px;` : '';
          
          return `
      <div class="message" style="${style}" data-id="${msg.id}">
        <div class="messageBubble ${typeClass}" style="width:100%">
          <div class="messageHeader">
            <span class="name">${msg.name}</span>
            ${msg.role ? `<span class="role">${msg.role}</span>` : ""}
            <span class="time">${msg.time}</span>
          </div>
          <div class="messageText">${msg.text}</div>
          <div class="messageActions">
            <!-- Votes removed for now per requirement
            <button class="btnAttachRound msgUpvote" type="button" title="Upvote comment"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg></button>
            <button class="btnAttachRound msgDownvote" type="button" title="Downvote comment"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
            -->
            <button class="btnReplyText msgReply" type="button" title="Reply"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 10 4 15 9 20"></polyline><path d="M20 4v7a4 4 0 0 1-4 4H4"></path></svg> Reply</button>
          </div>
          <div class="replyBox" style="display:none; margin-top:8px;">
            <textarea class="replyInput" placeholder="Write a reply..." rows="2" style="width:100%; border:1px solid #ccc; border-radius:6px; padding:6px; font-size:13px;"></textarea>
            <div style="text-align:right; margin-top:4px;">
                <button class="btnPrimary btnSmall sendReplyBtn">Send</button>
                <button class="btnSecondary btnSmall cancelReplyBtn">Cancel</button>
            </div>
          </div>
        </div>
      </div>
      ${msg.children.length > 0 ? renderList(msg.children, depth + 1) : ''}
      `;
      }).join("");
  };

  m.innerHTML = renderList(roots);
}

function renderInfo() {
  const info = [
    { label: "Post ID", value: ticketData.id },
    { label: "Topic", value: ticketData.topic },
    { label: "Created on", value: ticketData.createdOn },
    { label: "Visibility", value: ticketData.is_Public ? 'Public' : 'Private' },
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

// timeline not used in forum version
function renderTimeline() {}

function wireActions() {
  const sendBtn = document.getElementById("sendBtn");
  if (sendBtn) {
    sendBtn.addEventListener("click", async () => {
      const input = document.getElementById("replyInput");
      const text = input.value.trim();
      if (!text) return;
      
      try {
          const form = new FormData();
          form.append('id', String(ticketData.id));
          form.append('text', text);
          
          sendBtn.disabled = true;
          const res = await fetch(`forumAddComment`, { method: 'POST', credentials: 'include', body: form });
          sendBtn.disabled = false;
          if (res.ok) {
              const json = await res.json();
              if (json.ok && json.comment) {
                  conversation.push(json.comment); // Will be root 
                  input.value = "";
                  renderMessages();
                  ticketData.commentsCount = (ticketData.commentsCount || 0) + 1;
                  renderHeader();
              }
          }
      } catch(e) { console.error(e); sendBtn.disabled=false; }
    });
  }

  const deleteBtn = document.getElementById("deleteBtn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", () => {
      if (!ticketData || !ticketData.id) return;
      openDeleteModal();
    });
  }

  const voteBtn = document.getElementById("voteBtn");
  const voteDownBtn = document.getElementById("voteDownBtn");
  
  const handleVote = async (type) => {
    try {
        const form = new FormData();
        form.append('id', String(ticketData.id));
        form.append('type', type);
        
        const res = await fetch(`forumVote`, { method: 'POST', credentials: 'include', body: form });
        const json = await res.json();
        
        if (json.ok) {
            voteState.count = json.votes;
            voteState.voted = json.voted;
            
            document.getElementById("voteCount").textContent = String(voteState.count);
            if (voteBtn) {
                const isUp = voteState.voted === 1;
                voteBtn.setAttribute("aria-pressed", isUp ? "true" : "false");
                voteBtn.classList.toggle("isActive", isUp);
            }
            if (voteDownBtn) {
                const isDown = voteState.voted === -1;
                voteDownBtn.setAttribute("aria-pressed", isDown ? "true" : "false");
                voteDownBtn.classList.toggle("isActive", isDown);
            }
        }
    } catch(e) { console.error(e); }
  };

  if (voteBtn) voteBtn.addEventListener("click", () => handleVote('up'));
  if (voteDownBtn) voteDownBtn.addEventListener("click", () => handleVote('down'));

  // Comment actions (upvote/reply) via event delegation
  const messagesEl = document.getElementById("messages");
  if (messagesEl) {
    messagesEl.addEventListener("click", async (e) => {
    //   const up = e.target.closest(".msgUpvote");
    //   if (up) { up.classList.toggle("isActive"); return; }
      
      const rep = e.target.closest(".msgReply");
      if (rep) { 
          const bubble = rep.closest('.messageBubble');
          const box = bubble.querySelector('.replyBox');
          if (box) {
              box.style.display = box.style.display === 'none' ? 'block' : 'none';
              if (box.style.display === 'block') box.querySelector('textarea').focus();
          }
          return;
      }
      
      const cancel = e.target.closest(".cancelReplyBtn");
      if (cancel) {
          const box = cancel.closest('.replyBox');
          if (box) box.style.display = 'none';
          return;
      }
      
      const send = e.target.closest(".sendReplyBtn");
      if (send) {
          const bubble = send.closest('.messageBubble');
          const msgEl = send.closest('.message');
          const parentId = msgEl.getAttribute('data-id');
          const box = send.closest('.replyBox');
          const input = box.querySelector('textarea');
          const text = input.value.trim();
          
          if (!text) return;
          
          try {
              const form = new FormData();
              form.append('id', String(ticketData.id));
              form.append('text', text);
              form.append('parentId', parentId);
              
              send.disabled = true;
              const res = await fetch(`forumAddComment`, { method: 'POST', credentials: 'include', body: form });
              send.disabled = false;
              if (res.ok) {
                  const json = await res.json();
                  if (json.ok && json.comment) {
                      conversation.push(json.comment);
                      input.value = "";
                      box.style.display = 'none'; // hide reply box
                      renderMessages();
                      ticketData.commentsCount = (ticketData.commentsCount || 0) + 1;
                      renderHeader();
                  }
              }
          } catch(e) { console.error(e); send.disabled=false; }
        }
    });
  }


  // Quick actions
  document.getElementById("copyLinkBtn")?.addEventListener("click", () => {
    try { navigator.clipboard.writeText(window.location.href); } catch {}
  });
  document.getElementById("followBtn")?.addEventListener("click", (e) => {
    const btn = e.currentTarget;
    const active = btn.classList.toggle("isActive");
    btn.querySelector('.btnSecondaryText').textContent = active ? 'Following' : 'Follow';
  });

  // Edit post (placeholder only)
  document.getElementById("editPostBtn")?.addEventListener("click", () => {
    alert("Edit post coming soon.");
  });

  // Toggle public/private
  const toggleBtn = document.getElementById("toggleVisibilityBtn");
  if (toggleBtn) {
    const applyLabel = (state) => {
      const text = state === 'public' ? 'Make Private' : 'Make Public';
      toggleBtn.querySelector('.btnSecondaryText').textContent = text;
      toggleBtn.dataset.state = state;
      toggleBtn.style.background = state === 'public' ? '#dcfce7' : '#fee2e2';
    };
    if (ticketData && typeof ticketData.is_Public !== 'undefined') {
      applyLabel(ticketData.is_Public ? 'public' : 'private');
    }
    toggleBtn.addEventListener('click', async () => {
      if (!ticketData || !ticketData.id) return;
      const current = toggleBtn.dataset.state === 'public' ? 'public' : 'private';
      const next = current === 'public' ? 'private' : 'public';
      applyLabel(next);
      try {
        const form = new FormData();
        form.append('id', String(ticketData.id));
        form.append('state', next);
        const res = await fetch(`forumToggleVisibility`, { method: 'POST', credentials: 'include', body: form });
        if (!res.ok) throw new Error('toggle_failed');
        const payload = await res.json();
        if (!payload.ok) throw new Error('toggle_failed');
        ticketData.is_Public = payload.is_Public ? 1 : 0;
      } catch (e) {
        applyLabel(current);
        alert('Failed to update visibility.');
      }
    });
  }

  // Toggle status open/answered
  const toggleStatusBtn = document.getElementById('toggleStatusBtn');
  if (toggleStatusBtn) {
    const applyStatusButton = (statusNow) => {
      const isAnswered = (statusNow || '').toLowerCase() === 'answered';
      const label = isAnswered ? 'Make Open' : 'Make Answered';
      toggleStatusBtn.querySelector('.btnSecondaryText').textContent = label;
      toggleStatusBtn.dataset.status = isAnswered ? 'answered' : 'open';
      toggleStatusBtn.style.background = isAnswered ? '#fef9c3' : '#dcfce7';
    };
    if (ticketData && ticketData.status) {
      applyStatusButton(ticketData.status);
    }
    toggleStatusBtn.addEventListener('click', async () => {
      if (!ticketData || !ticketData.id) return;
      const currentUi = (ticketData.status || '').toLowerCase();
      const next = currentUi === 'answered' ? 'open' : 'answered';
      applyStatusButton(next);
      try {
        const form = new FormData();
        form.append('id', String(ticketData.id));
        form.append('status', next);
        const res = await fetch(`/${currentRole}/forumToggleStatus`, { method: 'POST', credentials: 'include', body: form });
        if (!res.ok) throw new Error('toggle_failed');
        const payload = await res.json();
        if (!payload.ok) throw new Error('toggle_failed');
        ticketData.status = next === 'answered' ? 'Answered' : 'Open';
        const statusEl = document.getElementById('ticketStatus');
        if (statusEl) {
          statusEl.className = statusClass(ticketData.status);
          statusEl.textContent = ticketData.status;
        }
      } catch (e) {
        const revert = currentUi;
        applyStatusButton(revert);
        alert('Failed to update status.');
      }
    });
  }
}

const CACHE_TTL_MS = 5 * 60 * 1000;
function cacheKeyFor(id) { return `forum_post_${currentRole}_${id}`; }
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

function getPostIdFromUrl() {
  const u = new URL(window.location.href);
  const id = u.searchParams.get("id");
  if (id) return parseInt(id, 10);
  const code = u.searchParams.get("code");
  if (code) { const m = code.match(/\d+/); if (m) return parseInt(m[0], 10); }
  return null;
}

async function fetchPost(id) {
  const res = await fetch(`/${currentRole}/forumPostData?id=${encodeURIComponent(id)}`, { credentials: "include" });
  if (!res.ok) throw new Error("Failed to fetch post");
  return res.json();
}

(async function init() {
  const id = getPostIdFromUrl();
  if (id) {
    ticketData = loadFromCache(id);
    if (!ticketData) {
      try { ticketData = await fetchPost(id); saveToCache(id, ticketData); }
      catch (e) { console.error(e); }
    } else {
      fetchPost(id).then((fresh) => { saveToCache(id, fresh); }).catch(() => {});
    }
  }
  if (!ticketData) {
    ticketData = { id: 0, code: "", title: "Post", status: "Open", createdOn: "", attachments: [], description: "", category: "", assigned: "" };
  }
  renderHeader();
  renderDescription();
  renderAttachments();
  
  // Fetch comments
  if (ticketData.id) {
      loadComments();
  }

  renderMessages();
  renderInfo();
  renderTimeline();
  applyPermissions();
  wireActions();
})();
// end

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
      if (!ticketData || !ticketData.id) throw new Error("missing_id");
      const res = await fetch(`/${currentRole}/forumDelete`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(ticketData.id)}`,
        credentials: "include",
      });
      if (!res.ok) throw new Error("delete_failed");
      // Clear cached post and bust list cache once
      try { localStorage.removeItem(cacheKeyFor(ticketData.id)); } catch (e) {}
      try { localStorage.setItem("forum_cache_bust", String(Date.now())); } catch (e) {}
      window.location.href = `/${currentRole}/forum`;
    } catch (e) {
      console.error(e);
      alert("Failed to delete the post.");
    } finally { close(); }
  };

  cancelBtn && cancelBtn.addEventListener("click", onCancel);
  confirmBtn && confirmBtn.addEventListener("click", onConfirm);
  backdropBtn && backdropBtn.addEventListener("click", onCancel);
}
