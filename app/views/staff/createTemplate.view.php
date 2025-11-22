<?php
if (isset($_SESSION['success'])) {
    echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);  // Clear flash
}
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Create Template</h2>
        <p class="page-subtitle">Define a new FAQ template for student issues</p>
    </div>
    
    <div class="content-area">
        <div class="tickets-container">
            <article class="ticket-card">
                <div class="ticket-header">
                    <div class="ticket-title-group">
                        <h3 class="ticket-title">New Template</h3>
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

                    <form method="POST" action="">
                        <input type="hidden" name="field_count" value="<?php echo htmlspecialchars($field_count ?? 1); ?>">
                        <div class="details-group">
                            <div class="detail-item">
                                <span class="detail-label">Template Name:</span>
                                <div class="detail-value-box">
                                    <input type="text" name="name" placeholder="Enter template name" value="<?php echo isset($post_data['name']) ? htmlspecialchars($post_data['name']) : ''; ?>" maxlength="100">
                                </div>
                            </div>
                                <div class="detail-item">
                                <span class="detail-label">Category:</span>
                                <div class="detail-value-box">
                                <select id="category" name="category" required>
                                    <option value="" disabled <?php echo empty($post_data['category']) ? 'selected' : ''; ?>>Select type</option>
                                    
                                    <?php foreach ($divisions as $division): ?>
                                    <option value="<?php echo $division['did']; ?>" 
                                            <?php echo (isset($post_data['category']) && $post_data['category'] == $division['did']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($division['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Required Fields:</span>
                                <div class="detail-value-box" id="fields-container">
                                    <?php for ($i = 1; $i <= ($field_count ?? 1); $i++): ?>
                                        <input type="text" name="field_<?php echo $i; ?>" placeholder="Field name (e.g., student_id)" value="<?php echo isset($post_data['field_' . $i]) ? htmlspecialchars($post_data['field_' . $i]) : ''; ?>" class="field-input">
                                    <?php endfor; ?>
                                    <button type="button" class="add-field-btn" onclick="addField()">Add Another Field</button>
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Letter Required:</span>
                                <div class="detail-value-box">
                                    <input type="checkbox" name="letter_required" value="1" <?php echo isset($post_data['letter_required']) ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-action">
                            <button type="submit" class="ticket-action-btn">Save Template</button>
                        </div>
                    </form>
                </div>
            </article>
        </div>
    </div>
</main>

<style>
    /* Main Layout */
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
    .success, .error {
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
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .ticket-action-btn {
        background: #4299e1;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(66, 153, 225, 0.2);
    }

    .ticket-action-btn:hover {
        background: #3182ce;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(66, 153, 225, 0.3);
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
        }

        .ticket-action-btn {
            width: 100%;
            max-width: 300px;
        }
    }
</style>

<script>
    function addField() {
        const container = document.getElementById('fields-container');
        const fieldInputs = container.querySelectorAll('input[name^="field_"]');
        const fieldCount = fieldInputs.length + 1;
        const newField = document.createElement('input');
        newField.type = 'text';
        newField.name = 'field_' + fieldCount;
        newField.placeholder = 'Field name (e.g., student_id)';
        newField.className = 'field-input';
        newField.required = true;
        container.insertBefore(newField, container.querySelector('.add-field-btn'));
        document.querySelector('input[name="field_count"]').value = fieldCount;
    }
</script>