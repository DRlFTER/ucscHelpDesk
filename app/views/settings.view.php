<?php
	$title = 'Settings';
	$head = '<link rel="stylesheet" href="/css/settings/settings.css">';
?>

<main>
	<div class="fullPage">
		<div class="pageLayout">
			<div class="pageHeader">
				<button type="button" class="backBtn" onclick="history.back()" aria-label="Go back">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
				</button>
				<div class="pageHeaderContent">
					<h1 class="pageTitle">Settings</h1>
					<?php if (!empty($roleLabel) || !empty($roleMessage)) : ?>
						<div class="roleNotice" style="margin-top:8px;color:#374151;font-size:14px;">
							<?php if (!empty($roleLabel)): ?>
								<strong><?php echo htmlspecialchars($roleLabel); ?></strong>:
							<?php endif; ?>
							<?php echo htmlspecialchars($roleMessage ?? 'Role-specific settings (dummy).'); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="pageContent">
				<section class="settingsLeft" aria-label="Settings navigation">
				</section>
				<aside class="settingsRight" aria-live="polite" aria-busy="false">
				</aside>
			</div>
		</div>
	</div>
</main>

<script src="/js/settings/settings.js"></script>

