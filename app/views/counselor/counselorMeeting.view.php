<main>
  <div class="fullPage">
    <div class="pageHeader">
      <h1 class="pageTitle">Meeting with Brian</h1>
      <p class="pageSubtitle">Private counselling session </p>
    </div>

    <div class="pageLayout meetingLayout">
      <!-- Meeting surface (full width; no side navbar) -->
      <aside class="adminRight">
        <div class="meetingSurface">
          <div class="videoStage">
            <div class="videoTile remoteTile" aria-label="Remote participant video">
              <div class="videoPlaceholder">
                <div class="avatar remoteAvatar">S</div>
                <span>Brian (Student)</span>
              </div>
            </div>
            <div class="videoTile localTile" aria-label="Your video">
              <div class="videoPlaceholder">
                <div class="avatar localAvatar">C</div>
                <span>You (Counselor)</span>
              </div>
            </div>
          </div>

          <div class="meetingSidebar" aria-label="Meeting details">
            <!-- Student Details Card -->
            <section class="sectionCard" aria-labelledby="studentDetailsTitle">
              <h3 id="studentDetailsTitle" class="sectionTitle">Student Details</h3>
              <div class="detailRow">
                <div class="label">Name</div>
                <div class="value">Brian Weerasinghe</div>
              </div>
              <div class="detailRow">
                <div class="label">Year</div>
                <div class="value">2nd Year</div>
              </div>
              <div class="detailRow">
                <div class="label">Course</div>
                <div class="value">Computer Science</div>
              </div>
              <div class="detailRow">
                <div class="label">Email</div>
                <div class="value">brian@gmail.com</div>
              </div>
            </section>

            <!-- Ticket Details Card -->
            <section class="sectionCard" aria-labelledby="ticketDetailsTitle">
              <h3 id="ticketDetailsTitle" class="sectionTitle">Ticket Details</h3>
              <div class="detailRow">
                <div class="label">Code</div>
                <div class="value">TKT-4827</div>
              </div>
              <div class="detailRow">
                <div class="label">Title</div>
                <div class="value">Can't Sleep</div>
              </div>
              <div class="detailRow column">
                <div class="label">Description</div>
                <div class="value">
                    I'm stressed so much, I just can't sleep at night. Any tips? Or articles that I can try to read and find help?
                </div>
              </div>
              <div class="detailRow">
                <div class="label">Created</div>
                <div class="value">Oct 21, 2025 at 10:15 AM</div>
              </div>
            </section>
          </div>
        </div>

        <!-- Bottom toolbar -->
        <div class="meetingToolbar" role="toolbar" aria-label="Meeting controls">
          <button type="button" class="btnSvg ctlBtn" data-action="mic" aria-pressed="false" title="Mute / Unmute microphone">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 14a4 4 0 0 0 4-4V6a4 4 0 1 0-8 0v4a4 4 0 0 0 4 4Zm6-4a6 6 0 0 1-12 0H4a8 8 0 0 0 16 0h-2Z" fill="#fff"/></svg>
          </button>
          <button type="button" class="btnSvg ctlBtn" data-action="camera" aria-pressed="false" title="Turn camera on/off">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M17 10.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3.5l4 4v-11l-4 4Z" fill="#fff"/></svg>
          </button>
          <button type="button" class="btnSvg ctlBtn" data-action="screen" aria-pressed="false" title="Share screen">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M3 4h18a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm7 14h4l1 2H9l1-2Z" fill="#fff"/></svg>
          </button>
          <div class="toolbarSpacer"></div>
          <button type="button" class="btnWSvg endBtn" data-action="leave" title="End meeting">
            <span class="btnPrimaryText">End</span>
          </button>
        </div>
      </aside>
    </div>
  </div>
</main>

<script src="/js/counselor/counselorMeeting.js" defer></script>
