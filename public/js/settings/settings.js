// Render settings side menu and content using a data model and Array.map
// Non-functional placeholders for now

(function () {
  const left = document.querySelector(".settingsLeft");
  const right = document.querySelector(".settingsRight");
  if (!left || !right) return;

  // Data model: side menu + sections with groups and simple controls
  const settingsSections = [
    {
      id: "profile",
      title: "Profile",
      description: "Manage your personal information and profile details.",
      groups: [
        {
          title: "Basic info",
          items: [
            {
              label: "Full name",
              control: { type: "input", placeholder: "John Doe" },
            },
            {
              label: "Email",
              control: { type: "input", placeholder: "john@university.edu" },
            },
          ],
        },
      ],
    },
    {
      id: "account",
      title: "Account",
      description: "Security and account configuration.",
      groups: [
        {
          title: "Security",
          items: [
            {
              label: "Password",
              control: {
                type: "input",
                placeholder: "••••••••",
                inputType: "password",
              },
            },
            {
              label: "Two-factor auth",
              control: {
                type: "select",
                options: ["Disabled", "SMS", "Authenticator App"],
              },
            },
          ],
        },
      ],
    },
    {
      id: "appearance",
      title: "Appearance",
      description: "Theme and UI preferences.",
      groups: [
        {
          title: "Theme",
          items: [
            {
              label: "Theme",
              control: { type: "select", options: ["System", "Light", "Dark"] },
            },
            {
              label: "Density",
              control: { type: "select", options: ["Comfortable", "Compact"] },
            },
          ],
        },
      ],
    },
    {
      id: "notifications",
      title: "Notifications",
      description: "Where and how you receive notifications.",
      groups: [
        {
          title: "Email notifications",
          items: [
            {
              label: "Ticket updates",
              control: { type: "select", options: ["All", "Mentions", "None"] },
            },
            {
              label: "Announcements",
              control: {
                type: "select",
                options: ["All", "Important only", "None"],
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
      groups: [
        {
          title: "Content density",
          items: [
            {
              label: "Table rows per page",
              control: {
                type: "input",
                placeholder: "10",
                inputType: "number",
              },
            },
          ],
        },
      ],
    },
  ];

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

  // Render a given section
  function renderSection(sectionId) {
    const section =
      settingsSections.find((s) => s.id === sectionId) || settingsSections[0];
    right.innerHTML = `
			
			<div class="settingsCard">
            <div class="settingsHeader">
				<h2>${section.title}</h2>
				<p>${section.description}</p>
			</div>
				${section.groups
          .map(
            (g) => `
						<div class="settingGroup">
							<div class="settingGroupTitle">${g.title}</div>
							${g.items
                .map((item) => {
                  const ctrl = item.control || { type: "input" };
                  const controlHtml = (() => {
                    if (ctrl.type === "select") {
                      const opts = (ctrl.options || [])
                        .map((o) => `<option>${o}</option>`)
                        .join("");
                      return `<select>${opts}</select>`;
                    }
                    if (ctrl.type === "textarea") {
                      return `<textarea rows="3" placeholder="${
                        ctrl.placeholder || ""
                      }"></textarea>`;
                    }
                    const t = ctrl.inputType || "text";
                    return `<input type="${t}" placeholder="${
                      ctrl.placeholder || ""
                    }" />`;
                  })();
                  return `
										<div class="settingRow">
											<div class="settingLabel">${item.label}</div>
											<div class="settingControl">${controlHtml}</div>
										</div>
									`;
                })
                .join("")}
							<div class="mutedText">These are placeholders and won’t be saved.</div>
						</div>
					`
          )
          .join("")}
			</div>
		`;
  }

  // Initial render
  renderSection(settingsSections[0].id);

  // Menu click handling + active state
  left.addEventListener("click", (e) => {
    const btn = e.target.closest(".settingsNavBtn");
    if (!btn) return;
    const target = btn.getAttribute("data-target");
    if (!target) return;
    // active state
    left
      .querySelectorAll(".settingsNavBtn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");
    // render content
    renderSection(target);
  });
})();
