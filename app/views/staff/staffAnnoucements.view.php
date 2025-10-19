<?php
// Expects $announcements (array) and optional $head provided by controller
?>

<main id="main-content" class="main-content">
	<div class="page-header">
		<h2 class="page-title">Announcements</h2>
		<p class="page-subtitle">Latest updates and notices</p>
	</div>

	<div class="ticket-action" style="width:250px;justify-content: center;align-items: center;display: flex;margin-left: auto;margin-right: auto;margin-top: 20px;margin-bottom: 20px;">
<button class="ticket-action-btn" onclick="console.log('Clicked!'); window.location.href='http://kaviv1/index.php?url=staff/staffAnnCreate';">
    <span>Create New Announcement</span>
</button>
	</div>

   

	<div class="tickets-container" id="announcements-root" data-announcements='<?php echo json_encode($announcements ?? []); ?>'>
		<!-- announcements.js will render announcements here -->
	</div>

<?php if (defined('DEBUG') && DEBUG && !empty($dbError)): ?>
    <div class="debug" style="max-width:900px;margin:12px auto;padding:10px;background:#fee;border:1px solid #fbb;color:#600;">
        <strong>DB Error:</strong> <?php echo htmlspecialchars($dbError); ?>
    </div>
<?php endif; ?>
</main>

<?php
if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
    echo "<div id='success-toast' style='position:fixed;top:20px;right:20px;background:#4c4;color:white;padding:10px;border-radius:5px;z-index:9999;'>
            $successMsg
          </div>
          <script>
            setTimeout(function() {
                document.getElementById('success-toast').style.display = 'none';
                window.location.reload();
            }, 2000);  // Auto-hide & reload after 2s
          </script>";
}
?>