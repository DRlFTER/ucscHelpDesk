<?php
	// Page metadata and CSS include for student forum full view
	$title = 'Forum Post';
	$head = '<link rel="stylesheet" href="/css/student/studentForumFull.css">';
?>

<main>
	<div class="ticketFullPage">
		<div class="pageHeader">
			<h1 class="pageTitle">Forum Post</h1>
			<p class="pageSubtitle">View the discussion and replies for this post</p>
		</div>

		<div class="ticketLayout">
			<!-- Left content -->
			<section class="ticketLeft">
				<div class="card ticketSummary">
					<div class="ticketHeader">
						<div class="voteBox" aria-label="Votes">
							<button id="voteBtn" class="btnAttachRound voteBtn" type="button" aria-pressed="false" title="Upvote">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
							</button>
							<div id="voteCount" class="voteCount">0</div>
							<button id="voteDownBtn" class="btnAttachRound voteBtnDown" type="button" aria-pressed="false" title="Downvote">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</button>
						</div>
						<div class="titleBlock">
							<h2 id="ticketTitle" class="ticketTitle"></h2>
							<span id="ticketStatus" class="status"></span>
						</div>
					</div>
					<div class="ticketMeta" id="ticketMeta"></div>
							<div class="summaryBody">
								<div class="summarySection">
									<h3 class="sectionTitle">Description</h3>
									<p id="ticketDescriptionText" class="descriptionText"></p>
								</div>
								<div class="summarySection">
									<h3 class="sectionTitle">Attachments</h3>
									<div id="attachmentsList" class="attachmentsList"></div>
								</div>
							</div>
				</div>

				<div class="card conversation">
					<div class="sectionTitleRow">
						<h3 class="sectionTitle">Comments</h3>
					</div>
					<div id="messages" class="messages"></div>
					<div class="replyBox">
						<input id="replyInput" type="text" placeholder="Type your reply here" aria-label="Reply" />
						<div class="replyActions">
							<button id="attachBtn" class="btnAttachRound" type="button">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12 20.5a6 6 0 0 1-8.49-8.49l10-10a4 4 0 0 1 5.66 5.66l-10 10a2 2 0 1 1-2.83-2.83l9-9"/></svg>
							</button>
							<button id="sendBtn" class="btnSvg" type="button" aria-label="Send">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
							</button>
						</div>
					</div>
				</section>

			<!-- Right sidebar -->
			<aside class="ticketRight">
				<div class="card ticketInfo">
					<h3 class="sectionTitle">Post information</h3>
					<div id="ticketInfoList" class="infoList"></div>
				</div>

				<!-- Helpful resources section to fill right pane -->
				<div class="card ticketActions">
					<h3 class="sectionTitle">Helpful resources</h3>
					<div class="infoList" style="display:flex; flex-direction:column; gap:8px; padding: 0 10px 10px;">
						<a href="/student/announcements" class="settingsNavBtn" style="text-decoration:none;">Announcements</a>
						<a href="/student/faq" class="settingsNavBtn" style="text-decoration:none;">FAQs</a>
						<a href="/student/forum" class="settingsNavBtn" style="text-decoration:none;">Browse other posts</a>
					</div>
				</div>

				<div class="card ticketActions">
					<h3 class="sectionTitle">Post Controls</h3>
					<div class="btnHolder">
						<button id="editPostBtn" class="btnSecondary" type="button"><span class="btnSecondaryText">Edit post</span></button>
					</div>
					<div class="btnHolder">
						<button id="toggleVisibilityBtn" class="btnSecondary" type="button" data-state="public" style="background:#dcfce7;"><span class="btnSecondaryText">Make Private</span></button>
					</div>
					<div class="btnHolder">
						<button id="toggleStatusBtn" class="btnSecondary" type="button" data-status="open" style="background:#fef9c3;"><span class="btnSecondaryText">Make Answered</span></button>
					</div>
					<div class="btnHolder">
						<button id="deleteBtn" class="btnSecondary" style="background-color: #ff7b7bff;" type="button"><span class="btnSecondaryText" style="color: white;">Delete post</span></button>
					</div>
				</div>
			</aside>
		</div>
	</div>
</main>

<!-- Delete confirmation modal -->
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
	<!-- clicking outside container closes modal via JS -->
	<button type="button" class="modalBackdropClose" aria-label="Close"></button>
  
</div>

<script src="/js/student/studentForumFull.js"></script>
