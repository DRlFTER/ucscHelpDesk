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
  if (typeof KB_DATA === 'undefined' || !Array.isArray(KB_DATA)) {
    const container = document.getElementById('kbSections');
    if (container) {
      container.innerHTML = `<div style="padding:12px 14px; border:1px solid #f59e0b; background:#fffbeb; color:#92400e; border-radius:8px;">Knowledge base data is not available.</div>`;
    }
    return;  // Exit early if no data
  }

  console.log('KB_DATA:', KB_DATA);  // Debug: Shows your grouped data (e.g., [{section: "General Documents", items: [...]}])

  function buildCard(item) {
    // Derive color from section (fixed keys to match your DB)
    const sectionColor = {
      'General Documents': 'blue',  // Matches your DB (capital D)
      'Policies and rules': 'green',
      'Academic resources': 'orange',
      'Uncategorized': 'gray'
    };
    const colorCls = item.sectionColor || (sectionColor[item.section] ? `kbCard--${sectionColor[item.section]}` : '');

    // Type fallback (hardcode or from DB)
    const typeBadge = item.type || 'Guide';

    return `
      <div class="kbCard ${colorCls}" data-id="${item.id}">  <!-- Added data-id for future downloads -->
        <div class="kbCardHeader">
          <div class="kbCardTitle">${item.title}</div>
          <div class="kbBadge">${typeBadge}</div>
        </div>
        <div class="kbMeta">Updated: ${item.updated}</div>
        <div class="kbDesc">${item.description}</div>  <!-- Fixed: 'description' not 'desc' -->
        <div class="kbFooter">
          <button class="kbDownloadBtn" type="button" title="Download">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Download</span>
          </button>
        </div>
      </div>
    `;
  }

  function renderSections(data) {
    const wrap = document.getElementById('kbSections');
    if (!wrap) return;

    // Pass section to items for color
    const dataWithColors = data.map(sec => ({
      ...sec,
      items: sec.items.map(item => ({ ...item, sectionColor: sec.section, section: sec.section }))  // Add both for flexibility
    }));

    const generatedHtml = dataWithColors.map(sec => `
      <section class="kbSection">
        <h3 class="kbSectionTitle">${sec.section}</h3>
        <div class="kbGrid">
          ${sec.items.map(buildCard).join('')}
        </div>
      </section>
    `).join('');

    wrap.innerHTML = generatedHtml;
    console.log('Generated HTML length:', generatedHtml.length);  // Debug: Should be >1000 if data renders
  }

  function populateCategories(data) {
    const sel = document.getElementById('kbCategorySelect');
    if (!sel) return;
    const cats = ['All categories'].concat(data.map(s => s.section));
    sel.innerHTML = cats.map((c, i) => `<option value="${i===0?'all':c}">${c}</option>`).join('');

    // Build custom select UI using forum/tickets styles
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
        items: s.items.filter(it =>
          it.title.toLowerCase().includes(ql) ||
          it.description.toLowerCase().includes(ql) ||  // Fixed: 'description' not 'desc'
          (it.type || 'Guide').toLowerCase().includes(ql)  // Include type (hardcoded fallback)
        )
      }))
      .filter(s => s.items.length > 0);
  }

  function wireInteractions() {
    const input = document.getElementById('kbSearchInput');  // By ID, as in your HTML
    const sel = document.getElementById('kbCategorySelect');
    const apply = () => {
      const q = input ? input.value : '';
      const cat = sel ? sel.value : 'all';
      const catVal = cat === 'all' ? 'all' : cat;
      const shown = filterData(q, catVal);
      renderSections(shown);
    };
    if (input) input.addEventListener('input', apply);  // 'input' event for real-time search
    if (sel) sel.addEventListener('change', apply);
    apply();  // Initial render with no filters
  }

  // Your init
  function init() {
    console.log('Init called with:', KB_DATA.length, 'sections');  // Debug: e.g., "Init called with: 1 sections"
    populateCategories(KB_DATA);
    renderSections(KB_DATA);
    wireInteractions();
  }
  init();
});