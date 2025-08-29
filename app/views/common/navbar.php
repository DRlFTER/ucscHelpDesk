<header>
  <nav class="navbar">
    <div class="navbarLeft">
      <img src="/assets/logo.svg" alt="UCSC Logo" class="logo" />
      <span class="navbarBrand">UCSC HelpDesk</span>
    </div>
    <div class="navbarCenter">
      <div class="navbarMenuContainer">
        <a href="#" class="navbarLink active">Dashboard</a>
        <a href="#" class="navbarLink">Calendar</a>
        <a href="#" class="navbarLink">Tickets</a>
        <a href="#" class="navbarLink">Forum</a>
        <div class="btnHolder">
          <button class="navbarNewTicket btnWSvg btnPrimaryText">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
              <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
            </svg>
            <span>New Ticket</span>
          </button>
        </div>
      </div>
    </div>
    <div class="navbarRight">
      <div class="icon notification"></div>
      <?php
        $sessionUser = $_SESSION['user'] ?? null;
        $displayName = $sessionUser['name'] ?? 'Guest';
        $displayEmail = $sessionUser['email'] ?? '';
        $displayRole = isset($sessionUser['role']) ? ucfirst($sessionUser['role']) : '';
      ?>
      <div class="profile">
        <img src="/assets/profile.svg" alt="Profile" />
        <span><?= htmlspecialchars($displayName) ?></span>
        <div class="profileDropdown">
         <div class="dropdownContent">
          <div class="userInfo">
           <div class="profilePicture"></div>
           <p class="name"><?= htmlspecialchars($displayName) ?></p>
           <?php if ($displayEmail): ?>
           <p class="email"><?= htmlspecialchars($displayEmail) ?></p>
           <?php endif; ?>
           <?php if ($displayRole): ?>
           <p class="role"><?= htmlspecialchars($displayRole) ?></p>
           <?php endif; ?>
          </div>
         <a href="#">My Account</a>
         <a href="#">Help / FAQ</a>
         <a href="#">Settings</a>

        <div class="<?= $sessionUser ? 'logout' : 'login' ?>">
          <a href="<?= $sessionUser ? '/auth/logout' : '/auth/login' ?>">
           <?= $sessionUser ? 'Log out' : 'Log in' ?>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
  </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const profile = document.querySelector('.profile');
  const dropdown = document.querySelector('.profileDropdown');

  profile.addEventListener('click', function (e) {
    e.stopPropagation(); 
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function () {
    dropdown.style.display = 'none';
  });
});
</script>
