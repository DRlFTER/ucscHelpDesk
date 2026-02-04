// scheduleModal.js
// Save to: /js/scheduleModal.js

/**
 * Open the schedule meeting modal
 * @param {number} ticketId - The ticket ID (optional)
 * @param {number} studentId - The student ID (optional)
 */
function openScheduleModal(ticketId = null, studentId = null) {
    // Remove any existing modal
    const existingModal = document.getElementById('scheduleModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create and show new modal
    const modal = createScheduleModal(ticketId, studentId);
    document.body.appendChild(modal);
    
    // Focus first input
    setTimeout(() => {
        document.getElementById('meeting_date').focus();
    }, 100);
}

/**
 * Create the schedule modal HTML
 */
function createScheduleModal(ticketId, studentId) {
    const modal = document.createElement('div');
    modal.className = 'schedule-modal-overlay';
    modal.id = 'scheduleModal';
    
    // Get today's date for min attribute
    const today = new Date().toISOString().split('T')[0];
    
    modal.innerHTML = `
        <div class="schedule-modal">
            <div class="schedule-header">
                <h2>Schedule meeting</h2>
                <button class="close-btn" onclick="closeScheduleModal()" type="button">&times;</button>
            </div>
            
            <div class="schedule-body">
                <form id="scheduleForm">
                    <!-- Hidden fields -->
                    <input type="hidden" id="ticket_id" value="${ticketId || ''}">
                    <input type="hidden" id="student_id" value="${studentId || ''}">
                    
                    <!-- Date and Time Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="meeting_date">Date</label>
                            <input type="date" 
                                   id="meeting_date" 
                                   class="form-input" 
                                   value="${today}" 
                                   min="${today}" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_time">Start time</label>
                            <input type="time" 
                                   id="start_time" 
                                   class="form-input" 
                                   value="09:00" 
                                   required>
                        </div>
                    </div>

                    <!-- Duration and Mode Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">Duration</label>
                            <select id="duration" class="form-input" required>
                                <option value="30">30 minutes</option>
                                <option value="60" selected>60 minutes</option>
                                <option value="90">90 minutes</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mode">Mode</label>
                            <select id="mode" class="form-input" required>
                                <option value="online">Online</option>
                                <option value="in-person" selected>In person</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location/Room (shows for in-person) -->
                    <div class="form-group" id="location-group">
                        <label for="room_location">Location/Room</label>
                        <input type="text" 
                               id="room_location" 
                               class="form-input" 
                               placeholder="E401">
                    </div>

                    <!-- Meeting Link (shows for online) -->
                    <div class="form-group" id="link-group" style="display: none;">
                        <label for="meeting_link">Meeting link (URL)</label>
                        <input type="url" 
                               id="meeting_link" 
                               class="form-input" 
                               placeholder="https://support.zoom.com">
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" 
                                  class="form-input" 
                                  rows="3" 
                                  placeholder="Bring a A4 sheet and a pen"></textarea>
                    </div>
                </form>
            </div>

            <div class="schedule-footer">
                <button class="btn-cancel" 
                        onclick="closeScheduleModal()" 
                        type="button">Cancel</button>
                <button class="btn-schedule" 
                        onclick="submitMeeting()" 
                        type="button" 
                        id="submitBtn">Schedule</button>
            </div>
        </div>
    `;
    
    // Set up mode change listener after modal is added to DOM
    setTimeout(() => {
        setupModeChangeListener();
    }, 100);
    
    return modal;
}

/**
 * Handle mode selection change (online/in-person/phone)
 */
function setupModeChangeListener() {
    const modeSelect = document.getElementById('mode');
    const locationGroup = document.getElementById('location-group');
    const linkGroup = document.getElementById('link-group');
    
    if (!modeSelect) return;
    
    modeSelect.addEventListener('change', function() {
        if (this.value === 'online') {
            locationGroup.style.display = 'none';
            linkGroup.style.display = 'block';
        } else if (this.value === 'in-person') {
            locationGroup.style.display = 'block';
            linkGroup.style.display = 'none';
        } else { // phone
            locationGroup.style.display = 'none';
            linkGroup.style.display = 'none';
        }
    });
}

/**
 * Submit the meeting form to backend
 */
async function submitMeeting() {
    const form = document.getElementById('scheduleForm');
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Collect form data
    const ticketId = document.getElementById('ticket_id').value;
    const studentId = document.getElementById('student_id').value;
    
    const meetingData = {
        ticket_id: ticketId !== '' ? parseInt(ticketId) : null,
        student_id: studentId !== '' ? parseInt(studentId) : null,
        meeting_date: document.getElementById('meeting_date').value,
        start_time: document.getElementById('start_time').value + ':00',
        duration: parseInt(document.getElementById('duration').value),
        mode: document.getElementById('mode').value,
        room_location: document.getElementById('room_location').value,
        meeting_link: document.getElementById('meeting_link').value,
        notes: document.getElementById('notes').value
    };

    console.log('Sending meeting data:', meetingData);

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Scheduling...';

    try {
        // Send to backend
        const response = await fetch('/meetingscheduler/scheduleMeeting', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(meetingData)
        });

        const data = await response.json();
        
        console.log('Response from server:', data);
        
        if (data.success) {
            // Success!
            showSuccessMessage('Meeting scheduled successfully!');
            closeScheduleModal();
            
            // Reload page after 1 second to show new meeting
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Error from backend
            alert('Error: ' + data.error);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
        
    } catch (error) {
        console.error('Error scheduling meeting:', error);
        alert('Failed to schedule meeting. Please check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'success-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M7 10L9 12L13 8M19 10C19 14.9706 14.9706 19 10 19C5.02944 19 1 14.9706 1 10C1 5.02944 5.02944 1 10 1C14.9706 1 19 5.02944 19 10Z" 
                      stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Close the modal
 */
function closeScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.remove();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('schedule-modal-overlay')) {
        closeScheduleModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeScheduleModal();
    }
});