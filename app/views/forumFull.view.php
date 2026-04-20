<?php
	$title = 'Forum Post';
	$head = '<link rel="stylesheet" href="/css/forum/forumFull.css"><link rel="stylesheet">';
	
	
	$sessionUser = $_SESSION['user'] ?? null;
	$role = strtolower($sessionUser['role'] ?? 'student');
?>

<main>
	<div class="fullPage">
		<div class="pageLayout">
			<div class="pageHeader">
				<button type="button" class="backBtn" onclick="history.back()" aria-label="Go back">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
				</button>
				<div class="pageHeaderContent">
					<h1 class="pageTitle">Forum Post</h1>
				</div>
			</div>
			<div class="pageContent">
				<section class="ticketLeft">
					<div class="card ticketSummary">
						<div class="ticketHeader">
							<div class="voteBox" aria-label="Votes">
								<button id="voteBtn" class="btnAttachRound voteBtn" type="button" aria-pressed="false" title="Upvote">
									<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
								</button>
								<div id="voteCount" class="voteCount">0</div>
								<button id="voteDownBtn" class="btnAttachRound voteBtn voteBtnDown" type="button" aria-pressed="false" title="Downvote">
									<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
								</button>
							</div>
							<div class="titleBlock">
								<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 12px;">
									<h2 id="ticketTitle" class="ticketTitle" style="margin: 0; flex: 1;"></h2>
									<div id="badgeContainer" style="display: flex; gap: 8px; align-items: center;">
										<span id="ticketFlag" class="status new" style="display:none; background:#f3f4f6; color:#374151; border:1px solid #d1d5db; padding:2px 8px; border-radius:12px; font-size:12px; margin-left:8px;"></span>
										<span id="ticketStatus" class="status"></span>
									</div>
								</div>
								<div class="summarySection" style="margin-top: 1.5rem;">
									<h3 class="sectionTitle">Description</h3>
									<p id="ticketDescriptionText" class="descriptionText"></p>
								</div>
							</div>
						</div>
						<div class="summaryBody">
							<div class="summarySection">
								<h3 class="sectionTitle">Attachments</h3>
								<div id="attachmentsList" class="attachmentsList"></div>
							</div>
						</div>
						<div class="ticketMeta" id="ticketMeta"></div>
					</div>

					<div class="card conversation">
						<div class="sectionTitleRow">
							<h3 class="sectionTitle">Comments</h3>
						</div>
						<div id="messages" class="messages"></div>
						<div class="replyBox">
							<input id="replyInput" type="text" placeholder="Type your comment here..." aria-label="Reply" />
							<div class="replyActions">
								<button id="attachBtn" class="btnAttachRound" type="button">
									<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12 20.5a6 6 0 0 1-8.49-8.49l10-10a4 4 0 0 1 5.66 5.66l-10 10a2 2 0 1 1-2.83-2.83l9-9"/></svg>
								</button>
								<button id="sendBtn" class="btnSvg" type="button" aria-label="Send">
									<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
									<div class="spinner"></div>
								</button>
							</div>
						</div>
					</div>
				</section>

				<aside class="ticketRight">
					<div class="card ticketInfo">
						<h3 class="sectionTitle">Post Information</h3>
						<div id="ticketInfoList" class="infoList"></div>
					</div>

					<div class="card ticketTimeline">
						<h3 class="sectionTitle">Helpful Resources</h3>
						<ul class="timelineList resourceList">
							<li class="timelineItem">
								<a href="/<?= htmlspecialchars($role) ?>/announcements" class="resourceLink">
									<span class="label">Announcements</span>
									<span class="time">View latest updates</span>
								</a>
							</li>
							<li class="timelineItem">
								<a href="/<?= htmlspecialchars($role) ?>/faq" class="resourceLink">
									<span class="label">FAQs</span>
									<span class="time">Common questions</span>
								</a>
							</li>
							<li class="timelineItem">
								<a href="/<?= htmlspecialchars($role) ?>/forum" class="resourceLink">
									<span class="label">Browse Posts</span>
									<span class="time">View all discussions</span>
								</a>
							</li>
						</ul>
					</div>

					<div class="card ticketActions">
						<h3 class="visuallyHidden">Post Controls</h3>
						<div class="btnHolder">
							<button id="editPostBtn" class="btnSecondary" type="button"><span class="btnSecondaryText">Edit post</span></button>
						</div>
						<div class="btnHolder">
							<button id="toggleVisibilityBtn" class="btnSecondary" type="button" data-state="public" style="background:#dcfce7;"><span class="btnSecondaryText">Make Private</span></button>
						</div>
						<div class="btnHolder">
							<button id="toggleStatusBtn" class="btnSecondary" type="button" data-status="open" style="background:#fef9c3;"><span class="btnSecondaryText">Mark Answered</span></button>
						</div>
						<div class="btnHolder">
							<button id="deleteBtn" class="btnSecondary" style="background-color: #ff7b7bff;" type="button"><span class="btnSecondaryText" style="color: white;">Delete post</span></button>
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
				<h3 id="deleteModalTitle" class="msgTitle">Delete this post?</h3>
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
		<div class="msgContainer" role="dialog" aria-modal="true" aria-labelledby="editModalTitle" style="min-width: 400px; padding: 24px; max-width: 90vw;">
			<div class="msgContent" style="width: 100%;">
				<h3 id="editModalTitle" class="msgTitle">Edit Post</h3>
				<form id="editPostForm" class="editForm" style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px; width: 100%;" enctype="multipart/form-data">
					<input type="text" id="editTitleInput" name="title" placeholder="Title" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; font-family: inherit; font-size: 14px;" />
					<textarea id="editDescInput" name="description" placeholder="Description" required rows="6" style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; font-family: inherit; font-size: 14px; resize: vertical;"></textarea>
					
					<div class="fileUploadWrapper">
						<label style="font-size: 13px; font-weight: 500;">Existing Attachments:</label>
						<div id="editExistingAttachments" style="font-size: 12px; color: #4b5563; margin-bottom: 8px; max-height: 80px; overflow-y: auto;"></div>

						<label style="font-size: 13px; font-weight: 500;">Add/Change Attachments</label>
						<p style="font-size: 11px; color: #6b7280; margin-top: 4px;">If you upload new files, all existing attachments will be replaced. Leave empty to keep existing ones.</p>
						<div id="editDropzone" class="dropzone">
							<div class="dzInner">
								<svg class="dzIconLg" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
								<div>Drag and drop files here or <span class="browse">browse files</span></div>
								<input type="file" id="editAttachmentsInput" name="attachments[]" multiple hidden />
							</div>
							<ul id="editFileList" class="fileList"></ul>
						</div>
					</div>

					<div class="msgActions" style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 8px;">
						<button id="cancelEditBtn" type="button" class="btnSecondary"><span class="btnSecondaryText">Cancel</span></button>
						<button id="saveEditBtn" type="submit" class="btnPrimary"><span class="btnPrimaryText">Save Changes</span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
</div>

<script>
window.ticketData = <?= json_encode($data ?? []) ?>;
</script>
<script src="/js/forum/forumFull.js"></script>
