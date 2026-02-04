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
  <div class="lostFoundContainer">
    <div class="lfHeader">
      <div class="lfTitles">
        <a class="backBtn" href="/student/dashboard" aria-label="Back">
          <img src="/assets/arrow-left.svg" alt="Back" />
        </a>
        <div class="titlesText">
          <h2>Lost &amp; Found</h2>
          <p class="pageSubtitle">Browse reported lost and found items</p>
        </div>
      </div>
      <div class="spacer"></div>
    </div>

    <div class="lfControls">
      <div class="searchWrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search Lost &amp; Found..." />
      </div>
      <div class="filters">
        <div class="filterGroup">
          <label><input type="radio" name="type" value="all" checked /> All</label>
          <label><input type="radio" name="type" value="lost" /> Lost</label>
          <label><input type="radio" name="type" value="found" /> Found</label>
          <label><input type="radio" name="type" value="my" /> My</label>
        </div>
      </div>
    </div>

      <section class="sectionCard">
        <div class="lfList tickets" data-current-user="<?= (int)($_SESSION['user']['u_id'] ?? 0) ?>">
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $it): ?>
            <?php $statusLower = strtolower($it['status'] ?? ''); $isResolved = ($statusLower === 'found' || $statusLower === 'claimed'); ?>
            <?php
              $statusClass = 'underReview';
              if ($statusLower === 'found' || $statusLower === 'claimed') {
                $statusClass = 'resolved';
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

            <article class="lfCard ticket <?= $isResolved ? 'found' : 'lost' ?>" data-u-id="<?= $ownerId ?>">
              <div class="ticketRow1">
                <div class="ticketName">
                  <h2><?= htmlspecialchars($it['item_title']) ?></h2>
                   <?php if (!empty($it['item_details'])): ?>
                   <p class="lfDetailsText" style="margin: 6px 0 10px 0; color:#374151; align-self: stretch;">
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
                <div class="ticketDetails" style="border-right:none;">
                  <div class="ticketDetail">
                    <h2>Type:</h2>
                    <div class="ticketDetailHolder"><?= htmlspecialchars($typeLabel) ?></div>
                  </div>
                  <div class="ticketDetail">
                    <h2>Status:</h2>
                    <div class="ticketDetailHolder" style="background:#eef2ff; color:#4338ca;">
                      <?= htmlspecialchars($statusLabel) ?>
                    </div>
                  </div>
                  <div class="ticketDetail">
                    <h2>Category:</h2>
                    <div class="ticketDetailHolder"><?= htmlspecialchars($categoryLabel) ?></div>
                  </div>
                  <div class="ticketDetail">
                    <h2>When:</h2>
                    <div class="ticketDetailHolder"><?php
                      if ($whenTs) {
                        echo htmlspecialchars(date('Y-m-d H:i', $whenTs));
                      } else {
                        echo '—';
                      }
                    ?></div>
                  </div>
                  <div class="ticketDetail">
                    <h2>Contact:</h2>
                    <div class="ticketDetailHolder"><?php
                      $contactPieces = [];
                      if (!empty($it['contact_mobile'])) { $contactPieces[] = htmlspecialchars($it['contact_mobile']); }
                      if (!empty($it['contact_email'])) { $contactPieces[] = htmlspecialchars($it['contact_email']); }
                      echo !empty($contactPieces) ? implode(' • ', $contactPieces) : '—';
                    ?></div>
                  </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-left:auto; flex-wrap:wrap;">
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
      </section>

      <aside class="lfActions">
  <a href="/student/newFoundItem" class="btnWSvg btnPrimaryText lfGreen" style="text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
            <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
          </svg>
          <span>Found Item</span>
        </a>
  <a href="/student/newLostItem" class="btnWSvg btnPrimaryText lfRed" style="text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
            <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
          </svg>
          <span>Lost Item</span>
        </a>
      </aside>
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