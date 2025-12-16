<?php
$success = '';
?>

<main id="main-content" class="main-content">
    <div class="page-header">
        <h2 class="page-title">Announcement Details</h2>
        <p class="page-subtitle">View and manage this announcement</p>
    </div>

    <div class="ticket-detail-card">
        <div class="ticket-header">
            <div class="ticket-title-group">
                <h3 class="ticket-title"><?php echo htmlspecialchars($announcement['topic']); ?></h3>
                <div class="ticket-meta">
                    <span class="meta-author"><?php echo htmlspecialchars($announcement['staff_name']); ?></span>
                    <span class="meta-division"><?php echo htmlspecialchars($announcement['division_name']); ?></span>
                    <span class="meta-id">ID: <?php echo htmlspecialchars($announcement['id']); ?></span>
                    <span class="meta-date"><?php echo htmlspecialchars($announcement['date_time']); ?></span>
                </div>
            </div>
        </div>
        <div class="ticket-body">
            <div class="details-group">
                <div class="detail-item">
                    <span class="detail-label">Author:</span>
                    <span class="detail-value-box"><?php echo htmlspecialchars($announcement['staff_name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Division:</span>
                    <span class="detail-value-box"><?php echo htmlspecialchars($announcement['division_name']); ?></span>
                </div>
            </div>
        </div>
        <div class="description-section">
            <h3 class="section-label">Description</h3>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
            </div>
        </div>

        <div class="file-section">
    <h3 class="section-label">Attached Files</h3>
    <?php if (empty($files)): ?>
        <p class="no-files">No files attached.</p>
    <?php else: ?>
        <ul class="files-list">
            <?php foreach ($files as $file): ?>
                <li class="file-item">
                    <a href="/<?php echo htmlspecialchars($file['file_path']); ?>" 
                       download="<?php echo htmlspecialchars($file['file_name']); ?>" 
                       class="file-link"
                       rel="noopener noreferrer">
                        <?php echo htmlspecialchars($file['file_name']); ?> (<?php echo number_format($file['file_size'] / 1024, 1); ?> KB)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

        <div class="edit-section">
            <h3 class="section-label">Edit Announcement</h3>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
                </div>
            <?php endif; ?>
            <form class="edit-form" method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                <div class="form-field">
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($announcement['topic']); ?>" maxlength="50" required placeholder="Announcement topic">
                </div>
                <div class="form-field">
                    <textarea class="edit-textarea" name="content" required placeholder="Announcement content"><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="update-ticket-btn" name="update_ticket">Update Announcement</button>
                    <button type="submit" class="delete-ticket-btn" name="delete_ticket" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete Announcement</button>
                </div>
            </form>
        </div>
    </div>
</main>
<style>
  /* Announcements page: Minimal tweaks to match tickets.css theme. No breaking layout changes. */
.main-content {
    padding: 45px 84px;
}

.page-header {
    text-align: center;
    margin-bottom: 30px;
}

.page-title {
    font-size: 35px;
    font-weight: 500;
    margin: 0 0 6px 0;
}

.page-subtitle {
    font-size: 25px;
    font-weight: 400;
    color: var(--color-text-body);
    margin: 0;
    letter-spacing: 0.5px;
}

/* Detail Card: Reuse existing ticket-detail-card */
.ticket-detail-card {
    background-color: var(--color-bg-card);
    border: 1px solid var(--color-border-card);
    border-radius: 15px;
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);  /* Subtle shadow to match tickets */
}

/* Header: Enhanced meta display */
.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    border-bottom: 1px solid var(--color-border-light);  /* Light separator */
}

.ticket-title-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.ticket-title {
    font-size: 28px;
    font-weight: 500;
    letter-spacing: 0.56px;
    margin: 0;
    color: var(--color-text-dark);
}

.ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;  /* Flexible gap */
    font-size: 16px;
    color: var(--color-text-light);
    letter-spacing: 0.32px;
    margin-top: 0.5rem;
}

.meta-author, .meta-division, .meta-id, .meta-date {
    background: #f3f4f6;  /* Light bg for badges */
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
}

/* Body: Reuse ticket-body */
.ticket-body {
    display: flex;
    gap: 30px;
    padding: 15px;
    flex-wrap: wrap;
}

/* Description Section: Simple, integrated */
.description-section {
    margin-top: 1rem;
    padding: 1.5rem;
    background: #f8fafc;  /* Light bg for contrast */
    border-radius: 10px;
    border-left: 4px solid var(--color-primary);
}

.section-label {
    font-size: 1.125rem;
    font-weight: 500;
    color: var(--color-text-dark);
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.description-content {
    line-height: 1.6;
    color: var(--color-text-body);
    font-size: 1rem;
    white-space: pre-wrap;  /* Preserves formatting */
}

/* Files Section: List-style for clarity */
.file-section {
    margin-top: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid var(--color-primary);
}

.files-list {
    list-style: none;
    padding: 0;
    margin: 0 0 0 1rem;  /* Indent like bullet */
}

.file-item {
    margin-bottom: 0.5rem;
}

.file-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.file-link:hover {
    color: #6366f1;
    text-decoration: underline;
}

.no-files {
    color: var(--color-text-light);
    font-style: italic;
    text-align: center;
    padding: 1rem;
    background: #f3f4f6;
    border-radius: 6px;
}

/* Edit Section: Reuse existing, enhance form */
.edit-section {
    margin-top: 20px;
    padding: 15px;
    background-color: #f5f5f5;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    border-left: 4px solid var(--color-primary);  /* Theme accent */
}

.edit-section h3 {
    font-size: 22px;
    margin-bottom: 10px;
    font-family: "Inter", sans-serif;
    color: var(--color-text-dark);
}

.edit-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;  /* Tighter for flow */
    margin-top: 15px;
}

.edit-textarea,
.edit-form input[type="text"] {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid var(--color-border-medium);
    border-radius: 8px;
    font-size: 16px;
    font-family: "Inter", sans-serif;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s ease;
}

.edit-textarea:focus,
.edit-form input[type="text"]:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(140, 140, 249, 0.1);  /* Purple focus ring */
}

/* Actions: Reuse existing buttons, ensure alignment */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
}

.update-ticket-btn {
    align-self: flex-end;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: var(--color-primary, #8c8cf9);
    color: #fff;
    font-family: "Poppins", sans-serif;
    font-size: 14px;
    font-weight: 400;
    cursor: pointer;
    transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
}

.update-ticket-btn:hover {
    background-color: #6a6af5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.delete-ticket-btn {
    align-self: flex-end;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: var(--status-rejected-bg, #ffd8d8);  /* Reuse rejected color for consistency */
    color: var(--status-rejected-text, #b50000);
    font-family: "Poppins", sans-serif;
    font-size: 14px;
    font-weight: 400;
    cursor: pointer;
    transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
    border: 1px solid var(--status-rejected-bg);
}

.delete-ticket-btn:hover {
    background: #f56565;  /* Deeper red on hover */
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
}

/* Success/Error: Reuse from tickets */
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
    background: var(--status-resolved-bg, #9effbc);
    color: var(--status-resolved-text, #166434);
    border-left: 4px solid #48bb78;
}

.error {
    background: var(--status-rejected-bg, #ffd8d8);
    color: var(--status-rejected-text, #b50000);
    border-left: 4px solid #f56565;
}

/* Responsive: Reuse from tickets, minor tweaks */
@media (max-width: 768px) {
    .main-content {
        padding: 20px 16px;
    }

    .ticket-detail-card {
        padding: 16px;
    }

    .ticket-meta {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .description-section,
    .file-section,
    .edit-section {
        padding: 1rem;
    }

    .form-actions {
        flex-direction: column;
        gap: 0.75rem;
    }

    .update-ticket-btn,
    .delete-ticket-btn {
        width: 100%;
        align-self: stretch;
    }

    .files-list {
        margin-left: 0;
    }
}
  
  </style>