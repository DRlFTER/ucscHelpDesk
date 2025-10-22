<?php
// views/staff/createTemplate.php (fixed layout and structure)
?>
<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Create Template</h2>
        <p class="page-subtitle">Define a new FAQ template for student issues</p>
    </div>
        <!-- Main Content Area -->
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
                                        <input type="text" name="category" placeholder="Enter category (e.g., Technical Support)" value="<?php echo isset($post_data['category']) ? htmlspecialchars($post_data['category']) : ''; ?>" maxlength="50">
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Required Fields:</span>
                                    <div class="detail-value-box" id="fields-container">
                                        <?php for ($i = 1; $i <= ($field_count ?? 1); $i++): ?>
                                            <input type="text" name="field_<?php echo $i; ?>" placeholder="Field name (e.g., student_id)" value="<?php echo isset($post_data['field_' . $i]) ? htmlspecialchars($post_data['field_' . $i]) : ''; ?>" style="margin-bottom: 5px; width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                        <?php endfor; ?>
                                        <button type="button" class="add-field-btn" onclick="addField()">Add Another Field</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Process:</span>
                                    <div class="detail-value-box">
                                        <textarea name="process" placeholder="Enter process steps" maxlength="1000" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-height: 100px; resize: vertical;"><?php echo isset($post_data['process']) ? htmlspecialchars($post_data['process']) : ''; ?></textarea>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Expected Outcome:</span>
                                    <div class="detail-value-box">
                                        <textarea name="outcome" placeholder="Enter expected outcome" maxlength="1000" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-height: 100px; resize: vertical;"><?php echo isset($post_data['outcome']) ? htmlspecialchars($post_data['outcome']) : ''; ?></textarea>
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
    </div>
</main>

<style>
    /* Form Styles */
    .main-content { padding: 20px; max-width: 1200px; margin: 0 auto; }
    .page-header { text-align: center; margin-bottom: 20px; }
    .page-title { font-size: 24px; color: #333; margin-bottom: 10px; }
    .page-subtitle { font-size: 16px; color: #666; }
    .ticket-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 20px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .ticket-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .ticket-title-group { flex-grow: 1; }
    .ticket-title { font-size: 18px; color: #333; margin: 0; }
    .ticket-meta { font-size: 12px; color: #666; }
    .ticket-body { padding: 10px 0; }
    .details-group { display: flex; flex-direction: column; gap: 10px; }
    .detail-item { display: flex; align-items: flex-start; }
    .detail-label { font-weight: bold; color: #444; width: 120px; margin-top: 8px; }
    .detail-value-box { flex-grow: 1; padding: 8px; border: 1px solid #e0e0e0; border-radius: 4px; background: #f9f9f9; }
    .detail-value-box input, .detail-value-box textarea, .detail-value-box select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; box-sizing: border-box; background: transparent; border: none; }
    .detail-value-box textarea { resize: vertical; min-height: 100px; }
    .add-field-btn { background: #4a90e2; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 5px; }
    .add-field-btn:hover { background: #357abd; }
    .error { color: red; font-size: 12px; margin-top: 5px; display: block; }
    .success { color: green; font-size: 14px; margin-bottom: 15px; text-align: center; }
    .ticket-action { text-align: right; margin-top: 15px; }
    .ticket-action-btn { background: #4a90e2; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; }
    .ticket-action-btn:hover { background: #357abd; }

    /* Layout Styles */
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
        overflow-y: auto;
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

    /* Responsive */
    @media (max-width: 992px) {
        .layout-container {
            flex-direction: column;
        }
        .navMenu {
            flex: none;
            border-right: none;
            border-bottom: 1px solid #dee2e6;
        }
        .content-area {
            padding: 10px;
        }
        .details-group {
            flex-direction: column;
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
        newField.style.marginBottom = '5px';
        newField.style.width = '100%';
        newField.style.padding = '8px';
        newField.style.border = '1px solid #ccc';
        newField.style.borderRadius = '4px';
        container.insertBefore(newField, container.querySelector('.add-field-btn'));
        document.querySelector('input[name="field_count"]').value = fieldCount;
    }
</script>