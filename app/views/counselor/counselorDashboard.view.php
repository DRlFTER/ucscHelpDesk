<main>
  <div class="fullPage">
    <div class="pageLayout">
      <aside class="adminRight">
        <div class="dashboardContent">
          <div class="cardContainer grid-4">
            <div class="cardBox">
              <div class="cardTitle">Open Counseling Tickets</div>
              <div class="cardValue"><?= (int)($cards['open'] ?? 0) ?></div>
            </div>
            <div class="cardBox">
              <div class="cardTitle">Assigned to You</div>
              <div class="cardValue"><?= (int)($cards['assigned'] ?? 0) ?></div>
            </div>
            <div class="cardBox">
              <div class="cardTitle">Meeting Requests</div>
              <div class="cardValue"><?= (int)($cards['meetings'] ?? 0) ?></div>
            </div>
            <div class="cardBox">
              <div class="cardTitle">Resolved by You</div>
              <div class="cardValue"><?= (int)($cards['resolvedByYou'] ?? 0) ?></div>
            </div>
          </div>

          <div class="contentRow grid-2">
            <div class="cardBox">
              <div class="cardHeader">
                <h3 style="margin:0;">Your Queue</h3>
                <span class="muted">Recent assigned</span>
              </div>
              <?php if (!empty($recentAssigned)): ?>
              <ul class="itemList">
                <?php foreach ($recentAssigned as $t): ?>
                  <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" style="text-decoration:none; color:inherit;">
                  <li class="item">
                    <div class="itemMain">
                      <div class="itemTitle">TKT-<?= (int)$t['ticket_id'] ?> · <?= htmlspecialchars($t['title'] ?? '') ?></div>
                      <div class="itemMeta">
                        <span class="badge priority <?= strtolower((string)($t['priority'] ?? 'low')) ?>"><?= strtoupper((string)($t['priority'] ?? '')) ?></span>
                        <span class="dot">•</span>
                        <span><?= htmlspecialchars($t['student_name'] ?? 'Student') ?></span>
                        <span class="dot">•</span>
                        <span><?= CounselorDashboard::relativeTime($t['created_at'] ?? null) ?></span>
                      </div>
                    </div>
                    <div class="itemRight">
                      <span class="status <?= strtolower((string)($t['status'] ?? '')) ?>"><?= ucfirst((string)($t['status'] ?? '')) ?></span>
                    </div>
                  </li>
                  </a>
                <?php endforeach; ?>
              </ul>
              <?php else: ?>
                <div class="empty">No assigned tickets yet.</div>
              <?php endif; ?>
            </div>

            <div class="cardBox">
              <div class="cardHeader">
                <h3 style="margin:0;">New Counseling Tickets</h3>
                <span class="muted">Unresolved (latest)</span>
              </div>
              <?php if (!empty($newPending)): ?>
              <ul class="itemList">
                <?php foreach ($newPending as $t): ?>
                  <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" style="text-decoration:none; color:inherit;">
                  <li class="item">
                    <div class="itemMain">
                      <div class="itemTitle">TKT-<?= (int)$t['ticket_id'] ?> · <?= htmlspecialchars($t['title'] ?? '') ?></div>
                      <div class="itemMeta">
                        <span class="badge priority <?= strtolower((string)($t['priority'] ?? 'low')) ?>"><?= strtoupper((string)($t['priority'] ?? '')) ?></span>
                        <span class="dot">•</span>
                        <span><?= htmlspecialchars($t['student_name'] ?? 'Student') ?></span>
                        <span class="dot">•</span>
                        <span><?= CounselorDashboard::relativeTime($t['created_at'] ?? null) ?></span>
                      </div>
                    </div>
                    <div class="itemRight">
                      <?php if (!empty($t['is_assigned'])): ?>
                        <span class="badge assigned">Assigned</span>
                      <?php else: ?>
                        <span class="badge unassigned">Unassigned</span>
                      <?php endif; ?>
                    </div>
                  </li>
                  </a>
                <?php endforeach; ?>
              </ul>
              <?php else: ?>
                <div class="empty">No new counseling tickets.</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="contentRow grid-1">
            <div class="cardBox">
              <div class="cardHeader">
                <h3 style="margin:0;">Meeting Requests</h3>
                <span class="muted">Requested or Scheduled</span>
              </div>
              <?php if (!empty($meetingTickets)): ?>
              <ul class="itemList">
                <?php foreach ($meetingTickets as $t): ?>
                  <a href="/counselor/ticketFull?id=<?= (int)$t['ticket_id'] ?>" style="text-decoration:none; color:inherit;">
                  <li class="item">
                    <div class="itemMain">
                      <div class="itemTitle">TKT-<?= (int)$t['ticket_id'] ?> · <?= htmlspecialchars($t['title'] ?? '') ?></div>
                      <div class="itemMeta">
                        <span><?= htmlspecialchars($t['student_name'] ?? 'Student') ?></span>
                        <span class="dot">•</span>
                        <span><?= CounselorDashboard::relativeTime($t['created_at'] ?? null) ?></span>
                      </div>
                    </div>
                    <div class="itemRight">
                      <span class="badge meeting <?= strtolower((string)($t['meeting_requested'] ?? '')) ?>"><?= ucfirst((string)($t['meeting_requested'] ?? '')) ?></span>
                      <?php if (!empty($t['assigned_to']) && isset($_SESSION['user']['u_id']) && (int)$t['assigned_to'] === (int)$_SESSION['user']['u_id']): ?>
                        <span class="badge assigned">Assigned to You</span>
                      <?php elseif (!empty($t['assigned_to'])): ?>
                        <span class="badge assigned">Assigned</span>
                      <?php else: ?>
                        <span class="badge unassigned">Unassigned</span>
                      <?php endif; ?>
                    </div>
                  </li>
                  </a>
                <?php endforeach; ?>
              </ul>
              <?php else: ?>
                <div class="empty">No meeting requests right now.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>