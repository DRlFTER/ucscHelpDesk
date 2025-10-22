(function () {
  const daysEl = document.getElementById("calendarDays");
  const monthYearEl = document.getElementById("monthYear");
  const prevBtn = document.getElementById("prevMonthBtn");
  const nextBtn = document.getElementById("nextMonthBtn");

  if (!daysEl || !monthYearEl) return;

  let current = new Date();
  current.setDate(1);

  const eventsByDay = Object.create(null);

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
      if (items.length === 0) {
      } else {
        for (const ev of items) {
          const item = document.createElement("span");
          item.className = "eventTitle";
          item.title = ev.title;
          item.textContent = ev.title;
          list.appendChild(item);
        }
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

  (function seedSamples() {
    const demo = new Date();
    const key1 = fmtDateKey(demo);
    eventsByDay[key1] = [{ title: "Team Sync" }, { title: "Ticket Review" }];
    const demo2 = new Date();
    demo2.setDate(demo.getDate() + 3);
    const key2 = fmtDateKey(demo2);
    eventsByDay[key2] = [{ title: "Maintenance Window" }];
  })();

  render();
})();
