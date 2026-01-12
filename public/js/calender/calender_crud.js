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

  // Load events from server
  async function loadEvents() {
    try {
      const year = current.getFullYear();
      const month = current.getMonth() + 1;
      const response = await fetch(`/calendarevent/read?month=${month}&year=${year}`);
      const data = await response.json();
      
      if (data.success) {
        eventsByDay = {};
        data.data.forEach(event => {
          const key = event.event_date;
          if (!eventsByDay[key]) eventsByDay[key] = [];
          eventsByDay[key].push(event);
        });
        render();
        renderTodayPane();
      }
    } catch (error) {
      console.error('Error loading events:', error);
    }
  }

  // Create event
  async function createEvent(eventData) {
    try {
      const response = await fetch('/calendarevent/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(eventData)
      });
      const data = await response.json();
      
      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || 'Failed to create event');
        return false;
      }
    } catch (error) {
      console.error('Error creating event:', error);
      alert('Error creating event');
      return false;
    }
  }

  // Update event
  async function updateEvent(eventId, eventData) {
    try {
      const response = await fetch('/calendarevent/update', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId, ...eventData })
      });
      const data = await response.json();
      
      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || 'Failed to update event');
        return false;
      }
    } catch (error) {
      console.error('Error updating event:', error);
      alert('Error updating event');
      return false;
    }
  }

  // Delete event
  async function deleteEvent(eventId) {
    try {
      const response = await fetch('/calendarevent/delete', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId })
      });
      const data = await response.json();
      
      if (data.success) {
        await loadEvents();
        return true;
      } else {
        alert(data.error || 'Failed to delete event');
        return false;
      }
    } catch (error) {
      console.error('Error deleting event:', error);
      alert('Error deleting event');
      return false;
    }
  }

  // Show event modal
  function showEventModal(date, event = null) {
    selectedDate = date;
    const modal = createEventModal(date, event);
    document.body.appendChild(modal);
    
    // Focus first input
    setTimeout(() => {
      const firstInput = modal.querySelector('input[type="text"]');
      if (firstInput) firstInput.focus();
    }, 100);
  }

  // Create event modal
  function createEventModal(date, event = null) {
    const isEdit = !!event;
    const modal = document.createElement('div');
    modal.className = 'event-modal-overlay';
    modal.innerHTML = `
      <div class="event-modal">
        <div class="event-modal-header">
          <h2>${isEdit ? 'Edit Event' : 'New Event'}</h2>
          <button class="modal-close-btn" type="button">&times;</button>
        </div>
        <div class="event-modal-body">
          <div class="form-group">
            <input type="text" id="modal-event-title" class="form-input" placeholder="Event title" value="${event ? event.title : ''}" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor">
                <path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/>
              </svg>
              ${date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
            </label>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <input type="time" id="modal-start-time" class="form-input" value="${event && event.start_time ? event.start_time : '09:00'}">
            </div>
            <span class="time-separator">–</span>
            <div class="form-group flex-1">
              <input type="time" id="modal-end-time" class="form-input" value="${event && event.end_time ? event.end_time : '10:00'}">
            </div>
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" id="modal-all-day" ${event && event.is_all_day ? 'checked' : ''}>
              <span>All day</span>
            </label>
          </div>

          <div class="form-group">
            <textarea id="modal-description" class="form-textarea" placeholder="Add description" rows="3">${event && event.description ? event.description : ''}</textarea>
          </div>
        </div>
        <div class="event-modal-footer">
          ${isEdit ? '<button class="btn-delete" type="button">Delete</button>' : '<div></div>'}
          <div class="modal-actions">
            <button class="btn-secondary" type="button" id="modal-cancel">Cancel</button>
            <button class="btn-primary" type="button" id="modal-save">${isEdit ? 'Save' : 'Create event'}</button>
          </div>
        </div>
      </div>
    `;

    // All day checkbox handler
    const allDayCheckbox = modal.querySelector('#modal-all-day');
    const startTimeInput = modal.querySelector('#modal-start-time');
    const endTimeInput = modal.querySelector('#modal-end-time');
    
    allDayCheckbox.addEventListener('change', () => {
      startTimeInput.disabled = allDayCheckbox.checked;
      endTimeInput.disabled = allDayCheckbox.checked;
    });
    
    if (allDayCheckbox.checked) {
      startTimeInput.disabled = true;
      endTimeInput.disabled = true;
    }

    // Close modal handler
    const closeModal = () => modal.remove();
    modal.querySelector('.modal-close-btn').addEventListener('click', closeModal);
    modal.querySelector('#modal-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });

    // Save handler
    modal.querySelector('#modal-save').addEventListener('click', async () => {
      const title = modal.querySelector('#modal-event-title').value.trim();
      if (!title) {
        alert('Please enter an event title');
        return;
      }

      const eventData = {
        title,
        description: modal.querySelector('#modal-description').value.trim(),
        event_date: fmtDateKey(date),
        start_time: modal.querySelector('#modal-start-time').value || null,
        end_time: modal.querySelector('#modal-end-time').value || null,
        is_all_day: modal.querySelector('#modal-all-day').checked ? 1 : 0
      };

      let success;
      if (isEdit) {
        success = await updateEvent(event.event_id, eventData);
      } else {
        success = await createEvent(eventData);
      }

      if (success) closeModal();
    });

    // Delete handler
    if (isEdit) {
      modal.querySelector('.btn-delete').addEventListener('click', async () => {
        if (confirm('Are you sure you want to delete this event?')) {
          const success = await deleteEvent(event.event_id);
          if (success) closeModal();
        }
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

    daysEl.innerHTML = "";

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
        item.addEventListener('click', (e) => {
          e.stopPropagation();
          showEventModal(d, ev);
        });
        list.appendChild(item);
      }
      cell.appendChild(list);

      // Click to add new event
      cell.addEventListener('click', () => {
        showEventModal(d);
      });

      daysEl.appendChild(cell);
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

  // Quick add from left panel
  if (addEventBtn && eventTitleInput) {
    const quickAdd = async () => {
      const title = eventTitleInput.value.trim();
      if (!title) return;

      const today = new Date();
      const eventData = {
        title,
        event_date: fmtDateKey(today),
        is_all_day: 1
      };

      const success = await createEvent(eventData);
      if (success) {
        eventTitleInput.value = '';
      }
    };

    addEventBtn.addEventListener('click', quickAdd);
    eventTitleInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') quickAdd();
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
    
    for (const ev of items) {
      const row = document.createElement("div");
      row.className = "event";
      row.addEventListener('click', () => showEventModal(today, ev));
      
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
      
      wrap.appendChild(row);
    }
  }

  // Initial load
  loadEvents();
})();