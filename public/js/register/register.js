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