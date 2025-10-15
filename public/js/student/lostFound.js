document.addEventListener('DOMContentLoaded', () => {
  // Category (All/Lost/Found) radios
  const radios = Array.from(document.querySelectorAll('.filterGroup input[type="radio"][name="type"]'));
  // Advanced filter selects (top + sidebar)
  const topDate = document.getElementById('lfDateTop');
  const topLoc = document.getElementById('lfLocationTop');
  const sideDate = document.getElementById('lfDateSide');
  const sideLoc = document.getElementById('lfLocationSide');
  const cards = Array.from(document.querySelectorAll('.lfList .lfCard'));

  // --- Custom select enhancer (from New Ticket) ---
  function enhanceSelect(select){
    if (!select || select.dataset.enhanced === '1') return;
    select.dataset.enhanced = '1';

    const wrap = document.createElement('div');
    wrap.className = 'selectWrap';
    wrap.style.position = 'relative';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'selectButton';
    button.setAttribute('aria-haspopup', 'listbox');
    button.setAttribute('aria-expanded', 'false');

    const labelSpan = document.createElement('span');
    labelSpan.className = 'selectLabel';
    labelSpan.textContent = select.options[select.selectedIndex]?.text || select.options[0]?.text || 'Select';
    button.appendChild(labelSpan);

    const chev = document.createElement('span');
    chev.className = 'selectChevron';
    chev.innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='18' height='18' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>";
    button.appendChild(chev);

    const list = document.createElement('ul');
    list.className = 'selectList';
    list.setAttribute('role', 'listbox');
    list.tabIndex = -1;

    Array.from(select.options).forEach((opt) => {
      const li = document.createElement('li');
      li.className = 'selectOption';
      li.setAttribute('role', 'option');
      li.dataset.value = opt.value;
      li.textContent = opt.text;
      if (opt.disabled) {
        li.setAttribute('aria-disabled', 'true');
        li.classList.add('isDisabled');
      }
      if (opt.selected) {
        li.setAttribute('aria-selected', 'true');
        li.classList.add('isSelected');
      }
      li.addEventListener('click', () => {
        if (opt.disabled) return;
        select.value = opt.value;
        select.dispatchEvent(new Event('change'));
        labelSpan.textContent = opt.text;
        list.querySelectorAll('.selectOption').forEach(o => {
          o.classList.remove('isSelected');
          o.setAttribute('aria-selected', 'false');
        });
        li.classList.add('isSelected');
        li.setAttribute('aria-selected', 'true');
        closeList();
      });
      list.appendChild(li);
    });

    function openList(){ wrap.classList.add('open'); button.setAttribute('aria-expanded', 'true'); list.focus(); }
    function closeList(){ wrap.classList.remove('open'); button.setAttribute('aria-expanded', 'false'); }

    button.addEventListener('click', (e) => { e.stopPropagation(); if (wrap.classList.contains('open')) closeList(); else openList(); });
    document.addEventListener('click', (e) => { if (!wrap.contains(e.target)) closeList(); });

    select.classList.add('selectHidden');
    const parent = select.parentNode;
    parent.insertBefore(wrap, select);
    wrap.appendChild(select);
    wrap.appendChild(button);
    wrap.appendChild(list);
  }

  // Enhance all four selects if present
  ['lfDateTop','lfLocationTop','lfDateSide','lfLocationSide'].forEach(id => {
    const el = document.getElementById(id);
    if (el) enhanceSelect(el);
  });

  // --- Filtering logic: combine category and advanced filters ---
  function getStateFilter(){
    return (radios.find(r => r.checked)?.value || 'all').toLowerCase();
  }

  function passesState(card, stateVal){
    if (stateVal === 'all') return true;
    const isLost = card.classList.contains('lost');
    const isFound = card.classList.contains('found');
    if (stateVal === 'lost') return isLost;
    if (stateVal === 'found') return isFound;
    return true;
  }

  function passesAdvanced(card){
    const locVal = (topLoc?.value || sideLoc?.value || 'all');
    // Location: text match for now (demo content)
    if (locVal !== 'all') {
      const text = card.textContent.toLowerCase();
      if (!text.includes(String(locVal).toLowerCase())) return false;
    }
    // Date: stub for now; add data-timestamp on cards to implement ranges
    return true;
  }

  function render(){
    const stateVal = getStateFilter();
    cards.forEach(card => {
      const show = passesState(card, stateVal) && passesAdvanced(card);
      card.style.display = show ? '' : 'none';
    });
  }

  // Category listeners
  radios.forEach(r => r.addEventListener('change', render));

  // Select syncing and listeners
  function sync(from, to){ if (from && to) to.value = from.value; }
  [topDate, topLoc, sideDate, sideLoc].forEach(el => {
    if (!el) return;
    el.addEventListener('change', () => {
      if (el === topDate) sync(topDate, sideDate);
      if (el === sideDate) sync(sideDate, topDate);
      if (el === topLoc) sync(topLoc, sideLoc);
      if (el === sideLoc) sync(sideLoc, topLoc);
      render();
    });
  });

  // Initial render
  render();
});
