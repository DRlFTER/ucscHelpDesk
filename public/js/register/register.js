function showForm(role, btn) {
        document.querySelectorAll('.form-container').forEach(div => {
                div.classList.remove('active');
        });
        document.querySelectorAll('.role-buttons button').forEach(b => {
            b.classList.remove('active-btn');
        });
        document.getElementById(role).classList.add('active');
        btn.classList.add('active-btn');
}

// Enhance the staff designation select to match New Ticket dropdown styles/behavior
(function(){
        function enhanceSelect(select){
                if (!select || select.dataset.enhanced === '1') return;
                select.dataset.enhanced = '1';
                const wrap = document.createElement('div');
                wrap.className = 'selectWrap';
                wrap.style.position = 'relative';

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'selectButton';
                button.setAttribute('aria-haspopup','listbox');
                button.setAttribute('aria-expanded','false');

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
                list.setAttribute('role','listbox');
                list.tabIndex = -1;

                Array.from(select.options).forEach(opt => {
                        const li = document.createElement('li');
                        li.className = 'selectOption';
                        li.setAttribute('role','option');
                        li.dataset.value = opt.value;
                        li.textContent = opt.text;
                        if (opt.disabled) { li.setAttribute('aria-disabled','true'); li.classList.add('isDisabled'); }
                        if (opt.selected) { li.setAttribute('aria-selected','true'); li.classList.add('isSelected'); }
                        li.addEventListener('click', () => {
                                if (opt.disabled) return;
                                select.value = opt.value;
                                select.dispatchEvent(new Event('change'));
                                labelSpan.textContent = opt.text;
                                list.querySelectorAll('.selectOption').forEach(o => { o.classList.remove('isSelected'); o.setAttribute('aria-selected','false'); });
                                li.classList.add('isSelected');
                                li.setAttribute('aria-selected','true');
                                closeList();
                        });
                        list.appendChild(li);
                });

                function openList(){ wrap.classList.add('open'); button.setAttribute('aria-expanded','true'); list.focus(); }
                function closeList(){ wrap.classList.remove('open'); button.setAttribute('aria-expanded','false'); }

                button.addEventListener('click', (e) => { e.stopPropagation(); if (wrap.classList.contains('open')) closeList(); else openList(); });
                document.addEventListener('click', (e) => { if (!wrap.contains(e.target)) closeList(); });

                select.classList.add('selectHidden');
                const parent = select.parentNode;
                parent.insertBefore(wrap, select);
                wrap.appendChild(select);
                wrap.appendChild(button);
                wrap.appendChild(list);
        }

        // Enhance when staff form is visible, and also on role switch
        function tryEnhance(){
                const select = document.getElementById('staffDesignation');
                if (select) enhanceSelect(select);
        }
        document.addEventListener('DOMContentLoaded', tryEnhance);
        // Re-run when toggling role to staff
        window.showForm = new Proxy(window.showForm, {
                apply(target, thisArg, args){
                        const result = Reflect.apply(target, thisArg, args);
                        setTimeout(tryEnhance, 0);
                        return result;
                }
        });
})();