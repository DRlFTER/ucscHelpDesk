// Simple image carousel for the .imgCarousel container on the login page
// Features: auto-rotate, pills navigation, pause on hover, swipe and arrow keys

(function () {
  document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector(".imgCarousel");
    if (!carousel) return;

    const imgEl = carousel.querySelector(".carouselImage");
    const titleEl = carousel.querySelector(".textCarousel h2");
    const pillsWrap = carousel.querySelector(".pillContainer");
    if (!imgEl || !titleEl || !pillsWrap) return;

    // Configure slides here; you can change/add paths as needed
    const slides = [
      { src: "/public/assets/imgs/1.png", title: "Welcome to HelpDesk" },
      {
        src: "/public/assets/imgs/2.png",
        title: "Lorem ipsum dolor sit amet",
      },
    ];

    let index = 0;
    let timer = null;
    const intervalMs = 4000;

    // Build pills dynamically based on slides
    pillsWrap.innerHTML = "";
    const pills = slides.map((_, i) => {
      const pill = document.createElement("div");
      pill.className = "pill" + (i === index ? " pillActive" : "");
      pill.role = "button";
      pill.tabIndex = 0;
      pill.ariaLabel = `Go to slide ${i + 1}`;
      pill.addEventListener("click", () => goTo(i));
      pill.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          goTo(i);
        }
      });
      pillsWrap.appendChild(pill);
      return pill;
    });

    function setActivePill(i) {
      pills.forEach((p, idx) => {
        if (idx === i) p.classList.add("pillActive");
        else p.classList.remove("pillActive");
      });
    }

    function render(i) {
      const slide = slides[i];
      // Trigger a quick fade animation via CSS class
      imgEl.classList.remove("fade");
      // Force reflow to restart animation reliably
      void imgEl.offsetWidth; // eslint-disable-line no-unused-expressions
      imgEl.src = slide.src;
      imgEl.alt = slide.title || `Slide ${i + 1}`;
      titleEl.textContent = slide.title || "";
      imgEl.classList.add("fade");
      setActivePill(i);
    }

    function goTo(i) {
      index = (i + slides.length) % slides.length;
      render(index);
      restartAuto();
    }

    function next() {
      goTo(index + 1);
    }

    function prev() {
      goTo(index - 1);
    }

    function startAuto() {
      stopAuto();
      timer = setInterval(next, intervalMs);
    }

    function stopAuto() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    function restartAuto() {
      startAuto();
    }

    // Pause on hover
    carousel.addEventListener("mouseenter", stopAuto);
    carousel.addEventListener("mouseleave", startAuto);

    // Keyboard navigation (left/right arrows)
    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowRight") next();
      else if (e.key === "ArrowLeft") prev();
    });

    // Touch swipe support
    let touchStartX = null;
    let touchStartY = null;
    carousel.addEventListener(
      "touchstart",
      (e) => {
        if (e.touches && e.touches.length > 0) {
          touchStartX = e.touches[0].clientX;
          touchStartY = e.touches[0].clientY;
        }
      },
      { passive: true }
    );

    carousel.addEventListener(
      "touchend",
      (e) => {
        if (touchStartX === null || touchStartY === null) return;
        const touch = e.changedTouches && e.changedTouches[0];
        if (!touch) return;
        const dx = touch.clientX - touchStartX;
        const dy = touch.clientY - touchStartY;
        // Horizontal swipe with a simple threshold
        if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
          if (dx < 0) next();
          else prev();
        }
        touchStartX = null;
        touchStartY = null;
      },
      { passive: true }
    );

    // Initial render and start
    render(index);
    startAuto();
  });
})();
