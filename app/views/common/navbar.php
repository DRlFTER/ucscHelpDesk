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
        ['href' => '/admin/reports', 'label' => 'Reports', 'icon' => 'reports'],
        ['href' => '/admin/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/admin/forum', 'label' => 'Forum', 'icon' => 'forum'],
      ],
      'student' => [
        ['href' => '/student/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/student/tickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/student/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/student/forum', 'label' => 'Forum', 'icon' => 'forum'],
        ['href' => '/student/FAQ', 'label' => 'FAQs', 'icon' => 'faq'],
        ['href' => '/student/knowledgebase', 'label' => 'Knowledge Base', 'icon' => 'knowledgebase'],
        ['href' => '/student/announcements', 'label' => 'Announcements', 'icon' => 'announcements'],
        ['href' => '/student/lostfound', 'label' => 'Lost & Found', 'icon' => 'lostfound'],
        ['href' => '/student/ticket', 'label' => 'New Ticket', 'icon' => 'plus', 'highlight' => true],
      ],
      'staff' => [
        ['href' => '/staff/staffDashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => '/staff/tickets', 'label' => 'Tickets', 'icon' => 'ticket'],
        ['href' => '/staff/staffFAQ', 'label' => 'FAQs', 'icon' => 'faq'],
        ['href' => '/staff/calender', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['href' => '/staff/staffForum', 'label' => 'Forum', 'icon' => 'forum'],
        ['href' => '/staff/staffKB', 'label' => 'Knowledge Base', 'icon' => 'knowledgebase'],
        ['href' => '/staff/staffAnnoucements', 'label' => 'Announcement', 'icon' => 'announcements'],
        ['href' => '/staff/staffTemplate', 'label' => 'Templates', 'icon' => 'template'],
        ['href' => '/staff/staffReports', 'label' => 'Reports', 'icon' => 'reports'],
        
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
      'lostfound' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
      'template' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10,9 9,9 8,9"></polyline></svg>',
      'reports' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
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
    <img src="/assets/imgs/logo.png" alt="UCSC HelpDesk" class="sidenavLogo" />
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

  <!-- Bottom Section: Notification + Profile -->
  <div class="sidenavBottom">
    <div class="sidenavProfile">
      <?= $displayProfile ? '<img src="' . htmlspecialchars($displayProfile) . '" alt="Profile" class="sidenavProfileImg" />' : '<img src="/assets/profile.svg" alt="Profile" class="sidenavProfileImg" />' ?>
      <div class="sidenavProfileInfo">
        <span class="sidenavProfileName"><?= htmlspecialchars($displayName) ?></span>
        <?php if ($displayRole): ?>
        <span class="sidenavProfileRole"><?= htmlspecialchars($displayRole) ?></span>
        <?php endif; ?>
      </div>

      <!-- Notification Bell -->
      <button class="sidenavNotifBtn" id="sidenavNotifBtn" title="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="sidenavNotifBadge" id="sidenavNotifBadge" style="display:none;"></span>
      </button>

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
          <?php
            $faqHref = '#';
            if ($role === 'admin') $faqHref = '/admin/faqs';
            elseif ($role === 'student') $faqHref = '/student/FAQ';
            elseif ($role === 'staff') $faqHref = '/staff/staffFAQ';
            elseif ($role === 'counselor') $faqHref = '/counselor/FAQ';
          ?>
          <a href="<?= $faqHref ?>">Help / FAQ</a>
          <a href="/settings">Settings</a>
          <div class="<?= $sessionUser ? 'sidenavLogout' : 'sidenavLogin' ?>">
            <a href="<?= $sessionUser ? '/auth/logout' : '/auth/login' ?>">
              <?= $sessionUser ? 'Log out' : 'Log in' ?>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Dropdown Panel -->
    <div class="sidenavNotifDropdown" id="sidenavNotifDropdown">
      <div class="sidenavNotifHeader">
        <span class="sidenavNotifTitle">Notifications</span>
        <div class="sidenavNotifActions">
          <button class="sidenavNotifRefreshBtn" id="sidenavNotifRefresh" title="Refresh">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 4 23 10 17 10"></polyline>
              <polyline points="1 20 1 14 7 14"></polyline>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
          </button>
          <button class="sidenavNotifMarkAll" id="sidenavNotifMarkAll">Mark all read</button>
        </div>
      </div>
      <div class="sidenavNotifList" id="sidenavNotifList">
        <div class="sidenavNotifEmpty">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <p>No notifications yet</p>
          <span>You're all caught up!</span>
        </div>
      </div>
    </div>
  </div>
</aside>
<script>
(function() {
  // Notifications
  const POLL_INTERVAL = 2000;
  let _notifCache = [];
  let _lastPollTime = '';
  let _unreadCount = 0;
  let _pollTimer = null;

  // DOM refs
  const profile = document.querySelector('.sidenavProfile');
  const profileDropdown = document.querySelector('.sidenavProfileDropdown');
  const notifBtn = document.getElementById('sidenavNotifBtn');
  const notifDropdown = document.getElementById('sidenavNotifDropdown');
  const notifList = document.getElementById('sidenavNotifList');
  const notifBadge = document.getElementById('sidenavNotifBadge');
  const markAllBtn = document.getElementById('sidenavNotifMarkAll');
  const refreshBtn = document.getElementById('sidenavNotifRefresh');

  // ─── Icon SVGs ───
  const BELL_OUTLINE = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
  const BELL_FILLED = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';

  const NOTIF_ICONS = {
    ticket_message: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
    ticket_status: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    ticket_assigned: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>',
    ticket_response: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>',
    default: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>'
  };

  // Toggle Dropdowns
  profile.addEventListener('click', function (e) {
    e.stopPropagation();
    notifDropdown.classList.remove('open');
    profileDropdown.classList.toggle('open');
  });

  notifBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    profileDropdown.classList.remove('open');
    notifDropdown.classList.toggle('open');
  });

  notifDropdown.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  document.addEventListener('click', function () {
    profileDropdown.classList.remove('open');
    notifDropdown.classList.remove('open');
  });

  // Update Bell Icon
  function updateBellIcon() {
    const svgContainer = notifBtn.querySelector('svg');
    if (!svgContainer) return;
    if (_unreadCount > 0) {
      svgContainer.setAttribute('fill', 'currentColor');
      notifBtn.classList.add('has-unread');
      notifBadge.style.display = 'block';
    } else {
      svgContainer.setAttribute('fill', 'none');
      notifBtn.classList.remove('has-unread');
      notifBadge.style.display = 'none';
    }
  }

  // Render Notifications
  function renderNotifications() {
    if (_notifCache.length === 0) {
      notifList.innerHTML = `
        <div class="sidenavNotifEmpty">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <p>No notifications yet</p>
          <span>You're all caught up!</span>
        </div>`;
      return;
    }

    let html = '';
    _notifCache.forEach(function(n) {
      const iconSvg = NOTIF_ICONS[n.type] || NOTIF_ICONS.default;
      const unreadClass = n.isRead ? '' : ' unread';
      const urlAttr = n.url ? ' data-url="' + n.url + '"' : '';
      html += '<div class="sidenavNotifItem' + unreadClass + '" data-id="' + n.id + '"' + urlAttr + '>'
            + '<div class="sidenavNotifItemIcon">' + iconSvg + '</div>'
            + '<div class="sidenavNotifItemContent">'
            + '<p class="sidenavNotifItemText">' + escapeHtml(n.message) + '</p>'
            + '<span class="sidenavNotifItemTime">' + escapeHtml(n.timeAgo) + '</span>'
            + '</div>'
            + '</div>';
    });
    notifList.innerHTML = html;

    // Attach click handlers
    notifList.querySelectorAll('.sidenavNotifItem').forEach(function(el) {
      el.addEventListener('click', function() {
        const id = parseInt(el.dataset.id);
        const url = el.dataset.url;
        // Mark as read
        if (el.classList.contains('unread')) {
          markRead(id);
          el.classList.remove('unread');
        }
        // Navigate if URL present
        if (url) {
          window.location.href = url;
        }
      });
    });
  }

  // Fetch Notifications
  function fetchNotifications() {
    refreshBtn.classList.add('spinning');
    fetch('/notifications')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.notifications) {
          _notifCache = data.notifications;
          _unreadCount = data.unreadCount || 0;
          _lastPollTime = data.serverTime || '';
          updateBellIcon();
          renderNotifications();
        }
      })
      .catch(function(err) {
        console.error('Notification fetch error:', err);
      })
      .finally(function() {
        refreshBtn.classList.remove('spinning');
      });
  }

  // Poll for New Notifications
  function pollNotifications() {
    const url = _lastPollTime
      ? '/notifications/poll?since=' + encodeURIComponent(_lastPollTime)
      : '/notifications';

    fetch(url)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data.notifications) return;
        _unreadCount = data.unreadCount || 0;
        _lastPollTime = data.serverTime || _lastPollTime;

        if (data.notifications.length > 0) {
          // Prepend new notifications to cache (avoid duplicates)
          const existingIds = new Set(_notifCache.map(function(n) { return n.id; }));
          const newOnes = data.notifications.filter(function(n) { return !existingIds.has(n.id); });
          if (newOnes.length > 0) {
            _notifCache = newOnes.concat(_notifCache);
            // Cap cache at 50
            if (_notifCache.length > 50) _notifCache = _notifCache.slice(0, 50);
            renderNotifications();
          }
        }
        updateBellIcon();
      })
      .catch(function(err) {
        console.error('Notification poll error:', err);
      });
  }

  // Mark Single Read
  function markRead(notifId) {
    fetch('/notifications/markRead', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: notifId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.unreadCount !== undefined) {
        _unreadCount = data.unreadCount;
      }
      // Update cache
      _notifCache.forEach(function(n) {
        if (n.id === notifId) n.isRead = true;
      });
      updateBellIcon();
    })
    .catch(function() {});
  }

  // Mark All Read
  markAllBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    fetch('/notifications/markAllRead', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      _unreadCount = 0;
      _notifCache.forEach(function(n) { n.isRead = true; });
      updateBellIcon();
      renderNotifications();
    })
    .catch(function() {});
  });

  // Refresh Button
  refreshBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    fetchNotifications();
  });

  // Utility
  function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // Init
  document.addEventListener('DOMContentLoaded', function() {
    fetchNotifications();
    _pollTimer = setInterval(pollNotifications, POLL_INTERVAL);
  });

  // Expose for debugging
  window._notifCache = function() { return _notifCache; };
})();
</script>
