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

<!-- Delete Photo Confirmation Modal -->
<div id="deletePhotoModal" class="modalOverlay" aria-hidden="true">
	<div class="msgHolder">
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="deletePhotoModalTitle">
			<div class="msgContent">
				<h3 id="deletePhotoModalTitle" class="msgTitle">Remove profile photo?</h3>
				<p class="msgText">Your profile photo will be removed. You can upload a new one at any time.</p>
				<div class="msgActions">
					<button id="cancelDeletePhotoBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
					<button id="confirmDeletePhotoBtn" type="button" class="btnPrimary btnDanger"><span class="btnPrimaryText">Remove</span></button>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modalOverlay" aria-hidden="true">
	<div class="msgHolder">
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="changePasswordModalTitle">
			<div class="msgContent" style="width:100%;">
				<h3 id="changePasswordModalTitle" class="msgTitle">Change Password</h3>
				<div class="msgText" style="margin-top:20px;">
                    <form id="changePasswordForm">
                        <div class="settingRow" style="flex-direction:column; align-items:flex-start; margin-bottom:15px; border:none; padding:0;">
                            <label style="margin-bottom:8px; font-size:14px; font-weight:600;">Current Password</label>
                            <input type="password" id="currentPasswordInput" required style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:10px;" />
                        </div>
                        <div class="settingRow" style="flex-direction:column; align-items:flex-start; margin-bottom:15px; border:none; padding:0;">
                            <label style="margin-bottom:8px; font-size:14px; font-weight:600;">New Password</label>
                            <input type="password" id="newPasswordInput" required minlength="8" style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:10px;" />
                        </div>
                        <div class="settingRow" style="flex-direction:column; align-items:flex-start; margin-bottom:15px; border:none; padding:0;">
                            <label style="margin-bottom:8px; font-size:14px; font-weight:600;">Confirm New Password</label>
                            <input type="password" id="confirmNewPasswordInput" required minlength="8" style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:10px;" />
                        </div>
						<div id="passwordErrorMsg" style="color:#ef4444; font-size:13px; margin-bottom:15px; display:none;"></div>
                    </form>
				</div>
				<div class="msgActions">
					<button id="cancelChangePasswordBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
					<button id="confirmChangePasswordBtn" type="button" class="btnPrimary"><span class="btnPrimaryText">Save Password</span></button>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

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

