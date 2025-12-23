<?php
	$__vt = '';
	if (isset($_GET['id']) && $_GET['id'] !== '') {
		$__vt = 'user-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string)$_GET['id']);
	}
?>
<main>
	<div class="fullPage">
		<div class="pageLayout">
			<div class="pageHeader">
				<button type="button" class="backBtn" onclick="history.back()" aria-label="Go back">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
				</button>
				<div class="pageHeaderContent">
					<h1 class="pageTitle">User details</h1>
				</div>
			</div>
			<div class="pageContent">
				<section class="ticketLeft">
				<div class="card ticketSummary" id="userSummaryCard" <?= $__vt ? ('style="view-transition-name: ' . htmlspecialchars($__vt) . '"') : '' ?> >
					<div class="ticketHeader">
						<h2 id="userName" class="ticketTitle"></h2>
						<span id="userStatus" class="status"></span>
					</div>
					<div class="ticketMeta" id="userMeta"></div>
							<div class="summaryBody">
								<div class="summarySection">
									<h3 class="sectionTitle">Basic information</h3>
									<div id="basicInfo" class="infoList"></div>
								</div>
								<div class="summarySection">
									<h3 class="sectionTitle">Notes</h3>
									<p class="descriptionText">suspension is UI only for now.</p>
								</div>
							</div>
				</div>

			</section>

			<aside class="ticketRight">
				<div class="card ticketInfo">
					<h3 class="sectionTitle">Account details</h3>
					<div id="userInfoList" class="infoList"></div>
				</div>

				<div class="card ticketActions">
					<h3 class="visuallyHidden">Actions</h3>
					<div class="btnHolder">
						<button id="editUserBtn" class="btnPrimary" type="button"><span class="btnPrimaryText">Edit user</span></button>
					</div>
					<div class="btnHolder">
						<button id="suspendBtn" class="btnSecondary" type="button"><span class="btnSecondaryText">Suspend user</span></button>
					</div>
					<div class="btnHolder" id="deleteHolder">
						<button id="deleteBtn" class="btnSecondary" style="background-color: #ff7b7bff;" type="button"><span class="btnSecondaryText" style="color: white;">Delete user</span></button>
					</div>
					<div class="btnHolder" id="restoreHolder" style="display: none;">
						<button id="restoreBtn" class="btnSecondary" style="background-color: #22c55e;" type="button"><span class="btnSecondaryText" style="color: white;">Restore user</span></button>
					</div>
				</div>
			</aside>
			</div>
		</div>
	</div>
</main>
<div id="deleteModal" class="modalOverlay" aria-hidden="true">
	<div class="msgHolder">
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
			<div class="msgContent">
				<h3 id="deleteModalTitle" class="msgTitle">Delete this user?</h3>
				<p class="msgText">The user will be marked as deleted and can be restored later.</p>
				<div class="msgActions">
					<button id="cancelDeleteBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
					<button id="confirmDeleteBtn" type="button" class="btnPrimary btnDanger"><span class="btnPrimaryText">Delete</span></button>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>

</div>

<div id="restoreModal" class="modalOverlay" aria-hidden="true">
	<div class="msgHolder">
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="restoreModalTitle">
			<div class="msgContent">
				<h3 id="restoreModalTitle" class="msgTitle">Restore this user?</h3>
				<p class="msgText">This will restore the user's account and they will be able to log in again.</p>
				<div class="msgActions">
					<button id="cancelRestoreBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
					<button id="confirmRestoreBtn" type="button" class="btnPrimary" style="background-color: #22c55e;"><span class="btnPrimaryText">Restore</span></button>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<div id="editModal" class="modalOverlay" aria-hidden="true">
	<div class="msgHolder">
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
			<div class="msgContent">
				<h3 id="editModalTitle" class="msgTitle">Edit user</h3>
				<form id="editUserForm" class="formGrid">
					<label>
						<span>Name</span>
						<input type="text" id="editName" name="name" required />
					</label>
					<label>
						<span>Email</span>
						<input type="email" id="editEmail" name="email" required />
					</label>
					<label>
						<span>Phone</span>
						<input type="text" id="editNumber" name="number" />
					</label>
					<label>
						<span>Role</span>
						<select id="editRole" name="role">
							<option value="student">Student</option>
							<option value="staff">Staff</option>
							<option value="lecturer">Lecturer</option>
							<option value="counselor">Counselor</option>
							<option value="admin">Admin</option>
						</select>
					</label>
					<label>
						<span>Designation</span>
						<input type="text" id="editDesignation" name="designation" />
					</label>
					<label>
						<span>Year</span>
						<input type="number" min="1900" max="2100" id="editYear" name="year" />
					</label>
					<div class="msgActions">
						<button type="button" id="cancelEditBtn" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
						<button type="submit" class="btnPrimary"><span class="btnPrimaryText">Save</span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<script src="/js/admin/adminUserFull.js"></script>
