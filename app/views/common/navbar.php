<aside class="sidenav">
  <?php
    // Determine role-based routes
    $sessionUser = $_SESSION['user'] ?? null;
    $role = strtolower($sessionUser['role'] ?? 'guest');
    
    // Define navigation items per role with icons
    $navItems = [
      'admin' => [
        ['href' => '/admin/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/admin/tickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/admin/users', 'label' => 'Users', 'icon' => 'users'],
        ['href' => '/admin/faqs', 'label' => 'FAQs', 'icon' => 'faq'],
        ['href' => '/admin/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/admin/forum', 'label' => 'Forum', 'icon' => 'forum'],
      ],
      'student' => [
        ['href' => '/student/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/student/tickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/student/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/student/forum', 'label' => 'Forum', 'icon' => 'forum'],
        ['href' => '/student/FAQ', 'label' => 'FAQs', 'icon' => 'faq'],
        ['href' => '/student/ticket', 'label' => 'New Ticket', 'icon' => 'plus', 'highlight' => true],
      ],
      'staff' => [
        ['href' => '/staff/staffDashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/staff/staffTickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/staff/staffFAQ', 'label' => 'FAQs', 'icon' => 'faq'],
        ['href' => '/staff/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/staff/staffForum', 'label' => 'Forum', 'icon' => 'forum'],
        ['href' => '/staff/staffKB', 'label' => 'Knowledge Base', 'icon' => 'knowledgebase'],
        ['href' => '/staff/staffAnnoucements', 'label' => 'Announcement', 'icon' => 'announcements'],
        ['href' => '/staff/staffTemplate', 'label' => 'Templates', 'icon' => 'template'],
        
      ],
      'counselor' => [
        ['href' => '/counselor/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/counselor/tickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/counselor/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/counselor/Forum', 'label' => 'Forum', 'icon' => 'forum'],
        ['href' => '/counselor/Reports', 'label' => 'Reports', 'icon' => 'reports'],
      ],
      'lecturer' => [
        ['href' => '/lecturer/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/admin/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
      ],
      'guest' => [
        ['href' => '/guest/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
      ],
    ];

    // Icon SVG definitions
    $icons = [
      'dashboard' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
      'ticket' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5v2"></path><path d="M15 11v2"></path><path d="M15 17v2"></path><path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V7a2 2 0 0 1 2-2z"></path></svg>',
      'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
      'forum' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
      'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
      'faq' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
      'plus' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
      'knowledgebase' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h12m0 2h-5v10l-2.5-2.25L8 12V4H6v16h12V4z"></path></svg>',
      'announcements' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h1v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h7l5 4v-4m0-4v-4m-9.5 4c-1.53-.75-2.5-2.3-2.5-4 0-1.7 1-3.25 2.5-4"></path></svg>',
      'template' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10,9 9,9 8,9"></polyline></svg>',
    ];

    // Get navigation items for current role
    $currentNavItems = $navItems[$role] ?? $navItems['guest'];

    // Determine current path for active link highlighting
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $isActive = function (string $href) use ($currentPath): bool {
      if (!$href || $href === '#') return false;
      return strpos($currentPath, $href) === 0;
    };

    $displayName = $sessionUser['name'] ?? 'Guest';
    $displayEmail = $sessionUser['email'] ?? '';
    $displayRole = isset($sessionUser['role']) ? ucfirst($sessionUser['role']) : '';
    $displayProfile = $sessionUser['profile_url'] ?? '';
  ?>

  <!-- Logo / Brand -->
  <div class="sidenavBrand">
    <img src="/assets/logo.svg" alt="UCSC Logo" class="sidenavLogo" />
    <span class="sidenavTitle">UCSC HelpDesk</span>
  </div>

  <!-- Navigation Links -->
  <nav class="sidenavMenu">
    <?php foreach ($currentNavItems as $item): 
      $href = $item['href'];
      $label = $item['label'];
      $icon = $icons[$item['icon']] ?? '';
      $active = $isActive($href);
      $highlight = $item['highlight'] ?? false;
      $classes = 'sidenavLink';
      if ($active) $classes .= ' active';
      if ($highlight) $classes .= ' sidenavNewTicket';
    ?>
    <a href="<?= htmlspecialchars($href) ?>" class="<?= $classes ?>">
      <?= $icon ?>
      <span><?= htmlspecialchars($label) ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- Bottom Section: Profile -->
  <div class="sidenavBottom">
    <div class="sidenavProfile">
      <?= $displayProfile ? '<img src="' . htmlspecialchars($displayProfile) . '" alt="Profile" class="sidenavProfileImg" />' : '<img src="/assets/profile.svg" alt="Profile" class="sidenavProfileImg" />' ?>
      <div class="sidenavProfileInfo">
        <span class="sidenavProfileName"><?= htmlspecialchars($displayName) ?></span>
        <?php if ($displayRole): ?>
        <span class="sidenavProfileRole"><?= htmlspecialchars($displayRole) ?></span>
        <?php endif; ?>
      </div>
      <div class="sidenavProfileDropdown">
        <div class="sidenavDropdownContent">
          <div class="sidenavUserInfo">
            <div class="sidenavProfilePicture"></div>
            <p class="sidenavUserName"><?= htmlspecialchars($displayName) ?></p>
            <?php if ($displayEmail): ?>
            <p class="sidenavUserEmail"><?= htmlspecialchars($displayEmail) ?></p>
            <?php endif; ?>
            <?php if ($displayRole): ?>
            <p class="sidenavUserRole"><?= htmlspecialchars($displayRole) ?></p>
            <?php endif; ?>
          </div>
          <a href="#">Help / FAQ</a>
          <a href="/settings">Settings</a>
          <div class="<?= $sessionUser ? 'sidenavLogout' : 'sidenavLogin' ?>">
            <a href="<?= $sessionUser ? '/auth/logout' : '/auth/login' ?>">
              <?= $sessionUser ? 'Log out' : 'Log in' ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const profile = document.querySelector('.sidenavProfile');
  const dropdown = document.querySelector('.sidenavProfileDropdown');

  profile.addEventListener('click', function (e) {
    e.stopPropagation(); 
    dropdown.classList.toggle('open');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function () {
    dropdown.classList.remove('open');
  });
});
</script>
