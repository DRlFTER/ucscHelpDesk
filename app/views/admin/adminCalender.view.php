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
                    <div class="noEvents">No events for today</div>
                    <div class="event">
                        <h2>Event Title</h2>
                        <p>All day</p>
                    </div>
                </div>
                <div class="addEvent">
                    <div class="inputEvent">
                        <input type="text" id="eventTitle" placeholder="Event Title" />
                         <div class="btnHolder">
                           <button class="btnSvg">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="25"
                height="25"
                viewBox="0 0 25 25"
                fill="none"
              >
                <path
                  d="M3.5 20.2793V14.2793L11.5 12.2793L3.5 10.2793V4.2793L22.5 12.2793L3.5 20.2793Z"
                  fill="white"
                />
              </svg>
            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="settingsRight">
                <div class="calendarContainer">
                    <div class="calendarHeader">
                        <button id="prevMonthBtn" class="btnSecondary"><span class="btnSecondaryText">&lt;</span></button>
                        <h2 id="monthYear"></h2>
                        <button id="nextMonthBtn" class="btnSecondary"><span class="btnSecondaryText">&gt;</span></button>
                    </div>
                    <div class="calendarGrid">
                        <div class="calendarWeekdays">
                            <div>Sun</div>
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                        </div>
                        <div id="calendarDays" class="calendarDays"></div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</main>

<script src="/js/admin/adminCalender.js"></script>
