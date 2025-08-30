<link rel="stylesheet" href="/public/css/global/components.css">
<link rel="stylesheet" href="/public/css/register/register.css">
<div class="registerContainer">
    <div class="registerBox">
        <div class="registerRight">
            <h2 class="titleText">Register for UCSC Help Desk</h2>
            <p class="paraText">Already have an account? <a href="/login">Sign in</a></p>
            <?php if (!empty($flash)): ?>
                <div class="alert <?= htmlspecialchars($flash['type']) ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>
            <div class="role-buttons">
                <button id="btn-student" class="btnSecondary btnSecondaryText active-btn" onclick="showForm('student', this)">Student</button>
                <button class="btnSecondary btnSecondaryText" onclick="showForm('lecturer', this)">Lecturer</button>
                <button class="btnSecondary btnSecondaryText" onclick="showForm('staff', this)">Staff</button>
                <button class="btnSecondary btnSecondaryText" onclick="showForm('counselor', this)">Counselor</button>
            </div>
            <div id="student" class="form-container active">
                <form action="/public/register" method="POST">
                    <input type="hidden" name="role" value="student">
                    <input type="text" name="fullName" placeholder="Full Name" required>
                    <input type="text" name="userName" placeholder="User name" required>
                    <input type="email" name="email" placeholder="Student Email" required>
                    <input type="text" name="regNumber" placeholder="Registration Number" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Student</button>
                    </div>
                </form>
            </div>
            <div id="lecturer" class="form-container">
                <form action="/public/register" method="POST">
                    <input type="hidden" name="role" value="lecturer">
                    <input type="text" name="fullName" placeholder="Full Name" required>
                    <input type="text" name="userName" placeholder="User name" required>
                    <input type="email" name="email" placeholder="Lecturer Email" required>
                    <input type="text" name="department" placeholder="Department" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Lecturer</button>
                    </div>
                </form>
            </div>
            <div id="staff" class="form-container">
                <form action="/public/register" method="POST">
                    <input type="hidden" name="role" value="staff">
                    <input type="text" name="fullName" placeholder="Full Name" required>
                    <input type="text" name="userName" placeholder="User name" required>
                    <input type="email" name="email" placeholder="Staff Email" required>
                    <input type="text" name="staffId" placeholder="Staff ID" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Staff</button>
                    </div>
                </form>
            </div>
            <div id="counselor" class="form-container">
                <form action="/public/register" method="POST">
                    <input type="hidden" name="role" value="counselor">
                    <input type="text" name="fullName" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Counselor Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Counselor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/js/register/register.js"></script>
