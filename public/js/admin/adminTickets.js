window.adminTicketsData = [];

(function () {
  const categories = [
    "All categories",
    "Academic",
    "Administrative",
    "Financial",
    "IT Support",
  ];

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
  const prioritySelect = document.getElementById("priorityFilter");
  const searchInput = document.getElementById("ticketSearch");
  const paginationHolder = document.querySelector(
    ".ticketsPagination .ticketsPageHolder"
  );

  populateSelect(categorySelect, categories);
  populateSelect(statusSelect, statuses);
  populateSelect(prioritySelect, priorities);

  // Read initial state from URL before building custom selects
  const urlParams = new URLSearchParams(window.location.search);
  const initial = {
    search: (urlParams.get("search") || "").trim(),
    category: urlParams.get("category") || "",
    status: urlParams.get("status") || "",
    priority: urlParams.get("priority") || "",
    page: (() => {
      const p = parseInt(urlParams.get("page"), 10);
      return Number.isFinite(p) && p > 0 ? p : 1;
    })(),
  };

  if (searchInput) searchInput.value = initial.search;
  // Safe set for selects only if the value exists in options
  function setIfOptionExists(select, value) {
    if (!select) return;
    if (!value) return; // default already selected
    const has = Array.from(select.options).some((o) => o.value === value);
    if (has) select.value = value;
  }
  setIfOptionExists(categorySelect, initial.category);
  setIfOptionExists(statusSelect, initial.status);
  setIfOptionExists(prioritySelect, initial.priority);

  // Build custom selects for better option UI consistency
  function buildCustomSelect(nativeSelect) {
    if (!nativeSelect) return;

    // Create wrapper and button
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

    // Build list options
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

    // Toggle open/close
    button.addEventListener("click", (e) => {
      e.stopPropagation();
      const isOpen = wrap.classList.contains("isOpen");
      document
        .querySelectorAll(".selectWrap.isOpen")
        .forEach((w) => w.classList.remove("isOpen"));
      if (!isOpen) wrap.classList.add("isOpen");
    });

    // Close on outside click / Escape
    document.addEventListener("click", () => wrap.classList.remove("isOpen"));
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") wrap.classList.remove("isOpen");
    });

    // Keep native select in DOM for accessibility/forms
    nativeSelect.classList.add("selectHidden");
    nativeSelect.parentNode.insertBefore(wrap, nativeSelect);
    wrap.appendChild(button);
    wrap.appendChild(list);

    // Sync if native select changes externally
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

  // Initialize custom selects
  [categorySelect, statusSelect, prioritySelect].forEach(buildCustomSelect);

  // Pagination & state
  let page = 1;
  // Apply initial page from URL
  page = initial.page;
  const perPage = 10;
  let meta = { total: 0, totalPages: 1 };

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
    // Only include non-empty filters to keep URL clean
    if (p.search) params.set("search", p.search);
    if (p.category) params.set("category", p.category);
    if (p.status) params.set("status", p.status);
    if (p.priority) params.set("priority", p.priority);
    params.set("page", String(page));
    const qs = params.toString();
    const newUrl = `${window.location.pathname}${qs ? `?${qs}` : ""}`;
    window.history.replaceState(null, "", newUrl);
  }

  // --- Tickets rendering using map() ---
  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function formatDate(iso) {
    // Expect YYYY-MM-DD; fallback to raw
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

        return `
            <div class="ticket">
                <div class="ticketRow1">
                    <div class="ticketName">
                        <h2>${esc(t.title)}</h2>
                        <div class="ticketInfo">
                            <p>${esc(t.code)}</p>
                            <p>${formatDate(t.createdAt)}</p>
                            <p>${esc(t.student?.name || "")}</p>
                        </div>
                    </div>
                    <div class="status ${status.cls}">${esc(status.label)}</div>
                </div>
                <div class="ticketRow2">
                    <div class="ticketDetails">
                        <div class="ticketDetail">
                            <h2>Category:</h2>
                            <div class="ticketDetailHolder">${esc(
                              t.category
                            )}</div>
                        </div>
                    </div>
                    <div class="ticketData">
                        <div class="ticketDetail">
                            <h2>Meeting:</h2>
                            <div class="ticketDataHolder">${esc(
                              meetingLabel
                            )}</div>
                        </div>
                        <div class="ticketDetail">
                            <h2>Priority:</h2>
                            <div class="ticketDataHolder" style="${
                              pr.style
                            }">${esc(pr.label)}</div>
                        </div>
                    </div>
                </div>
            </div>`;
      })
      .join("");

    container.innerHTML = html;
  }

  function renderPagination() {
    if (!paginationHolder) return;
    paginationHolder.innerHTML = "";
    const totalPages = Math.max(1, parseInt(meta.totalPages || 1, 10));

    // Helper to create a page button; can render text or raw HTML content (e.g., SVG)
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

    // Previous
    if (page > 1) {
      const leftSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="m121.38-480 289.31 289.31q10.92 10.92 11.12 27.07.19 16.16-10.73 27.08-10.93 10.92-27.08 10.92t-27.08-10.92L59.08-434.77q-9.85-9.85-14.08-21.31-4.23-11.46-4.23-23.92T45-503.92q4.23-11.46 14.08-21.31l297.84-297.85q10.93-10.92 26.89-11.11 15.96-.19 26.88 10.73 10.92 10.92 10.92 27.08 0 16.15-10.92 27.07L121.38-480Z"/></svg>';
      paginationHolder.appendChild(
        makeBtn(page - 1, false, leftSvg, true, "Previous page")
      );
    }

    // Windowed pages (max 5)
    const maxButtons = 5;
    let start = Math.max(1, page - Math.floor(maxButtons / 2));
    let end = Math.min(totalPages, start + maxButtons - 1);
    start = Math.max(1, Math.min(start, Math.max(1, end - maxButtons + 1)));
    for (let p = start; p <= end; p++) {
      paginationHolder.appendChild(makeBtn(p, p === page));
    }

    // Next
    if (page < totalPages) {
      const rightSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="M550.23-480 260.92-769.31q-10.92-10.92-11.11-26.88-.19-15.96 10.73-26.89Q271.46-834 287.42-834q15.96 0 26.89 10.92l298.23 297.85q9.84 9.85 14.07 21.31 4.24 11.46 4.24 23.92t-4.24 23.92q-4.23 11.46-14.07 21.31L314.69-136.92q-10.92 10.92-27.07 11.11-16.16.19-27.08-10.73-10.92-10.92-10.92-26.88 0-15.96 10.92-26.89L550.23-480Z"/></svg>';
      paginationHolder.appendChild(
        makeBtn(page + 1, false, rightSvg, true, "Next page")
      );
    }
  }

  // Fetch tickets from backend API and render
  async function loadTickets(nextPage) {
    if (typeof nextPage === "number") page = nextPage;
    // Keep URL in sync with current UI state and page
    syncUrlState();
    const container = document.querySelector(".tickets");
    if (container) {
      container.innerHTML =
        '<div class="ticketsLoading">Loading tickets…</div>';
    }
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
      const res = await fetch(`/admin/ticketsData?${qs.toString()}`, {
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to load tickets");
      const payload = await res.json();
      const data = Array.isArray(payload?.data) ? payload.data : [];
      meta = payload?.meta || { total: data.length, totalPages: 1 };
      window.adminTicketsData = data;
      renderTickets(window.adminTicketsData);
      renderPagination();
    } catch (err) {
      if (container) {
        container.innerHTML =
          '<div class="ticketsError">Unable to load tickets. Please try again.</div>';
      }
      // eslint-disable-next-line no-console
      console.error("Tickets load error:", err);
    }
  }

  // Wire up filters/search
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
