<main>
  <div class="fullPage">
    <div class="pageLayout">
      <section class="settingsRight">
        <div class=" welcomeCard">
              <?php
                $fullName = trim($_SESSION['user']['name'] ?? '');
                $firstName = '';
                if ($fullName !== '') {
                  $parts = preg_split('/\s+/', $fullName);
                  $firstName = $parts[0] ?? '';
                }
                if ($firstName === '') { $firstName = 'Staff'; }
              ?>
              <h2>Welcome Back, <?= htmlspecialchars($firstName) ?>!</h2>
              <p>Here’s what’s happening with your assigned tickets and recent activity</p>
            </div>

            <div class="sectionCard">
              <h3 class="sectionCardTitle" style="margin-bottom: 24px;">Your Stats</h3>
              <div class="quickActions staff-stats-container">
                <div class="quickActionItem staff-stat-item">
                  <div class="staff-stat-number staff-stat-total"><?= $stats['total'] ?? 0 ?></div>
                  <div class="staff-stat-label">Total Tickets</div>
                </div>
                <div class="quickActionItem staff-stat-item">
                  <div class="staff-stat-number staff-stat-pending"><?= $stats['pending'] ?? 0 ?></div>
                  <div class="staff-stat-label">Pending</div>
                </div>
                <div class="quickActionItem staff-stat-item">
                  <div class="staff-stat-number staff-stat-assigned"><?= $stats['assigned'] ?? 0 ?></div>
                  <div class="staff-stat-label">Assigned</div>
                </div>
                <div class="quickActionItem staff-stat-item">
                  <div class="staff-stat-number staff-stat-resolved"><?= $stats['resolved'] ?? 0 ?></div>
                  <div class="staff-stat-label">Resolved</div>
                </div>
              </div>
            </div>

            <div class="dashboardGrid">
              <div class="col colLeft">
                <div class="activeTickets sectionCard">
                  <h3 class="sectionCardTitle">Recent Assigned Tickets</h3>
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
                      
                      $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>'; 
                    ?>
                      <a href="/staff/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="activeTicketItem">
                      <div class="reqIcon">
                        <?= $iconSvg ?>
                      </div>
                      <div class="reqCardBody">
                        <div class="reqCardHeader">
                          <h4 class="reqTitle"><?= htmlspecialchars($t['title']) ?></h4>
                          <div class="reqStatus <?= $statusBadgeClass ?>"><?= htmlspecialchars($status) ?></div>
                        </div>
                        <p class="reqMeta">Ticket #<?= htmlspecialchars($t['ticket_id']) ?> &bull; Student: <?= htmlspecialchars($t['student_name'] ?? 'Unknown') ?></p>
                        <div class="reqProgress">
                          <div class="reqProgressBar" style="width: <?= $progress ?>%;"></div>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="mutedText sectionCardEmpty">No assigned tickets at the moment.</p>
                <?php endif; ?>
              </div>

              <a href="/staff/tickets" class="sectionCardBtn">
                View All Assigned Tickets
              </a>
            </div>
            
          </div>

          <div class="col colRight">
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
                    <a href="/staff/staffCalender" class="calendarEventItem">
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

              <a href="/staff/staffCalender" class="sectionCardBtn">
                Browse Full Calendar
              </a>
            </div>

            <div class="announcements sectionCard">
              <h3 class="sectionCardTitle">Recent Announcements</h3>
              <?php if (!empty($recentAnnouncements)): ?>
                <div class="sectionCardList">
                  <?php foreach ($recentAnnouncements as $a): ?>
                    <a href="/staff/anView?id=<?= (int)($a['id'] ?? 0) ?>" class="announcementItem">
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

              <a href="/staff/announcements" class="sectionCardBtn">
                View All Announcements
              </a>
            </div>

          </div>
        </div>
      </section>
    </div>
  </div>
</main>
