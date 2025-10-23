window.adminUsersData = [];

(function () {
  const CACHE_TTL_MS = 5 * 60 * 1000;

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

  const types = [
    "All types",
    "Admin",
    "Staff",
    "Counselor",
    "Lecturer",
    "Student",
  ];

  function toValue(label, isFirst) {
    if (isFirst) return "";
    return label.toLowerCase();
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

  const typeSelect = document.getElementById("typeFilter");
  const designationSelect = document.getElementById("designationFilter");
  const searchInput = document.getElementById("userSearch");
  const paginationHolder = document.querySelector(
    ".ticketsPagination .ticketsPageHolder"
  );

  populateSelect(typeSelect, types);

  function buildCustomSelect(nativeSelect) {
    if (!nativeSelect) return;
    if (nativeSelect.classList.contains("selectHidden")) return;

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
      if (opt.disabled) li.classList.add("isDisabled");
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

  // Pagination & URL state
  let page = 1;
  const perPage = 10;
  let meta = { total: 0, totalPages: 1, designations: [] };

  // Initialize from URL
  const urlParams = new URLSearchParams(window.location.search);
  const initial = {
    search: (urlParams.get("search") || "").trim(),
    type: urlParams.get("type") || "",
    designation: urlParams.get("designation") || "",
    page: (() => {
      const p = parseInt(urlParams.get("page"), 10);
      return Number.isFinite(p) && p > 0 ? p : 1;
    })(),
  };
  if (searchInput) searchInput.value = initial.search;
  populateSelect(typeSelect, types);
  // Set initial values if present
  function setIfOptionExists(select, value) {
    if (!select) return;
    if (!value) return;
    const has = Array.from(select.options).some((o) => o.value === value);
    if (has) select.value = value;
  }
  setIfOptionExists(typeSelect, initial.type);
  setIfOptionExists(designationSelect, initial.designation);
  page = initial.page;

  // After we fetch, we'll populate designation options from meta
  buildCustomSelect(typeSelect);

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
      type: valueOrEmpty(typeSelect?.value || ""),
      designation: valueOrEmpty(designationSelect?.value || ""),
    };
  }

  function syncUrlState() {
    const p = buildQueryParams();
    const params = new URLSearchParams();
    if (p.search) params.set("search", p.search);
    if (p.type) params.set("type", p.type);
    if (p.designation) params.set("designation", p.designation);
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

  function renderUsers(data) {
    const container = document.querySelector(".tickets");
    if (!container) return;
    const html = (data || [])
      .map((u) => {
        const id = u.id != null ? String(u.id) : "";
        const vt = `user-${esc(id)}`;
        const role = (u.role || "").toString();
        const roleCap = role
          ? role.charAt(0).toUpperCase() + role.slice(1)
          : "";
        const designation = u.designation || "—";
        const year = u.year != null ? String(u.year) : "—";
        const phone = u.number || "—";
        const email = u.email || "";
        const href = id ? `/admin/user?id=${encodeURIComponent(id)}` : "#";
        return `
        <a class="ticket" href="${href}" aria-label="Open user ${esc(
          u.name
        )}" style="view-transition-name: ${vt}; text-decoration: none; color: inherit;">
          <div class="ticketRow1">
            <div class="ticketName">
              <h2>${esc(u.name)}</h2>
              <div class="ticketInfo">
                <p>${esc(email)}</p>
                <p>${esc(roleCap)}</p>
                <p>${esc(designation)}</p>
              </div>
            </div>
            <div class="status ${esc(role)}">${esc(roleCap)}</div>
          </div>
          <div class="ticketRow2">
            <div class="ticketDetails">
              <div class="ticketDetail">
                <h2>Phone:</h2>
                <div class="ticketDetailHolder">${esc(phone)}</div>
              </div>
              <div class="ticketDetail">
                <h2>Year:</h2>
                <div class="ticketDetailHolder">${esc(year)}</div>
              </div>
            </div>
            <div class="ticketData">
              <div class="ticketDetail">
                <h2>ID:</h2>
                <div class="ticketDataHolder">${esc(id)}</div>
              </div>
            </div>
          </div>
        </a>`;
      })
      .join("");

    container.innerHTML = html;
    container.addEventListener(
      "click",
      (e) => {
        const a = e.target && e.target.closest && e.target.closest("a.ticket");
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
      },
      { once: true }
    );
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
      aria = null
    ) => {
      const d = document.createElement("div");
      d.className = "ticketsPageNum" + (active ? " active" : "");
      if (isHtml) d.innerHTML = label;
      else d.innerHTML = `<h2>${esc(label || String(num))}</h2>`;
      if (aria) d.setAttribute("aria-label", aria);
      d.addEventListener("click", () => {
        if (num >= 1 && num <= totalPages && num !== page) {
          page = num;
          loadUsers(page);
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

  async function loadUsers(nextPage) {
    if (typeof nextPage === "number") page = nextPage;
    syncUrlState();
    const container = document.querySelector(".tickets");
    if (container)
      container.innerHTML = '<div class="ticketsLoading">Loading users…</div>';

    try {
      const p = buildQueryParams();
      const qs = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
        search: p.search,
        type: p.type,
        designation: p.designation,
      });

      const CACHE_KEY = `admin_users_${qs.toString()}`;
      let forceBypass = false;
      try {
        if (localStorage.getItem("admin_users_bust")) {
          forceBypass = true;
          localStorage.removeItem("admin_users_bust");
        }
      } catch {}

      const cached = forceBypass ? null : getCache(CACHE_KEY);
      if (cached) {
        const data = Array.isArray(cached?.data) ? cached.data : [];
        meta = cached?.meta || {
          total: data.length,
          totalPages: 1,
          designations: [],
        };
        window.adminUsersData = data;
        if (
          designationSelect &&
          designationSelect.options.length <= 1 &&
          Array.isArray(meta.designations)
        ) {
          const opts = ["All designations", ...meta.designations];
          populateSelect(designationSelect, opts);
          buildCustomSelect(designationSelect);
        }
        renderUsers(window.adminUsersData);
        renderPagination();

        fetch(`/admin/usersData?${qs.toString()}`, { credentials: "include" })
          .then((res) =>
            res.ok ? res.json() : Promise.reject(new Error("Bad response"))
          )
          .then((fresh) => {
            setCache(CACHE_KEY, fresh);
            const newData = Array.isArray(fresh?.data) ? fresh.data : [];
            meta = fresh?.meta || {
              total: newData.length,
              totalPages: 1,
              designations: [],
            };
            window.adminUsersData = newData;
            renderUsers(window.adminUsersData);
            renderPagination();
          })
          .catch((e) => console.warn("Users background refresh failed", e));
        return;
      }

      const res = await fetch(`/admin/usersData?${qs.toString()}`, {
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to load users");
      const payload = await res.json();
      setCache(CACHE_KEY, payload);
      const data = Array.isArray(payload?.data) ? payload.data : [];
      meta = payload?.meta || {
        total: data.length,
        totalPages: 1,
        designations: [],
      };
      window.adminUsersData = data;

      if (designationSelect && Array.isArray(meta.designations)) {
        const opts = ["All designations", ...meta.designations];
        populateSelect(designationSelect, opts);
        buildCustomSelect(designationSelect);
      }

      renderUsers(window.adminUsersData);
      renderPagination();
    } catch (err) {
      if (container)
        container.innerHTML =
          '<div class="ticketsError">Unable to load users. Please try again.</div>';
      console.error("Users load error:", err);
    }
  }

  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(() => {
        page = 1;
        loadUsers(page);
      }, 300)
    );
  }
  [typeSelect, designationSelect].forEach((sel) => {
    if (!sel) return;
    sel.addEventListener("change", () => {
      page = 1;
      loadUsers(page);
    });
  });

  // initial load
  loadUsers(page);
})();
