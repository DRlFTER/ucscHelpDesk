<main>
  <div class="dashboardContainer">
    <div class="dashboardContent">
      <div class="navMenu">
        <div class="sideNav">
          <a href="#">Dashboard</a>
          <a href="/student/announcements">Announcements</a>
          <a href="/student/faq">FAQs</a>
          <a href="/student/knowledgebase">Knowledge Base</a>
          <a href="/student/lostfound">Lost &amp; Found</a>
        </div>
      </div>
      <div class="dashboardColumnOne">
        <div class="sectioncard welcomeCard">
          <?php
            $fullName = trim($_SESSION['user']['name'] ?? '');
            $firstName = '';
            if ($fullName !== '') {
              $parts = preg_split('/\s+/', $fullName);
              $firstName = $parts[0] ?? '';
            }
            if ($firstName === '') { $firstName = 'Student'; }
          ?>
          <h2>Welcome Back, <?= htmlspecialchars($firstName) ?>!</h2>
          <p>Here’s what’s happening with your support requests</p>
          <div class="ticket-info">
            <?php
              $openCount = isset($openCount) ? (int)$openCount : 0;
              $lastActivity = $lastActivity ?? null;
            ?>
            <span><?= $openCount ?> Open Ticket<?= $openCount === 1 ? '' : 's' ?></span>
            <span>Last Activity: <?= $lastActivity ? htmlspecialchars(time_ago($lastActivity)) : '—' ?></span>
          </div>
          </div>
          <div class="sectionCard">
            <h3>Quick Actions</h3>
            <div class="quickActions">
            <a href="/student/ticket" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg></div>
            </div>New Ticket</a>
            <a href="/student/faq" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
              </div>
              </div>View FAQs</a>
            <a href="/student/newForum" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-160v-600q0-33 23.5-56.5T200-840h480q33 0 56.5 23.5T760-760v203q-10-2-20-2.5t-20-.5q-10 0-20 .5t-20 2.5v-203H200v400h283q-2 10-2.5 20t-.5 20q0 10 .5 20t2.5 20H240L120-160Zm160-440h320v-80H280v80Zm0 160h200v-80H280v80Zm400 280v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM200-360v-400 400Z"/></svg>
              </div>
              </div>New Post</a>
            <a href="#" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M680-80v-120H560v-80h120v-120h80v120h120v80H760v120h-80Zm-480-80q-33 0-56.5-23.5T120-240v-480q0-33 23.5-56.5T200-800h40v-80h80v80h240v-80h80v80h40q33 0 56.5 23.5T760-720v244q-20-3-40-3t-40 3v-84H200v320h280q0 20 3 40t11 40H200Zm0-480h480v-80H200v80Zm0 0v-80 80Z"/></svg>
              </div>
            </div>New Event</a>
            <a href="/student/newLostItem" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Zm-40-60v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z"/></svg>
              </div>
            </div>Report Lost Item</a>
            
          </div>
        </div>
  <div class="knowledgeBase sectionCard clickable" role="link" tabindex="0" onclick="window.location.href='/student/knowledgebase'" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href='/student/knowledgebase';}">
          <h3>Knowledge Base</h3>
          <div class="kbSearch">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search FAQs, forums, and help articles..." />
          </div>
        </div>
          <div class="recentTickets sectionCard">
            <h3>Recent Tickets</h3>
            <?php if (!empty($recentTickets)): ?>
              <?php foreach ($recentTickets as $t): ?>
                <a href="/student/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="ticket">
                  <div class="ticketDetails">
                    <p><span class="ticketTitle"><?= htmlspecialchars($t['title']) ?></span></p>
                    <div class="ticketMeta">
                      <span class="ticketCategory">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>
                        <?= htmlspecialchars($t['category']) ?>
                      </span>
                      <span class="ticketTimestamp">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
                        <?= htmlspecialchars(time_ago($t['created_at'])) ?>
                      </span>
                    </div>
                  </div>
                  <?php
                    $status = strtolower($t['status']);
                    $statusClass = $status === 'resolved' ? 'resolved' : ($status === 'open' ? 'open' : 'inProgress');
                  ?>
                  <span class="status <?= $statusClass ?>"><?php echo ucfirst(htmlspecialchars($t['status'])); ?></span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="mutedText">No recent tickets yet.</p>
            <?php endif; ?>
          </div>
      </div>
      <div class="dashboardColumnTwo">
        <div class="priority sectionCard">
          <h3>Priority</h3>
          <a href="#" class="priorityItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px" fill="currentColor"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
            <div>
              <span class="priorityTitle">Lecture Hall Changed</span><br>
            <span class="priorityDescription">Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00</span>
            </div>
          </a>
        </div>
        <div class="announcements sectionCard">
          <h3>Announcements</h3>
          <?php if (!empty($recentAnnouncements)): ?>
            <?php foreach ($recentAnnouncements as $a): ?>
              <a href="/student/announcement?id=<?= (int)($a['id'] ?? 0) ?>" class="announcement info">
                <svg class="announcementIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill=""><path d="M726.67-446.67v-66.66H880v66.66H726.67ZM776-160l-123.33-92 40-53.33 123.33 92L776-160Zm-81.33-495.33-40-53.34L776-800l40 53.33-121.33 91.34ZM206.67-200v-160h-60Q119-360 99.5-379.5T80-426.67v-106.66Q80-561 99.5-580.5t47.17-19.5H320l200-120v480L320-360h-46.67v160h-66.66ZM560-346v-268q27 24 43.5 58.5T620-480q0 41-16.5 75.5T560-346Z"/></svg>
                <div>
                  <span class="announcementTitle"><?= htmlspecialchars($a['topic'] ?? 'Announcement') ?></span><br>
                  <span class="announcementDescription">
                    <?php 
                      $content = (string)($a['content'] ?? '');
                      $snippet = trim(mb_substr(strip_tags($content), 0, 90));
                      echo htmlspecialchars($snippet . (mb_strlen($content) > 90 ? '…' : ''));
                    ?>
                  </span>
                </div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="mutedText">No announcements yet.</p>
          <?php endif; ?>
        </div>
        <div class="calendar sectionCard">
          <h3>Calendar</h3>
          <a href="#" class="event">
            <div>June 28</div>
            <div>Meeting with Mr. Prasad at W003</div>
            <div>5:00 PM</div>
          </a>
        </div>
        <div class="account sectionCard">
          <h3>Account</h3>
        <div class="accountItems">
          <a href="/settings" class="accountItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/></svg>
            Settings<br>
          </a>
          <a href="/student/tickets" class="accountItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>
            Ticket History<br>
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="/js/student/studentDashboard.js"></script>