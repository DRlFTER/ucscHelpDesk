<?php
// views/staff/createKB.view.php (fixed button styling - no nested colors)
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Add Knowledge Base Resource</h2>
        <p class="page-subtitle">Create a new guide, policy, or document for the Knowledge Base</p>
    </div>
    
   

        <!-- Main Content Area -->
        <div class="content-area">
            <div class="tickets-container">
                <article class="ticket-card">
                    <div class="ticket-header">
                        <div class="ticket-title-group">
                            <h3 class="ticket-title">New Resource</h3>
                            <div class="ticket-meta">
                                <span>Created by: <?php echo htmlspecialchars($staff_id ?? ''); ?></span>
                                <span>Date: <?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-body">
                        <?php if (!empty($success)): ?>
                            <div class="success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <?php foreach ($errors as $error): ?>
                                <div class="error"><?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data" class="kb-form">
                            <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff_id ?? ''); ?>">
                            <div class="details-group">
                                <div class="detail-item">
                                    <span class="detail-label">Title:</span>
                                    <div class="detail-value-box">
                                        <input type="text" name="title" placeholder="Enter resource title (e.g., 'UCSC Wi-Fi Setup Guide')" value="<?php echo isset($post_data['title']) ? htmlspecialchars($post_data['title']) : ''; ?>" maxlength="200" required>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Category:</span>
                                    <div class="detail-value-box">
                                        <input type="text" name="category" placeholder="Enter category (e.g., Technical Support)" value="<?php echo isset($post_data['category']) ? htmlspecialchars($post_data['category']) : ''; ?>" maxlength="50">
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Description:</span>
                                    <div class="detail-value-box">
                                        <textarea name="description" placeholder="Enter detailed description or content" maxlength="5000" rows="6"><?php echo isset($post_data['description']) ? htmlspecialchars($post_data['description']) : ''; ?></textarea>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Upload Resource File:</span>
                                    <div class="detail-value-box">
                                        <input type="file" name="resource_file" accept=".pdf,.doc,.docx,.jpg,.png,.txt">
                                        <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Supported: PDF, DOC, DOCX, JPG, PNG, TXT (Max 10MB)</small>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tags (optional):</span>
                                    <div class="detail-value-box">
                                        <input type="text" name="tags" placeholder="Enter comma-separated tags (e.g., wifi, setup, guide)" value="<?php echo isset($post_data['tags']) ? htmlspecialchars($post_data['tags']) : ''; ?>" maxlength="100">
                                    </div>
                                </div>
                            </div>
                          
                                <button type="submit" class="kb-save-btn">Save Resource</button>
                                <a href="/staff/staffKB" class="kb-cancel-btn">Cancel</a>
                            
                        </form>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>

<style>
    /* Form Styles - Fixed Alignment & Widths */
    .kb-form {
        width: 100%;
    }

    .details-group {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        flex-wrap: wrap;
    }

    .detail-label {
        font-weight: bold;
        color: #444;
        min-width: 140px;
        flex-shrink: 0;
        margin-top: 8px;
        font-size: 14px;
    }

    .detail-value-box {
        flex: 1;
        padding: 8px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background: #f9f9f9;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .detail-value-box input,
    .detail-value-box textarea,
    .detail-value-box select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
        background: transparent;
        border: none;
        resize: vertical;
    }

    .detail-value-box textarea {
        min-height: 100px;
    }

    /* File Input Styling */
    .detail-value-box input[type="file"] {
        background: white;
        border: none;
        padding: 5px;
        width: 100%;
    }

    .detail-value-box small {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 12px;
    }

    /* Button Row Alignment - Fixed No Nested Colors */
    .ticket-action {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        background: none; /* Remove outer purple background */
        padding: 0; /* No padding */
    }

    .kb-save-btn, .kb-cancel-btn {
        display: inline-block;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        min-width: auto;
        border: none; /* No border for clean look */
        transition: background 0.2s ease;
    }

    .kb-save-btn {
        background: #4a90e2; /* Solid blue - outer color you like */
        color: white;
    }

    .kb-save-btn:hover {
        background: #357abd;
    }

    .kb-cancel-btn {
        background: #6c757d; /* Gray for cancel */
        color: white;
    }

    .kb-cancel-btn:hover {
        background: #5a6268;
    }

    /* Override ticket-action-btn from staffTickets.css - no nesting effect */
    .ticket-action-btn {
        background: none !important; /* Remove any inherited none */
        padding: 8px 16px !important; /* Fixed padding */
        border: none !important;
        border-radius: 4px !important;
    }

    /* Layout Styles */
    .layout-container { display: flex; gap: 0; }
    .navMenu { flex: 0 0 250px; background: #f8f9fa; border-right: 1px solid #dee2e6; }
    .sideNav { display: flex; flex-direction: column; gap: 8px; padding: 20px 15px; }
    .nav-link { display: block; padding: 12px 15px; color: #495057; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; }
    .nav-link:hover, .nav-link.active { background: #007bff; color: white; }
    .content-area { flex: 1; padding: 20px; }

    /* Scrolling Fix (full page) */
    .content-area { overflow: visible !important; height: auto !important; }
    .layout-container { height: auto !important; overflow: visible !important; }
    html, body { height: auto !important; overflow-y: auto !important; overflow-x: hidden !important; }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-item { flex-direction: column; gap: 5px; }
        .detail-label { min-width: auto; width: 100%; text-align: left; }
        .detail-value-box { min-width: auto; width: 100%; }
        .ticket-action { flex-direction: column; gap: 10px; }
        .kb-save-btn, .kb-cancel-btn { width: 100%; }
    }
</style>