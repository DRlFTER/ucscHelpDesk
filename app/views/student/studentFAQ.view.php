<?php
?>

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
    <?php if (!empty($faqs)): ?>
        <?php foreach ($faqs as $faq): ?>
            <details class="faq-item">
                <summary><?= htmlspecialchars($faq['question']) ?></summary>
                <div class="faq-answer">
                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                </div>
            </details>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No FAQs found.</p>
    <?php endif; ?>
    </section>
  </div>

  <p class="faq-cta">Still need help? <a href="/student/ticket">Submit a ticket</a> or <a href="#" id="contact-support-link">contact support</a>.</p>
</main>

<script src="/js/student/studentFAQ.js"></script>
