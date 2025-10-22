// Copied from studentTickets.js for student forum view; keeping behavior identical for now.
window.studentTicketsData = [];

(function () {
  const CACHE_TTL_MS = 5 * 60 * 1000; // 5 minutes

  function getCache(key) {
    try {
      const raw = localStorage.getItem(key);
      if (!raw) return null;
      const obj = JSON.parse(raw);
      if (!obj || !obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
      return obj.data || null;
    } catch {
      return null;
    }
  }

  function setCache(key, data) {
    try {
      localStorage.setItem(key, JSON.stringify({ _ts: Date.now(), data }));
    } catch {}
  }

  const categories = [
    "All topics",
    "General",
    "IT Support",
    "Finance",
    "Examinations",
    "Counselling",
    "Other",
  ];

  const statuses = [
    "All statuses",
    "Answered",
    "Open",
  ];

  const sortOptions = ["Votes", "Comments", "Latest", "Oldest"];

  function toValue(label, isFirst) {
    if (isFirst) return "";
    return label.toLowerCase().replace(/\s+/g, "-");
  }

  function populateSelect(select, list) {
    if (!select) return;
    select.innerHTML = "";
    list.forEach((label, idx) => {
      const opt = document.createElement("option");
      opt.value = toValue(label, idx === 0);
      opt.textContent = label;
      if (idx === 0) opt.selected = true;
      select.appendChild(opt);
    });
  }

  const categorySelect = document.getElementById("categoryFilter");
  const statusSelect = document.getElementById("statusFilter");
  const sortSelect = document.getElementById("sortFilter");
  const searchInput = document.getElementById("ticketSearch");
  const paginationHolder = document.querySelector(
    ".ticketsPagination .ticketsPageHolder"
  );

  populateSelect(categorySelect, categories);
  populateSelect(statusSelect, statuses);
  // Populate Sort by (custom so first doesn't become empty value)
  if (sortSelect) {
    sortSelect.innerHTML = "";
    sortOptions.forEach((label, idx) => {
      const opt = document.createElement("option");
      opt.value = label.toLowerCase();
      opt.textContent = label;
      if (idx === 0) opt.selected = true;
      sortSelect.appendChild(opt);
    });
  }

  const urlParams = new URLSearchParams(window.location.search);
  const initial = {
    search: (urlParams.get("search") || "").trim(),
    category: urlParams.get("category") || "",
    status: urlParams.get("status") || "",
  sort: urlParams.get("sort") || (sortSelect?.value || "votes"),
    page: (() => {
      const p = parseInt(urlParams.get("page"), 10);
      return Number.isFinite(p) && p > 0 ? p : 1;
    })(),
  };

  if (searchInput) searchInput.value = initial.search;
  function setIfOptionExists(select, value) {
    if (!select) return;
    if (!value) return;
    const has = Array.from(select.options).some((o) => o.value === value);
    if (has) select.value = value;
  }
  setIfOptionExists(categorySelect, initial.category);
  setIfOptionExists(statusSelect, initial.status);
  setIfOptionExists(sortSelect, initial.sort);

  function buildCustomSelect(nativeSelect) {
    if (!nativeSelect) return;
    const wrap = document.createElement("div");
    wrap.className = "selectWrap";

    const button = document.createElement("button");
    button.type = "button";
    button.className = "selectButton";
    const labelSpan = document.createElement("span");
    labelSpan.className = "selectedLabel";
    labelSpan.textContent =
      nativeSelect.options[nativeSelect.selectedIndex]?.text || "";
    const chevron = document.createElement("span");
    chevron.className = "selectChevron";
    chevron.innerHTML =
      "<svg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><polyline points='6 9 12 15 18 9' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>";
    button.appendChild(labelSpan);
    button.appendChild(chevron);

    const list = document.createElement("ul");
    list.className = "selectList";
    list.setAttribute("role", "listbox");

    Array.from(nativeSelect.options).forEach((opt) => {
      const li = document.createElement("li");
      li.className = "selectOption" + (opt.selected ? " isSelected" : "");
      li.textContent = opt.textContent;
      li.dataset.value = opt.value;
      li.setAttribute("role", "option");
      if (opt.disabled) {
        li.classList.add("isDisabled");
      }
      li.addEventListener("click", () => {
        if (opt.disabled) return;
        nativeSelect.value = opt.value;
        nativeSelect.dispatchEvent(new Event("change", { bubbles: true }));
        labelSpan.textContent = opt.textContent;
        list
          .querySelectorAll(".selectOption")
          .forEach((el) => el.classList.remove("isSelected"));
        li.classList.add("isSelected");
        wrap.classList.remove("isOpen");
      });
      list.appendChild(li);
    });

    button.addEventListener("click", (e) => {
      e.stopPropagation();
      const isOpen = wrap.classList.contains("isOpen");
      document
        .querySelectorAll(".selectWrap.isOpen")
        .forEach((w) => w.classList.remove("isOpen"));
      if (!isOpen) wrap.classList.add("isOpen");
    });
    document.addEventListener("click", () => wrap.classList.remove("isOpen"));
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") wrap.classList.remove("isOpen");
    });

    nativeSelect.classList.add("selectHidden");
    nativeSelect.parentNode.insertBefore(wrap, nativeSelect);
    wrap.appendChild(button);
    wrap.appendChild(list);

    nativeSelect.addEventListener("change", () => {
      const o = nativeSelect.options[nativeSelect.selectedIndex];
      labelSpan.textContent = o?.text || "";
      const value = nativeSelect.value;
      list.querySelectorAll(".selectOption").forEach((el) => {
        if (el.dataset.value === value) el.classList.add("isSelected");
        else el.classList.remove("isSelected");
      });
    });
  }

  [categorySelect, statusSelect, sortSelect].forEach(buildCustomSelect);

  let page = initial.page;
  const perPage = 10;
  let meta = { total: 0, totalPages: 1 };
  let listenersBound = false;

  function debounce(fn, wait) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null, args), wait);
    };
  }

  function valueOrEmpty(v) {
    return v == null ? "" : String(v);
  }

  function buildQueryParams() {
    const typeEl = document.querySelector('.filterGroup input[name="type"]:checked');
    const typeVal = typeEl ? typeEl.value : '';
    return {
      search: valueOrEmpty(searchInput?.value || "").trim(),
      category: valueOrEmpty(categorySelect?.value || ""),
      status: valueOrEmpty(statusSelect?.value || ""),
      sort: valueOrEmpty(sortSelect?.value || ""),
      type: valueOrEmpty(typeVal || ""),
    };
  }

  function syncUrlState() {
    const p = buildQueryParams();
    const params = new URLSearchParams();
    if (p.search) params.set("search", p.search);
    if (p.category) params.set("category", p.category);
    if (p.status) params.set("status", p.status);
  if (p.sort) params.set("sort", p.sort);
    params.set("page", String(page));
    const qs = params.toString();
    const newUrl = `${window.location.pathname}${qs ? `?${qs}` : ""}`;
    window.history.replaceState(null, "", newUrl);
  }

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function formatDate(iso) {
    try {
      const d = new Date(iso);
      if (isNaN(d.getTime())) return esc(iso || "");
      return d.toLocaleDateString(undefined, {
        month: "short",
        day: "2-digit",
        year: "numeric",
      });
    } catch {
      return esc(iso || "");
    }
  }

  function getStatusMeta(status) {
    const s = (status || "").toLowerCase();
    if (s === "open") return { cls: "underReview", label: "Open" };
    if (s === "answered") return { cls: "resolved", label: "Answered" };
    return { cls: "", label: esc(status || "") };
  }

  // Meeting/priority removed for forum; votes/comments used instead

  function openTicket(el) {
    const id = el.getAttribute("data-id");
    const code = el.getAttribute("data-code");
      if (id) {
        window.location.assign(`/staff/staffForumFull?id=${encodeURIComponent(id)}`);
      } else if (code) {
        window.location.assign(`/staff/staffForumFull?code=${encodeURIComponent(code)}`);
    }
  }

  function renderTickets(data) {
    const container = document.querySelector(".tickets");
    if (!container) return;
    const html = (data || [])
      .map((t) => {
  const status = getStatusMeta((t && t.status) || "");
  const vis = t && typeof t.is_Public !== 'undefined' ? (t.is_Public ? 'public' : 'private') : 'public';
  const votesUp = Number.isFinite(t?.votesUp) ? t.votesUp : 0;
  const votesDown = Number.isFinite(t?.votesDown) ? t.votesDown : 0;
  const comments = Number.isFinite(t?.comments) ? t.comments : 0;

        return `
      <div class="ticket" tabindex="0" role="link" aria-label="Open ticket ${esc(
        t.title
      )}" data-id="${esc(t.id)}" data-code="${esc(t.code)}">
                <div class="ticketRow1">
                    <div class="ticketName">
                        <h2>${esc(t.title)}</h2>
                        <div class="ticketInfo">
                            <p>${esc(t.code)}</p>
                            <p>${formatDate(t.createdAt)}</p>
                            <p>${esc(t.student?.name || "")}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                      <div class="status ${status.cls}">${esc(status.label)}</div>
                      <div class="status ${vis === 'public' ? 'inProgress' : 'rejected'}" title="${vis === 'public' ? 'Public' : 'Private'}">${vis === 'public' ? 'Public' : 'Private'}</div>
                    </div>
                </div>
                <div class="ticketRow2">
                    <div class="ticketDetails">
                        <div class="ticketDetail">
                            <h2>Topic:</h2>
                            <div class="ticketDetailHolder">${esc(t.topic)}</div>
                        </div>
                    </div>
                    <div class="ticketData">
            <div class="ticketDetail">
              <h2>Votes:</h2>
              <div class="ticketDataHolder votesHolder" data-id="${esc(t.id)}">
                <button type="button" class="voteBtn up" aria-label="Upvote post ${esc(t.title)}">▲</button>
                <span class="voteUpCount">${esc(votesUp)}</span>
                <span style="display:inline-block; width:10px;"></span>
                <button type="button" class="voteBtn down" aria-label="Downvote post ${esc(t.title)}">▼</button>
                <span class="voteDownCount">${esc(votesDown)}</span>
              </div>
            </div>
                        <div class="ticketDetail">
                            <h2>Comments:</h2>
                            <div class="ticketDataHolder">${esc(comments)}</div>
                        </div>
                    </div>
                </div>
            </div>`;
      })
      .join("");

    container.innerHTML = html;

    if (!listenersBound) {
      container.addEventListener("click", (e) => {
        const voteBtn = e.target.closest('.voteBtn');
        if (voteBtn) {
          e.stopPropagation();
          const holder = voteBtn.closest('.votesHolder');
          if (!holder) return;
          const up = holder.querySelector('.voteUpCount');
          const down = holder.querySelector('.voteDownCount');
          if (voteBtn.classList.contains('up') && up) {
            up.textContent = String(parseInt(up.textContent || '0', 10) + 1);
          } else if (voteBtn.classList.contains('down') && down) {
            down.textContent = String(parseInt(down.textContent || '0', 10) + 1);
          }
          return;
        }
        const card = e.target.closest(".ticket");
        if (card && container.contains(card)) openTicket(card);
      });

      container.addEventListener("keydown", (e) => {
        const card = e.target.closest(".ticket");
        if (!card) return;
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          openTicket(card);
        }
      });

      listenersBound = true;
    }
  }

  function renderPagination() {
    if (!paginationHolder) return;
    paginationHolder.innerHTML = "";
    const totalPages = Math.max(1, parseInt(meta.totalPages || 1, 10));

    const makeBtn = (
      num,
      active = false,
      label,
      isHtml = false,
      ariaLabel = null
    ) => {
      const d = document.createElement("div");
      d.className = "ticketsPageNum" + (active ? " active" : "");
      if (isHtml) {
        d.innerHTML = label;
      } else {
        d.innerHTML = `<h2>${esc(label || String(num))}</h2>`;
      }
      if (ariaLabel) d.setAttribute("aria-label", ariaLabel);
      d.addEventListener("click", () => {
        if (num >= 1 && num <= totalPages && num !== page) {
          page = num;
          loadTickets(page);
        }
      });
      return d;
    };

    if (page > 1) {
      const leftSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="m121.38-480 289.31 289.31q10.92 10.92 11.12 27.07.19 16.16-10.73 27.08-10.93 10.92-27.08 10.92t-27.08-10.92L59.08-434.77q-9.85-9.85-14.08-21.31-4.23-11.46-4.23-23.92T45-503.92q4.23-11.46 14.08-21.31l297.84-297.85q10.93-10.92 26.89-11.11 15.96-.19 26.88 10.73 10.92 10.92 10.92 27.08 0 16.15-10.92 27.07L121.38-480Z"/></svg>';
      paginationHolder.appendChild(
        makeBtn(page - 1, false, leftSvg, true, "Previous page")
      );
    }

    const maxButtons = 5;
    let start = Math.max(1, page - Math.floor(maxButtons / 2));
    let end = Math.min(totalPages, start + maxButtons - 1);
    start = Math.max(1, Math.min(start, Math.max(1, end - maxButtons + 1)));
    for (let p = start; p <= end; p++) {
      paginationHolder.appendChild(makeBtn(p, p === page));
    }

    if (page < totalPages) {
      const rightSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="M550.23-480 260.92-769.31q-10.92-10.92-11.11-26.88-.19-15.96 10.73-26.89Q271.46-834 287.42-834q15.96 0 26.89 10.92l298.23 297.85q9.84 9.85 14.07 21.31 4.24 11.46 4.24 23.92t-4.24 23.92q-4.23 11.46-14.07 21.31L314.69-136.92q-10.92 10.92-27.07 11.11-16.16.19-27.08-10.73-10.92-10.92-10.92-26.88 0-15.96 10.92-26.89L550.23-480Z"/></svg>';
      paginationHolder.appendChild(
        makeBtn(page + 1, false, rightSvg, true, "Next page")
      );
    }
  }

  async function loadTickets(nextPage) {
    if (typeof nextPage === "number") page = nextPage;
    syncUrlState();
    const container = document.querySelector(".tickets");
    if (container) {
      container.innerHTML =
        '<div class="ticketsLoading">Loading posts…</div>';
    }
    try {
      const p = buildQueryParams();
      const qs = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
        search: p.search,
        category: p.category,
        status: p.status,
        sort: p.sort,
        type: p.type,
      });

  const CACHE_KEY = `student_forum_${qs.toString()}`;

      let forceBypass = false;
      try {
        if (localStorage.getItem("student_tickets_bust")) {
          forceBypass = true;
          localStorage.removeItem("student_tickets_bust");
        }
      } catch {}

      const cached = forceBypass ? null : getCache(CACHE_KEY);
      if (cached) {
        const data = Array.isArray(cached?.data) ? cached.data : [];
        meta = cached?.meta || { total: data.length, totalPages: 1 };
        window.studentTicketsData = data;
        renderTickets(window.studentTicketsData);
        renderPagination();

  fetch(`/student/forumData?${qs.toString()}`, { credentials: "include" })
          .then((res) => (res.ok ? res.json() : Promise.reject(new Error("Bad response"))))
          .then((fresh) => {
            setCache(CACHE_KEY, fresh);
            const newData = Array.isArray(fresh?.data) ? fresh.data : [];
            meta = fresh?.meta || { total: newData.length, totalPages: 1 };
            window.studentTicketsData = newData;
            renderTickets(window.studentTicketsData);
            renderPagination();
          })
          .catch((e) => {
            console.warn("Tickets background refresh failed", e);
          });
        return;
      }

      const res = await fetch(`/staff/staffForumData?${qs.toString()}`, { credentials: "include" });
      if (!res.ok) throw new Error("Failed to load tickets");
      const payload = await res.json();
      setCache(CACHE_KEY, payload);
      const data = Array.isArray(payload?.data) ? payload.data : [];
      meta = payload?.meta || { total: data.length, totalPages: 1 };
      window.studentTicketsData = data;
      renderTickets(window.studentTicketsData);
      renderPagination();
    } catch (err) {
      if (container) {
        container.innerHTML =
          '<div class="ticketsError">Unable to load posts. Please try again.</div>';
      }
      console.error("Tickets load error:", err);
    }
  }

  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(() => {
        page = 1;
        loadTickets(page);
      }, 300)
    );
  }
  [categorySelect, statusSelect, sortSelect].forEach((sel) => {
    if (!sel) return;
    sel.addEventListener("change", () => {
      page = 1;
      loadTickets(page);
    });
  });

  // Listen to radio changes for type filter
  document.querySelectorAll('.filterGroup input[name="type"]').forEach((el) => {
    el.addEventListener('change', () => {
      page = 1;
      loadTickets(page);
    });
  });

  loadTickets(page);
})();
