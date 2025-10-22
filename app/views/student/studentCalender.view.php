<main>
	<div class="fullPage">
		<div class="pageHeader">
			<h1 class="pageTitle">Calendar</h1>
			<p class="pageSubtitle">View and manage calendar events</p>
		</div>
		<div class="pageLayout">
            <div class="settingsLeft">
                <div class="todayDate">
                    <h2>21</h2>
                    <h2>Monday</h2>
                </div>
                <div class="todayEvents">
                    <div class="event">
                        <h2>Team sync</h2>
                        <p>All day</p>
                    </div>
                    <div class="event">
                        <h2>Ticket review</h2>
                        <p>All day</p>
                    </div>
                </div>
                <?php require dirname(__DIR__) . '/calender.view.php'; ?>