(function () {
  // Configuration - uses global config from PHP
  const config = window.ANNOUNCEMENTS_CONFIG || {
    role: 'student',
    announcementUrlBase: '/student/announcement',
    canCreate: false,
    canDelete: false
  };

  const ANNOUNCEMENT_URL_BASE = config.announcementUrlBase;
  const perPage = 10;

  // Division options for filter
  const divisions = [
    { label: 'All divisions', value: '' },
    { label: 'Examination and Registration', value: 'examination and registration' },
    { label: 'CSC and NOC', value: 'csc and noc' },
    { label: 'Postgraduate research and project', value: 'postgraduate research and project' },
    { label: 'Establishment', value: 'establishment' },
    { label: 'Academic Publication and Welfare', value: 'academic publication and welfare' },
    { label: 'General Administration', value: 'general administration' },
    { label: 'Engineering', value: 'engineering' },
    { label: 'Finance', value: 'finance' },
    { label: 'Library', value: 'library' },

  ];

  // Sort options
  const sortOptions = [
    { label: 'Newest first', value: 'newest' },
    { label: 'Oldest first', value: 'oldest' },
  ];

  // Selectors
  const divisionSelect = document.getElementById('divisionFilter');
  const sortSelect = document.getElementById('sortFilter');
  const searchInput = document.getElementById('ticketSearch');
  const paginationHolder = document.querySelector('.ticketsPagination .ticketsPageHolder');
  const root = document.getElementById('announcements-root') || document.querySelector('.tickets');

  // Data
  let announcements = [];
  let filteredAnnouncements = [];
  let page = 1;
  let listenersBound = false;

  // Get data from container
  if (root) {
    const raw = root.getAttribute('data-announcements') || '[]';
    try { announcements = JSON.parse(raw); } catch (e) { console.error('Invalid announcements JSON', e); }
  }

  // Helper functions
  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatDate(iso) {
    try {
      const d = new Date(iso);
      if (isNaN(d.getTime())) return esc(iso || '');
      return d.toLocaleDateString(undefined, {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
      });
    } catch {
      return esc(iso || '');
    }
  }

  function truncate(str, len = 100) {
    if (!str) return '';
    if (str.length <= len) return str;
    return str.substring(0, len) + '...';
  }

  function getDivisionClass(division) {
    const d = (division || '').toLowerCase();
    if (d.includes('Examination and Registration')) return 'division-exam and registration';
    if (d.includes('academic')) return 'division-academic';
    if (d.includes('admin')) return 'division-admin';
    if (d.includes('facilities') || d.includes('facility')) return 'division-facilities';
    return '';
  }

  // Populate select dropdowns
  function populateSelect(select, list) {
    if (!select) return;
    select.innerHTML = '';
    list.forEach((item, idx) => {
      const opt = document.createElement('option');
      opt.value = item.value;
      opt.textContent = item.label;
      if (idx === 0) opt.selected = true;
      select.appendChild(opt);
    });
  }

  // Build custom select UI
  function buildCustomSelect(nativeSelect) {
    if (!nativeSelect) return;

    const wrap = document.createElement('div');
    wrap.className = 'selectWrap';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'selectButton';
    const labelSpan = document.createElement('span');
    labelSpan.className = 'selectedLabel';
    labelSpan.textContent = nativeSelect.options[nativeSelect.selectedIndex]?.text || '';
    const chevron = document.createElement('span');
    chevron.className = 'selectChevron';
    chevron.innerHTML = "<svg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><polyline points='6 9 12 15 18 9' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>";
    button.appendChild(labelSpan);
    button.appendChild(chevron);

    const list = document.createElement('ul');
    list.className = 'selectList';
    list.setAttribute('role', 'listbox');

    Array.from(nativeSelect.options).forEach((opt) => {
      const li = document.createElement('li');
      li.className = 'selectOption' + (opt.selected ? ' isSelected' : '');
      li.textContent = opt.textContent;
      li.dataset.value = opt.value;
      li.setAttribute('role', 'option');
      if (opt.disabled) {
        li.classList.add('isDisabled');
      }
      li.addEventListener('click', () => {
        if (opt.disabled) return;
        nativeSelect.value = opt.value;
        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
        labelSpan.textContent = opt.textContent;
        list.querySelectorAll('.selectOption').forEach((el) => el.classList.remove('isSelected'));
        li.classList.add('isSelected');
        wrap.classList.remove('isOpen');
      });
      list.appendChild(li);
    });

    button.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = wrap.classList.contains('isOpen');
      document.querySelectorAll('.selectWrap.isOpen').forEach((w) => w.classList.remove('isOpen'));
      if (!isOpen) wrap.classList.add('isOpen');
    });
    document.addEventListener('click', () => wrap.classList.remove('isOpen'));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') wrap.classList.remove('isOpen');
    });

    nativeSelect.classList.add('selectHidden');
    nativeSelect.parentNode.insertBefore(wrap, nativeSelect);
    wrap.appendChild(button);
    wrap.appendChild(list);

    nativeSelect.addEventListener('change', () => {
      const o = nativeSelect.options[nativeSelect.selectedIndex];
      labelSpan.textContent = o?.text || '';
      const value = nativeSelect.value;
      list.querySelectorAll('.selectOption').forEach((el) => {
        if (el.dataset.value === value) el.classList.add('isSelected');
        else el.classList.remove('isSelected');
      });
    });
  }

  // Filter and sort announcements
  function filterAnnouncements() {
    const search = (searchInput?.value || '').toLowerCase().trim();
    const division = (divisionSelect?.value || '').toLowerCase();
    const sort = sortSelect?.value || 'newest';

    filteredAnnouncements = announcements.filter((a) => {
      // Search filter
      if (search) {
        const searchable = [
          a.topic || '',
          a.content || '',
          a.staff_name || '',
          a.division_name || '',
        ].join(' ').toLowerCase();
        if (!searchable.includes(search)) return false;
      }
      // Division filter
      if (division) {
        const divName = (a.division_name || '').toLowerCase();
        console.log('Filtering division:', division, 'against', divName);
        if (!divName.includes(division)) return false;
      }
      return true;
    });

    // Sort
    filteredAnnouncements.sort((a, b) => {
      const dateA = new Date(a.date_time || 0);
      const dateB = new Date(b.date_time || 0);
      return sort === 'newest' ? dateB - dateA : dateA - dateB;
    });

    page = 1;
  }

  // Render announcements
  function renderAnnouncements() {
    if (!root) return;

    if (!filteredAnnouncements || filteredAnnouncements.length === 0) {
      root.innerHTML = `
        <div class="ticketsEmpty">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
          </svg>
          <p>No announcements found.</p>
        </div>`;
      renderPagination();
      return;
    }

    const start = (page - 1) * perPage;
    const end = start + perPage;
    const pageData = filteredAnnouncements.slice(start, end);

    const html = pageData.map((a) => {
      const id = a.id != null ? String(a.id) : '';
      const url = id ? `${ANNOUNCEMENT_URL_BASE}?id=${encodeURIComponent(id)}` : '#';
      const vt = `announcement-${esc(id)}`;
      const divClass = getDivisionClass(a.division_name);

      const inner = `
        <div class="ticketRow1">
          <div class="ticketName">
            <h2>${esc(a.topic)}</h2>
            <div class="ticketInfo">
              <p>Announcement #${esc(a.id)}</p>
              <p>${formatDate(a.date_time)}</p>
              <p>${esc(a.staff_name || '')}</p>
            </div>
          </div>
          <div style="display:flex; gap:10px; align-items:center;">
            <div class="status ${divClass}">${esc(a.division_name || 'General')}</div>
          </div>
        </div>
        <div class="ticketRow2">
          <div class="ticketDetails">
            <div class="ticketDetail">
              <h2>Division:</h2>
              <div class="ticketDetailHolder">${esc(a.division_name || 'N/A')}</div>
            </div>
          </div>
          <div class="ticketData">
            <div class="ticketDetail">
              <h2>Posted by:</h2>
              <div class="ticketDataHolder">${esc(a.staff_name || 'Staff')}</div>
            </div>
            <div class="ticketDetail">
              <h2>Preview:</h2>
              <div class="ticketDataHolder">${esc(truncate(a.content, 60))}</div>
            </div>
          </div>
        </div>`;

      return `<a class="ticket" href="${url}" aria-label="View announcement ${esc(a.topic)}" style="view-transition-name: ${vt}; text-decoration: none; color: inherit;">${inner}</a>`;
    }).join('');

    root.innerHTML = html;

    if (!listenersBound) {
      root.addEventListener('click', (e) => {
        const a = e.target?.closest('a.ticket');
        if (!a || !root.contains(a)) return;
        if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const href = a.getAttribute('href');
        if (!href || href === '#') return;
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

    renderPagination();
  }

  // Render pagination
  function renderPagination() {
    if (!paginationHolder) return;
    paginationHolder.innerHTML = '';
    const totalPages = Math.max(1, Math.ceil(filteredAnnouncements.length / perPage));

    const makeBtn = (num, active = false, label, isHtml = false, ariaLabel = null) => {
      const d = document.createElement('div');
      d.className = 'ticketsPageNum' + (active ? ' active' : '');
      if (isHtml) {
        d.innerHTML = label;
      } else {
        d.innerHTML = `<h2>${esc(label || String(num))}</h2>`;
      }
      if (ariaLabel) d.setAttribute('aria-label', ariaLabel);
      d.addEventListener('click', () => {
        if (num >= 1 && num <= totalPages && num !== page) {
          page = num;
          renderAnnouncements();
        }
      });
      return d;
    };

    if (page > 1) {
      const leftSvg = '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="m121.38-480 289.31 289.31q10.92 10.92 11.12 27.07.19 16.16-10.73 27.08-10.93 10.92-27.08 10.92t-27.08-10.92L59.08-434.77q-9.85-9.85-14.08-21.31-4.23-11.46-4.23-23.92T45-503.92q4.23-11.46 14.08-21.31l297.84-297.85q10.93-10.92 26.89-11.11 15.96-.19 26.88 10.73 10.92 10.92 10.92 27.08 0 16.15-10.92 27.07L121.38-480Z"/></svg>';
      paginationHolder.appendChild(makeBtn(page - 1, false, leftSvg, true, 'Previous page'));
    }

    const maxButtons = 5;
    let start = Math.max(1, page - Math.floor(maxButtons / 2));
    let end = Math.min(totalPages, start + maxButtons - 1);
    if (end - start + 1 < maxButtons) start = Math.max(1, end - maxButtons + 1);

    for (let i = start; i <= end; i++) {
      paginationHolder.appendChild(makeBtn(i, i === page, String(i), false));
    }

    if (page < totalPages) {
      const rightSvg = '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="M579.08-480 289.77-769.31q-10.92-10.92-10.73-26.88.2-15.96 11.12-26.89 10.92-10.92 26.88-10.92 15.96 0 27.08 10.92l297.84 297.85q9.85 9.85 14.08 21.31 4.23 11.46 4.23 23.92t-4.23 23.92q-4.23 11.46-14.08 21.31L344.12-136.92q-10.93 10.92-27.08 10.92-16.16 0-27.08-10.92-10.92-10.92-10.92-27.08 0-16.15 10.92-27.07L579.08-480Z"/></svg>';
      paginationHolder.appendChild(makeBtn(page + 1, false, rightSvg, true, 'Next page'));
    }
  }

  // Debounce helper
  function debounce(fn, wait) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null, args), wait);
    };
  }

  // Initialize
  function init() {
    populateSelect(divisionSelect, divisions);
    populateSelect(sortSelect, sortOptions);

    [divisionSelect, sortSelect].forEach(buildCustomSelect);

    filterAnnouncements();
    renderAnnouncements();

    // Event listeners
    const debouncedFilter = debounce(() => {
      filterAnnouncements();
      renderAnnouncements();
    }, 300);

    if (searchInput) {
      searchInput.addEventListener('input', debouncedFilter);
    }
    if (divisionSelect) {
      divisionSelect.addEventListener('change', () => {
        filterAnnouncements();
        renderAnnouncements();
      });
    }
    if (sortSelect) {
      sortSelect.addEventListener('change', () => {
        filterAnnouncements();
        renderAnnouncements();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
