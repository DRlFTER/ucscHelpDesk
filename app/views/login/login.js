(function () {
  document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector(".imgCarousel");
    if (!carousel) return;

    const imgEl = carousel.querySelector(".carouselImage");
    const titleEl = carousel.querySelector(".textCarousel h2");
    const pillsWrap = carousel.querySelector(".pillContainer");
    if (!imgEl || !titleEl || !pillsWrap) return;

    imgEl.draggable = false;

    // Texts for the three positions (left, center, right)
    const texts = [
      "Welcome to UCSC HelpDesk",
      "Get support fast",
      "Lorem ipsum",
    ];

    // Use existing pills if present, else create 3
    let pills = Array.from(pillsWrap.querySelectorAll(".pill"));
    if (pills.length !== 3) {
      pillsWrap.innerHTML = "";
      pills = Array.from({ length: 3 }, (_, i) => {
        const p = document.createElement("div");
        p.className = "pill" + (i === 0 ? " pillActive" : "");
        pillsWrap.appendChild(p);
        return p;
      });
    }

    // Geometry and state
    let positions = [0, 0, 0]; // x offsets for left, center, right (px)
    let current = 0; // 0: left, 1: center, 2: right
    let dir = 1; // direction across states: 1 forward, -1 backward
    let animId = 0;
    let timerId = 0;
    let paused = false;

    // Animation config
    const stepMs = 16; // fallback when RAF not available
    const animDuration = 600; // ms per transition
    const autoInterval = 4000; // ms between transitions

    function computePositions() {
      const cWidth = carousel.clientWidth;
      const cHeight = carousel.clientHeight;

      // Estimate displayed image width when height is constrained to container height
      let imgDisplayWidth;
      if (imgEl.naturalWidth && imgEl.naturalHeight && cHeight) {
        imgDisplayWidth = (imgEl.naturalWidth * cHeight) / imgEl.naturalHeight;
      } else {
        imgDisplayWidth = imgEl.getBoundingClientRect().width;
      }

      const overflow = Math.max(0, Math.round(imgDisplayWidth - cWidth));
      const left = 0;
      const right = -overflow;
      const center = Math.round((left + right) / 2);
      positions = [left, center, right];

      // Snap to current state's position when recomputing
      applyTransform(positions[current]);
    }

    function setActivePill(i) {
      pills.forEach((p, idx) => {
        p.classList.toggle("pillActive", idx === i);
      });
    }

    function setText(i) {
      titleEl.textContent = texts[i] || "";
    }

    function applyTransform(x) {
      imgEl.style.transform = `translateX(${x}px)`;
    }

    function animateTo(targetX, onDone) {
      cancelAnim();
      const startX = getCurrentX();
      const delta = targetX - startX;
      if (Math.abs(delta) < 1) {
        applyTransform(targetX);
        onDone && onDone();
        return;
      }
      const startTime = performance.now();

      function tick(now) {
        const t = Math.min(1, (now - startTime) / animDuration);
        // easeInOutQuad
        const eased = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
        const x = startX + delta * eased;
        applyTransform(x);
        if (t < 1) {
          animId = requestAnimationFrame(tick);
        } else {
          applyTransform(targetX);
          onDone && onDone();
        }
      }

      animId = requestAnimationFrame(tick);
    }

    function getCurrentX() {
      const tr = getComputedStyle(imgEl).transform;
      if (tr && tr !== "none") {
        const m = tr.match(/matrix\(([^)]+)\)/);
        if (m && m[1]) {
          const parts = m[1].split(",").map((s) => parseFloat(s.trim()));
          // matrix(a,b,c,d,tx,ty) => tx is parts[4]
          return parts[4] || 0;
        }
      }
      return 0;
    }

    function gotoState(i) {
      current = Math.max(0, Math.min(2, i));
      setActivePill(current);
      setText(current);
      animateTo(positions[current]);
      restartAuto();
    }

    function autoNext() {
      if (paused) return;
      // bounce between 0 -> 1 -> 2 -> 1 -> 0 -> ...
      if (current === 2) dir = -1;
      else if (current === 0) dir = 1;
      gotoState(current + dir);
    }

    function startAuto() {
      stopAuto();
      timerId = window.setInterval(autoNext, autoInterval);
    }

    function stopAuto() {
      if (timerId) {
        clearInterval(timerId);
        timerId = 0;
      }
    }

    function restartAuto() {
      startAuto();
    }

    function cancelAnim() {
      if (animId) cancelAnimationFrame(animId);
      animId = 0;
    }

    // Hover pause
    carousel.addEventListener("mouseenter", () => (paused = true));
    carousel.addEventListener("mouseleave", () => (paused = false));

    // Pill click navigation
    pills.forEach((p, i) => p.addEventListener("click", () => gotoState(i)));

    // Recompute layout on resize and image load
    window.addEventListener("resize", computePositions);
    imgEl.addEventListener("load", computePositions);

    // Init
    computePositions();
    setText(current);
    setActivePill(current);
    startAuto();
  });
})();
