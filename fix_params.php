<?php
$path = '/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/public/js/ticketFull/ticketFull.js';
$content = file_get_contents($path);

// Replace Assign Payload
$assignFrom = 'formData.append("ticket_id", getTicketIdFromUrl());
          const res = await fetch(`/staff/ticketAssign`';
$assignTo = 'formData.append("id", getTicketIdFromUrl());
          const res = await fetch(`/staff/ticketAssign`';
$content = str_replace($assignFrom, $assignTo, $content);

// Replace Forward Payload
$forwardFrom = 'formData.append("ticket_id", getTicketIdFromUrl());
          formData.append("staff_id", select.value);';
$forwardTo = 'formData.append("id", getTicketIdFromUrl());
          formData.append("forward_to", select.value);';
$content = str_replace($forwardFrom, $forwardTo, $content);

// Update specific resolve logic
$resolveFrom = 'const resolveBtn = document.getElementById("resolveBtn");
    if (resolveBtn) {
      resolveBtn.addEventListener("click", () => openResolveModal());
    }';
$resolveTo = 'const resolveBtn = document.getElementById("resolveBtn");
    if (resolveBtn) {
      resolveBtn.addEventListener("click", async () => {
        if (confirm("Mark ticket as resolved?")) {
            const formData = new FormData();
            formData.append("id", getTicketIdFromUrl());
            try {
                // Determine the correct route dynamically
                const route = ROLE === "staff" ? "/staff/ticketResolve" : `/${ROLE}/resolveTicket`;
                const res = await fetch(route, { method: "POST", body: formData });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || data.message || "Failed");
                }
            } catch(e) { console.error(e); }
        }
      });
    }';
$content = str_replace($resolveFrom, $resolveTo, $content);

file_put_contents($path, $content);
echo "DONE\n";
?>
