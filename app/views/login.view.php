<div><h2>Log into UCSC Help Desk</h2></div>
<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
<div><form action="/public/auth/login" method="POST">
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Log In</button>
    </div>
</form></div>