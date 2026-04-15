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
        // Re run when toggling role to staff
        window.showForm = new Proxy(window.showForm, {
                apply(target, thisArg, args){
                        const result = Reflect.apply(target, thisArg, args);
                        setTimeout(tryEnhance, 0);
                        return result;
                }
        });
})();

function showError(input, message) {
        input.classList.add('inputError');
        let err = input.parentNode.querySelector('.fieldError');
        if (!err) {
                err = document.createElement('span');
                err.className = 'fieldError';
                input.after(err);
        }
        err.textContent = message;
}

function clearError(input) {
        input.classList.remove('inputError');
        const err = input.parentNode.querySelector('.fieldError');
        if (err) err.remove();
}

document.querySelectorAll('.form-container form').forEach(form => {
        form.addEventListener('submit', function(e) {
                let valid = true;

                this.querySelectorAll('input, select').forEach(field => clearError(field));

                const role = this.querySelector('input[name="role"]').value;
                const fullName = this.querySelector('input[name="fullName"]');
                const email = this.querySelector('input[name="email"]');
                const password = this.querySelector('input[name="password"]');
                const confirmPassword = this.querySelector('input[name="confirmPassword"]');

                if (!fullName.value.trim()) {
                        showError(fullName, 'Full Name is required');
                        valid = false;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim()) {
                        showError(email, 'Email is required');
                        valid = false;
                } else if (!emailRegex.test(email.value.trim())) {
                        showError(email, 'Invalid email format');
                        valid = false;
                }

                if (!password.value) {
                        showError(password, 'Password is required');
                        valid = false;
                } else if (password.value.length < 8) {
                        showError(password, 'Password must be at least 8 characters');
                        valid = false;
                }

                if (password.value !== confirmPassword.value) {
                        showError(confirmPassword, 'Passwords do not match');
                        valid = false;
                }

                if (role === 'student') {
                   const reg = this.querySelector('[name="regNumber"]');
                   if (!reg.value.trim()) { showError(reg, 'Registration number is required.'); valid = false; }
                }

                if (role === 'lecturer') {
                    const dept = this.querySelector('[name="department"]');
                    if (!dept.value.trim()) { showError(dept, 'Department is required.'); valid = false; }
                }

                if (role === 'staff') {
                    const desig = this.querySelector('[name="designation"]');
                    if (!desig.value) { showError(desig, 'Please select a designation.'); valid = false; }
                }

                if (!valid) e.preventDefault();
        })
})

document.querySelectorAll('input, select').forEach(field => {
    field.addEventListener('input', () => clearError(field));
});

// Setup password visibility toggles
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('input[type="password"]').forEach(function(pwInput) {
        const wrapper = document.createElement('div');
        wrapper.className = 'password-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.width = '100%';
        wrapper.style.display = 'flex';
        
        pwInput.parentNode.insertBefore(wrapper, pwInput);
        wrapper.appendChild(pwInput);
        
        pwInput.style.width = '100%';
        pwInput.style.paddingRight = '40px';
        
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'toggle-password-btn';
        toggleBtn.style.position = 'absolute';
        toggleBtn.style.right = '10px';
        toggleBtn.style.top = '50%';
        toggleBtn.style.transform = 'translateY(-50%)';
        toggleBtn.style.background = 'none';
        toggleBtn.style.border = 'none';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.padding = '0';
        toggleBtn.style.display = 'flex';
        toggleBtn.style.alignItems = 'center';
        toggleBtn.style.justifyContent = 'center';
        toggleBtn.style.color = '#666';
        
        const eyeSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        const eyeOffSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        
        toggleBtn.innerHTML = eyeSvg;
        
        toggleBtn.addEventListener('click', function() {
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                toggleBtn.innerHTML = eyeOffSvg;
            } else {
                pwInput.type = 'password';
                toggleBtn.innerHTML = eyeSvg;
            }
        });
        
        wrapper.appendChild(toggleBtn);
    });
});