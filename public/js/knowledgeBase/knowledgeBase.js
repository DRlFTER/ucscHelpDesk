(function () {
  // Keep navbar height CSS var in sync
  function setNavbarVar() {
    const nav = document.querySelector('.navbar');
    const h = nav ? nav.offsetHeight : 64;
    document.documentElement.style.setProperty('--navbar-height', h + 'px');
  }
  let rafId;
  function onResize() { if (rafId) cancelAnimationFrame(rafId); rafId = requestAnimationFrame(setNavbarVar); }
  window.addEventListener('resize', onResize);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setNavbarVar); else setNavbarVar();
})();

document.addEventListener("DOMContentLoaded", () => {
  // Get role and permissions from window (set by PHP)
  const role = window.USER_ROLE || 'student';
  const canManage = window.CAN_MANAGE_KB || false;
  let KB_DATA = window.KB_DATA || [];

  function buildCard(item) {
    // Color by type (green for Guide, blue for Schedule)
    const colorCls = item.type === 'Guide' ? 'kbCard--green' : (item.type === 'Schedule' ? 'kbCard--blue' : '');
    const typeBadge = item.type || 'Guide';
    
    // Use desc or description depending on data format
    const description = item.description || item.desc || '';
    const updated = item.updated || '';

    // Download button - works for all users
    let downloadBtn = '';
    if (item.fileUrl) {
      downloadBtn = `
        <a href="${item.fileUrl}" class="kbDownloadBtn" download title="Download">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>`;
    } else {
      downloadBtn = `
        <button class="kbDownloadBtn" type="button" title="Download" data-id="${item.id}">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </button>`;
    }

    // Management buttons for staff/admin only
    let manageBtn = '';
    if (canManage) {
      manageBtn = `<button class="kbUpdateBtn" type="button">View Resource</button>`;
    }

    return `
      <div class="kbCard ${colorCls}" data-id="${item.id}">
        <div class="kbCardHeader">
          <div class="kbCardTitle">${item.title}</div>
          <div class="kbBadge">${typeBadge}</div>
        </div>
        <div class="kbMeta">Updated: ${updated}</div>
        <div class="kbDesc">${description}</div>
        <div class="kbFooter">
          ${downloadBtn}
          ${manageBtn}
        </div>
      </div>
    `;
  }

  function renderSections(data) {
    const wrap = document.getElementById('kbSections');
    if (!wrap) return;

    if (!data || data.length === 0) {
      wrap.innerHTML = '<p style="text-align:center; color:#6b7280; padding: 40px;">No knowledge base articles found.</p>';
      return;
    }

    wrap.innerHTML = data.map(sec => `
      <section class="kbSection">
        <h3 class="kbSectionTitle">${sec.section}</h3>
        <div class="kbGrid">
          ${sec.items.map(buildCard).join('')}
        </div>
      </section>
    `).join('');
  }

  function populateCategories(data) {
    const sel = document.getElementById('kbCategorySelect');
    if (!sel) return;
    const cats = ['All categories'].concat(data.map(s => s.section));
    sel.innerHTML = cats.map((c, i) => `<option value="${i===0?'all':c}">${c}</option>`).join('');

    // Build custom select UI
    const wrap = document.getElementById('kbCategoryWrap');
    const btn = wrap?.querySelector('.selectButton');
    const list = document.getElementById('kbCategoryList');
    const label = document.getElementById('kbCategoryLabel');
    if (!wrap || !btn || !list || !label) return;

    const options = cats.map((c, i) => ({ label: c, value: i===0?'all':c }));
    list.innerHTML = options.map((o, idx) => `<li class="selectOption${idx===0?' isSelected':''}" role="option" data-value="${o.value}">${o.label}</li>`).join('');
    let isOpen = false;
    let selectedIndex = 0;

    const setOpen = (open) => {
      isOpen = open;
      wrap.classList.toggle('isOpen', isOpen);
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (isOpen) list.focus();
    };
    const applySelection = (idx) => {
      selectedIndex = idx;
      const liList = Array.from(list.querySelectorAll('.selectOption'));
      liList.forEach((li, i) => li.classList.toggle('isSelected', i === idx));
      const opt = options[idx];
      if (!opt) return;
      label.textContent = opt.label;
      sel.value = opt.value;
      sel.dispatchEvent(new Event('change'));
    };

    btn.addEventListener('click', () => setOpen(!isOpen));
    list.addEventListener('click', (e) => {
      const li = e.target.closest('.selectOption');
      if (!li) return;
      const value = li.getAttribute('data-value');
      const idx = options.findIndex(o => o.value === value);
      if (idx >= 0) applySelection(idx);
      setOpen(false);
    });
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
        e.preventDefault(); setOpen(true);
      }
    });
    list.addEventListener('keydown', (e) => {
      const max = options.length - 1;
      if (e.key === 'Escape') { setOpen(false); btn.focus(); }
      else if (e.key === 'ArrowDown') { e.preventDefault(); applySelection(Math.min(max, selectedIndex + 1)); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); applySelection(Math.max(0, selectedIndex - 1)); }
      else if (e.key === 'Enter') { e.preventDefault(); setOpen(false); btn.focus(); }
    });
  }

  function filterData(q, cat) {
    const ql = (q || '').toLowerCase();
    return KB_DATA
      .filter(s => cat === 'all' || s.section === cat)
      .map(s => ({
        section: s.section,
        items: s.items.filter(it => {
          const desc = it.description || it.desc || '';
          return it.title.toLowerCase().includes(ql) ||
            desc.toLowerCase().includes(ql) ||
            (it.type || 'Guide').toLowerCase().includes(ql);
        })
      }))
      .filter(s => s.items.length > 0);
  }

  function wireInteractions() {
    const input = document.getElementById('kbSearchInput');
    const sel = document.getElementById('kbCategorySelect');
    const apply = () => {
      const q = input ? input.value : '';
      const cat = sel ? sel.value : 'all';
      const shown = filterData(q, cat);
      renderSections(shown);
    };
    if (input) input.addEventListener('input', apply);
    if (sel) sel.addEventListener('change', apply);
    apply();

    // Wire card buttons (delegated)
    document.addEventListener('click', (e) => {
      const card = e.target.closest('.kbCard');
      if (!card) return;
      const id = card.dataset.id;

      // Update/View Resource Button (staff/admin only)
      if (e.target.classList.contains('kbUpdateBtn')) {
        if (id) window.location.href = `/${role}/updateKB/${id}`;
        e.preventDefault();
      }

      // Download Button - trigger download via server
      if (e.target.closest('.kbDownloadBtn') && !e.target.closest('a')) {
        if (id) {
          window.location.href = `/${role}/downloadKB/${id}`;
          e.preventDefault();
        }
      }
    });
  }

  async function loadData() {
    // If KB_DATA is already populated from PHP, use it
    if (KB_DATA && KB_DATA.length > 0) {
      init();
      return;
    }

    // Otherwise fetch from API
    try {
      const res = await fetch(`/${role}/knowledgebaseData`);
      if (!res.ok) throw new Error('Failed to load data');
      KB_DATA = await res.json();
      window.KB_DATA = KB_DATA;
      init();
    } catch (e) {
      console.error(e);
      const wrap = document.getElementById('kbSections');
      if(wrap) wrap.innerHTML = '<p style="text-align:center; color:red;">Failed to load knowledge base.</p>';
    }
  }

  function init() {
    if (!KB_DATA || !Array.isArray(KB_DATA)) {
      const container = document.getElementById('kbSections');
      if (container) {
        container.innerHTML = `<div style="padding:12px 14px; border:1px solid #f59e0b; background:#fffbeb; color:#92400e; border-radius:8px;">Knowledge base data is not available.</div>`;
      }
      return;
    }
    populateCategories(KB_DATA);
    renderSections(KB_DATA);
    wireInteractions();
  }

  loadData();
});
