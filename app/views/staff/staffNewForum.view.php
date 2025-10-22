<?php
include_once(__DIR__ . "/../../views/common/navbar.php");
$head = '<link rel="stylesheet" href="/css/student/studentNewForum.css">';
?>

<main>
	<div class="ticketPage">
		<?php if (!empty($flash) && ($flash['type'] ?? '') === 'error'): ?>
			<div class="toast" role="status" aria-live="polite" style="background:#ef4444">
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14h-2v-2h2v2zm0-4h-2V6h2v6z"/></svg>
				<span><?= htmlspecialchars($flash['message'] ?? '') ?></span>
			</div>
		<?php endif; ?>
		<div class="ticketHeader">
			<h2 class="titleText">New Forum Post</h2>
			<p class="subtitle">Start a discussion with peers or ask a question</p>
		</div>

		<div class="ticketGrid">
			<!-- Post form (copied from ticket form) -->
			<section class="ticketCard">
				<form id="ticketForm" action="/student/newForum" method="POST" enctype="multipart/form-data">

					<!-- Ticket type toggle -->
					<div class="field">
						<label class="label">Post Options</label>
						<div class="ticketToggle" role="group" aria-label="Post visibility">
							<button type="button" class="btnAttach btnAttachText active" data-value="public">
								<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm-40-82v-78q-33 0-56.5-23.5T360-320v-40L168-552q-3 18-5.5 36t-2.5 36q0 121 79.5 212T440-162Zm276-102q41-45 62.5-100.5T800-480q0-98-54.5-179T600-776v16q0 33-23.5 56.5T520-680h-80v80q0 17-11.5 28.5T400-560h-80v80h240q17 0 28.5 11.5T600-440v120h40q26 0 47 15.5t29 40.5Z"/></svg> 
									Public post
							</button>
							<button type="button" class="btnAttach btnAttachText" data-value="draft">
								<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm240-200q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z"/></svg> 
									Save Draft
							</button>
						</div>
						<input type="hidden" name="ticketType" id="ticketType" value="public">
					</div>

					<!-- Title -->
					<div class="field">
						<label class="label" for="title">Title/Subject <span style="color:#ef4444; margin-left:2px">*</span></label>
						<input id="title" name="title" type="text" placeholder="What do you want to discuss?" required>
					</div>

					<div class="row2">
						<div class="field full-width">
							<label class="label" for="subcategory">Topic <span style="color:#ef4444; margin-left:2px">*</span></label>
							<input type="hidden" name="category" id="mainCategory" value="">
							<select id="subcategory" name="subcategory" required>
								<option value="" disabled selected>Select topic</option>
								<option value="general" data-main="General">General</option>
								<option value="it_support" data-main="IT Support">IT Support</option>
								<option value="finance" data-main="Finance">Finance</option>
								<option value="exams" data-main="Examinations">Examinations</option>
								<option value="counselling" data-main="Counselling">Counselling</option>
								<option value="other" data-main="Other">Other</option>
							</select>
						</div>
					</div>

					<!-- Details -->
					<div class="field">
						<label class="label" for="details">Description <span style="color:#ef4444; margin-left:2px">*</span></label>
						<textarea id="details" name="details" rows="6" placeholder="Write your question or discussion details here." required></textarea>
					</div>

					<!-- Attachments -->
					<div class="field">
						<label class="label">Attachments</label>
						<div id="dropzone" class="dropzone">
							<div class="dzInner">
								<svg class="dzIconLg" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
								<div>Drag and drop files here or <span class="browse">browse files</span></div>
								<input id="fileInput" type="file" name="attachments[]" multiple hidden>
							</div>
							<ul id="fileList" class="fileList"></ul>
						</div>
					</div>

					<div class="btnHolder">
						<button class="btnPrimary btnPrimaryText" type="submit">
							<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-160v-240l320-80-320-80v-240l760 320-760 320Z"/></svg>
                            Publish Post
						</button>
					</div>
				</form>
			</section>

			<!-- Right: Sidebar (copied) -->
			<aside class="sidebar">
				<section class="sideCard">
					<h3>Tips for Better Posts</h3>
					<div class="tipsList">
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Provide context and what you’ve tried</span>
						</div>
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Use clear, searchable titles</span>
						</div>
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Tag the right topic</span>
						</div>
					</div>
				</section>

				<section class="sideCard">
					<h3>Knowledge Base</h3>
					<div class="kbSearch">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search FAQs, forums, and help articles..." />
					</div>
				</section>
			</aside>
		</div>
	</div>
</main>

<script src="/js/staff/staffTickets.js"></script>
<script src="/js/staff/staffNewForum.js"></script>

<?php if (!empty($flash) && ($flash['type'] ?? '') === 'success'): ?>
	<div class="toast" role="status" aria-live="polite">
		<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.2 4.8 12l1.4-1.4L9 13.4 17.8 4.6l1.4 1.4z"/></svg>
		<span><?= htmlspecialchars($flash['message'] ?? 'Saved successfully.') ?></span>
	</div>
	<script>
		setTimeout(() => { window.location.href = '/student/forum'; }, 1500);
	</script>
<?php endif; ?>
