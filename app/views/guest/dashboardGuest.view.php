<main>
	<div class="fullPage">
			<div class="pageLayout">
				<aside class="adminRight">
				<div id="dashError" style="display:none;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;padding:8px 12px;border-radius:8px;">
					Error: Failed to load data.
				</div>
				<div class="dashboardContent">
						<div class="guestWelcome cardBox" id="guestWelcome"></div>

						<div class="cardContainer" id="cardContainer"></div>

						<div class="contentRow">
							<div class="recentAnnouncements cardBox" id="recentAnnouncements"></div>
							<div class="recentTickets cardBox" id="recentTickets"></div>
						</div>
				</div>
			</aside>
		</div>
	</div>
</main>

<div id="loginPromptOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:10000; align-items:center; justify-content:center;">
	<div style="max-width:420px; width:90%; background:#fff; border-radius:12px; padding:18px; box-shadow:0 10px 30px rgba(0,0,0,0.15);">
		<h3 style="margin:0 0 8px 0; font-weight:600;">Sign in required</h3>
		<p style="margin:0 0 16px 0; color:#394353;">Please log in to view full details and interact with tickets and announcements.</p>
		<div style="display:flex; gap:10px; justify-content:flex-end;">
			<button id="loginCancelBtn" class="btnSecondary btnSecondaryText" style="min-width:110px;">Cancel</button>
			<button id="loginGoBtn" class="btnWSvg btnPrimaryText" style="min-width:110px;">Log in</button>
		</div>
	</div>
  
</div>

<script src="/js/guest/guestDashboard.js" defer></script>