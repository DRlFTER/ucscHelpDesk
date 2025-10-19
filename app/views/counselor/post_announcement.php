<?php
// post_announcement.php
session_start();
include 'db_connect.php';

$counselor_name = "Mrs. Nalani Perera"; // Replace with session data
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $priority = $_POST['priority'] ?? 'medium';
    $target_audience = $_POST['target_audience'] ?? 'all';
    $expires_at = $_POST['expires_at'] ?? '';
    
    // Validation
    if (empty($title) || empty($content)) {
        $error_message = "Title and content are required.";
    } else {
        // Insert announcement into database
        try {
            $stmt = $conn->prepare("INSERT INTO announcements (title, content, category, priority, target_audience, expires_at, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssss", $title, $content, $category, $priority, $target_audience, $expires_at, $counselor_name);
            
            if ($stmt->execute()) {
                $success_message = "Announcement posted successfully!";
                // Clear form data
                $title = $content = $expires_at = '';
            } else {
                $error_message = "Error posting announcement. Please try again.";
            }
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

// Get recent announcements
$recent_announcements = [];
try {
    $result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $recent_announcements[] = $row;
        }
    }
} catch (Exception $e) {
    // Handle database error gracefully
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement - UCSC Help Desk</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
    <style>
        .announcement-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4285f4;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #4285f4, #34a853);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(66, 133, 244, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .recent-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .recent-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .announcement-item {
            padding: 15px;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: box-shadow 0.2s;
        }

        .announcement-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .announcement-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .announcement-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .announcement-excerpt {
            font-size: 14px;
            color: #555;
            line-height: 1.4;
        }

        .priority-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-high { background: #ffebee; color: #c62828; }
        .priority-medium { background: #fff3e0; color: #ef6c00; }
        .priority-low { background: #e8f5e8; color: #2e7d32; }

        .category-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            background: #e3f2fd;
            color: #1565c0;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .announcement-container {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'coun_navbar.html'; ?>

    <header>
        <h2>Post Announcement</h2>
        <p>Share important information with students and staff</p>
    </header>

    <div class="announcement-container">
        <!-- Form Section -->
        <div class="form-section">
            <h2>Create New Announcement</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?= $error_message ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Announcement Title *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required placeholder="Enter announcement title">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="general" <?= ($category ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                            <option value="academic" <?= ($category ?? '') === 'academic' ? 'selected' : '' ?>>Academic</option>
                            <option value="counseling" <?= ($category ?? '') === 'counseling' ? 'selected' : '' ?>>Counseling</option>
                            <option value="event" <?= ($category ?? '') === 'event' ? 'selected' : '' ?>>Event</option>
                            <option value="maintenance" <?= ($category ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            <option value="urgent" <?= ($category ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority Level</label>
                        <select id="priority" name="priority">
                            <option value="low" <?= ($priority ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= ($priority ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= ($priority ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="target_audience">Target Audience</label>
                        <select id="target_audience" name="target_audience">
                            <option value="all" <?= ($target_audience ?? '') === 'all' ? 'selected' : '' ?>>All Users</option>
                            <option value="students" <?= ($target_audience ?? '') === 'students' ? 'selected' : '' ?>>Students Only</option>
                            <option value="staff" <?= ($target_audience ?? '') === 'staff' ? 'selected' : '' ?>>Staff Only</option>
                            <option value="counselors" <?= ($target_audience ?? '') === 'counselors' ? 'selected' : '' ?>>Counselors Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expires_at">Expiry Date (Optional)</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" value="<?= htmlspecialchars($expires_at ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Announcement Content *</label>
                    <textarea id="content" name="content" required placeholder="Write your announcement content here..."><?= htmlspecialchars($content ?? '') ?></textarea>
                </div>

                <button type="submit" class="submit-btn">📢 Post Announcement</button>
            </form>
        </div>

        <!-- Recent Announcements -->
        <div class="recent-section">
            <h3>📋 Recent Announcements</h3>
            
            <?php if (empty($recent_announcements)): ?>
                <p style="color: #666; font-style: italic;">No recent announcements found.</p>
            <?php else: ?>
                <?php foreach ($recent_announcements as $announcement): ?>
                    <div class="announcement-item">
                        <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                        <div class="announcement-meta">
                            <span class="priority-badge priority-<?= $announcement['priority'] ?>">
                                <?= ucfirst($announcement['priority']) ?>
                            </span>
                            <span class="category-badge"><?= ucfirst($announcement['category']) ?></span>
                            <br>
                            <small>By <?= htmlspecialchars($announcement['created_by']) ?> • <?= date('M j, Y g:i A', strtotime($announcement['created_at'])) ?></small>
                        </div>
                        <div class="announcement-excerpt">
                            <?= htmlspecialchars(substr($announcement['content'], 0, 100)) ?><?= strlen($announcement['content']) > 100 ? '...' : '' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="view_all_announcements.php" style="color: #4285f4; text-decoration: none; font-weight: 500; display: inline-block; margin-top: 15px;">View All Announcements →</a>
        </div>
    </div>

    <script>
        // Auto-hide success message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        successAlert.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>