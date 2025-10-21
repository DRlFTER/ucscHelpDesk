<link rel="stylesheet" href="/css/global/components.css">
<link rel="stylesheet" href="/css/register/register.css">
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
                <form action="/register" method="POST">
                    <input type="hidden" name="role" value="student">
            <input type="text" name="fullName" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Student Email" required>
            <input type="text" name="regNumber" placeholder="Registration Number / Year" required>
            <input type="text" name="number" placeholder="Phone Number (optional)">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Student</button>
                    </div>
                </form>
            </div>
            <div id="lecturer" class="form-container">
                <form action="/register" method="POST">
                    <input type="hidden" name="role" value="lecturer">
            <input type="text" name="fullName" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Lecturer Email" required>
            <input type="text" name="department" placeholder="Department / Designation" required>
            <input type="text" name="number" placeholder="Phone Number (optional)">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Lecturer</button>
                    </div>
                </form>
            </div>
            <div id="staff" class="form-container">
                <form action="/register" method="POST">
                    <input type="hidden" name="role" value="staff">
            <input type="text" name="fullName" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Staff Email" required>
            <select id="staffDesignation" name="designation" required>
                <option value="" disabled selected>Select Department</option>
                <option value="1">General Administration</option>
                <option value="2">Establishment</option>
                <option value="3">Academic Publication and Welfare</option>
                <option value="4">Postgraduate Research and Project</option>
                <option value="5">Examination and Registration</option>
                <option value="6">Engineering</option>
                <option value="7">Finance</option>
                <option value="8">Library</option>
                <option value="9">CSC and NOC</option>
            </select>
            <input type="text" name="number" placeholder="Phone Number (optional)">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Staff</button>
                    </div>
                </form>
            </div>
            <div id="counselor" class="form-container">
                <form action="/register" method="POST">
                    <input type="hidden" name="role" value="counselor">
                    <input type="text" name="fullName" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Counselor Email" required>
            <input type="text" name="number" placeholder="Phone Number (optional)">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <div class="btnHolder">
                        <button class="btnPrimary btnPrimaryText" type="submit">Register as Counselor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/js/register/register.js"></script>
