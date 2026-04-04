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

            <div class="sectionCard">
              <h3 class="sectionCardTitle" style="margin-bottom: 24px;">Quick Actions</h3>
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

            <div class="activeTickets sectionCard">
              <h3 class="sectionCardTitle">Active Tickets</h3>
              <div class="sectionCardList">
                <?php if (!empty($recentTickets)): ?>
                  <?php foreach ($recentTickets as $t): ?>
                    <?php
                      $statusStr = strtolower($t['status']);
                      $status = strtoupper($t['status']);
                      $statusBadgeClass = in_array($statusStr, ['resolved', 'closed', 'agent-closed']) ? 'resolved' : (in_array($statusStr, ['open', 'pending', 'submitted']) ? 'open' : 'inProgress');
                      
                      // Calculate timeline progress
                      if (in_array($statusStr, ['resolved', 'closed', 'agent-closed'])) {
                          $progress = 100;
                      } elseif (in_array($statusStr, ['under review', 'in progress'])) {
                          $progress = 75;
                      } elseif (in_array($statusStr, ['agent assigned', 'assigned to staff', 'assigned'])) {
                          $progress = 50;
                      } else {
                          $progress = 25; // submitted or pending
                      }
                      
                      // Map category to a suitable icon
                      $cat = $t['category'] ?? '';
                      $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>'; // default
                      if (stripos($cat, 'CSC') !== false || stripos($cat, 'NOC') !== false || stripos($cat, 'Technical') !== false || stripos($cat, 'Wifi') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-83 0-141.5-58.5T280-600q0-16 2.5-31.5T290-662l190 190 190-190q7 15 9.5 30.5T680-600q0 83-58.5 141.5T480-400Zm-248 10-67-67q129-129 315-129t315 129l-67 67q-101-100-248-100T232-390Zm-114 113L51-344q178-176 429-176t429 176l-67 67q-150-150-362-150T118-277Z"/></svg>'; 
                      } elseif (stripos($cat, 'Finance') !== false || stripos($cat, 'Payment') !== false || stripos($cat, 'Welfare') !== false || stripos($cat, 'Bursary') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-440v-80h160v-80h-80v-40h-80v40h-40q-17 0-28.5 11.5T360-640v160q0 17 11.5 28.5T400-440h120v80H360v80h80v80ZM80-120v-80h800v80H80Zm160-240v-480h480v480H240Zm80-80h320v-320H320v320Z"/></svg>'; 
                      } elseif (stripos($cat, 'Exam') !== false || stripos($cat, 'Registration') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg>'; 
                      } elseif (stripos($cat, 'Library') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-260q-96 0-184-38t-156-102v320q68 64 156 102t184 38q96 0 184-38t156-102v-320q-68 64-156 102T480-260Zm0 100q-90 0-172.5-31.5T160-280v-480q64 57 147.5 88.5T480-640q90 0 172.5-31.5T800-760v480q-65 57-147.5 88.5T480-160Zm0-180q-96 0-180-32.5T160-460q64-57 148-88T480-580q88 0 172 32t148 88q-60 55-144 87.5T480-340ZM140-120q-24 0-42-18t-18-42v-560q0-24 18-42t42-18h40v80h-40v480q64 57 147.5 88.5T480-560q90 0 172.5-31.5T800-680v-480h-40v-80h40q24 0 42 18t18 42v560q0 24-18 42t-42 18H140Z"/></svg>'; 
                      } elseif (stripos($cat, 'Engineering') !== false || stripos($cat, 'Facilities') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-400l400-320 400 320v400H600v-240H360v240H120Zm80-80h80v-240h400v240h80v-300L480-740 200-500v300Zm280-230q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29Zm0-60q17 0 28.5-11.5T520-530q0-17-11.5-28.5T480-570q-17 0-28.5 11.5T440-530q0 17 11.5 28.5T480-490Z"/></svg>'; 
                      } elseif (stripos($cat, 'Counselling') !== false || stripos($cat, 'Mental') !== false) {
                          $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>'; 
                      }
                    ?>
                    <a href="/student/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="activeTicketItem">
                      <div class="reqIcon">
                        <?= $iconSvg ?>
                      </div>
                      <div class="reqCardBody">
                        <div class="reqCardHeader">
                          <h4 class="reqTitle"><?= htmlspecialchars($t['title']) ?></h4>
                          <div class="reqStatus <?= $statusBadgeClass ?>"><?= htmlspecialchars($status) ?></div>
                        </div>
                        <p class="reqMeta">Ticket #<?= htmlspecialchars($t['ticket_id']) ?> &bull; Submitted <?= htmlspecialchars(time_ago($t['created_at'])) ?></p>
                        <div class="reqProgress">
                          <div class="reqProgressBar" style="width: <?= $progress ?>%;"></div>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="mutedText sectionCardEmpty">No active tickets yet.</p>
                <?php endif; ?>
              </div>

              <a href="/student/tickets" class="sectionCardBtn">
                View All Tickets
              </a>
            </div>

                        
            <div class="knowledgeBase">
              <div class="kbCarousel">
                <a href="/student/knowledgebase" class="kbCard" style="background-image: url('/assets/imgs/kb/hanbook.jpg');">
                  <div class="kbCardContent">
                    <div class="kbCardText">
                      <span class="kbCardSubtitle">RESOURCE</span>
                      <h4 class="kbCardTitle">Student Handbook</h4>
                    </div>
                    <div class="kbCardBtn">Read Guide</div>
                  </div>
                </a>
                <a href="/student/knowledgebase" class="kbCard" style="background-image: url('/assets/imgs/kb/map.png');">
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
            <div class="announcements sectionCard">
              <h3 class="sectionCardTitle">Announcements</h3>
              <?php if (!empty($recentAnnouncements)): ?>
                <div class="sectionCardList">
                  <?php foreach ($recentAnnouncements as $a): ?>
                    <a href="/student/announcement?id=<?= (int)($a['id'] ?? 0) ?>" class="announcementItem">
                      <div class="announcementIconWrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="M726.67-446.67v-66.66H880v66.66H726.67ZM776-160l-123.33-92 40-53.33 123.33 92L776-160Zm-81.33-495.33-40-53.34L776-800l40 53.33-121.33 91.34ZM206.67-200v-160h-60Q119-360 99.5-379.5T80-426.67v-106.66Q80-561 99.5-580.5t47.17-19.5H320l200-120v480L320-360h-46.67v160h-66.66ZM560-346v-268q27 24 43.5 58.5T620-480q0 41-16.5 75.5T560-346Z"/></svg>
                      </div>
                      <div class="announcementContent">
                        <div class="announcementTopic"><?= htmlspecialchars($a['topic'] ?? 'Announcement') ?></div>
                        <div class="announcementSnippet">
                          <?php 
                            $content = (string)($a['content'] ?? '');
                            $snippet = trim(mb_substr(strip_tags($content), 0, 65));
                            echo htmlspecialchars($snippet . (mb_strlen($content) > 65 ? '…' : ''));
                          ?>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="mutedText sectionCardEmpty">No announcements yet.</p>
              <?php endif; ?>

              <a href="/student/announcements" class="sectionCardBtn">
                View All Announcements
              </a>
            </div>

            <div class="calendar sectionCard">
              <h3 class="sectionCardTitle">Upcoming Events</h3>
              <?php if (!empty($upcomingEvents)): ?>
                <div class="sectionCardList">
                  <?php foreach ($upcomingEvents as $e): ?>
                    <?php
                        $d = new DateTime($e['event_date']);
                        $month = $d->format('M');
                        $day = $d->format('j');
                        $timeStr = ((int)$e['is_all_day'] === 1) ? 'All Day' : date('g:i A', strtotime($e['start_time']));
                        $location = !empty($e['location']) ? $e['location'] : 'Campus';
                    ?>
                    <a href="/student/calender" class="calendarEventItem">
                      <div class="calendarEventDate">
                        <span class="calendarEventMonth"><?= htmlspecialchars($month) ?></span>
                        <span class="calendarEventDay"><?= htmlspecialchars($day) ?></span>
                      </div>
                      <div class="calendarEventContent">
                        <div class="calendarEventTitle"><?= htmlspecialchars($e['title']) ?></div>
                        <div class="calendarEventMeta"><?= htmlspecialchars($timeStr) ?> &bull; <?= htmlspecialchars($location) ?></div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                 <p class="mutedText sectionCardEmpty">No upcoming events.</p>
              <?php endif; ?>

              <a href="/student/calender" class="sectionCardBtn">
                Browse Full Calendar
              </a>
            </div>

            <div class="account sectionCard">
              <h3 class="sectionCardTitle">Recent Forum Posts</h3>
              <?php if (!empty($recentForumPosts)): ?>
                <div class="sectionCardList">
                  <?php foreach ($recentForumPosts as $post): ?>
                    <a href="/student/forumFull?id=<?= (int)($post['id']) ?>" class="forumPostItem">
                      <div class="forumIconWrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="M240-400h320v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/></svg>
                      </div>
                      <div class="forumContent">
                        <div class="forumTopic"><?= htmlspecialchars($post['title']) ?></div>
                        <div class="forumMeta"><?= htmlspecialchars($post['author'] ?? 'Anonymous') ?> &bull; <?= htmlspecialchars(time_ago($post['created_at'])) ?></div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="mutedText sectionCardEmpty">No recent posts.</p>
              <?php endif; ?>

              <a href="/student/forum" class="sectionCardBtn">
                Browse Forum
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<script src="/js/student/studentDashboard.js"></script>