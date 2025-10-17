<?php
// Helper to render relative "time ago" strings from a datetime value
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
  // older than a week — show a short date
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
        <div class="lfList" data-current-user="<?= (int)($_SESSION['user']['u_id'] ?? 0) ?>">
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $it): ?>
            <?php $isFound = strtolower($it['status'] ?? '') === 'found'; ?>
            <article class="lfCard <?= $isFound ? 'found' : 'lost' ?>" data-u-id="<?= (int)($it['u_id'] ?? 0) ?>">
              <h3><span class="state <?= $isFound ? 'found' : 'lost' ?>"><?= $isFound ? 'Found' : 'Lost' ?></span> <?= htmlspecialchars($it['item_title']) ?></h3>
              <p><?= nl2br(htmlspecialchars($it['item_details'])) ?></p>
              <ul class="meta">
                <?php if (!empty($it['category'])): ?><li>Category: <?= htmlspecialchars(ucfirst($it['category'])) ?></li><?php endif; ?>
                <?php if (!empty($it['when'])):
                    $whenTs = strtotime($it['when']);
                    $whenDisplay = $whenTs ? date('Y-m-d H:i', $whenTs) : $it['when'];
                ?>
                  <li>Date &amp; Time: <?= htmlspecialchars($whenDisplay) ?></li>
                <?php endif; ?>
                <?php if (!empty($it['contact_mobile'])): ?><li>Contact: <?= htmlspecialchars($it['contact_mobile']) ?></li><?php endif; ?>
                <?php if (!empty($it['contact_email'])): ?><li>Email: <?= htmlspecialchars($it['contact_email']) ?></li><?php endif; ?>
              </ul>
              <div class="lfFooter">
                <span>Request No #<?= (int)$it['q_id'] ?></span>
                <?php $currentUserId = (int)($_SESSION['user']['u_id'] ?? 0); ?>
                <?php if ($currentUserId && $currentUserId === (int)($it['u_id'] ?? 0)): ?>
                  <form method="POST" action="/student/lostfound_delete/<?= (int)$it['q_id'] ?>" onsubmit="return confirm('Delete this submission? This cannot be undone.');" style="margin-left:12px;">
                    <button type="submit" class="btnWSvg btnDangerText" style="padding:6px 10px; border-radius:8px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; cursor:pointer;">
                      Delete
                    </button>
                  </form>
                <?php endif; ?>
                <span class="time"><?=
                    isset($it['created_at']) ? htmlspecialchars(time_ago_label($it['created_at'])) : 'recent'
                ?></span>
              </div>
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
  // Minimal success popup. You can replace with your existing toast component if available.
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