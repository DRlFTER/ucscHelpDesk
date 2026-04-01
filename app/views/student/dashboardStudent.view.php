<main>
  <div class="fullPage">
    <div class="pageLayout">
      <section class="settingsRight">
        <div class="dashboardGrid">
          <div class="col colLeft">
            <div class=" welcomeCard">
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
            </div>

            <div class="activeTickets">
              <div class="activeTicketsHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h3 style="margin-bottom: 0;">Active Tickets</h3>
                <a href="/student/tickets" style="color: #6a6af5; font-size: 14px; text-decoration: none; font-weight: 300;">View All</a>
              </div>
              <div class="activeTicketsGrid">
                <?php if (!empty($recentTickets)): ?>
                  <?php foreach ($recentTickets as $t): ?>
                    <?php
                      $status = strtoupper($t['status']);
                      $statusBadgeClass = strtolower($t['status']) === 'resolved' ? 'resolved' : (strtolower($t['status']) === 'open' ? 'open' : 'inProgress');
                      // Calculate pseudo-progress
                      $progress = strtolower($t['status']) === 'resolved' ? 100 : (strtolower($t['status']) === 'in progress' ? 50 : 20);
                    ?>
                    <a href="/student/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="activeTicketCard">
                      <div class="reqCardHeader">
                        <div class="reqIcon">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>
                        </div>
                        <div class="reqStatus <?= $statusBadgeClass ?>"><?= htmlspecialchars($status) ?></div>
                      </div>
                      <div class="reqCardBody">
                        <h4 class="reqTitle"><?= htmlspecialchars($t['title']) ?></h4>
                        <p class="reqMeta">Ticket #<?= htmlspecialchars($t['ticket_id']) ?> &bull; Submitted <?= htmlspecialchars(time_ago($t['created_at'])) ?></p>
                      </div>
                      <div class="reqProgress">
                        <div class="reqProgressBar" style="width: <?= $progress ?>%;"></div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="mutedText">No active tickets yet.</p>
                <?php endif; ?>
              </div>
            </div>

            <div class="quickActions">
              <div class="quickActions">
                <a href="/student/ticket" class="quickActionItem">
                  <div class="icon">
                    <div class="quickActionSvg">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                    </div>
                  </div>
                  New Ticket
                </a>
                <a href="/student/newForum" class="quickActionItem">
                  <div class="icon">
                    <div class="quickActionSvg">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-160v-600q0-33 23.5-56.5T200-840h480q33 0 56.5 23.5T760-760v203q-10-2-20-2.5t-20-.5q-10 0-20 .5t-20 2.5v-203H200v400h283q-2 10-2.5 20t-.5 20q0 10 .5 20t2.5 20H240L120-160Zm160-440h320v-80H280v80Zm0 160h200v-80H280v80Zm400 280v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM200-360v-400 400Z"/></svg>
                    </div>
                  </div>
                  New Post
                </a>
                <a href="/student/Calender" class="quickActionItem">
                  <div class="icon">
                    <div class="quickActionSvg">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M680-80v-120H560v-80h120v-120h80v120h120v80H760v120h-80Zm-480-80q-33 0-56.5-23.5T120-240v-480q0-33 23.5-56.5T200-800h40v-80h80v80h240v-80h80v80h40q33 0 56.5 23.5T760-720v244q-20-3-40-3t-40 3v-84H200v320h280q0 20 3 40t11 40H200Zm0-480h480v-80H200v80Zm0 0v-80 80Z"/></svg>
                    </div>
                  </div>
                  New Event
                </a>
                <a href="/student/newLostItem" class="quickActionItem">
                  <div class="icon">
                    <div class="quickActionSvg">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Zm-40-60v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z"/></svg>
                    </div>
                  </div>
                  Report Lost Item
                </a>
              </div>
            </div>
                        
            <div class="knowledgeBase">
              <div class="kbCarousel">
                <a href="/assets/imgs/kb/hanbook.jpg" target="_blank" class="kbCard" style="background-image: url('/assets/imgs/kb/hanbook.jpg');">
                  <div class="kbCardContent">
                    <div class="kbCardText">
                      <span class="kbCardSubtitle">RESOURCE</span>
                      <h4 class="kbCardTitle">Student Handbook</h4>
                    </div>
                    <div class="kbCardBtn">Read Guide</div>
                  </div>
                </a>
                <a href="/assets/imgs/kb/map.png" target="_blank" class="kbCard" style="background-image: url('/assets/imgs/kb/map.png');">
                  <div class="kbCardContent">
                    <div class="kbCardText">
                      <span class="kbCardSubtitle">RESOURCES</span>
                      <h4 class="kbCardTitle">University Map</h4>
                    </div>
                    <div class="kbCardBtn">View Map</div>
                  </div>
                </a>
              </div>
            </div>

          </div>

          <div class="col colRight">
            <div class="card priority sectionCard">
              <h3>Priority</h3>
              <a href="/student/announcements" class="priorityItem">
                <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px" fill="currentColor"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
                <div>
                  <span class="priorityTitle">Lecture Hall Changed</span><br>
                  <span class="priorityDescription">Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00</span>
                </div>
              </a>
            </div>

            <div class="card announcements sectionCard">
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

            <div class="card calendar sectionCard">
              <h3>Calendar</h3>
              <?php if (!empty($upcomingEvents)): ?>
                <?php foreach ($upcomingEvents as $e): ?>
                  <?php
                      $d = new DateTime($e['event_date']);
                      $dateStr = $d->format('M j');
                      $timeStr = ((int)$e['is_all_day'] === 1) ? 'All Day' : date('g:i A', strtotime($e['start_time']));
                  ?>
                  <a href="/student/calender" class="event">
                    <div><?= htmlspecialchars($dateStr) ?></div>
                    <div class="eventTitle"><?= htmlspecialchars($e['title']) ?></div>
                    <div class="eventTime"><?= htmlspecialchars($timeStr) ?></div>
                  </a>
                <?php endforeach; ?>
              <?php else: ?>
                 <p class="mutedText">No upcoming events.</p>
              <?php endif; ?>
            </div>

            <div class="card account sectionCard">
              <h3>Account</h3>
              <div class="accountItems">
                <a href="/settings" class="accountItem">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/></svg>
                  Settings<br>
                </a>
                <a href="/student/tickets" class="accountItem">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114t28.5 140.5q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>
                  Ticket History<br>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<script src="/js/student/studentDashboard.js"></script>