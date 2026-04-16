(function () {
  const daysEl = document.getElementById("calendarDays");
  const monthYearEl = document.getElementById("monthYear");
  const prevBtn = document.getElementById("prevMonthBtn");
  const nextBtn = document.getElementById("nextMonthBtn");
  const addEventBtn = document.getElementById("addEventBtn");
  const eventTitleInput = document.getElementById("eventTitle");

  if (!daysEl || !monthYearEl) return;

  // Left pane: today date labels
  (function setTodayLabels() {
    const now = new Date();
    const numEl = document.getElementById("todayNum");
    const weekdayEl = document.getElementById("todayWeekday");
    if (numEl) numEl.textContent = String(now.getDate());
    if (weekdayEl)
      weekdayEl.textContent = now.toLocaleString(undefined, {
        weekday: "long",
      });
  })();

  let current = new Date();
  current.setDate(1);

  let eventsByDay = {};
  let selectedDate = null;
  let activeModal = null; // Track active modal to prevent duplicates

  const ROLE = (
    window.CALENDAR_CONFIG && window.CALENDAR_CONFIG.role
      ? String(window.CALENDAR_CONFIG.role)
      : "guest"
  ).toLowerCase();

  function fmtDateKey(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
  }

  function isSameDate(a, b) {
    return (
      a.getFullYear() === b.getFullYear() &&
      a.getMonth() === b.getMonth() &&
      a.getDate() === b.getDate()
    );
  }

  // ==================== Smart Text Parser ====================
  const SmartEventParser = {
    // Patterns for date detection
    datePatterns: [
      // "tomorrow", "today"
      { regex: /\b(today)\b/i, handler: () => new Date() },
      {
        regex: /\b(tomorrow)\b/i,
        handler: () => {
          const d = new Date();
          d.setDate(d.getDate() + 1);
          return d;
        },
      },
      // "next monday", "next tuesday", etc.
      {
        regex:
          /\bnext\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/i,
        handler: (match) => {
          const days = [
            "sunday",
            "monday",
            "tuesday",
            "wednesday",
            "thursday",
            "friday",
            "saturday",
          ];
          const targetDay = days.indexOf(match[1].toLowerCase());
          const today = new Date();
          const currentDay = today.getDay();
          let daysUntil = targetDay - currentDay;
          if (daysUntil <= 0) daysUntil += 7;
          daysUntil += 7; // next week
          today.setDate(today.getDate() + daysUntil);
          return today;
        },
      },
      // "monday", "tuesday", etc. (this week or next)
      {
        regex:
          /\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/i,
        handler: (match) => {
          const days = [
            "sunday",
            "monday",
            "tuesday",
            "wednesday",
            "thursday",
            "friday",
            "saturday",
          ];
          const targetDay = days.indexOf(match[1].toLowerCase());
          const today = new Date();
          const currentDay = today.getDay();
          let daysUntil = targetDay - currentDay;
          if (daysUntil <= 0) daysUntil += 7;
          today.setDate(today.getDate() + daysUntil);
          return today;
        },
      },
      // "Jan 15", "January 15", "15 Jan", "15 January"
      {
        regex:
          /\b(jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|june?|july?|aug(?:ust)?|sep(?:t(?:ember)?)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?)\s+(\d{1,2})(?:st|nd|rd|th)?\b/i,
        handler: (match) => {
          const months = {
            jan: 0,
            feb: 1,
            mar: 2,
            apr: 3,
            may: 4,
            jun: 5,
            jul: 6,
            aug: 7,
            sep: 8,
            oct: 9,
            nov: 10,
            dec: 11,
          };
          const monthKey = match[1].toLowerCase().substring(0, 3);
          const day = parseInt(match[2]);
          const d = new Date();
          d.setMonth(months[monthKey], day);
          if (d < new Date()) d.setFullYear(d.getFullYear() + 1);
          return d;
        },
      },
      {
        regex:
          /\b(\d{1,2})(?:st|nd|rd|th)?\s+(jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|june?|july?|aug(?:ust)?|sep(?:t(?:ember)?)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?)\b/i,
        handler: (match) => {
          const months = {
            jan: 0,
            feb: 1,
            mar: 2,
            apr: 3,
            may: 4,
            jun: 5,
            jul: 6,
            aug: 7,
            sep: 8,
            oct: 9,
            nov: 10,
            dec: 11,
          };
          const day = parseInt(match[1]);
          const monthKey = match[2].toLowerCase().substring(0, 3);
          const d = new Date();
          d.setMonth(months[monthKey], day);
          if (d < new Date()) d.setFullYear(d.getFullYear() + 1);
          return d;
        },
      },
      // "1/15", "01/15", "1-15", "01-15" (MM/DD or MM-DD)
      {
        regex: /\b(\d{1,2})[\/\-](\d{1,2})\b/,
        handler: (match) => {
          const month = parseInt(match[1]) - 1;
          const day = parseInt(match[2]);
          const d = new Date();
          d.setMonth(month, day);
          if (d < new Date()) d.setFullYear(d.getFullYear() + 1);
          return d;
        },
      },
    ],

    // Patterns for time detection
    timePatterns: [
      // "at 3pm", "at 3:30pm", "at 15:00"
      {
        regex: /\bat\s+(\d{1,2})(?::(\d{2}))?\s*(am|pm)?\b/i,
        handler: (match) => {
          let hours = parseInt(match[1]);
          const minutes = match[2] ? parseInt(match[2]) : 0;
          const ampm = match[3]?.toLowerCase();
          if (ampm === "pm" && hours < 12) hours += 12;
          if (ampm === "am" && hours === 12) hours = 0;
          return { hours, minutes };
        },
      },
      // "3pm", "3:30pm", "3 pm"
      {
        regex: /\b(\d{1,2})(?::(\d{2}))?\s*(am|pm)\b/i,
        handler: (match) => {
          let hours = parseInt(match[1]);
          const minutes = match[2] ? parseInt(match[2]) : 0;
          const ampm = match[3].toLowerCase();
          if (ampm === "pm" && hours < 12) hours += 12;
          if (ampm === "am" && hours === 12) hours = 0;
          return { hours, minutes };
        },
      },
      // "15:00", "9:30" (24-hour or ambiguous)
      {
        regex: /\b(\d{1,2}):(\d{2})\b/,
        handler: (match) => {
          const hours = parseInt(match[1]);
          const minutes = parseInt(match[2]);
          if (hours < 24 && minutes < 60) return { hours, minutes };
          return null;
        },
      },
    ],

    // Duration patterns for end time
    durationPatterns: [
      // "for 2 hours", "for 30 minutes", "for 1.5 hours"
      {
        regex: /\bfor\s+(\d+(?:\.\d+)?)\s*(hours?|hrs?|minutes?|mins?)\b/i,
        handler: (match) => {
          const value = parseFloat(match[1]);
          const unit = match[2].toLowerCase();
          if (unit.startsWith("h")) return { minutes: Math.round(value * 60) };
          return { minutes: Math.round(value) };
        },
      },
    ],

    parse(text) {
      const result = {
        title: text,
        date: null,
        startTime: null,
        endTime: null,
        highlights: [],
      };

      let cleanTitle = text;

      // Find date
      for (const pattern of this.datePatterns) {
        const match = text.match(pattern.regex);
        if (match) {
          result.date = pattern.handler(match);
          result.highlights.push({
            start: match.index,
            end: match.index + match[0].length,
            type: "date",
          });
          cleanTitle = cleanTitle.replace(match[0], "").trim();
          break;
        }
      }

      // Find time
      for (const pattern of this.timePatterns) {
        const match = text.match(pattern.regex);
        if (match) {
          const time = pattern.handler(match);
          if (time) {
            result.startTime = `${String(time.hours).padStart(2, "0")}:${String(time.minutes).padStart(2, "0")}`;
            result.highlights.push({
              start: match.index,
              end: match.index + match[0].length,
              type: "time",
            });
            cleanTitle = cleanTitle.replace(match[0], "").trim();

            // Calculate end time (default 1 hour later)
            let endHours = time.hours + 1;
            let endMinutes = time.minutes;
            if (endHours >= 24) endHours = 23;
            result.endTime = `${String(endHours).padStart(2, "0")}:${String(endMinutes).padStart(2, "0")}`;
            break;
          }
        }
      }

      // Find duration (overrides default end time)
      for (const pattern of this.durationPatterns) {
        const match = text.match(pattern.regex);
        if (match && result.startTime) {
          const duration = pattern.handler(match);
          const [startH, startM] = result.startTime.split(":").map(Number);
          let totalMinutes = startH * 60 + startM + duration.minutes;
          let endH = Math.floor(totalMinutes / 60);
          let endM = totalMinutes % 60;
          if (endH >= 24) {
            endH = 23;
            endM = 59;
          }
          result.endTime = `${String(endH).padStart(2, "0")}:${String(endM).padStart(2, "0")}`;
          result.highlights.push({
            start: match.index,
            end: match.index + match[0].length,
            type: "duration",
          });
          cleanTitle = cleanTitle.replace(match[0], "").trim();
          break;
        }
      }

      // Clean up title
      cleanTitle = cleanTitle
        .replace(/\s+/g, " ")
        .replace(/^[\s,\-–]+|[\s,\-–]+$/g, "")
        .trim();
      result.title = cleanTitle || text;

      return result;
    },

    hasRequiredFields(parsed) {
      return parsed.title && parsed.title.length > 0 && parsed.date;
    },
  };

  // ==================== Highlight Input ====================
  function createHighlightedInput() {
    if (!eventTitleInput) return;

    // Create wrapper if not exists
    let wrapper = eventTitleInput.parentElement;
    if (!wrapper.classList.contains("smart-input-wrapper")) {
      wrapper = document.createElement("div");
      wrapper.className = "smart-input-wrapper";
      eventTitleInput.parentNode.insertBefore(wrapper, eventTitleInput);
      wrapper.appendChild(eventTitleInput);

      // Create highlight layer
      const highlightLayer = document.createElement("div");
      highlightLayer.className = "smart-input-highlights";
      wrapper.insertBefore(highlightLayer, eventTitleInput);

      // Add input event for real-time highlighting
      eventTitleInput.addEventListener("input", updateHighlights);
      eventTitleInput.addEventListener("scroll", syncScroll);
    }
  }

  function updateHighlights() {
    const wrapper = eventTitleInput.closest(".smart-input-wrapper");
    if (!wrapper) return;

    const highlightLayer = wrapper.querySelector(".smart-input-highlights");
    if (!highlightLayer) return;

    const text = eventTitleInput.value;
    const parsed = SmartEventParser.parse(text);

    if (parsed.highlights.length === 0) {
      highlightLayer.innerHTML = escapeHtml(text);
      return;
    }

    // Sort highlights by position
    const highlights = [...parsed.highlights].sort((a, b) => a.start - b.start);

    let html = "";
    let lastIndex = 0;

    for (const h of highlights) {
      // Add text before highlight
      if (h.start > lastIndex) {
        html += escapeHtml(text.substring(lastIndex, h.start));
      }
      // Add highlighted text
      html += `<mark class="highlight-${h.type}">${escapeHtml(text.substring(h.start, h.end))}</mark>`;
      lastIndex = h.end;
    }

    // Add remaining text
    if (lastIndex < text.length) {
      html += escapeHtml(text.substring(lastIndex));
    }

    highlightLayer.innerHTML = html;
  }

  function syncScroll() {
    const wrapper = eventTitleInput.closest(".smart-input-wrapper");
    if (!wrapper) return;
    const highlightLayer = wrapper.querySelector(".smart-input-highlights");
    if (highlightLayer) {
      highlightLayer.scrollLeft = eventTitleInput.scrollLeft;
    }
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  // Load events from server
  async function loadEvents() {
    try {
      const year = current.getFullYear();
      const month = current.getMonth() + 1;
      const response = await fetch(
        `/calendarevent/read?month=${month}&year=${year}`,
      );
      const data = await response.json();

      if (data.success) {
        eventsByDay = {};
        data.data.forEach((event) => {
          const key = event.event_date;
          if (!eventsByDay[key]) eventsByDay[key] = [];
          eventsByDay[key].push(event);
        });
        render();
        renderTodayPane();
      }
    } catch (error) {
      console.error("Error loading events:", error);
    }
  }

  // Create event
  async function createEvent(eventData) {
    try {
      const response = await fetch("/calendarevent/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(eventData),
      });
      const data = await response.json();

      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || "Failed to create event");
        return false;
      }
    } catch (error) {
      console.error("Error creating event:", error);
      alert("Error creating event");
      return false;
    }
  }

  // Update event
  async function updateEvent(eventId, eventData) {
    try {
      const response = await fetch("/calendarevent/update", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event_id: eventId, ...eventData }),
      });
      const data = await response.json();

      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || "Failed to update event");
        return false;
      }
    } catch (error) {
      console.error("Error updating event:", error);
      alert("Error updating event");
      return false;
    }
  }

  // Delete event
  async function deleteEvent(eventId) {
    try {
      const response = await fetch("/calendarevent/delete", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event_id: eventId }),
      });
      const data = await response.json();

      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || "Failed to delete event");
        return false;
      }
    } catch (error) {
      console.error("Error deleting event:", error);
      alert("Error deleting event");
      return false;
    }
  }

  // Show event modal
  function showEventModal(date, event = null, prefill = null) {
    // Prevent multiple modals
    if (activeModal) {
      closeActiveModal();
    }

    selectedDate = date;
    const modal = createEventModal(date, event, prefill);
    activeModal = modal;

    // Use requestAnimationFrame for smoother rendering
    requestAnimationFrame(() => {
      document.body.appendChild(modal);

      // Focus first input
      requestAnimationFrame(() => {
        const firstInput = modal.querySelector('input[type="text"]');
        if (firstInput) firstInput.focus();
      });
    });
  }

  // Close active modal helper
  function closeActiveModal() {
    if (activeModal) {
      activeModal.remove();
      activeModal = null;
    }
  }

  // Show confirmation modal (replaces browser confirm)
  function showConfirmModal(message, onConfirm, onCancel) {
    const confirmModal = document.createElement("div");
    confirmModal.className = "event-modal-overlay confirm-modal-overlay";
    confirmModal.innerHTML = `
      <div class="event-modal confirm-modal">
        <div class="event-modal-body" style="padding: 32px 24px; text-align: center;">
          <div class="confirm-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48" fill="#dc2626">
              <path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/>
            </svg>
          </div>
          <p class="confirm-message">${escapeHtml(message)}</p>
        </div>
        <div class="event-modal-footer" style="justify-content: center; gap: 16px;">
          <div class="modal-actions" style="gap: 16px; width: 100%;">
            <button class="btnSecondary btnSecondaryText" style="flex: 1; white-space: nowrap;" type="button" id="confirm-cancel">Cancel</button>
            <button class="btnPrimary btnDanger btnPrimaryText" style="flex: 1; white-space: nowrap;" type="button" id="confirm-yes">Delete</button>
          </div>
        </div>
      </div>
    `;

    const closeConfirm = () => {
      confirmModal.style.opacity = "0";
      setTimeout(() => confirmModal.remove(), 150);
    };

    confirmModal
      .querySelector("#confirm-cancel")
      .addEventListener("click", () => {
        closeConfirm();
        if (onCancel) onCancel();
      });

    confirmModal.querySelector("#confirm-yes").addEventListener("click", () => {
      closeConfirm();
      if (onConfirm) onConfirm();
    });

    confirmModal.addEventListener("click", (e) => {
      if (e.target === confirmModal) {
        closeConfirm();
        if (onCancel) onCancel();
      }
    });

    document.body.appendChild(confirmModal);
  }

  // Create event modal
  function createEventModal(date, event = null, prefill = null) {
    const isEdit = !!event;
    const modal = document.createElement("div");
    modal.className = "event-modal-overlay";

    // Pre-fill values
    const titleValue = prefill?.title || (event ? event.title : "");
    const startTimeValue =
      prefill?.startTime ||
      (event && event.start_time ? event.start_time : "09:00");
    const endTimeValue =
      prefill?.endTime || (event && event.end_time ? event.end_time : "10:00");
    const isAllDay = prefill?.isAllDay ?? (event && event.is_all_day);
    const description = event && event.description ? event.description : "";

    modal.innerHTML = `
      <div class="event-modal">
        <div class="event-modal-header">
          <h2>${isEdit ? "Edit Event" : "New Event"}</h2>
          <button class="modal-close-btn" type="button" aria-label="Close">&times;</button>
        </div>
        <div class="event-modal-body">
          <div class="form-group">
            <input type="text" id="modal-event-title" class="form-input" placeholder="Event title" value="${escapeHtml(titleValue)}" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor">
                <path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/>
              </svg>
              ${date.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric", year: "numeric" })}
            </label>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <input type="time" id="modal-start-time" class="form-input" value="${startTimeValue}">
            </div>
            <span class="time-separator">–</span>
            <div class="form-group flex-1">
              <input type="time" id="modal-end-time" class="form-input" value="${endTimeValue}">
            </div>
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" id="modal-all-day" ${isAllDay ? "checked" : ""}>
              <span>All day</span>
            </label>
          </div>

          <div class="form-group">
            <textarea id="modal-description" class="form-textarea" placeholder="Add description" rows="3">${escapeHtml(description)}</textarea>
          </div>
        </div>
        <div class="event-modal-footer" style="gap: 16px; flex-wrap: wrap;">
          ${isEdit ? '<button class="btnPrimary btnDanger btnPrimaryText" style="flex: 1; white-space: nowrap;" type="button">Delete</button>' : "<div></div>"}
          <div class="modal-actions" style="flex: 2; gap: 16px;">
            <button class="btnSecondary btnSecondaryText" style="flex: 1; white-space: nowrap;" type="button" id="modal-cancel">Cancel</button>
            <button class="btnPrimary btnPrimaryText" style="flex: 1; white-space: nowrap;" type="button" id="modal-save">${isEdit ? "Save" : "Create event"}</button>
          </div>
        </div>
      </div>
    `;

    // All day checkbox handler
    const allDayCheckbox = modal.querySelector("#modal-all-day");
    const startTimeInput = modal.querySelector("#modal-start-time");
    const endTimeInput = modal.querySelector("#modal-end-time");

    allDayCheckbox.addEventListener("change", () => {
      startTimeInput.disabled = allDayCheckbox.checked;
      endTimeInput.disabled = allDayCheckbox.checked;
    });

    if (allDayCheckbox.checked) {
      startTimeInput.disabled = true;
      endTimeInput.disabled = true;
    }

    // Close modal handler - using a single function and preventing event bubbling
    let isClosing = false;
    const closeModal = (e) => {
      if (isClosing) return;
      isClosing = true;

      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }

      modal.style.opacity = "0";
      modal.querySelector(".event-modal").style.transform =
        "translate3d(0, 20px, 0)";
      setTimeout(() => {
        modal.remove();
        if (activeModal === modal) activeModal = null;
      }, 150);
    };

    // Use { once: true } to prevent multiple clicks
    modal
      .querySelector(".modal-close-btn")
      .addEventListener("click", closeModal, { once: true });
    modal
      .querySelector("#modal-cancel")
      .addEventListener("click", closeModal, { once: true });

    // Click on backdrop to close
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal(e);
    });

    // ESC key to close
    const handleEsc = (e) => {
      if (e.key === "Escape") {
        closeModal(e);
        document.removeEventListener("keydown", handleEsc);
      }
    };
    document.addEventListener("keydown", handleEsc);

    // Save handler
    modal.querySelector("#modal-save").addEventListener("click", async () => {
      const title = modal.querySelector("#modal-event-title").value.trim();
      if (!title) {
        modal.querySelector("#modal-event-title").focus();
        modal.querySelector("#modal-event-title").classList.add("input-error");
        return;
      }

      const eventData = {
        title,
        description: modal.querySelector("#modal-description").value.trim(),
        event_date: fmtDateKey(date),
        start_time: modal.querySelector("#modal-start-time").value || null,
        end_time: modal.querySelector("#modal-end-time").value || null,
        is_all_day: modal.querySelector("#modal-all-day").checked ? 1 : 0,
      };

      let success;
      if (isEdit) {
        success = await updateEvent(event.event_id, eventData);
      } else {
        success = await createEvent(eventData);
      }

      if (success) closeModal();
    });

    // Delete handler - using custom confirmation modal
    if (isEdit) {
      modal.querySelector(".btnDanger").addEventListener("click", () => {
        showConfirmModal(
          "Are you sure you want to delete this event?",
          async () => {
            const success = await deleteEvent(event.event_id);
            if (success) closeModal();
          },
        );
      });
    }

    return modal;
  }

  function render() {
    const monthLabel = current.toLocaleString(undefined, {
      month: "long",
      year: "numeric",
    });
    monthYearEl.textContent = monthLabel;

    const year = current.getFullYear();
    const month = current.getMonth();

    const firstWeekday = new Date(year, month, 1).getDay();
    const gridStart = new Date(year, month, 1 - firstWeekday);

    // Use DocumentFragment for better performance
    const fragment = document.createDocumentFragment();
    const today = new Date();

    for (let i = 0; i < 35; i++) {
      const d = new Date(gridStart);
      d.setDate(gridStart.getDate() + i);

      const cell = document.createElement("div");
      cell.className = "dayCell";
      if (d.getMonth() !== month) cell.classList.add("inactive");
      if (isSameDate(d, today)) cell.classList.add("today");

      const num = document.createElement("div");
      num.className = "dayNum";
      num.textContent = String(d.getDate());
      cell.appendChild(num);

      const list = document.createElement("div");
      list.className = "eventsList";
      const key = fmtDateKey(d);
      const items = eventsByDay[key] || [];
      for (const ev of items) {
        const item = document.createElement("span");
        item.className = "eventTitle";
        item.title = ev.title;
        item.textContent = ev.title;
        item.dataset.eventId = ev.event_id;
        item.dataset.date = key;
        list.appendChild(item);
      }
      cell.appendChild(list);

      cell.dataset.date = key;
      fragment.appendChild(cell);
    }

    // Single DOM update
    daysEl.innerHTML = "";
    daysEl.appendChild(fragment);

    // Event delegation for clicks
    daysEl.addEventListener("click", handleCalendarClick);
  }

  // Event delegation handler
  function handleCalendarClick(e) {
    const eventTitle = e.target.closest(".eventTitle");
    const dayCell = e.target.closest(".dayCell");

    if (eventTitle) {
      e.stopPropagation();
      const dateKey = eventTitle.dataset.date;
      const eventId = eventTitle.dataset.eventId;
      const [year, month, day] = dateKey.split("-").map(Number);
      const date = new Date(year, month - 1, day);
      const items = eventsByDay[dateKey] || [];
      const event = items.find((ev) => ev.event_id == eventId);
      if (event) showEventModal(date, event);
    } else if (dayCell) {
      const dateKey = dayCell.dataset.date;
      const [year, month, day] = dateKey.split("-").map(Number);
      const date = new Date(year, month - 1, day);
      showEventModal(date);
    }
  }

  prevBtn &&
    prevBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() - 1);
      loadEvents();
    });

  nextBtn &&
    nextBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() + 1);
      loadEvents();
    });

  // Quick add from left panel with smart parsing
  if (addEventBtn && eventTitleInput) {
    // Initialize smart input highlighting
    createHighlightedInput();

    const smartQuickAdd = async () => {
      const rawText = eventTitleInput.value.trim();
      if (!rawText) return;

      const parsed = SmartEventParser.parse(rawText);

      // Check if we have all required fields (title and date with time)
      const hasFullDetails = parsed.title && parsed.date && parsed.startTime;

      if (hasFullDetails) {
        // Create event directly
        const eventData = {
          title: parsed.title,
          event_date: fmtDateKey(parsed.date),
          start_time: parsed.startTime,
          end_time: parsed.endTime || null,
          is_all_day: 0,
        };

        const success = await createEvent(eventData);
        if (success) {
          eventTitleInput.value = "";
          updateHighlights();
        }
      } else {
        // Open modal with pre-filled data
        const eventDate = parsed.date || new Date();
        const prefill = {
          title: parsed.title || rawText,
          startTime: parsed.startTime || "09:00",
          endTime: parsed.endTime || "10:00",
          isAllDay: !parsed.startTime,
        };

        eventTitleInput.value = "";
        updateHighlights();
        showEventModal(eventDate, null, prefill);
      }
    };

    addEventBtn.addEventListener("click", smartQuickAdd);
    eventTitleInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") smartQuickAdd();
    });
  }

  // Populate left pane Today list
  function renderTodayPane() {
    const wrap = document.querySelector(".todayEvents");
    if (!wrap) return;
    const noRow = document.getElementById("noEventsRow");
    wrap.querySelectorAll(".event").forEach((el) => el.remove());

    const today = new Date();
    const key = fmtDateKey(today);
    const items = eventsByDay[key] || [];

    if (!items.length) {
      if (noRow) noRow.style.display = "";
      return;
    }

    if (noRow) noRow.style.display = "none";

    // Use DocumentFragment for better performance
    const fragment = document.createDocumentFragment();

    for (const ev of items) {
      const row = document.createElement("div");
      row.className = "event";
      row.dataset.eventId = ev.event_id;
      row.dataset.date = key;

      const h2 = document.createElement("h2");
      h2.textContent = ev.title;
      row.appendChild(h2);

      const p = document.createElement("p");
      if (ev.is_all_day) {
        p.textContent = "All day";
      } else if (ev.start_time) {
        p.textContent = ev.start_time.substring(0, 5);
      }
      row.appendChild(p);

      fragment.appendChild(row);
    }

    wrap.appendChild(fragment);

    // Single event listener using delegation
    wrap.addEventListener("click", (e) => {
      const eventEl = e.target.closest(".event");
      if (eventEl) {
        const eventId = eventEl.dataset.eventId;
        const dateKey = eventEl.dataset.date;
        const [year, month, day] = dateKey.split("-").map(Number);
        const date = new Date(year, month - 1, day);
        const items = eventsByDay[dateKey] || [];
        const event = items.find((ev) => ev.event_id == eventId);
        if (event) showEventModal(date, event);
      }
    });
  }

  // Initial load
  loadEvents();
})();
