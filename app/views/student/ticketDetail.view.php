<?php include_once(__DIR__ . "/../../views/common/navbar.php"); ?>

<main>
  <div class="ticketDetailCard">
    <div class="topBar">
      <a href="/student/dashboard" class="btnBackIcon" aria-label="Back to Dashboard">
        <img src="/assets/arrow-left.svg" alt="Back" width="20" height="20" />
      </a>
      <?php if (!empty($ticket)): ?>
        <form method="POST" action="/student/delete/<?= (int)$ticket['ticket_id'] ?>" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
          <button type="submit" class="btnDelete" aria-label="Delete ticket">Delete Ticket</button>
        </form>
      <?php endif; ?>
    </div>
    <?php if (empty($ticket)): ?>
      <h2 class="emptyTitle">Ticket not found</h2>
      <p class="emptyText">We couldn't find that ticket, or you don't have access to it.</p>
      
    <?php else: ?>
      <h2 class="ticketDetailTitle"><?= htmlspecialchars($ticket['title']) ?></h2>
      <?php
        $statusRaw = strtolower($ticket['status'] ?? '');
        $statusClass = in_array($statusRaw, ['open','closed','completed','resolved','pending','cancelled','rejected','requested','underreview'])
          ? $statusRaw
          : ($statusRaw === 'in progress' || $statusRaw === 'in_progress' ? 'inProgress' : 'pending');
        if ($statusRaw === 'under review') { $statusClass = 'underReview'; }

        $priorityRaw = strtolower($ticket['priority'] ?? '');
        $priorityClass = in_array($priorityRaw, ['low','medium','high']) ? $priorityRaw : ($priorityRaw === 'urgent' ? 'high' : 'low');
      ?>
      <div class="ticketMeta">
        <span>Category: <strong><?= htmlspecialchars($ticket['category']) ?></strong></span>
        <span>Status: <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars(ucfirst($ticket['status'])) ?></span></span>
        <span>Priority: <span class="status <?= htmlspecialchars($priorityClass) ?>"><?= htmlspecialchars(ucfirst($ticket['priority'])) ?></span></span>
      </div>
    <div class="ticketOpened">
        Opened <?= htmlspecialchars(time_ago($ticket['created_at'])) ?>
        <?php if (!empty($ticket['meeting_requested'])): ?>
      <span class="status requested ml8">Meeting Requested</span>
        <?php endif; ?>
      </div>

      <hr class="divider"/>

      <h3 class="ticketSectionTitle">Description</h3>
      <p class="ticketDescription">
        <?= nl2br(htmlspecialchars($ticket['description'])) ?>
      </p>

      
    <?php endif; ?>
  </div>
</main>