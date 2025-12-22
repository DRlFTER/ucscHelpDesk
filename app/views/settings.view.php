<?php
	$title = 'Settings';
	$head = '<link rel="stylesheet" href="/css/settings/settings.css">';
?>

<main>
	<div class="fullPage">
		<div class="pageLayout">
			<div class="pageHeader">
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

