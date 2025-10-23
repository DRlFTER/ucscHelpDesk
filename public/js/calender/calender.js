(function () {
  const daysEl = document.getElementById("calendarDays");
  const monthYearEl = document.getElementById("monthYear");
  const prevBtn = document.getElementById("prevMonthBtn");
  const nextBtn = document.getElementById("nextMonthBtn");

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

  const eventsByDay = Object.create(null);

  const ROLE = (window.CALENDAR_CONFIG && window.CALENDAR_CONFIG.role
    ? String(window.CALENDAR_CONFIG.role)
    : 'guest').toLowerCase();

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
        list.appendChild(item);
      }
      cell.appendChild(list);

      daysEl.appendChild(cell);
    }
  }

  prevBtn &&
    prevBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() - 1);
      render();
    });
  nextBtn &&
    nextBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() + 1);
      render();
    });

  // Seed sample events
  (function seedSamples() {
    const demo = new Date();
    const key1 = fmtDateKey(demo);
    eventsByDay[key1] = [{ title: "Team Sync" }, { title: "Ticket Review" }];
    const demo2 = new Date();
    demo2.setDate(demo.getDate() + 3);
    const key2 = fmtDateKey(demo2);
    eventsByDay[key2] = [{ title: "Maintenance Window" }];

    // Counselor-specific seed: 24th October (current year)
    if (ROLE === 'counselor') {
      const y = new Date().getFullYear();
      const keyCounselor = `${y}-10-23`;
      const list = eventsByDay[keyCounselor] || [];
      list.push({ title: "Meeting with Brian", isMeeting: true, url: "/counselor/meeting" });
      eventsByDay[keyCounselor] = list;
    }
  })();

  render();

  // Populate left pane Today list from eventsByDay
  (function renderTodayPane() {
    const wrap = document.querySelector('.todayEvents');
    if (!wrap) return;
    const noRow = document.getElementById('noEventsRow');
    // Remove any placeholder items
    wrap.querySelectorAll('.event').forEach(el => el.remove());

    const today = new Date();
    const key = fmtDateKey(today);
    const items = eventsByDay[key] || [];
    if (!items.length) {
      if (noRow) noRow.style.display = '';
      return;
    }
    if (noRow) noRow.style.display = 'none';
    for (const ev of items) {
      const row = document.createElement('div');
      row.className = 'event';
      const h2 = document.createElement('h2');
      h2.textContent = ev.title;
      row.appendChild(h2);

      if (ev.isMeeting) {
        const btn = document.createElement('a');
        btn.className = 'ctaButton';
        btn.href = ev.url || '/counselor/meeting';
        btn.textContent = 'Attend Meeting';
        row.appendChild(btn);
      } else {
        const p = document.createElement('p');
        p.textContent = 'All day';
        row.appendChild(p);
      }
      wrap.appendChild(row);
    }
  })();
})();
