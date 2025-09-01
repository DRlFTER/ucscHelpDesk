// Admin tickets data now fetched from backend. We'll attach it to window for reuse.
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

  populateSelect(categorySelect, categories);
  populateSelect(statusSelect, statuses);
  populateSelect(prioritySelect, priorities);

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
        wrap.classList.remove("open");
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
                        </div>
                    </div>
                    <div class="status ${esc(status.cls)}">${esc(
          status.label
        )}</div>
                </div>
                <div class="ticketRow2">
                    <div class="ticketDetails">
                        <div class="ticketDetail">
                            <h2>Student:</h2>
                            <div class="ticketDetailHolder">${esc(
                              t.student?.name
                            )}</div>
                        </div>
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

  // Fetch tickets from backend API and render
  async function loadTickets() {
    const container = document.querySelector(".tickets");
    if (container) {
      container.innerHTML =
        '<div class="ticketsLoading">Loading tickets…</div>';
    }
    try {
      const res = await fetch("/admin/ticketsData", { credentials: "include" });
      if (!res.ok) throw new Error("Failed to load tickets");
      const payload = await res.json();
      const data = Array.isArray(payload?.data) ? payload.data : [];
      window.adminTicketsData = data;
      renderTickets(window.adminTicketsData);
    } catch (err) {
      if (container) {
        container.innerHTML =
          '<div class="ticketsError">Unable to load tickets. Please try again.</div>';
      }
      // eslint-disable-next-line no-console
      console.error("Tickets load error:", err);
    }
  }

  loadTickets();
})();
