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
            if ($firstName === '') { $firstName = 'Counselor'; }
          ?>
          <h2>Welcome Back, <?= htmlspecialchars($firstName) ?>!</h2>
          <p>Here’s an overview of your active tasks and recent counseling activities</p>
        </div>

        <div class="sectionCard">
          <h3 class="sectionCardTitle" style="margin-bottom: 24px;">Your Stats</h3>
          <div class="quickActions staff-stats-container">
            <div class="quickActionItem staff-stat-item">
              <div class="staff-stat-number staff-stat-total"><?= $cards['open'] ?? 0 ?></div>
              <div class="staff-stat-label">Open Tickets</div>
            </div>
            <div class="quickActionItem staff-stat-item">
              <div class="staff-stat-number staff-stat-assigned"><?= $cards['assigned'] ?? 0 ?></div>
              <div class="staff-stat-label">Assigned to You</div>
            </div>
            <div class="quickActionItem staff-stat-item">
              <div class="staff-stat-number staff-stat-pending"><?= $cards['meetings'] ?? 0 ?></div>
              <div class="staff-stat-label">Meeting Requests</div>
            </div>
            <div class="quickActionItem staff-stat-item">
              <div class="staff-stat-number staff-stat-resolved"><?= $cards['resolvedByYou'] ?? 0 ?></div>
              <div class="staff-stat-label">Resolved by You</div>
            </div>
          </div>
        </div>

        <div class="dashboardGrid">
          <div class="col colLeft">
            
            <div class="activeTickets sectionCard">
              <h3 class="sectionCardTitle">Recently Assigned to You</h3>
              <div class="sectionCardList">
                <?php if (!empty($recentAssigned)): ?>
                  <?php foreach ($recentAssigned as $t): ?>
                    <?php
                      $statusStr = strtolower($t['status']);
                      $status = strtoupper($t['status']);
                      $statusBadgeClass = in_array($statusStr, ['resolved', 'closed', 'agent-closed']) ? 'resolved' : (in_array($statusStr, ['open', 'pending', 'submitted']) ? 'open' : 'inProgress');
                      
                      if (in_array($statusStr, ['resolved', 'closed', 'agent-closed'])) {
                          $progress = 100;
                      } elseif (in_array($statusStr, ['under review', 'in progress'])) {
                          $progress = 75;
                      } elseif (in_array($statusStr, ['agent assigned', 'assigned to staff', 'assigned'])) {
                          $progress = 50;
                      } else {
                          $progress = 25;
                      }
                      
                      $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>'; 
                    ?>
                    <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="activeTicketItem">
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
              <a href="/counselor/tickets" class="sectionCardBtn">View Your Tickets</a>
            </div>

            <div class="activeTickets sectionCard">
              <h3 class="sectionCardTitle">All Counseling Tickets</h3>
              <div class="sectionCardList">
                <?php if (!empty($allCounseling)): ?>
                  <?php foreach ($allCounseling as $t): ?>
                    <?php
                      $statusStr = strtolower($t['status']);
                      $status = strtoupper($t['status']);
                      $statusBadgeClass = in_array($statusStr, ['resolved', 'closed', 'agent-closed']) ? 'resolved' : (in_array($statusStr, ['open', 'pending', 'submitted']) ? 'open' : 'inProgress');
                      
                      if (in_array($statusStr, ['resolved', 'closed', 'agent-closed'])) {
                          $progress = 100;
                      } elseif (in_array($statusStr, ['under review', 'in progress'])) {
                          $progress = 75;
                      } elseif (in_array($statusStr, ['agent assigned', 'assigned to staff', 'assigned'])) {
                          $progress = 50;
                      } else {
                          $progress = 25;
                      }
                      
                      $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M447-80q-15 0-30-6t-27-18L104-390q-12-12-17.5-26.5T81-446q0-15 5.5-30t17.5-27l352-353q11-11 26-17.5t31-6.5h287q33 0 56.5 23.5T880-800v287q0 16-6 30.5T857-457L504-104q-12 12-27 18t-30 6Zm253-560q25 0 42.5-17.5T760-700q0-25-17.5-42.5T700-760q-25 0-42.5 17.5T640-700q0 25 17.5 42.5T700-640Z"/></svg>'; 
                      
                      $assignBadge = $t['is_assigned'] ? '<span class="reqStatus inProgress" style="background:#e0f2fe; color:#0e7490;">Assigned</span>' : '<span class="reqStatus pending" style="background:#fef9c3; color:#a16207;">Unassigned</span>';
                    ?>
                    <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="activeTicketItem">
                      <div class="reqIcon">
                        <?= $iconSvg ?>
                      </div>
                      <div class="reqCardBody">
                        <div class="reqCardHeader">
                          <h4 class="reqTitle"><?= htmlspecialchars($t['title']) ?></h4>
                          <div style="display:flex; gap:8px;">
                              <?= $assignBadge ?>
                              <div class="reqStatus <?= $statusBadgeClass ?>"><?= htmlspecialchars($status) ?></div>
                          </div>
                        </div>
                        <p class="reqMeta">Ticket #<?= htmlspecialchars($t['ticket_id']) ?> &bull; Student: <?= htmlspecialchars($t['student_name'] ?? 'Unknown') ?></p>
                        <div class="reqProgress">
                          <div class="reqProgressBar" style="width: <?= $progress ?>%;"></div>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="mutedText sectionCardEmpty">No counseling tickets found.</p>
                <?php endif; ?>
              </div>
              <a href="/counselor/tickets" class="sectionCardBtn">View All Counseling Tickets</a>
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
                    <a href="/counselor/calender" class="calendarEventItem">
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
              <a href="/counselor/calender" class="sectionCardBtn">Browse Full Calendar</a>
            </div>

            <div class="announcements sectionCard">
              <h3 class="sectionCardTitle">Meeting Requests</h3>
              <?php if (!empty($meetingTickets)): ?>
                <div class="sectionCardList">
                  <?php foreach ($meetingTickets as $t): ?>
                    <?php
                       $meetStatus = strtolower((string)($t['meeting_requested'] ?? ''));
                       $statusLabel = ucfirst($meetStatus);
                    ?>
                    <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" class="announcementItem">
                      <div class="announcementIconWrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
                      </div>
                      <div class="announcementContent">
                        <div class="announcementTopic">TKT-<?= (int)$t['ticket_id'] ?>: <?= htmlspecialchars($t['title']) ?></div>
                        <div class="announcementSnippet">
                          Student: <?= htmlspecialchars($t['student_name'] ?? 'Unknown') ?>
                          &bull; <span style="font-weight:600;"><?= $statusLabel ?></span>
                        </div>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="mutedText sectionCardEmpty">No meeting requests right now.</p>
              <?php endif; ?>

              <a href="/counselor/meeting" class="sectionCardBtn">
                View All Meetings
              </a>
            </div>

          </div>
        </div>
      </section>
    </div>
  </div>
</main>