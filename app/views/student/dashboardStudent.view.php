<main>
  <div class="dashboardContainer">

    <div class="dashboardContent">
      <div class="navMenu">
        <div class="sideNav">
          <a href="#">Dashboard</a>
          <a href="#">My Tickets</a>
          <a href="#">FAQs</a>
          <a href="#">Forums</a>
          <a href="#">Calendar</a>
          <a href="#">Settings</a>
        </div>
      </div>
      <div class="dashboardColumnOne">
        <div class="welcomeCard">
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
            <span>2 Open Tickets</span>
            <span>Last Activity: 2 hours ago</span>
          </div>
          </div>
          <div class="sectionCard">
            <h3>Quick Actions</h3>
            <div class="quickActions">
            <a href="/student/ticket" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg></div>
            </div>New Ticket</a>
            <a href="#" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
              </div>
              </div>View FAQs</a>
            <a href="#" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M880-80 720-240H320q-33 0-56.5-23.5T240-320v-40h440q33 0 56.5-23.5T760-440v-280h40q33 0 56.5 23.5T880-640v560ZM160-473l47-47h393v-280H160v327ZM80-280v-520q0-33 23.5-56.5T160-880h440q33 0 56.5 23.5T680-800v280q0 33-23.5 56.5T600-440H240L80-280Zm80-240v-280 280Z"/></svg>
              </div>
              </div>Forums</a>
            <a href="#" class="quickActionItem"><div class="icon">
              <div class="quickActionSvg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M580-240q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-880h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
              </div>
            </div>Appointments</a>
          </div>
        </div>
        <div class="knowledgeBase sectionCard">
          <h3>Knowledge Base</h3>
          <input type="text" placeholder="Search FAQs, forums, and help articles..." />
        </div>
          <div class="recentTickets sectionCard">
            <h3>Recent Tickets</h3>
            <?php if (!empty($recentTickets)): ?>
              <?php foreach ($recentTickets as $t): ?>
                <a href="/student/details/<?= (int)$t['ticket_id'] ?>" class="ticket">
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
              <p style="color:#6b7280;">No recent tickets yet.</p>
            <?php endif; ?>
          </div>
      </div>
      <div class="dashboardColumnTwo">
        <div class="priority sectionCard">
          <h3>Priority</h3>
          <a href="#" class="priorityItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="red"><path d="M200-80v-760h640l-80 200 80 200H280v360h-80Z"/></svg>
            <div>
              <span class="priorityTitle">Lecture Hall Changed</span><br>
            <span class="priorityDescription">Lecture - SCS2308 moved to lecture hall - S203, 10:00-12:00</span>
            </div>
          </a>
        </div>
        <div class="announcements sectionCard">
          <h3>Announcements</h3>
          <a href="#" class="announcement warning">
            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#9D5226"><path d="M480-79q-16 0-30.5-6T423-102L102-423q-11-12-17-26.5T79-480q0-16 6-31t17-26l321-321q12-12 26.5-17.5T480-881q16 0 31 5.5t26 17.5l321 321q12 11 17.5 26t5.5 31q0 16-5.5 30.5T858-423L537-102q-11 11-26 17t-31 6Zm-40-361h80v-240h-80v240Zm40 120q17 0 28.5-11.5T520-360q0-17-11.5-28.5T480-400q-17 0-28.5 11.5T440-360q0 17 11.5 28.5T480-320Z"/></svg>
            <div>
              <span class="announcementTitle">System Maintenance</span><br>
            <span class="announcementDescription">Scheduled maintenance on Dec 25, 2:00-4:00 AM</span>
            </div>
          </a>
          <a href="#" class="announcement info">
            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#2786FF"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
            <div>
              <span class="announcementTitle">New FAQ Section</span><br>
              <span class="announcementDescription">Check out our new updated WIFI troubleshooting guide</span>
            </div>
          </a>
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
          <a href="#" class="accountItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/></svg>
            Profile Settings<br>
          </a>
          <a href="#" class="accountItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-80q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v13q-10 20-15 42t-5 45q0 83 58.5 141.5T720-520v240h80v80H160Zm560-400q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35Z"/></svg>
            Notifications<br>
          </a>
          <a href="#" class="accountItem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-138 0-240.5-91.5T122-440h82q14 104 92.5 172T480-200q117 0 198.5-81.5T760-480q0-117-81.5-198.5T480-760q-69 0-129 32t-101 88h110v80H120v-240h80v94q51-64 124.5-99T480-840q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480q0 75-28.5 140.5t-77 114q-48.5 48.5-114 77T480-120Zm112-192L440-464v-216h80v184l128 128-56 56Z"/></svg>
            Ticket History<br>
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="/js/student/dashboard.js"></script>