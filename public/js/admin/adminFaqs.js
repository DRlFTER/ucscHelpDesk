(function () {
  const searchInput = document.getElementById("faqSearch");
  const listEl = document.getElementById("faqsList");
  const pagerEl = document.getElementById("faqsPagination");
  const newFaqBtn = document.getElementById("newFaqBtn");

  const deleteModal = document.getElementById("deleteFaqModal");
  const cancelDeleteBtn = document.getElementById("cancelDeleteFaqBtn");
  const confirmDeleteBtn = document.getElementById("confirmDeleteFaqBtn");

  const editModal = document.getElementById("editFaqModal");
  const faqForm = document.getElementById("faqForm");
  const faqQuestion = document.getElementById("faqQuestion");
  const faqAnswer = document.getElementById("faqAnswer");
  const cancelFaqEditBtn = document.getElementById("cancelFaqEditBtn");
  const editFaqModalTitle = document.getElementById("editFaqModalTitle");

  let faqs = [];
  let page = 1;
  const perPage = (() => {
    const stored = parseInt(localStorage.getItem("ucsc_table_rows"), 10);
    return Number.isFinite(stored) && stored >= 5 && stored <= 100 ? stored : 10;
  })();
  let meta = { total: 0, totalPages: 1 };
  let editingId = null;
  let pendingDeleteId = null;

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function debounce(fn, wait) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null, args), wait);
    };
  }

  function openModal(el) {
    if (!el) return;
    el.classList.add("open");
    el.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
  }
  function closeModal(el) {
    if (!el) return;
    el.classList.remove("open");
    el.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  }

  function renderList() {
    if (!listEl) return;
    const html = (faqs || [])
      .map(
        (f) => `
        <div class="ticket faqCard" data-id="${esc(f.id)}">
          <div class="ticketRow1">
            <div class="ticketName">
              <h2>${esc(f.question)}</h2>
            </div>
            <div class="faqActions">
              <button type="button" class="btnSecondary editFaqBtn" aria-label="Edit FAQ ${esc(
                f.id
              )}"><span class="btnAttachText">Edit</span></button>
              <button type="button" class="btnPrimary btnDanger deleteFaqBtn" aria-label="Delete FAQ ${esc(
                f.id
              )}" style="background:#fee2e1;color:#7f1d1d;"><span class="btnPrimaryText">Delete</span></button>
            </div>
          </div>
          <div class="ticketRow2">
            <div class="ticketDetails" style="border-right:none;padding-right:0;">
              <div class="ticketDetail" style="max-width:100%;">
                <h2>Answer:</h2>
                <div class="ticketDetailHolder faqAnswerText">${esc(
                  f.answer
                )}</div>
              </div>
            </div>
          </div>
        </div>`
      )
      .join("");

    listEl.innerHTML =
      html ||
      '<div class="ticketsEmpty" style="padding:20px;">No FAQs found.</div>';

    renderPagination();
  }

  function renderPagination() {
    if (!pagerEl) return;
    const totalPages = Math.max(1, parseInt(meta.totalPages || 1, 10));
    pagerEl.innerHTML = "";

    const makeBtn = (
      num,
      active = false,
      label,
      isHtml = false,
      aria = null
    ) => {
      const d = document.createElement("div");
      d.className = "ticketsPageNum" + (active ? " active" : "");
      if (isHtml) d.innerHTML = label;
      else d.innerHTML = `<h2>${esc(label || String(num))}</h2>`;
      if (aria) d.setAttribute("aria-label", aria);
      d.addEventListener("click", () => {
        if (num >= 1 && num <= totalPages && num !== page) {
          page = num;
          loadFaqs();
        }
      });
      return d;
    };

    if (page > 1) {
      const leftSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="m121.38-480 289.31 289.31q10.92 10.92 11.12 27.07.19 16.16-10.73 27.08-10.93 10.92-27.08 10.92t-27.08-10.92L59.08-434.77q-9.85-9.85-14.08-21.31-4.23-11.46-4.23-23.92T45-503.92q4.23-11.46 14.08-21.31l297.84-297.85q10.93-10.92 26.89-11.11 15.96-.19 26.88 10.73 10.92 10.92 10.92 27.08 0 16.15-10.92 27.07L121.38-480Z"/></svg>';
      pagerEl.appendChild(
        makeBtn(page - 1, false, leftSvg, true, "Previous page")
      );
    }

    const maxButtons = 5;
    let start = Math.max(1, page - Math.floor(maxButtons / 2));
    let end = Math.min(totalPages, start + maxButtons - 1);
    start = Math.max(1, Math.min(start, Math.max(1, end - maxButtons + 1)));
    for (let p = start; p <= end; p++) {
      pagerEl.appendChild(makeBtn(p, p === page));
    }

    if (page < totalPages) {
      const rightSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#000000"><path d="M550.23-480 260.92-769.31q-10.92-10.92-11.11-26.88-.19-15.96 10.73-26.89Q271.46-834 287.42-834q15.96 0 26.89 10.92l298.23 297.85q9.84 9.85 14.07 21.31 4.24 11.46 4.24 23.92t-4.24 23.92q-4.23 11.46-14.07 21.31L314.69-136.92q-10.92 10.92-27.07 11.11-16.16.19-27.08-10.73-10.92-10.92-10.92-26.88 0-15.96 10.92-26.89L550.23-480Z"/></svg>';
      pagerEl.appendChild(
        makeBtn(page + 1, false, rightSvg, true, "Next page")
      );
    }
  }

  async function loadFaqs() {
    if (listEl)
      listEl.innerHTML = '<div class="ticketsLoading">Loading FAQs…</div>';
    try {
      const q = (searchInput?.value || "").trim();
      const qs = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
        search: q,
      });
      const res = await fetch(`/admin/faqsData?${qs.toString()}`, {
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to load FAQs");
      const payload = await res.json();
      faqs = Array.isArray(payload?.data) ? payload.data : [];
      meta = payload?.meta || { total: faqs.length, totalPages: 1 };
      renderList();
    } catch (err) {
      if (listEl)
        listEl.innerHTML =
          '<div class="ticketsError">Unable to load FAQs. Please try again.</div>';
      console.error("FAQs load error:", err);
    }
  }

  async function apiPost(url, data) {
    const form = new URLSearchParams();
    Object.entries(data || {}).forEach(([k, v]) => form.append(k, v));
    const res = await fetch(url, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: form.toString(),
    });
    if (!res.ok) throw new Error(`Request failed: ${res.status}`);
    return res.json();
  }

  listEl?.addEventListener("click", (e) => {
    const editBtn = e.target.closest?.(".editFaqBtn");
    const delBtn = e.target.closest?.(".deleteFaqBtn");
    const card = e.target.closest?.(".faqCard");
    if (!card) return;
    const id = parseInt(card.getAttribute("data-id"), 10);
    if (!Number.isFinite(id)) return;

    if (editBtn) {
      const item = faqs.find((f) => f.id === id);
      if (!item) return;
      editingId = id;
      editFaqModalTitle.textContent = "Edit FAQ";
      faqQuestion.value = item.question || "";
      faqAnswer.value = item.answer || "";
      openModal(editModal);
      return;
    }
    if (delBtn) {
      pendingDeleteId = id;
      openModal(deleteModal);
      return;
    }
  });

  newFaqBtn?.addEventListener("click", () => {
    editingId = null;
    editFaqModalTitle.textContent = "New FAQ";
    faqForm.reset();
    openModal(editModal);
  });

  cancelDeleteBtn?.addEventListener("click", () => closeModal(deleteModal));
  deleteModal
    ?.querySelector(".modalBackdropClose")
    ?.addEventListener("click", () => closeModal(deleteModal));
  confirmDeleteBtn?.addEventListener("click", async () => {
    if (!Number.isFinite(pendingDeleteId)) return;
    try {
      await apiPost("/admin/faqDelete", { id: String(pendingDeleteId) });
      closeModal(deleteModal);
      pendingDeleteId = null;
      // reload current page
      await loadFaqs();
    } catch (e) {
      console.error("Delete failed", e);
    }
  });

  cancelFaqEditBtn?.addEventListener("click", () => closeModal(editModal));
  editModal
    ?.querySelector(".modalBackdropClose")
    ?.addEventListener("click", () => closeModal(editModal));
  faqForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const q = (faqQuestion.value || "").trim();
    const a = (faqAnswer.value || "").trim();
    if (!q || !a) return;
    try {
      if (editingId == null) {
        await apiPost("/admin/faqCreate", { question: q, answer: a });
      } else {
        await apiPost("/admin/faqUpdate", {
          id: String(editingId),
          question: q,
          answer: a,
        });
      }
      closeModal(editModal);
      await loadFaqs();
    } catch (err) {
      console.error("Save failed", err);
    }
  });

  searchInput?.addEventListener(
    "input",
    debounce(() => {
      page = 1;
      loadFaqs();
    }, 250)
  );

  loadFaqs();
})();
