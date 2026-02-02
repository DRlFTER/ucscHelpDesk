(function () {
  const cfg = window.TICKETS_CONFIG || {};
  const ROLE = (cfg.role || "guest").toLowerCase();
  const API_BASE = cfg.apiBase || "/admin/ticketsData";
  const TICKET_URL_BASE = cfg.ticketUrlBase || "/admin/ticket";
  const SHOW_LINKS = !!cfg.showLinks;

  // Caching config for tickets list
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

  // Categories per role
  const groupedCategories = [
    { label: "All categories", value: "" },
    { label: "IT & Access", value: "it-access" },
    { label: "Facilities & Equipment", value: "facilities-equipment" },
    { label: "Academic Services", value: "academic-services" },
    { label: "Administrative & Other", value: "administrative-other" },
  ];

  const counselorCategories = [
    { label: "All categories", value: "" },
    { label: "Counselling", value: "counselling" },
  ];

  const categories =
    ROLE === "counselor" ? counselorCategories : groupedCategories;

  const statuses = [
    "All statuses",
    "Open",
    "In Progress",
    "Resolved",
    "Rejected",
  ];
  const priorities = ["All priorities", "Low", "Medium", "High", "Urgent"];

  function toValue(label, isFirst) {
    if (isFirst) return "";
    return label
      .toLowerCase()
      .replace(/&/g, "and")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  function populateSelect(select, list) {
    if (!select) return;
    select.innerHTML = "";
    list.forEach((item, idx) => {
      const isObj = typeof item === "object" && item !== null;
      const label = isObj ? item.label : item;
      const val = isObj
        ? idx === 0
          ? ""
          : item.value
        : toValue(label, idx === 0);
      const opt = document.createElement("option");
      opt.value = val;
      opt.textContent = label;
      if (idx === 0) opt.selected = true;
      select.appendChild(opt);
    });
  }

  const categorySelect = document.getElementById("categoryFilter");
  const statusSelect = document.getElementById("statusFilter");
  const prioritySelect = document.getElementById("priorityFilter");
  const searchInput = document.getElementById("ticketSearch");
  const paginationHolder = document.querySelector(
    ".ticketsPagination .ticketsPageHolder"
  );

  populateSelect(categorySelect, categories);
  populateSelect(statusSelect, statuses);
  populateSelect(prioritySelect, priorities);

  const urlParams = new URLSearchParams(window.location.search);

  function normalizeLegacyCategory(value) {
    const v = (value || "").toLowerCase();
    if (ROLE === "counselor") {
      return v === "counselling" ? "counselling" : "";
    }
    switch (v) {
      case "administrative":
      case "financial":
        return "administrative-other";
      case "it":
      case "it-support":
      case "tech":
        return "it-access";
      case "academic":
        return "academic-services";
      case "facilities":
      case "facility":
        return "facilities-equipment";
      default:
        return v;
    }
  }

  const initial = {
    search: (urlParams.get("search") || "").trim(),
    category: normalizeLegacyCategory(urlParams.get("category") || ""),
    status: urlParams.get("status") || "",
    priority: urlParams.get("priority") || "",
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
  setIfOptionExists(prioritySelect, initial.priority);

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

  [categorySelect, statusSelect, prioritySelect].forEach(buildCustomSelect);

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
    return {
      search: valueOrEmpty(searchInput?.value || "").trim(),
      category: valueOrEmpty(categorySelect?.value || ""),
      status: valueOrEmpty(statusSelect?.value || ""),
      priority: valueOrEmpty(prioritySelect?.value || ""),
    };
  }

  function syncUrlState() {
    const p = buildQueryParams();
    const params = new URLSearchParams();
    if (p.search) params.set("search", p.search);
    if (p.category) params.set("category", p.category);
    if (p.status) params.set("status", p.status);
    if (p.priority) params.set("priority", p.priority);
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
    const map = {
      open: { cls: "underReview", label: "Under review" },
      "in-progress": { cls: "inProgress", label: "In progress" },
      resolved: { cls: "resolved", label: "Resolved" },
      rejected: { cls: "rejected", label: "Rejected" },
    };
    return map[status] || { cls: "", label: esc(status || "") };
  }

  function getMeetingLabel(meeting) {
    const map = {
      none: "None",
      requested: "Requested",
      scheduled: "Scheduled",
    };
    return map[meeting] || esc(meeting || "");
  }

  function getPriorityMeta(priority) {
    const label = (priority || "").toString();
    const p = label.toLowerCase();
    const styles = {
      low: "color:#155724; background:#D4EDDA;",
      medium: "color:#856404; background:#FFF3CD;",
      high: "color:#B50000; background:#FFD8D8;",
      urgent: "color:#7F0000; background:#FFC9C9;",
    };
    return {
      label: label.charAt(0).toUpperCase() + label.slice(1),
      style: styles[p] || "",
    };
  }

  function renderTickets(data) {
    const container = document.querySelector(".tickets");
    if (!container) return;
    const html = (data || [])
      .map((t) => {
        const status = getStatusMeta((t && t.status) || "");
        const pr = getPriorityMeta((t && t.priority) || "");
        const meetingLabel = getMeetingLabel((t && t.meeting) || "");

        const id = t.id != null ? String(t.id) : "";
        const code = t.code != null ? String(t.code) : "";
        const url = id
          ? `${TICKET_URL_BASE}?id=${encodeURIComponent(id)}`
          : code
          ? `${TICKET_URL_BASE}?code=${encodeURIComponent(code)}`
          : "#";
        const vt = `ticket-${esc(id || code)}`;

        // Public/Private visibility indicator (student only)
        let visibilityHtml = '';
        if (ROLE === 'student') {
          const visibility = (t.visibility || 'private').toLowerCase();
          const visLabel = visibility.charAt(0).toUpperCase() + visibility.slice(1);
          const visClass = visibility === 'public' ? 'visibility-public' : 'visibility-private';
          visibilityHtml = `<div class="status ${visClass}">${esc(visLabel)}</div>`;
        }

        const inner = `
        <div class="ticketRow1">
          <div class="ticketName">
            <h2>${esc(t.title)}</h2>
            <div class="ticketInfo">
              <p>${esc(t.code)}</p>
              <p>${formatDate(t.createdAt)}</p>
              <p>${esc(t.student?.name || "")}</p>
            </div>
          </div>
          <div style="display:flex; gap:10px; align-items:center;">
            ${visibilityHtml}
            <div class="status ${status.cls}">${esc(status.label)}</div>
          </div>
        </div>
        <div class="ticketRow2">
          <div class="ticketDetails">
            <div class="ticketDetail">
              <h2>Category:</h2>
              <div class="ticketDetailHolder">${esc(t.category)}</div>
            </div>
          </div>
          <div class="ticketData">
            <div class="ticketDetail">
              <h2>Meeting:</h2>
              <div class="ticketDataHolder">${esc(meetingLabel)}</div>
            </div>
            <div class="ticketDetail">
              <h2>Priority:</h2>
              <div class="ticketDataHolder" style="${pr.style}">${esc(
          pr.label
        )}</div>
            </div>
          </div>
        </div>`;

        if (SHOW_LINKS && url && url !== "#") {
          return `<a class="ticket" href="${url}" aria-label="Open ticket ${esc(
            t.title
          )}" style="view-transition-name: ${vt}; text-decoration: none; color: inherit;">${inner}</a>`;
        }
        return `<div class="ticket" style="view-transition-name: ${vt};">${inner}</div>`;
      })
      .join("");

    container.innerHTML = html;

    if (SHOW_LINKS && !listenersBound) {
      container.addEventListener("click", (e) => {
        const a = e.target?.closest("a.ticket");
        if (!a || !container.contains(a)) return;
        if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey)
          return;
        const href = a.getAttribute("href");
        if (!href || href === "#") return;
        e.preventDefault();
        if (document.startViewTransition) {
          document.startViewTransition(() => {
            window.location.href = href;
          });
        } else {
          window.location.href = href;
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
    if (container)
      container.innerHTML =
        '<div class="ticketsLoading">Loading tickets…</div>';
    try {
      const p = buildQueryParams();
      const qs = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
        search: p.search,
        category: p.category,
        status: p.status,
        priority: p.priority,
      });

      const CACHE_KEY = `${ROLE}_tickets_${qs.toString()}`;

      let forceBypass = false;
      try {
        if (localStorage.getItem(`${ROLE}_tickets_bust`)) {
          forceBypass = true;
          localStorage.removeItem(`${ROLE}_tickets_bust`);
        }
      } catch {}

      const cached = forceBypass ? null : getCache(CACHE_KEY);
      if (cached) {
        const data = Array.isArray(cached?.data) ? cached.data : [];
        meta = cached?.meta || { total: data.length, totalPages: 1 };
        renderTickets(data);
        renderPagination();

        fetch(`${API_BASE}?${qs.toString()}`, { credentials: "include" })
          .then((res) =>
            res.ok ? res.json() : Promise.reject(new Error("Bad response"))
          )
          .then((fresh) => {
            setCache(CACHE_KEY, fresh);
            const newData = Array.isArray(fresh?.data) ? fresh.data : [];
            meta = fresh?.meta || { total: newData.length, totalPages: 1 };
            renderTickets(newData);
            renderPagination();
          })
          .catch(() => {});
        return;
      }

      const res = await fetch(`${API_BASE}?${qs.toString()}`, {
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to load tickets");
      const payload = await res.json();
      setCache(CACHE_KEY, payload);
      const data = Array.isArray(payload?.data) ? payload.data : [];
      meta = payload?.meta || { total: data.length, totalPages: 1 };
      renderTickets(data);
      renderPagination();
    } catch (err) {
      if (container)
        container.innerHTML =
          '<div class="ticketsError">Unable to load tickets. Please try again.</div>';
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
  [categorySelect, statusSelect, prioritySelect].forEach((sel) => {
    if (!sel) return;
    sel.addEventListener("change", () => {
      page = 1;
      loadTickets(page);
    });
  });

  loadTickets(page);
})();
