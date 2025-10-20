<header>
  <nav class="navbar">
  <?php
    // Determine role-based routes
    $sessionUser = $_SESSION['user'] ?? null;
    $role = strtolower($sessionUser['role'] ?? 'guest');
    $routes = [
      'admin' => [
        'dashboard' => '/admin/dashboard',
        'tickets'   => '/admin/tickets',
        'calender'  => '/admin/calender',
        'forum'     => '/admin/forum',
        'newTicket' => null,
      ],
      'student' => [
        'dashboard' => '/student/dashboard',
        'tickets'   => '/student/tickets',
        'calender'  => '/admin/calender',
        'forum'     => '/student/forum',
        'newTicket' => '/student/ticket',
      ],
      'staff' => [
        // No separate dashboard; use tickets view for landing
        'dashboard' => '/staff/staffTickets',
        'tickets'   => '/staff/staffTickets',
        'calender'  => '/admin/calender',
        'newTicket' => null,
      ],
      'counselor' => [
        'dashboard' => '/counselor/dashboard',
        'tickets'   => null,
        'calender'  => '/admin/calender',
        'newTicket' => null,
      ],
      'lecturer' => [
        'dashboard' => '/lecturer/dashboard',
        'tickets'   => null,
        'calender'  => '/admin/calender',
        'newTicket' => null,
      ],
      'guest' => [
        'dashboard' => '/guest/dashboard',
        'tickets'   => null,
        'calender'  => null,
        'newTicket' => null,
      ],
    ];

  $dashboardHref = $routes[$role]['dashboard'] ?? '#';
  $ticketsHref   = $routes[$role]['tickets']   ?? '#';
  $calenderHref  = $routes[$role]['calender']  ?? '#';
  $forumHref     = $routes[$role]['forum']     ?? '#';
    $newTicketHref = $routes[$role]['newTicket'] ?? null;
    $hasNewTicket  = !empty($newTicketHref);

    // Determine current path for active link highlighting
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $isActive = function (?string $href) use ($currentPath): bool {
      if (!$href || $href === '#') return false;
      return strpos($currentPath, $href) === 0;
    };
  $dashboardActive = $isActive($dashboardHref);
  $ticketsActive   = $isActive($ticketsHref);
  $calenderActive  = $isActive($calenderHref);
  $forumActive     = $isActive($forumHref);

    // Avoid both active if both hrefs are the same (e.g., student)
    if (($routes[$role]['tickets'] ?? null) === ($routes[$role]['dashboard'] ?? null)) {
      $ticketsActive = false;
    }

    // Role-specific tweaks
    if ($role === 'staff') {
      // Mark tickets active on ticket detail routes too
      if (!$ticketsActive && strpos($currentPath, '/staff/ticket') === 0) {
        $ticketsActive = true;
        $dashboardActive = false;
      }
    }
    if ($role === 'student') {
      if (!$ticketsActive && strpos($currentPath, '/student/ticket') === 0) {
        $ticketsActive = true;
        $dashboardActive = false;
      }
      if (!$forumActive && strpos($currentPath, '/student/forum') === 0) {
        $forumActive = true;
        $dashboardActive = false;
      }
    }
  ?>
    <div class="navbarLeft">
      <img src="/assets/logo.svg" alt="UCSC Logo" class="logo" />
      <span class="navbarBrand">UCSC HelpDesk</span>
    </div>
    <div class="navbarCenter">
      <div class="navbarMenuContainer">
  <a href="<?= htmlspecialchars($dashboardHref) ?>" class="navbarLink <?= $dashboardActive ? 'active pointer-events:none' : 'pointer-events:none; opacity:0.5;' ?>">Dashboard</a>
  <a href="<?= $ticketsHref ? htmlspecialchars($ticketsHref) : '#' ?>" class="navbarLink <?= $ticketsActive ? 'active' : '' ?>" style="<?= $ticketsHref ? '' : 'pointer-events:none; opacity:0.5;' ?>">Tickets</a>
  <!-- <a href="<?= $calenderHref ? htmlspecialchars($calenderHref) : '#' ?>" class="navbarLink <?= $calenderActive ? 'active' : '' ?>" style="<?= $calenderHref ? '' : 'pointer-events:none; opacity:0.5;' ?>">Calendar</a> -->
  <a href="<?= $forumHref ? htmlspecialchars($forumHref) : '#' ?>" class="navbarLink <?= $forumActive ? 'active' : '' ?>" style="<?= $forumHref ? '' : 'pointer-events:none; opacity:0.5;' ?>">Forum</a>
        <div class="btnHolder">
          <a href="<?= $hasNewTicket ? htmlspecialchars($newTicketHref) : '#' ?>" class="navbarNewTicket btnWSvg btnPrimaryText" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?= $hasNewTicket ? '' : 'pointer-events:none; opacity:0.5;' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
              <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
            </svg>
            <span>New Ticket</span>
          </a>
        </div>
      </div>
    </div>
    <div class="navbarRight">
      <div class="icon notification"></div>
      <?php
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
         <a href="/settings">Settings</a>

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
