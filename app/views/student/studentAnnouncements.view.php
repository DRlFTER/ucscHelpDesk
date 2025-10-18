<?php
// Student announcements view. Expects $announcements (array) and optional $head provided by controller.
?>

<main id="main-content" class="main-content">
	<div class="page-header">
		<h2 class="page-title">Announcements</h2>
		<p class="page-subtitle">Latest updates and notices</p>
	</div>

	<div class="tickets-container" id="announcements-root" data-announcements='<?php echo json_encode($announcements ?? []); ?>'>
		<!-- studentAnnouncements.js will render announcements here -->
	</div>
</main>

<script src="/js/student/studentAnnouncements.js"></script>
