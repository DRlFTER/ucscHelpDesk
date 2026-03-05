(function(){
  // Toggle between public/draft post options
  const typeInput = document.getElementById('ticketType');
  const toggleBtns = document.querySelectorAll('.ticketToggle .btnAttach');
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      toggleBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const val = btn.dataset.value || 'public';
      if (typeInput) typeInput.value = val;
    });
  });

  // File upload dropzone handling
  const dz = document.getElementById('dropzone');
  const fileInput = document.getElementById('fileInput');
  const fileList = document.getElementById('fileList');

  let buffer = new DataTransfer();
  function humanKB(size){ const kb = size/1024; return kb < 1024 ? `${Math.round(kb)} KB` : `${(kb/1024).toFixed(2)} MB`; }
  function syncInput(){ fileInput.files = buffer.files; }
  function removeAt(index){ const next = new DataTransfer(); Array.from(buffer.files).forEach((f,i)=>{ if(i!==index) next.items.add(f); }); buffer = next; syncInput(); renderFiles(); }
  function addFiles(files){ Array.from(files).forEach(f => buffer.items.add(f)); syncInput(); renderFiles(); }
  function renderFiles(){ 
    if(!fileList) return; 
    fileList.innerHTML=''; 
    Array.from(buffer.files).forEach((f,idx)=>{ 
      const li=document.createElement('li'); 
      li.className='fileItem'; 
      const left=document.createElement('div'); 
      left.className='fileMeta'; 
      const icon=document.createElement('div'); 
      icon.className='fileIcon'; 
      icon.innerHTML="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='18' height='18' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'></path><polyline points='14 2 14 8 20 8'></polyline></svg>"; 
      const name=document.createElement('span'); 
      name.className='fileName'; 
      name.textContent=f.name; 
      const size=document.createElement('span'); 
      size.className='fileSize'; 
      size.textContent=humanKB(f.size); 
      left.appendChild(icon); 
      left.appendChild(name); 
      left.appendChild(size); 
      const removeBtn=document.createElement('button'); 
      removeBtn.type='button'; 
      removeBtn.className='fileRemove'; 
      removeBtn.setAttribute('aria-label',`Remove ${f.name}`); 
      removeBtn.innerHTML="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='16' height='16' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'></line><line x1='6' y1='6' x2='18' y2='18'></line></svg>"; 
      removeBtn.addEventListener('click', (e)=>{ e.stopPropagation(); removeAt(idx); }); 
      li.appendChild(left); 
      li.appendChild(removeBtn); 
      fileList.appendChild(li); 
    }); 
  }

  if (dz && fileInput) {
    dz.addEventListener('click', ()=> fileInput.click());
    dz.addEventListener('dragover', e=>{ e.preventDefault(); dz.classList.add('drag'); });
    dz.addEventListener('dragleave', ()=> dz.classList.remove('drag'));
    dz.addEventListener('drop', e=>{ e.preventDefault(); dz.classList.remove('drag'); const dt=e.dataTransfer; if(dt && dt.files) addFiles(dt.files); });
    fileInput.addEventListener('change', e=> addFiles(e.target.files));
  }

  // Custom select dropdown enhancement
  function enhanceSelect(select){
    if (!select || select.dataset.enhanced==='1') return; 
    select.dataset.enhanced='1';
    const wrap=document.createElement('div'); 
    wrap.className='selectWrap'; 
    wrap.style.position='relative';
    const button=document.createElement('button'); 
    button.type='button'; 
    button.className='selectButton'; 
    button.setAttribute('aria-haspopup','listbox'); 
    button.setAttribute('aria-expanded','false');
    const labelSpan=document.createElement('span'); 
    labelSpan.className='selectLabel'; 
    labelSpan.textContent=select.options[select.selectedIndex]?.text || select.options[0]?.text || 'Select'; 
    button.appendChild(labelSpan);
    const chev=document.createElement('span'); 
    chev.className='selectChevron'; 
    chev.innerHTML="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='18' height='18' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>"; 
    button.appendChild(chev);
    const list=document.createElement('ul'); 
    list.className='selectList'; 
    list.setAttribute('role','listbox'); 
    list.tabIndex=-1;
    Array.from(select.options).forEach((opt)=>{ 
      const li=document.createElement('li'); 
      li.className='selectOption'; 
      li.setAttribute('role','option'); 
      li.dataset.value=opt.value; 
      li.textContent=opt.text; 
      if(opt.disabled){ li.setAttribute('aria-disabled','true'); li.classList.add('isDisabled'); } 
      if(opt.selected){ li.setAttribute('aria-selected','true'); li.classList.add('isSelected'); } 
      li.addEventListener('click',()=>{ 
        if(opt.disabled) return; 
        select.value=opt.value; 
        select.dispatchEvent(new Event('change')); 
        labelSpan.textContent=opt.text; 
        list.querySelectorAll('.selectOption').forEach(o=>{ o.classList.remove('isSelected'); o.setAttribute('aria-selected','false'); }); 
        li.classList.add('isSelected'); 
        li.setAttribute('aria-selected','true'); 
        closeList(); 
      }); 
      list.appendChild(li); 
    });
    function openList(){ wrap.classList.add('open'); button.setAttribute('aria-expanded','true'); list.focus(); }
    function closeList(){ wrap.classList.remove('open'); button.setAttribute('aria-expanded','false'); }
    button.addEventListener('click', (e)=>{ e.stopPropagation(); if(wrap.classList.contains('open')) closeList(); else openList(); });
    document.addEventListener('click', (e)=>{ if(!wrap.contains(e.target)) closeList(); });
    select.classList.add('selectHidden'); 
    const parent=select.parentNode; 
    parent.insertBefore(wrap, select); 
    wrap.appendChild(select); 
    wrap.appendChild(button); 
    wrap.appendChild(list);
  }

  // Sync main category based on subcategory selection
  const subcat = document.getElementById('subcategory');
  const mainCatInput = document.getElementById('mainCategory');
  if (subcat && mainCatInput) {
    const syncMain = () => {
      const opt = subcat.options[subcat.selectedIndex];
      const main = opt ? (opt.getAttribute('data-main') || '') : '';
      mainCatInput.value = main;
    };
    subcat.addEventListener('change', syncMain);
    syncMain();
    const form = document.getElementById('ticketForm');
    if (form) { form.addEventListener('submit', () => { syncMain(); }); }
  }

  enhanceSelect(document.getElementById('subcategory'));
})();
