(function () {
  function setNavbarVar() {
    const nav = document.querySelector(".navbar");
    const h = nav ? nav.offsetHeight : 64;
    document.documentElement.style.setProperty("--navbar-height", h + "px");
  }
  let rafId;
  function onResize() {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = requestAnimationFrame(setNavbarVar);
  }
  window.addEventListener("resize", onResize);
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", setNavbarVar);
  } else {
    setNavbarVar();
  }
})();

const CACHE_TTL_MS = 5 * 60 * 1000;
function cacheKeyForUser(id) {
  return `admin_user_${id}`;
}
function loadFromCache(id) {
  try {
    const str = localStorage.getItem(cacheKeyForUser(id));
    if (!str) return null;
    const obj = JSON.parse(str);
    if (!obj || !obj._ts || Date.now() - obj._ts > CACHE_TTL_MS) return null;
    return obj.data || null;
  } catch {
    return null;
  }
}
function saveToCache(id, data) {
  try {
    localStorage.setItem(
      cacheKeyForUser(id),
      JSON.stringify({ _ts: Date.now(), data })
    );
  } catch {}
}

function getUserIdFromUrl() {
  const u = new URL(window.location.href);
  const id = u.searchParams.get("id");
  return id ? parseInt(id, 10) : null;
}

async function fetchUser(id) {
  const res = await fetch(`/admin/userData?id=${encodeURIComponent(id)}`, {
    credentials: "include",
  });
  if (!res.ok) throw new Error("Failed to fetch user");
  return res.json();
}

let userData = null;

function esc(s) {
  return String(s == null ? "" : s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function statusClassByRole(role) {
  const r = (role || "").toLowerCase();
  switch (r) {
    case "admin":
      return "status resolved";
    case "staff":
      return "status inProgress";
    case "lecturer":
      return "status underReview";
    case "counselor":
      return "status requested";
    case "student":
    default:
      return "status";
  }
}

function renderHeader() {
  const nameEl = document.getElementById("userName");
  const statusEl = document.getElementById("userStatus");
  const metaEl = document.getElementById("userMeta");
  if (nameEl) nameEl.textContent = userData.name || "User";
  if (statusEl) {
    // Show deleted status if user is deleted, or suspended if suspended
    if (userData.isDeleted) {
      statusEl.className = "status deleted";
      statusEl.textContent = "Deleted";
    } else if (userData.isSuspended) {
      statusEl.className = "status suspended";
      statusEl.textContent = "Suspended";
    } else {
      statusEl.className = statusClassByRole(userData.role);
      const role = (userData.role || "").toString();
      statusEl.textContent = role.charAt(0).toUpperCase() + role.slice(1);
    }
  }
  if (metaEl) {
    const created = userData.createdOn || "";
    let deletedInfo = "";
    if (userData.isDeleted && userData.deletedAt) {
      deletedInfo = `<span style="color: #dc2626;">Deleted: ${esc(
        userData.deletedAt
      )}</span>`;
    }
    let suspendedInfo = "";
    if (userData.isSuspended && userData.suspendedAt && !userData.isDeleted) {
      suspendedInfo = `<span style="color: #d97706;">Suspended: ${esc(
        userData.suspendedAt
      )}</span>`;
    }
    metaEl.innerHTML = `<span>ID: ${esc(userData.id)}</span><span>Email: ${esc(
      userData.email || ""
    )}</span>${
      created ? `<span>Created: ${esc(created)}</span>` : ""
    }${deletedInfo}${suspendedInfo}`;
  }

  // Toggle delete/restore buttons based on user state
  updateDeleteRestoreButtons();
  // Toggle suspend/unsuspend buttons based on user state
  updateSuspendUnsuspendButtons();
}

function updateSuspendUnsuspendButtons() {
  const suspendHolder = document.getElementById("suspendHolder");
  const unsuspendHolder = document.getElementById("unsuspendHolder");

  if (userData.isSuspended) {
    if (suspendHolder) suspendHolder.style.display = "none";
    if (unsuspendHolder) unsuspendHolder.style.display = "block";
  } else {
    if (suspendHolder) suspendHolder.style.display = "block";
    if (unsuspendHolder) unsuspendHolder.style.display = "none";
  }
}

function updateDeleteRestoreButtons() {
  const deleteHolder = document.getElementById("deleteHolder");
  const restoreHolder = document.getElementById("restoreHolder");

  if (userData.isDeleted) {
    if (deleteHolder) deleteHolder.style.display = "none";
    if (restoreHolder) restoreHolder.style.display = "block";
  } else {
    if (deleteHolder) deleteHolder.style.display = "block";
    if (restoreHolder) restoreHolder.style.display = "none";
  }
}

function renderBasic() {
  const c = document.getElementById("basicInfo");
  if (!c) return;
  const rows = [
    { label: "Email", value: userData.email },
    { label: "Phone", value: userData.number || "—" },
    { label: "Role", value: userData.role },
    { label: "Designation", value: userData.designation || "—" },
    {
      label: "Year",
      value: userData.year != null ? String(userData.year) : "—",
    },
  ];
  c.innerHTML = rows
    .map(
      (r) => `
      <div class="infoRow">
        <span class="label">${esc(r.label)}</span>
        <span class="value">${esc(r.value)}</span>
      </div>`
    )
    .join("");
}

function renderInfo() {
  const c = document.getElementById("userInfoList");
  if (!c) return;
  const rows = [
    { label: "User ID", value: userData.id },
    { label: "Type", value: userData.role },
    { label: "Designation", value: userData.designation || "—" },
    { label: "Phone", value: userData.number || "—" },
  ];
  c.innerHTML = rows
    .map(
      (r) => `
      <div class="infoRow">
        <span class="label">${esc(r.label)}</span>
        <span class="value">${esc(r.value)}</span>
      </div>`
    )
    .join("");
}

function openModal(id) {
  const overlay = document.getElementById(id);
  if (!overlay) return () => {};
  overlay.classList.add("open");
  document.body.classList.add("modal-open");
  const closeBtn = overlay.querySelector(".modalBackdropClose");
  const close = () => {
    overlay.classList.remove("open");
    document.body.classList.remove("modal-open");
  };
  closeBtn && closeBtn.addEventListener("click", close, { once: true });
  return close;
}

function wireActions() {
  const editBtn = document.getElementById("editUserBtn");
  const suspendBtn = document.getElementById("suspendBtn");
  const unsuspendBtn = document.getElementById("unsuspendBtn");
  const deleteBtn = document.getElementById("deleteBtn");
  const restoreBtn = document.getElementById("restoreBtn");

  if (editBtn) {
    editBtn.addEventListener("click", () => {
      document.getElementById("editName").value = userData.name || "";
      document.getElementById("editEmail").value = userData.email || "";
      document.getElementById("editNumber").value = userData.number || "";
      document.getElementById("editRole").value = userData.role || "student";
      document.getElementById("editDesignation").value =
        userData.designation || "";
      document.getElementById("editYear").value = userData.year || "";
      const close = openModal("editModal");

      const form = document.getElementById("editUserForm");
      const cancel = document.getElementById("cancelEditBtn");
      const onCancel = (e) => {
        e && e.preventDefault();
        form.removeEventListener("submit", onSubmit);
        cancel.removeEventListener("click", onCancel);
        close();
      };
      const onSubmit = async (e) => {
        e.preventDefault();
        try {
          const fd = new FormData(form);
          fd.append("id", String(userData.id));
          const res = await fetch("/admin/userUpdate", {
            method: "POST",
            body: new URLSearchParams(fd),
            credentials: "include",
          });
          if (!res.ok) throw new Error("Update failed");
          const updated = await res.json();
          userData = updated;
          saveToCache(userData.id, userData);
          renderHeader();
          renderBasic();
          renderInfo();
          try {
            localStorage.setItem("admin_users_bust", String(Date.now()));
          } catch {}
          onCancel();
        } catch (err) {
          console.error(err);
          alert("Failed to update user");
        }
      };
      form.addEventListener("submit", onSubmit);
      cancel.addEventListener("click", onCancel);
    });
  }

  if (deleteBtn) {
    console.log("Delete button found, attaching click handler");
    deleteBtn.addEventListener("click", () => {
      console.log("Delete button clicked");
      const overlay = document.getElementById("deleteModal");
      if (!overlay) {
        console.error("Delete modal not found");
        return;
      }
      const close = openModal("deleteModal");
      const cancelBtn = document.getElementById("cancelDeleteBtn");
      const confirmBtn = document.getElementById("confirmDeleteBtn");

      const onCancel = (e) => {
        e && e.preventDefault();
        cleanup();
        close();
      };
      const onConfirm = async (e) => {
        e && e.preventDefault();
        console.log("Confirming delete for user:", userData.id);
        try {
          const res = await fetch("/admin/userDelete", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(userData.id)}`,
            credentials: "include",
          });
          console.log("Delete response status:", res.status);
          if (!res.ok) throw new Error("Delete failed");
          // Refresh user data to show deleted state
          userData.isDeleted = true;
          localStorage.removeItem(cacheKeyForUser(userData.id));
          try {
            localStorage.setItem("admin_users_bust", String(Date.now()));
          } catch {}
          renderHeader();
          cleanup();
          close();
        } catch (err) {
          console.error("Delete error:", err);
          alert("Failed to delete user");
          cleanup();
          close();
        }
      };

      const cleanup = () => {
        cancelBtn && cancelBtn.removeEventListener("click", onCancel);
        confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      };

      cancelBtn && cancelBtn.addEventListener("click", onCancel);
      confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    });
  }

  if (suspendBtn) {
    console.log("Suspend button found, attaching click handler");
    suspendBtn.addEventListener("click", () => {
      console.log("Suspend button clicked");
      const overlay = document.getElementById("suspendModal");
      if (!overlay) {
        console.error("Suspend modal not found");
        return;
      }
      const close = openModal("suspendModal");
      const cancelBtn = document.getElementById("cancelSuspendBtn");
      const confirmBtn = document.getElementById("confirmSuspendBtn");

      const onCancel = (e) => {
        e && e.preventDefault();
        cleanup();
        close();
      };
      const onConfirm = async (e) => {
        e && e.preventDefault();
        console.log("Confirming suspend for user:", userData.id);
        try {
          const res = await fetch("/admin/userSuspend", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(userData.id)}`,
            credentials: "include",
          });
          console.log("Suspend response status:", res.status);
          if (!res.ok) throw new Error("Suspend failed");
          // Refresh user data to show suspended state
          userData.isSuspended = true;
          userData.suspendedAt = new Date()
            .toISOString()
            .slice(0, 19)
            .replace("T", " ");
          localStorage.removeItem(cacheKeyForUser(userData.id));
          try {
            localStorage.setItem("admin_users_bust", String(Date.now()));
          } catch {}
          renderHeader();
          cleanup();
          close();
        } catch (err) {
          console.error("Suspend error:", err);
          alert("Failed to suspend user");
          cleanup();
          close();
        }
      };

      const cleanup = () => {
        cancelBtn && cancelBtn.removeEventListener("click", onCancel);
        confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      };

      cancelBtn && cancelBtn.addEventListener("click", onCancel);
      confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    });
  }

  if (unsuspendBtn) {
    console.log("Unsuspend button found, attaching click handler");
    unsuspendBtn.addEventListener("click", () => {
      console.log("Unsuspend button clicked");
      const overlay = document.getElementById("unsuspendModal");
      if (!overlay) {
        console.error("Unsuspend modal not found");
        return;
      }
      const close = openModal("unsuspendModal");
      const cancelBtn = document.getElementById("cancelUnsuspendBtn");
      const confirmBtn = document.getElementById("confirmUnsuspendBtn");

      const onCancel = (e) => {
        e && e.preventDefault();
        cleanup();
        close();
      };
      const onConfirm = async (e) => {
        e && e.preventDefault();
        console.log("Confirming unsuspend for user:", userData.id);
        try {
          const res = await fetch("/admin/userUnsuspend", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(userData.id)}`,
            credentials: "include",
          });
          console.log("Unsuspend response status:", res.status);
          if (!res.ok) throw new Error("Unsuspend failed");
          // Refresh user data to show unsuspended state
          userData.isSuspended = false;
          userData.suspendedAt = null;
          localStorage.removeItem(cacheKeyForUser(userData.id));
          try {
            localStorage.setItem("admin_users_bust", String(Date.now()));
          } catch {}
          renderHeader();
          cleanup();
          close();
        } catch (err) {
          console.error("Unsuspend error:", err);
          alert("Failed to unsuspend user");
          cleanup();
          close();
        }
      };

      const cleanup = () => {
        cancelBtn && cancelBtn.removeEventListener("click", onCancel);
        confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      };

      cancelBtn && cancelBtn.addEventListener("click", onCancel);
      confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    });
  }

  if (restoreBtn) {
    restoreBtn.addEventListener("click", () => {
      const overlay = document.getElementById("restoreModal");
      if (!overlay) return;
      const close = openModal("restoreModal");
      const cancelBtn = document.getElementById("cancelRestoreBtn");
      const confirmBtn = document.getElementById("confirmRestoreBtn");

      const onCancel = (e) => {
        e && e.preventDefault();
        cleanup();
        close();
      };
      const onConfirm = async (e) => {
        e && e.preventDefault();
        try {
          const res = await fetch("/admin/userRestore", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(userData.id)}`,
            credentials: "include",
          });
          if (!res.ok) throw new Error("Restore failed");
          // Refresh user data to show restored state
          userData.isDeleted = false;
          localStorage.removeItem(cacheKeyForUser(userData.id));
          try {
            localStorage.setItem("admin_users_bust", String(Date.now()));
          } catch {}
          renderHeader();
          cleanup();
          close();
        } catch (err) {
          console.error(err);
          alert("Failed to restore user");
          cleanup();
          close();
        }
      };

      const cleanup = () => {
        cancelBtn && cancelBtn.removeEventListener("click", onCancel);
        confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      };

      cancelBtn && cancelBtn.addEventListener("click", onCancel);
      confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    });
  }
}

(async function init() {
  const id = getUserIdFromUrl();
  if (id) {
    userData = loadFromCache(id);
    if (!userData) {
      try {
        userData = await fetchUser(id);
        saveToCache(id, userData);
      } catch (e) {
        console.error(e);
      }
    } else {
      fetchUser(id)
        .then((fresh) => {
          saveToCache(id, fresh);
        })
        .catch(() => {});
    }
  }
  if (!userData) {
    userData = {
      id: 0,
      name: "User",
      email: "",
      role: "",
      number: "",
      designation: "",
      year: "",
    };
  }
  try {
    if (userData.id) {
      const summary = document.getElementById("userSummaryCard");
      if (summary) summary.style.viewTransitionName = `user-${userData.id}`;
    }
  } catch {}
  renderHeader();
  renderBasic();
  renderInfo();
  wireActions();
})();
