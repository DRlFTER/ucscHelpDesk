<?php
	$title = 'Settings';
	$head = '<link rel="stylesheet" href="/css/settings/settings.css">';
	
	// Get user data from session
	$user = $_SESSION['user'] ?? null;
	$role = strtolower($user['role'] ?? 'guest');
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

<!-- Pass user data to JavaScript -->
<script>
	window.SETTINGS_USER_DATA = <?= json_encode([
		'u_id' => $user['u_id'] ?? null,
		'name' => $user['name'] ?? '',
		'email' => $user['email'] ?? '',
		'number' => $user['number'] ?? '',
		'role' => $user['role'] ?? '',
		'year' => $user['year'] ?? '',
		'designation' => $user['designation'] ?? '',
		'profile_photo' => $user['profile_url'] ?? $user['profile_photo'] ?? null,
	]) ?>;
	window.SETTINGS_ROLE = <?= json_encode($role) ?>;
	window.SETTINGS_API_BASE = <?= json_encode('/' . $role) ?>;
</script>
<script src="/js/settings/settings.js"></script>

