// Announcement Full View - Interactive behavior
(function () {
  const config = window.ANNOUNCEMENT_CONFIG || {
    role: 'student',
    canEdit: false
  };

  document.addEventListener('DOMContentLoaded', () => {
    // Handle form validation for edit section (if present)
    const editForm = document.querySelector('.editForm');
    if (editForm && config.canEdit) {
      const topicInput = editForm.querySelector('input[name="topic"]');
      const contentTextarea = editForm.querySelector('textarea[name="content"]');

      // Real-time validation feedback
      if (topicInput) {
        topicInput.addEventListener('input', () => {
          if (topicInput.value.trim().length < 3) {
            topicInput.style.borderColor = '#f56565';
          } else {
            topicInput.style.borderColor = '#e2e8f0';
          }
        });
      }

      if (contentTextarea) {
        contentTextarea.addEventListener('input', () => {
          if (contentTextarea.value.trim().length < 10) {
            contentTextarea.style.borderColor = '#f56565';
          } else {
            contentTextarea.style.borderColor = '#e2e8f0';
          }
        });
      }

      // Form submission validation
      editForm.addEventListener('submit', (e) => {
        const isDelete = e.submitter && e.submitter.name === 'delete_announcement';
        
        if (!isDelete) {
          let valid = true;
          
          if (topicInput && topicInput.value.trim().length < 3) {
            valid = false;
            topicInput.style.borderColor = '#f56565';
          }
          
          if (contentTextarea && contentTextarea.value.trim().length < 10) {
            valid = false;
            contentTextarea.style.borderColor = '#f56565';
          }
          
          if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields properly.');
          }
        }
      });
    }

    // Handle attachment downloads with loading state
    const attachmentItems = document.querySelectorAll('.attachmentItem');
    attachmentItems.forEach(item => {
      item.addEventListener('click', (e) => {
        // Add visual feedback for download
        item.style.opacity = '0.7';
        setTimeout(() => {
          item.style.opacity = '1';
        }, 200);
      });
    });

    // Smooth scroll for long content
    const announcementLayout = document.querySelector('.announcementLayout');
    if (announcementLayout) {
      announcementLayout.style.scrollBehavior = 'smooth';
    }
  });
})();
