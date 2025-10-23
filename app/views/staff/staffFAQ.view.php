<?php
?>
<main id="main-content" class="main-content">
   
        <div class="content-area">
            <div class="ticket-action" style="width:250px; justify-content: center; align-items: center; display: flex; margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 20px;">
                <button class="ticket-action-btn" onclick="window.location.href='/staff/createFAQ';">
                    <span>Create New FAQ</span>
                </button>
            </div>

            <main id="main-content" class="faq-main">
                <div class="faq-header">
                    <h1 class="faq-title">Frequently Asked Questions</h1>
                    <div class="faq-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="faq-search" type="text" placeholder="Search FAQs..." autocomplete="off"/>
                    </div>
                </div>
                <div class="faq-container">
                    <section class="faq-list" id="faq-list">
                        <details class="faq-item">
                            <summary>How do I reset my UCSC portal password if I forgot it?</summary>
                            <div class="faq-answer">
                                Go to the <a href="#" target="_blank" rel="noopener">UCSC Login Portal</a>, click on “Forgot Password,” and follow the instructions. You’ll need to enter your student ID and registered email address. If you don’t receive the reset email within 5 minutes, check your spam folder or create a ticket through UCSC HelpDesk.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>I submitted a support ticket — how can I check the status or get updates?</summary>
                            <div class="faq-answer">
                                You can view your ticket history and status from your dashboard under “Recent Tickets.” Click the ticket to see details and any updates from staff. You’ll also receive email notifications when your ticket status changes.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>The classroom projector isn’t working — who do I report this to?</summary>
                            <div class="faq-answer">
                                Please create a new ticket under the Facilities category and describe the room number, time, and the issue you encountered. Urgent facility issues may be prioritized and routed directly to maintenance staff.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>Can I change or cancel an appointment I made with the help desk?</summary>
                            <div class="faq-answer">
                                Yes. If your ticket includes an appointment, reply to the ticket and request a new time. For same‑day changes, please submit a new comment on the ticket so staff are notified immediately.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>How do I connect to the university Wi‑Fi on a new device?</summary>
                            <div class="faq-answer">
                                Open your device’s Wi‑Fi settings and select the university network. Sign in with your student credentials. Some devices may require a security certificate — follow the on‑screen prompts. If you still cannot connect, search for the Wi‑Fi guide here or submit a ticket.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>I can’t access my email or LMS (Learning Management System). What should I do?</summary>
                            <div class="faq-answer">
                                First, verify you can sign in to the UCSC portal. If portal sign‑in works, try resetting your LMS/email password if available. Clear your browser cache or try a different browser. If the issue persists, submit a ticket with screenshots and timestamps.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>What should I do if there’s a power cut or AC issue during class?</summary>
                            <div class="faq-answer">
                                Use the “Facilities Issue” category on the Help Desk portal to report it immediately. These reports are visible to all students and can be upvoted if others are facing the same issue. Critical facility problems are prioritized and sent directly to campus maintenance.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>Are there walk‑in hours for in‑person support?</summary>
                            <div class="faq-answer">
                                Walk‑in availability may vary by semester. Check announcements or contact the help desk for the latest schedule. Booking via ticket ensures faster handling and reduces waiting time.
                            </div>
                        </details>

                        <details class="faq-item">
                            <summary>How long does it usually take to resolve a submitted ticket?</summary>
                            <div class="faq-answer">
                                Resolution time varies by category and priority. Most tickets receive an initial response within one business day. Complex issues (e.g., facilities or system outages) may require additional time.
                            </div>
                        </details>
                    </section>
                </div>
</div>
            </main>
        </div>
    </div>

</main>

<style>

    .layout-container {
        display: flex;
        gap: 0;
        min-height: 70vh;
    }
    .navMenu {
        flex: 0 0 250px;
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        padding: 20px 0;
    }
    .sideNav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 0 15px;
    }
    .nav-link {
        display: block;
        padding: 12px 15px;
        color: #495057;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .nav-link:hover,
    .nav-link.active {
        background: #007bff;
        color: white;
    }
    .content-area {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }

    @media (max-width: 992px) {
        .layout-container { flex-direction: column; }
        .navMenu { border-right: none; border-bottom: 1px solid #dee2e6; }
        .faq-header { flex-direction: column; align-items: stretch; }
        .faq-search { width: 100%; }
    }


    .ticket-action {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  background: var(--CTA, #8c8cf9);
  cursor: pointer;
  transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
}

.ticket-action:hover {
  background-color: #6a6af5;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.ticket-action-btn {
  color: #fff;
  font-family: "Poppins", sans-serif;
  font-size: 14px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
  letter-spacing: 0.16px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}
</style>

<script src="/js/staff/staffFAQ.js"></script>