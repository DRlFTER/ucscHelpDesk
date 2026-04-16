// Settings page - Functional profile section with editable user info
(function () {
  const left = document.querySelector(".settingsLeft");
  const right = document.querySelector(".settingsRight");
  if (!left || !right) return;

  // Get user data from the page
  const userData = window.SETTINGS_USER_DATA || {};
  const userRole = window.SETTINGS_ROLE || "guest";
  const apiBase = window.SETTINGS_API_BASE || "";

  // Track original values to detect changes
  let originalValues = {};
  let hasChanges = false;

  // Data model: side menu + sections with groups and controls
  const allSettingsSections = [
    {
      id: "profile",
      title: "Profile",
      description: "Manage your personal information and profile details.",
      groups: [
        {
          title: "Profile Photo",
          type: "photo",
        },
        {
          title: "Basic Info",
          items: [
            {
              id: "name",
              label: "Full Name",
              control: {
                type: "input",
                placeholder: "Enter your full name",
                editable: true,
              },
              value: userData.name || "",
            },
            {
              id: "email",
              label: "Email",
              control: {
                type: "input",
                placeholder: "your@email.com",
                editable: false,
              },
              value: userData.email || "",
              hint: "Email cannot be changed",
            },
            {
              id: "number",
              label: "Phone Number",
              control: {
                type: "input",
                placeholder: "Enter your phone number",
                inputType: "tel",
                editable: true,
              },
              value: userData.number || "",
            },
            {
              id: "role",
              label: "Role",
              control: { type: "input", editable: false },
              value: userData.role
                ? userData.role.charAt(0).toUpperCase() + userData.role.slice(1)
                : "",
              hint: "Role is assigned by administrators",
            },
          ],
        },
      ],
    },
    {
      id: "account",
      title: "Account",
      description: "Security and account configuration.",
      functional: true,
      groups: [
        {
          title: "Security",
          items: [
            {
              label: "Password",
              control: {
                type: "button",
                text: "Change Password",
                action: "changePassword",
              },
            },
          ],
        },
      ],
    },
    {
      id: "display",
      title: "Display",
      description: "Layout and visibility settings.",
      functional: true,
      roles: ["admin"],
      groups: [
        {
          title: "Content density",
          items: [
            {
              id: "display_rows",
              label: "Table rows per page",
              control: {
                type: "input",
                placeholder: "10",
                inputType: "number",
              },
              value: localStorage.getItem("ucsc_table_rows") || "10",
              hint: "Applies to all paginated tables (5–100)"
            },
          ],
        },
      ],
    },
  ];

  // Filter sections by role
  const settingsSections = allSettingsSections.filter((s) => {
    if (!s.roles) return true;
    return s.roles.includes(userRole);
  });

  // Build side menu
  const menuButtons = settingsSections
    .map(
      (s, idx) =>
        `<button class="settingsNavBtn${
          idx === 0 ? " active" : ""
        }" data-target="${s.id}"><span>${s.title}</span></button>`
    )
    .join("");

  // Inject menu
  left.innerHTML = menuButtons;

  // Generate profile photo section HTML
  function renderPhotoSection() {
    const photoUrl = userData.profile_photo || null;
    const initials = getInitials(userData.name || "User");

    return `
      <div class="profilePhotoSection">
        <div class="profilePhotoWrapper${photoUrl ? " hasPhoto" : ""}">
          ${
            photoUrl
              ? `<img src="${photoUrl}" alt="Profile photo" class="profilePhoto" id="profilePhotoImg">`
              : `<div class="profilePhotoPlaceholder" id="profilePhotoPlaceholder">${initials}</div>`
          }
          <input type="file" id="photoInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
          ${
            photoUrl
              ? `
          <div class="profilePhotoOverlay splitOverlay" id="photoOverlay">
            <div class="overlayAction overlayChange" id="changePhotoArea">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </div>
            <div class="overlayAction overlayRemove" id="removePhotoArea">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </div>
          </div>
          `
              : `
          <div class="profilePhotoOverlay" id="photoOverlay">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
              <circle cx="12" cy="13" r="4"></circle>
            </svg>
          </div>
          `
          }
        </div>
        <p class="photoHint">Supported formats: <br> JPEG, PNG, GIF, WebP. Max size: 5MB</p>
      </div>
    `;
  }

  // Get initials from name
  function getInitials(name) {
    return name
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .slice(0, 2);
  }

  // Render a given section
  function renderSection(sectionId) {
    const section =
      settingsSections.find((s) => s.id === sectionId) || settingsSections[0];
    const isProfileSection = sectionId === "profile";

    // Store original values for profile section
    if (isProfileSection) {
      originalValues = {
        name: userData.name || "",
        number: userData.number || "",
      };
      hasChanges = false;
    }

    right.innerHTML = `
      <div class="settingsCard">
        <div class="settingsHeader">
          <h2>${section.title}</h2>
          <p>${section.description}</p>
        </div>
        ${section.groups
          .map((g) => {
            // Handle photo section separately
            if (g.type === "photo") {
              return `
                <div class="settingGroup">
                  <div class="settingGroupTitle">${g.title}</div>
                  ${renderPhotoSection()}
                </div>
              `;
            }

            return `
              <div class="settingGroup">
                <div class="settingGroupTitle">${g.title}</div>
                ${(g.items || [])
                  .map((item) => {
                    const ctrl = item.control || { type: "input" };
                    const isEditable = ctrl.editable !== false;
                    const controlHtml = (() => {
                      const val = item.value !== undefined ? item.value : "";
                      const localDataAttr = item.id && (item.id.startsWith("display_") || item.id.startsWith("notif_")) ? `data-local="${item.id}"` : "";
                      const minMax = item.id === "display_rows" ? 'min="5" max="100" step="1"' : "";
                      if (ctrl.type === "select") {
                        const opts = (ctrl.options || [])
                          .map((o) => `<option ${o === val ? "selected" : ""}>${escapeHtml(o)}</option>`)
                          .join("");
                        return `<select ${
                          !isEditable ? "disabled" : ""
                        } ${localDataAttr}>${opts}</select>`;
                      }
                      if (ctrl.type === "textarea") {
                        return `<textarea rows="3" placeholder="${
                          ctrl.placeholder || ""
                        }" ${!isEditable ? "disabled" : ""}>${escapeHtml(val)}</textarea>`;
                      }
                      if (ctrl.type === "button") {
                        return `<button type="button" class="btnSecondary btnSmall" data-action="${
                          ctrl.action || ""
                        }">${ctrl.text || "Click"}</button>`;
                      }
                      const t = ctrl.inputType || "text";
                      return `<input type="${t}" placeholder="${
                        ctrl.placeholder || ""
                      }" value="${escapeHtml(val)}" ${
                        !isEditable ? "disabled readonly" : ""
                      } ${minMax} ${item.id && !localDataAttr ? `data-field="${item.id}"` : localDataAttr}/>`;
                    })();

                    return `
                      <div class="settingRow">
                        <div class="settingLabel">
                          ${item.label}
                          ${
                            item.hint
                              ? `<span class="settingHint">${item.hint}</span>`
                              : ""
                          }
                        </div>
                        <div class="settingControl">${controlHtml}</div>
                      </div>
                    `;
                  })
                  .join("")}
              </div>
            `;
          })
          .join("")}
        
        ${
          isProfileSection
            ? `
          <div class="settingsActions" id="settingsActions" style="display: none;">
            <button type="button" class="btnSecondary" id="cancelChangesBtn">Cancel</button>
            <button type="button" class="btnPrimary" id="saveProfileBtn">
              <span class="btnText">Save Changes</span>
              <span class="btnLoader" style="display: none;">
                <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-linecap="round"/>
                </svg>
              </span>
            </button>
          </div>
          <div class="settingsMessage" id="settingsMessage"></div>
        `
            : section.functional
              ? `<div class="settingsMessage" id="settingsMessage"></div>`
              : `<div class="mutedText">These settings are placeholders and won't be saved yet.</div>`
        }
      </div>
    `;

    // Setup event listeners for profile section
    if (isProfileSection) {
      setupProfileListeners();
    }

    // Setup account section listeners (change password button)
    if (sectionId === "account") {
      const changePasswordBtn = right.querySelector(
        '[data-action="changePassword"]'
      );
      if (changePasswordBtn) {
        changePasswordBtn.addEventListener("click", openChangePasswordModal);
      }
    }

    // Setup display section listeners (rows per page input)
    if (sectionId === "display") {
      setupDisplayListeners();
    }
  }

  // Escape HTML to prevent XSS
  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  // Setup profile section event listeners
  function setupProfileListeners() {
    // Track changes on editable inputs
    const editableInputs = right.querySelectorAll("input[data-field]");
    editableInputs.forEach((input) => {
      input.addEventListener("input", checkForChanges);
    });

    // Photo input and overlay elements
    const photoInput = document.getElementById("photoInput");
    const photoOverlay = document.getElementById("photoOverlay");
    const changePhotoArea = document.getElementById("changePhotoArea");
    const removePhotoArea = document.getElementById("removePhotoArea");

    // If split overlay (has photo), handle separately
    if (changePhotoArea && photoInput) {
      changePhotoArea.addEventListener("click", (e) => {
        e.stopPropagation();
        photoInput.click();
      });
    }

    if (removePhotoArea) {
      removePhotoArea.addEventListener("click", (e) => {
        e.stopPropagation();
        handlePhotoRemove();
      });
    }

    // Single overlay (no photo) - click to upload
    if (photoOverlay && !changePhotoArea && photoInput) {
      photoOverlay.addEventListener("click", () => {
        photoInput.click();
      });
    }

    if (photoInput) {
      photoInput.addEventListener("change", handlePhotoUpload);
    }

    // Cancel button
    const cancelBtn = document.getElementById("cancelChangesBtn");
    if (cancelBtn) {
      cancelBtn.addEventListener("click", () => {
        // Reset values
        const nameInput = right.querySelector('input[data-field="name"]');
        const numberInput = right.querySelector('input[data-field="number"]');

        if (nameInput) nameInput.value = originalValues.name;
        if (numberInput) numberInput.value = originalValues.number;

        checkForChanges();
      });
    }

    // Save button
    const saveBtn = document.getElementById("saveProfileBtn");
    if (saveBtn) {
      saveBtn.addEventListener("click", saveProfile);
    }

  }

  // Setup display section event listeners
  function setupDisplayListeners() {
    const rowsInput = right.querySelector('[data-local="display_rows"]');
    if (!rowsInput) return;

    // Debounced save for the rows input
    let saveTimeout = null;
    rowsInput.addEventListener("input", () => {
      clearTimeout(saveTimeout);
      saveTimeout = setTimeout(() => {
        const raw = rowsInput.value.trim();
        if (!raw) return;
        const val = parseInt(raw, 10);
        if (isNaN(val) || val < 5) {
          showMessage("Rows per page must be at least 5.", "error");
          return;
        }
        if (val > 100) {
          showMessage("Rows per page cannot exceed 100.", "error");
          return;
        }
        localStorage.setItem("ucsc_table_rows", String(val));
        rowsInput.value = String(val);
        showMessage("Table rows per page updated to " + val + ".", "success");
      }, 600);
    });
  }

  // Open Change Password modal
  function openChangePasswordModal() {
    const overlay = document.getElementById("changePasswordModal");
    if (!overlay) return;
    overlay.classList.add("open");
    document.body.classList.add("modal-open");

    const cancelBtn = document.getElementById("cancelChangePasswordBtn");
    const confirmBtn = document.getElementById("confirmChangePasswordBtn");
    const backdropBtn = overlay.querySelector(".modalBackdropClose");
    const form = document.getElementById("changePasswordForm");
    const errorMsg = document.getElementById("passwordErrorMsg");

    if (form) form.reset();
    if (errorMsg) errorMsg.style.display = "none";

    const close = () => {
      overlay.classList.remove("open");
      document.body.classList.remove("modal-open");
      cancelBtn && cancelBtn.removeEventListener("click", onCancel);
      confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      backdropBtn && backdropBtn.removeEventListener("click", onCancel);
    };

    const onCancel = (e) => {
      e && e.preventDefault();
      close();
    };

    const onConfirm = async (e) => {
      e && e.preventDefault();
      const currentPwd = document.getElementById("currentPasswordInput").value;
      const newPwd = document.getElementById("newPasswordInput").value;
      const confirmPwd = document.getElementById("confirmNewPasswordInput").value;

      if (!currentPwd || !newPwd || !confirmPwd) {
        errorMsg.textContent = "All fields are required.";
        errorMsg.style.display = "block";
        return;
      }

      if (newPwd !== confirmPwd) {
        errorMsg.textContent = "New passwords do not match.";
        errorMsg.style.display = "block";
        return;
      }
      
      if (newPwd.length < 8) {
        errorMsg.textContent = "New password must be at least 8 characters.";
        errorMsg.style.display = "block";
        return;
      }

      setLoading(confirmBtn, true);
      errorMsg.style.display = "none";

      try {
        const response = await fetch("/settings/updatePassword", {
           method: "POST",
           headers: { "Content-Type": "application/json" },
           body: JSON.stringify({ currentPassword: currentPwd, newPassword: newPwd })
        });
        const data = await response.json();
        
        if (data.success) {
           close();
           showMessage("Password updated successfully!", "success");
        } else {
           errorMsg.textContent = data.error || "Failed to update password.";
           errorMsg.style.display = "block";
        }
      } catch (err) {
        errorMsg.textContent = "An error occurred. Please try again.";
        errorMsg.style.display = "block";
      } finally {
        setLoading(confirmBtn, false);
      }
    };

    cancelBtn && cancelBtn.addEventListener("click", onCancel);
    confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    backdropBtn && backdropBtn.addEventListener("click", onCancel);
  }

  // Handle photo upload
  async function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    if (!allowedTypes.includes(file.type)) {
      showMessage("Invalid file type. Allowed: JPEG, PNG, GIF, WebP", "error");
      event.target.value = "";
      return;
    }

    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
      showMessage("File too large. Maximum size is 5MB", "error");
      event.target.value = "";
      return;
    }

    const uploadBtn = document.getElementById("uploadPhotoBtn");
    setLoading(uploadBtn, true);

    try {
      const formData = new FormData();
      formData.append("photo", file);

      const response = await fetch("/settings/uploadPhoto", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        // Update local data
        userData.profile_photo = data.url;

        // Re-render the photo section
        const photoSection = right.querySelector(".profilePhotoSection");
        if (photoSection) {
          photoSection.outerHTML = renderPhotoSection();
          // Re-attach event listeners for the new elements
          const newUploadBtn = document.getElementById("uploadPhotoBtn");
          const newPhotoInput = document.getElementById("photoInput");
          const newPhotoOverlay = document.getElementById("photoOverlay");
          const newRemoveBtn = document.getElementById("removePhotoBtn");

          if (newUploadBtn && newPhotoInput) {
            newUploadBtn.addEventListener("click", () => newPhotoInput.click());
            newPhotoInput.addEventListener("change", handlePhotoUpload);
          }
          if (newPhotoOverlay && newPhotoInput) {
            newPhotoOverlay.addEventListener("click", () =>
              newPhotoInput.click()
            );
          }
          if (newRemoveBtn) {
            newRemoveBtn.addEventListener("click", handlePhotoRemove);
          }
        }

        showMessage("Profile photo updated successfully!", "success");
      } else {
        showMessage(data.error || "Failed to upload photo", "error");
      }
    } catch (error) {
      console.error("Error uploading photo:", error);
      showMessage(
        "An error occurred while uploading. Please try again.",
        "error"
      );
    } finally {
      setLoading(uploadBtn, false);
      event.target.value = "";
    }
  }

  // Open delete photo modal
  function openDeletePhotoModal() {
    const overlay = document.getElementById("deletePhotoModal");
    if (!overlay) return;
    overlay.classList.add("open");
    document.body.classList.add("modal-open");

    const cancelBtn = document.getElementById("cancelDeletePhotoBtn");
    const confirmBtn = document.getElementById("confirmDeletePhotoBtn");
    const backdropBtn = overlay.querySelector(".modalBackdropClose");

    const close = () => {
      overlay.classList.remove("open");
      document.body.classList.remove("modal-open");
      cancelBtn && cancelBtn.removeEventListener("click", onCancel);
      confirmBtn && confirmBtn.removeEventListener("click", onConfirm);
      backdropBtn && backdropBtn.removeEventListener("click", onCancel);
    };

    const onCancel = (e) => {
      e && e.preventDefault();
      close();
    };

    const onConfirm = async (e) => {
      e && e.preventDefault();
      close();
      await performPhotoRemoval();
    };

    cancelBtn && cancelBtn.addEventListener("click", onCancel);
    confirmBtn && confirmBtn.addEventListener("click", onConfirm);
    backdropBtn && backdropBtn.addEventListener("click", onCancel);
  }

  // Handle photo removal (called after modal confirmation)
  function handlePhotoRemove() {
    openDeletePhotoModal();
  }

  // Perform the actual photo removal
  async function performPhotoRemoval() {
    const removeBtn = document.getElementById("removePhotoBtn");
    setLoading(removeBtn, true);

    try {
      const response = await fetch("/settings/deletePhoto", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      });

      const data = await response.json();

      if (data.success) {
        // Update local data
        userData.profile_photo = null;

        // Re-render the photo section
        const photoSection = right.querySelector(".profilePhotoSection");
        if (photoSection) {
          photoSection.outerHTML = renderPhotoSection();
          // Re-attach event listeners for the new elements
          const newUploadBtn = document.getElementById("uploadPhotoBtn");
          const newPhotoInput = document.getElementById("photoInput");
          const newPhotoOverlay = document.getElementById("photoOverlay");

          if (newUploadBtn && newPhotoInput) {
            newUploadBtn.addEventListener("click", () => newPhotoInput.click());
            newPhotoInput.addEventListener("change", handlePhotoUpload);
          }
          if (newPhotoOverlay && newPhotoInput) {
            newPhotoOverlay.addEventListener("click", () =>
              newPhotoInput.click()
            );
          }
        }

        showMessage("Profile photo removed successfully!", "success");
      } else {
        showMessage(data.error || "Failed to remove photo", "error");
      }
    } catch (error) {
      console.error("Error removing photo:", error);
      showMessage(
        "An error occurred while removing. Please try again.",
        "error"
      );
    } finally {
      setLoading(removeBtn, false);
    }
  }

  // Check if there are changes
  function checkForChanges() {
    const nameInput = right.querySelector('input[data-field="name"]');
    const numberInput = right.querySelector('input[data-field="number"]');
    const actionsDiv = document.getElementById("settingsActions");

    const currentName = nameInput ? nameInput.value : "";
    const currentNumber = numberInput ? numberInput.value : "";

    hasChanges =
      currentName !== originalValues.name ||
      currentNumber !== originalValues.number;

    if (actionsDiv) {
      actionsDiv.style.display = hasChanges ? "flex" : "none";
    }

    // Clear any previous messages when user makes changes
    hideMessage();
  }

  // Save profile
  async function saveProfile() {
    const nameInput = right.querySelector('input[data-field="name"]');
    const numberInput = right.querySelector('input[data-field="number"]');
    const saveBtn = document.getElementById("saveProfileBtn");

    const name = nameInput ? nameInput.value.trim() : "";
    const number = numberInput ? numberInput.value.trim() : "";

    // Validation
    if (!name) {
      showMessage("Name is required", "error");
      return;
    }

    if (name.length > 50) {
      showMessage("Name must be 50 characters or less", "error");
      return;
    }

    if (number && number.length > 15) {
      showMessage("Phone number must be 15 characters or less", "error");
      return;
    }

    // Show loading state
    setLoading(saveBtn, true);

    try {
      const response = await fetch("/settings/updateProfile", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ name, number }),
      });

      const data = await response.json();

      if (data.success) {
        // Update local data and original values
        userData.name = name;
        userData.number = number;
        originalValues.name = name;
        originalValues.number = number;

        // Update the initials in photo placeholder if present
        const placeholder = document.getElementById("profilePhotoPlaceholder");
        if (placeholder) {
          placeholder.textContent = getInitials(name);
        }

        hasChanges = false;
        const actionsDiv = document.getElementById("settingsActions");
        if (actionsDiv) {
          actionsDiv.style.display = "none";
        }

        showMessage("Profile updated successfully!", "success");
      } else {
        showMessage(data.error || "Failed to update profile", "error");
      }
    } catch (error) {
      console.error("Error saving profile:", error);
      showMessage("An error occurred while saving. Please try again.", "error");
    } finally {
      setLoading(saveBtn, false);
    }
  }

  // Set loading state on button
  function setLoading(button, loading) {
    if (!button) return;

    const textSpan = button.querySelector(".btnText");
    const loaderSpan = button.querySelector(".btnLoader");

    if (loading) {
      button.disabled = true;
      if (textSpan) textSpan.style.display = "none";
      if (loaderSpan) loaderSpan.style.display = "inline-flex";
    } else {
      button.disabled = false;
      if (textSpan) textSpan.style.display = "inline";
      if (loaderSpan) loaderSpan.style.display = "none";
    }
  }

  // Show message
  function showMessage(text, type = "info") {
    const messageDiv = document.getElementById("settingsMessage");
    if (!messageDiv) return;

    messageDiv.textContent = text;
    messageDiv.className = `settingsMessage ${type}`;
    messageDiv.style.display = "block";

    // Auto-hide success messages after 5 seconds
    if (type === "success") {
      setTimeout(() => hideMessage(), 5000);
    }
  }

  // Hide message
  function hideMessage() {
    const messageDiv = document.getElementById("settingsMessage");
    if (messageDiv) {
      messageDiv.style.display = "none";
    }
  }

  // Initial render
  renderSection(settingsSections[0].id);

  // Menu click handling + active state
  left.addEventListener("click", (e) => {
    const btn = e.target.closest(".settingsNavBtn");
    if (!btn) return;
    const target = btn.getAttribute("data-target");
    if (!target) return;

    // Warn about unsaved changes
    if (hasChanges) {
      if (
        !confirm("You have unsaved changes. Are you sure you want to leave?")
      ) {
        return;
      }
    }

    // active state
    left
      .querySelectorAll(".settingsNavBtn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");
    // render content
    renderSection(target);
  });

  // Local storage auto-save for functioning preference toggles (selects only; display_rows handled in setupDisplayListeners)
  document.body.addEventListener("change", (e) => {
     const fieldId = e.target.getAttribute("data-local");
     if (fieldId && fieldId !== "display_rows") {
        const val = e.target.value;
        if (fieldId === "notif_tickets") localStorage.setItem("ucsc_notif_tickets", val);
        else if (fieldId === "notif_announcements") localStorage.setItem("ucsc_notif_announcements", val);
        
        showMessage("Preference updated.", "success");
     }
  });
})();
