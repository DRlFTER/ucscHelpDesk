<?php
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Add Knowledge Base Resource</h2>
        <p class="page-subtitle">Create a new guide, policy, or document for the Knowledge Base</p>
    </div>
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
                                <span class="detail-label">Topic:</span>
                                <div class="detail-value-box">
                                    <input type="text" name="topic"
                                        placeholder="Enter resource title (e.g., 'UCSC Wi-Fi Setup Guide')"
                                        value="<?php echo isset($post_data['topic']) ? htmlspecialchars($post_data['topic']) : ''; ?>"
                                        maxlength="200" required>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Section:</span>
                                <div class="detail-value-box">
                                    <select id="section" name="section" required>
                                        <option value=''>Select a section</option>
                                        <option value='General Documents' <?php echo (isset($post_data['section']) && $post_data['section'] === 'General Documents') ? 'selected' : ''; ?>>General
                                            Documents</option>
                                        <option value='Policies and rules' <?php echo (isset($post_data['section']) && $post_data['section'] === 'Policies and rules') ? 'selected' : ''; ?>>Policies
                                            and rules</option>
                                        <option value='Academic resources' <?php echo (isset($post_data['section']) && $post_data['section'] === 'Academic resources') ? 'selected' : ''; ?>>Academic
                                            resources</option>
                                    </select>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Description:</span>
                                <div class="detail-value-box">
                                    <textarea class="description" name="description"
                                        placeholder="Enter detailed description or content" maxlength="5000"
                                        rows="6"><?php echo isset($post_data['description']) ? htmlspecialchars($post_data['description']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Upload Resource File:</span>
                                <div class="detail-value-box">
                                    <input class="resource-file" type="file" name="resource_file"
                                        accept=".pdf,.doc,.docx,.jpg,.png,.txt">
                                    <small
                                        style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Supported:
                                        PDF, DOC, DOCX, JPG, PNG, TXT (Max 10MB)</small>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Type:</span>
                                <div class="detail-value-box">
                                    <select id="type" name="type" required>
                                        <option value=''>Select a type</option>
                                        <option value='Guide' <?php echo (isset($post_data['type']) && $post_data['type'] === 'Guide') ? 'selected' : ''; ?>>Guide</option>
                                        <option value='Schedule' <?php echo (isset($post_data['type']) && $post_data['type'] === 'Schedule') ? 'selected' : ''; ?>>Schedule</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="ticket-action">
                            <button type="submit" class="ticket-action-btn">Add the Document</button>
                            <form method="POST" action="" style="display: contents;">  <!-- Nested form with display: contents to inline without breaking flex -->
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="action-btn reject-btn" onclick="window.history.back(); return false;">Close</button>
                            </form>
                        </div>
                    </form>
                </div>
            </article>
        </div>
    </div>
</main>
<style>
    .main-content {
        padding: 40px 20px;
        max-width: 900px;
        margin: 0 auto;
        min-height: 100vh;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 8px 0;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        font-size: 18px;
        color: #718096;
        margin: 0;
        font-weight: 400;
    }

    .content-area {
        padding: 0;
    }

    /* Card Styling */
    .tickets-container {
        padding: 0;
        border-color: #8c8cf9;
    }

    .description {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        background: transparent;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    .ticket-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 0;
    }

    .ticket-header {
        background: #f7fafc;
        color: #4a5568;
        padding: 24px 32px;
        margin: 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .ticket-title-group {
        flex-grow: 1;
    }

    .ticket-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 4px 0;
        letter-spacing: -0.01em;
        color: #2d3748;
    }

    .ticket-meta {
        display: flex;
        gap: 16px;
        font-size: 14px;
        color: #a0aec0;
    }

    .ticket-body {
        padding: 32px;
    }

    /* Success/Error Messages */
    .success,
    .error {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .success {
        background: #f0fff4;
        color: #38a169;
        border-left: 4px solid #48bb78;
    }

    .error {
        background: #fff5f5;
        color: #e53e3e;
        border-left: 4px solid #f56565;
    }

    /* Form Details Group */
    .details-group {
        display: flex;
        flex-direction: column;
        gap: 28px;
        margin-bottom: 0;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-label {
        font-size: 15px;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    .detail-value-box {
        padding: 24px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        min-height: 80px;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .detail-value-box:hover {
        background: #fafbfc;
        border-color: #cbd5e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .detail-value-box input,
    .detail-value-box select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        background: transparent;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }

    .detail-value-box input:focus,
    .detail-value-box select:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .resource-file {
        font-size: 15px;
        font-family: "Poppins", sans-serif;
    }

    /* Fields Container */
    #fields-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 24px;
        background: #fafbfc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        min-height: 200px;
    }

    #fields-container .field-input {
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        transition: border-color 0.2s ease;
        background: white;
    }

    #fields-container .field-input:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .add-field-btn {
        background: #4299e1;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.3s ease;
        align-self: flex-start;
        margin-top: 8px;
    }

    .add-field-btn:hover {
        background: #3182ce;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
    }

    /* Checkbox */
    .detail-value-box input[type="checkbox"] {
        width: auto;
        margin-right: 8px;
        transform: scale(1.2);
    }

    /* Ticket Action */
    .ticket-action {
        display: flex;
        justify-content: flex-end;
        gap: 12px;  /* Space between buttons */
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .ticket-action-btn {
        padding: 12px 24px;  /* Match reject-btn padding for alignment */
        border: none;
        border-radius: 10px;  /* Match reject-btn radius */
        background: #8c8cf9;
        color: #fff;
        font-family: "Poppins", sans-serif;
        font-size: 15px;  /* Match reject-btn size */
        font-weight:400;  /* Match reject-btn weight */
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(140, 140, 249, 0.2);  /* Purple shadow like reject's red */
    }

    .ticket-action-btn:hover {
        background-color: #6a6af5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(106, 110, 245, 0.3);  /* Intensified purple shadow */
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            padding: 20px 16px;
        }

        .content-area {
            padding: 0;
        }

        .ticket-body {
            padding: 24px;
        }

        .details-group {
            gap: 20px;
        }

        .detail-label {
            width: auto;
        }

        .ticket-action {
            justify-content: center;
            flex-direction: column;  /* Stack on mobile */
            align-items: stretch;
        }

        .ticket-action-btn,
        .action-btn.reject-btn {
            width: 100%;
            max-width: 300px;
            margin: 0 auto 8px auto;  /* Center and space on mobile */
        }
    }

    .action-btn.reject-btn {
        background: #fee2e2;
        color: #dc2626;
        padding: 12px 24px;
        border: 1px solid #f87171;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        margin: 0;  /* Remove old margin-top */
        display: inline-flex;  /* Keep for button content alignment */
    }

    .action-btn.reject-btn:hover {
        background: #fecaca;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        border-color: #ef4444;
    }
</style>