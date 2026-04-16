<?php
function time_ago_label($datetime)
{
  if (empty($datetime)) return 'recent';
  $ts = strtotime($datetime);
  if ($ts === false) return 'recent';
  $diff = time() - $ts;
  if ($diff < 5) return 'just now';
  if ($diff < 60) return $diff . ' seconds ago';
  if ($diff < 3600) {
    $m = floor($diff / 60);
    return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago';
  }
  if ($diff < 86400) {
    $h = floor($diff / 3600);
    return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago';
  }
  if ($diff < 604800) {
    $d = floor($diff / 86400);
    return $d . ' day' . ($d > 1 ? 's' : '') . ' ago';
  }
  $format = date('Y', $ts) === date('Y') ? 'M j' : 'M j, Y';
  return date($format, $ts);
}

?>

<main>
  <div class="fullPage">
    <div class="pageLayout">
      <div class="ticketsFilters">
        <div class="search">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 22 21" fill="none">
            <path d="M17.65 18.375L12.1375 12.8625C11.7 13.2125 11.1969 13.4896 10.6281 13.6938C10.0594 13.8979 9.45417 14 8.8125 14C7.22292 14 5.8776 13.4495 4.77656 12.3484C3.67552 11.2474 3.125 9.90208 3.125 8.3125C3.125 6.72292 3.67552 5.3776 4.77656 4.27656C5.8776 3.17552 7.22292 2.625 8.8125 2.625C10.4021 2.625 11.7474 3.17552 12.8484 4.27656C13.9495 5.3776 14.5 6.72292 14.5 8.3125C14.5 8.95417 14.3979 9.55937 14.1938 10.1281C13.9896 10.6969 13.7125 11.2 13.3625 11.6375L18.875 17.15L17.65 18.375ZM8.8125 12.25C9.90625 12.25 10.8359 11.8672 11.6016 11.1016C12.3672 10.3359 12.75 9.40625 12.75 8.3125C12.75 7.21875 12.3672 6.28906 11.6016 5.52344C10.8359 4.75781 9.90625 4.375 8.8125 4.375C7.71875 4.375 6.78906 4.75781 6.02344 5.52344C5.25781 6.28906 4.875 7.21875 4.875 8.3125C4.875 9.40625 5.25781 10.3359 6.02344 11.1016C6.78906 11.8672 7.71875 12.25 8.8125 12.25Z" fill="#808080"/>
          </svg>
          <input type="text" placeholder="Search Lost &amp; Found..." />
        </div>
        <div class="filters">
          <a href="/student/newFoundItem" class="btnWSvg" style="text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="btnPrimaryText">Found Item</span>
          </a>
          <a href="/student/newLostItem" class="btnWSvg" style="text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="btnPrimaryText">Lost Item</span>
          </a>
          <div class="filterGroup" role="radiogroup" aria-label="Type">
            <label><input type="radio" name="type" value="all" checked /> All</label>
            <label><input type="radio" name="type" value="lost" /> Lost</label>
            <label><input type="radio" name="type" value="found" /> Found</label>
            <label><input type="radio" name="type" value="my" /> My</label>
          </div>
        </div>
      </div>

      <div class="tickets lfList" data-current-user="<?= (int)($_SESSION['user']['u_id'] ?? 0) ?>">
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $it): ?>
            <?php $statusLower = strtolower($it['status'] ?? ''); $isResolved = ($statusLower === 'found' || $statusLower === 'claimed'); ?>
            <?php
              // Map Lost & Found status to existing global .status colors
              if ($statusLower === 'claimed') {
                  $statusClass = 'underReview'; // Yellowish tag
              } elseif ($statusLower === 'found') {
                  $statusClass = 'resolved'; // Green tag
              } else {
                  $statusClass = 'rejected'; // Red tag
              }
              $statusLabel = $isResolved ? ($statusLower === 'claimed' ? 'Claimed' : 'Found') : 'Lost';

              $whenTs = !empty($it['when']) ? strtotime($it['when']) : null;
              $whenDisplay = $whenTs ? date('Y-m-d', $whenTs) : '';
              $createdAgo = isset($it['created_at']) ? time_ago_label($it['created_at']) : 'recent';
              $code = 'LF-' . (int)($it['q_id'] ?? 0);
              $currentUserId = (int)($_SESSION['user']['u_id'] ?? 0);
              $ownerId = (int)($it['u_id'] ?? 0);
              if ($currentUserId && $currentUserId === $ownerId) {
                $ownerLabel = 'You';
              } else if (!empty($it['owner_name'])) {
                $ownerLabel = $it['owner_name'];
              } else if (!empty($it['contact_email'])) {
                $ownerLabel = $it['contact_email'];
              } else {
                $ownerLabel = '—';
              }
              $typeLabel = ucfirst($statusLower === 'lost' ? 'Lost' : 'Found');
              $categoryLabel = !empty($it['category']) ? ucfirst($it['category']) : '—';
            ?>

            <article class="ticket <?= $isResolved ? 'found' : 'lost' ?>" data-u-id="<?= $ownerId ?>">
              <div class="ticketRow1">
                <div class="ticketName">
                  <h2><?= htmlspecialchars($it['item_title']) ?></h2>
                   <?php if (!empty($it['item_details'])): ?>
                   <p style="margin: 6px 0 10px 0; color:#374151; font-size:14px; align-self: stretch;">
                     <?= nl2br(htmlspecialchars($it['item_details'])) ?>
                   </p>
                   <?php endif; ?>
                  <div class="ticketInfo">
                    <p><?= htmlspecialchars($code) ?></p>
                    <p><?= htmlspecialchars($createdAgo) ?></p>
                    <p><?= htmlspecialchars($ownerLabel) ?></p>
                  </div>
                </div>
                <div class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusLabel) ?></div>
              </div>

              <div class="ticketRow2">
                <div class="ticketDetails">
                  <div class="ticketDetail">
                    <h2>Type:</h2>
                    <div class="ticketDetailHolder"><?= htmlspecialchars($typeLabel) ?></div>
                  </div>
                  <div class="ticketDetail">
                    <h2>Category:</h2>
                    <div class="ticketDetailHolder"><?= htmlspecialchars($categoryLabel) ?></div>
                  </div>
                </div>
                <div class="ticketData">
                  <div class="ticketDetail">
                    <h2>When:</h2>
                    <div class="ticketDataHolder"><?php
                      if ($whenTs) {
                        echo htmlspecialchars(date('Y-m-d H:i', $whenTs));
                      } else {
                        echo '—';
                      }
                    ?></div>
                  </div>
                  <div class="ticketDetail">
                    <h2>Contact:</h2>
                    <div class="ticketDataHolder"><?php
                      $contactPieces = [];
                      if (!empty($it['contact_mobile'])) { $contactPieces[] = htmlspecialchars($it['contact_mobile']); }
                      if (!empty($it['contact_email'])) { $contactPieces[] = htmlspecialchars($it['contact_email']); }
                      echo !empty($contactPieces) ? implode(' • ', $contactPieces) : '—';
                    ?></div>
                  </div>
                </div>
                <div class="ticketActions" style="display:flex; align-items:center; gap:8px; margin-left:auto; flex-wrap:wrap;">
                  <?php if ($currentUserId && $currentUserId === $ownerId): ?>
                    <?php if (!$isResolved): ?>
                      <form method="POST" action="/student/lostfound_markfound/<?= (int)$it['q_id'] ?>">
                        <button type="submit" class="btnWSvg" data-mark-found-btn style="padding:10px 16px; font-size:14px; border-radius:10px; background:#dcfce7; color:#166534; border:1px solid #bbf7d0; cursor:pointer;">Mark as claimed</button>
                      </form>
                    <?php elseif ($statusLower === 'found'): ?>
                      <form method="POST" action="/student/lostfound_claim/<?= (int)$it['q_id'] ?>">
                        <button type="submit" class="btnWSvg" data-claim-btn style="padding:10px 16px; font-size:14px; border-radius:10px; background:#dcfce7; color:#166534; border:1px solid #bbf7d0; cursor:pointer;">Mark as claimed</button>
                      </form>
                    <?php else: ?>
                      <button type="button" class="btnWSvg" disabled style="padding:10px 16px; font-size:14px; border-radius:10px; background:#e5e7eb; color:#374151; border:1px solid #d1d5db; cursor:not-allowed;">Item claimed</button>
                    <?php endif; ?>
                    <form method="POST" action="/student/lostfound_delete/<?= (int)$it['q_id'] ?>" onsubmit="return confirm('Delete this submission? This cannot be undone.');">
                      <button type="submit" class="btnWSvg btnDangerText" style="padding:10px 16px; font-size:14px; border-radius:10px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; cursor:pointer;">Delete</button>
                    </form>
                  <?php elseif ($currentUserId): ?>
                    <?php if ($statusLower === 'found'): ?>
                      <form method="POST" action="/student/lostfound_claim/<?= (int)$it['q_id'] ?>">
                        <button type="submit" class="btnWSvg" data-claim-btn style="padding:10px 16px; font-size:14px; border-radius:10px; background:#dcfce7; color:#166534; border:1px solid #bbf7d0; cursor:pointer;">Mark as claimed</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>

              <span class="state <?= $statusLower === 'claimed' ? 'claimed' : ($isResolved ? 'found' : 'lost') ?>" style="display:none;">&nbsp;</span>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="color:#6b7280; font-size:14px;">No items yet.</div>
        <?php endif; ?>
        </div>
    </div>
  </div>
</main>
<script src="/js/student/studentLostFound.js"></script>
<?php if (!empty($flash) && ($flash['type'] ?? '') === 'success'): ?>
<script>
  (function(){
    const msg = <?= json_encode($flash['message'] ?? 'Submitted successfully.') ?>;
    const el = document.createElement('div');
    el.textContent = msg;
  el.style.position='fixed';
  el.style.right='20px';
  el.style.top='20px';
    el.style.background='#10b981';
    el.style.color='#fff';
    el.style.padding='12px 16px';
    el.style.borderRadius='10px';
    el.style.boxShadow='0 8px 30px rgba(0,0,0,.12)';
    el.style.zIndex='9999';
    document.body.appendChild(el);
    setTimeout(()=>{ el.style.transition='opacity .3s'; el.style.opacity='0'; setTimeout(()=>el.remove(), 300); }, 2200);
  })();
</script>
<?php endif; ?>