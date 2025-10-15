<main>
  <div class="lostFoundContainer">
    <div class="lfHeader">
      <a class="backBtn" href="/student/dashboard" aria-label="Back">
        <img src="/assets/arrow-left.svg" alt="Back" />
      </a>
      <h2>Lost &amp; Found</h2>
      <div class="spacer"></div>
    </div>

    <div class="lfControls">
      <div class="searchWrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search Lost &amp; Found..." />
      </div>
      <div class="filters">
        <div class="filterGroup">
          <label><input type="radio" name="type" value="all" checked /> All</label>
          <label><input type="radio" name="type" value="lost" /> Lost</label>
          <label><input type="radio" name="type" value="found" /> Found</label>
        </div>
      </div>
    </div>

    <div class="lfContent">
      <aside class="lfSidebar">
        <h4>Filters</h4>
        <div class="sidebarDropdowns">
          <div class="field">
            <label class="label" for="lfDateSide">Date</label>
            <select id="lfDateSide" name="dateSide">
              <option value="any" selected>Any time</option>
              <option value="24h">Past 24 hours</option>
              <option value="7d">Past 7 days</option>
              <option value="30d">Past 30 days</option>
            </select>
          </div>
          <div class="field">
            <label class="label" for="lfLocationSide">Location</label>
            <select id="lfLocationSide" name="locationSide">
              <option value="all" selected>All locations</option>
              <option value="s104">S104</option>
              <option value="cafeteria">UCSC Cafeteria</option>
              <option value="w003">W003</option>
            </select>
          </div>
        </div>
      </aside>

      <section class="lfList">
        <article class="lfCard found">
          <h3><span class="state found">Found</span> Wallet with UCSC ID inside – Washroom Corridor</h3>
          <p>Found a black wallet near the washroom corridor. It has a UCSC student ID (Kaweesha P) and a few cards inside. I submitted it to the Help Desk front desk.</p>
          <ul class="meta">
            <li>Location: S104</li>
            <li>Found on: August 2, around 12 PM</li>
            <li>Photo Attached</li>
          </ul>
          <div class="lfFooter">
            <strong>4 Comments</strong>
            <span>DM Finder</span>
            <span class="time">7 hours ago</span>
          </div>
        </article>

        <article class="lfCard lost">
          <h3><span class="state lost">Lost</span> Blue Samsung Earbuds – Cafeteria Bench</h3>
          <p>I lost a pair of blue Samsung Galaxy Buds (with a bit of a crack on the lid) this morning around 10:30 AM near the cafeteria benches. If anyone finds it, please let me know here or drop it at the Help Desk counter.</p>
          <ul class="meta">
            <li>Location: UCSC Cafeteria</li>
            <li>Lost on: August 2, 10:30 AM</li>
            <li>Contact: auto-linked to UCSC email</li>
          </ul>
          <div class="lfFooter">
            <strong>2 Comments</strong>
            <span>I’ve seen it</span>
            <span class="time">19 hours ago</span>
          </div>
        </article>
      </section>

      <aside class="lfActions">
        <a href="#" class="btnWSvg btnPrimaryText lfGreen" style="text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
            <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
          </svg>
          <span>Found Item</span>
        </a>
  <a href="#" class="btnWSvg btnPrimaryText lfRed" style="text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none">
            <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z" fill="#FEF7FF"/>
          </svg>
          <span>Lost Item</span>
        </a>
      </aside>
    </div>
  </div>
</main>
<script src="/js/student/lostFound.js"></script>