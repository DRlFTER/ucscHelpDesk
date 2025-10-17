<?php /* Navbar is included in layouts/main.php */ ?>

<main>
	<div class="ticketPage">
		<div class="ticketHeader">
			<h2 class="titleText">Report a <?= (isset($mode) && $mode === 'found') ? 'Found' : 'Lost' ?> Item</h2>
			<p class="subtitle">
				<?= (isset($mode) && $mode === 'found')
					? 'Describe the found item and where/when you found it'
					: 'Describe the lost item and when/where you last saw it' ?>
			</p>
		</div>

		<div class="ticketGrid">
			<!-- Lost item form (copied from New Ticket) -->
			<section class="ticketCard">
				<form id="ticketForm" action="<?= htmlspecialchars($formAction ?? '/student/newLostItem') ?>" method="POST" enctype="multipart/form-data">

					<!-- Title -->
					<div class="field">
						<label class="label" for="title">Item Title</label>
						<input id="title" name="title" type="text" placeholder="e.g., <?= (isset($mode) && $mode === 'found') ? 'Wallet near S104 washroom' : 'Blue Samsung Earbuds' ?>" required>
					</div>

					<!-- Category + Priority -->
					<div class="row2">
						<div class="field">
							<label class="label" for="category">Category</label>
							<select id="category" name="category" required>
								<option value="" disabled selected>Select category</option>
								<option value="electronics">Electronics</option>
								<option value="accessories">Accessories</option>
								<option value="documents">Documents</option>
								<option value="other">Other</option>
							</select>
						</div>
						<div class="field whenField">
							<label class="label" for="when">Date &amp; Time</label>
							<input id="when" name="when" type="datetime-local" required
								   value="<?= htmlspecialchars($_POST['when'] ?? '') ?>" />
						</div>
					</div>

					<!-- Issue details -->
					<div class="field">
						<label class="label" for="details">Details</label>
						<textarea id="details" name="details" rows="6" placeholder="Describe the lost item, last seen location, date/time, and any identifiers (color, brand, unique marks)." required></textarea>
					</div>

                    <!-- Contact Info -->
					<div class="field">
						<label class="label">Contact Info</label>
					</div>
					<div class="row2">
						<div class="field">
							<label class="label" for="contact_mobile">Mobile Number</label>
							<input id="contact_mobile" name="contact_mobile" type="tel" inputmode="tel" pattern="[0-9+\-\s()]{7,20}" placeholder="e.g., 071 234 5678">
						</div>
						<div class="field">
							<label class="label" for="contact_email">Email</label>
							<input id="contact_email" name="contact_email" type="email" placeholder="e.g., index@stu.ucsc.cmb.aclk">
						</div>
					</div>

					<!-- Attachments -->
					<div class="field">
						<label class="label">Attachments</label>
						<div id="dropzone" class="dropzone">
							<div class="dzInner">
								<svg class="dzIconLg" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
								<div>Drag and drop photos here or <span class="browse">browse files</span></div>
								<input id="fileInput" type="file" name="attachments[]" multiple hidden>
							</div>
							<ul id="fileList" class="fileList"></ul>
						</div>
					</div>

					<div class="btnHolder">
						<button class="btnPrimary btnPrimaryText" type="submit">
							<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-160v-240l320-80-320-80v-240l760 320-760 320Z"/></svg>
							<?= (isset($mode) && $mode === 'found') ? 'Submit Found Item' : 'Submit Lost Item' ?>
						</button>
					</div>
				</form>
			</section>

			<!-- Right: Sidebar -->
			<aside class="sidebar">
				<section class="sideCard">
					<h3>Tips to help you recover your item</h3>
					<div class="tipsList">
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Add details like color, brand, or unique marks</span>
						</div>
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Specify last known location and time</span>
						</div>
						<div class="tipLine">
							<svg class="tipIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="20" height="20"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>
							<span>Attach a clear photo if you have one</span>
						</div>
					</div>
				</section>

			</aside>
		</div>
	</div>
</main>

<script src="/js/student/studentNewLostItem.js"></script>
