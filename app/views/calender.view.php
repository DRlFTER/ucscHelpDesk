<?php
    $title = 'Calendar';
    $head = '<link rel="stylesheet" href="/css/calender/calender.css">';
?>

<main>
    <div class="fullPage">
        <div class="pageHeader">
            <h1 class="pageTitle">Calendar</h1>
            <p class="pageSubtitle">View and manage calendar events</p>
        </div>
        <div class="pageLayout">
            <div class="settingsLeft">
                <div class="todayDate">
                    <h2 id="todayNum">21</h2>
                    <h2 id="todayWeekday">Monday</h2>
                </div>
                <div class="todayEvents">
                    <div class="sectionTitle">Today</div>
                    <div class="noEvents" id="noEventsRow" style="display:none;">No events today</div>
                    <div class="event">
                        <h2>Team sync</h2>
                        <p>All day</p>
                    </div>
                    <div class="event">
                        <h2>Ticket review</h2>
                        <p>All day</p>
                    </div>
                </div>
                <div class="addEvent">
                    <input type="text" class="inputEvent" id="eventTitle" placeholder="Add event" />
                    <button class="btnSvg" id="addEventBtn" title="Add event">
                        <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#ffffffff"><path d="M433-433H222q-19.75 0-33.37-13.68Q175-460.35 175-480.18q0-19.82 13.63-33.32Q202.25-527 222-527h211v-211q0-19.63 13.68-33.81Q460.35-786 480.18-786q19.82 0 33.32 14.19Q527-757.63 527-738v211h211q19.63 0 33.81 13.68Q786-499.65 786-479.82q0 19.82-14.19 33.32Q757.63-433 738-433H527v211q0 19.75-13.68 33.37Q499.65-175 479.82-175q-19.82 0-33.32-13.63Q433-202.25 433-222v-211Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="settingsRight">
                <div class="calendarContainer">
                    <div class="calendarHeader">
                        <button id="prevMonthBtn" class="btnSecondary" style="background:none;"><span class="btnSecondaryText">&lt;</span></button>
                        <h2 id="monthYear"></h2>
                        <button id="nextMonthBtn" class="btnSecondary" style="background:none;"><span class="btnSecondaryText">&gt;</span></button>
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

    <script src="/js/calender/calender.js"></script>
</main>
