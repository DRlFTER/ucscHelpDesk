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
				<h1 class="pageTitle">User details</h1>
				<p class="pageSubtitle">View and manage user information</p>
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
					<div class="btnHolder">
						<button id="deleteBtn" class="btnSecondary" style="background-color: #ff7b7bff;" type="button"><span class="btnSecondaryText" style="color: white;">Delete user</span></button>
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
				<p class="msgText">This action is permanent and cannot be undone.</p>
				<div class="msgActions">
					<button id="cancelDeleteBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
					<button id="confirmDeleteBtn" type="button" class="btnPrimary btnDanger"><span class="btnPrimaryText">Delete</span></button>
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
