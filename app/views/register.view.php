<div><h2>Register for UCSC Help Desk</h2></div>
<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Role selection -->
<div class="role-buttons">
    <button id="btn-student" class="active-btn" onclick="showForm('student', this)">Student</button>
    <button onclick="showForm('lecturer', this)">Lecturer</button>
    <button onclick="showForm('staff', this)">Staff</button>
    <button onclick="showForm('counselor', this)">Counselor</button>
</div>

<!-- Student Form (default active) -->
<div id="student" class="form-container active">
    <form action="/public/register" method="POST">
        <input type="hidden" name="role" value="student">
        <input type="text" name="fullName" placeholder="Full Name" required>
        <input type="text" name="userName" placeholder="User name" required>
        <input type="email" name="email" placeholder="Student Email" required>
        <input type="text" name="regNumber" placeholder="Registration Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
        <input type="submit" value="Register as Student">
    </form>
</div>

<!-- Lecturer Form -->
<div id="lecturer" class="form-container">
    <form action="/public/register" method="POST">
        <input type="hidden" name="role" value="lecturer">
        <input type="text" name="fullName" placeholder="Full Name" required>
        <input type="text" name="userName" placeholder="User name" required>
        <input type="email" name="email" placeholder="Lecturer Email" required>
        <input type="text" name="department" placeholder="Department" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register as Lecturer">
    </form>
</div>

<!-- Staff Form -->
<div id="staff" class="form-container">
    <form action="/public/register" method="POST">
        <input type="hidden" name="role" value="staff">
        <input type="text" name="fullName" placeholder="Full Name" required>
        <input type="text" name="userName" placeholder="User name" required>
        <input type="email" name="email" placeholder="Staff Email" required>
        <input type="text" name="staffId" placeholder="Staff ID" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register as Staff">
    </form>
</div>

<!-- Counselor Form -->
<div id="counselor" class="form-container">
    <form action="/public/register" method="POST">
        <input type="hidden" name="role" value="counselor">
        <input type="text" name="fullName" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Counselor Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register as Counselor">
    </form>
</div>

<script>
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
</script>
