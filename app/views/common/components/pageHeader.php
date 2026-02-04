<?php
/**
 * Page Header Component with Back Button
 * 
 * Usage: 
 * $pageTitle = 'Your Title';
 * $pageSubtitle = 'Your subtitle'; // optional
 * include __DIR__ . '/../common/components/pageHeader.php';
 */
$_pageTitle = $pageTitle ?? 'Page';
$_pageSubtitle = $pageSubtitle ?? '';
?>
<div class="pageHeader">
    <button type="button" class="backBtn" onclick="history.back()" aria-label="Go back">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
        </svg>
    </button>
    <div class="pageHeaderContent">
        <h1 class="pageTitle"><?= htmlspecialchars($_pageTitle) ?></h1>
        <?php if ($_pageSubtitle): ?>
        <p class="pageSubtitle"><?= htmlspecialchars($_pageSubtitle) ?></p>
        <?php endif; ?>
    </div>
</div>
