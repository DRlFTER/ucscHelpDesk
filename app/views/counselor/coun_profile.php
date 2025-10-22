<?php
// profile_settings.php
session_start();
include 'db_connect.php';

$success_message = "";
$error_message = "";

// Get current counselor ID from session (for now, using default)
$counselor_id = $_SESSION['counselor_id'] ?? 1; // Replace with actual session

// Fetch current counselor data
$counselor = null;
$stmt = $conn->prepare("SELECT * FROM counselors WHERE id = ?");
$stmt->bind_param("i", $counselor_id);
$stmt->execute();
$result = $stmt->get_result();
$counselor = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $office_location = trim($_POST['office_location'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email)) {
        $error_message = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Update counselor information
        $update_query = "UPDATE counselors SET 
                        name = ?, 
                        email = ?, 
                        phone = ?, 
                        specialization = ?, 
                        office_location = ?, 
                        bio = ?,
                        updated_at = NOW() 
                        WHERE id = ?";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssi", $name, $email, $phone, $specialization, $office_location, $bio, $counselor_id);
        
        if ($stmt->execute()) {
            // Update password if provided
            if (!empty($new_password)) {
                if ($new_password !== $confirm_password) {
                    $error_message = "New passwords do not match.";
                } elseif (strlen($new_password) < 6) {
                    $error_message = "Password must be at least 6 characters.";
                } else {
                    // Hash and update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $pwd_stmt = $conn->prepare("UPDATE counselors SET password = ? WHERE id = ?");
                    $pwd_stmt->bind_param("si", $hashed_password, $counselor_id);
                    $pwd_stmt->execute();
                    $pwd_stmt->close();
                }
            }
            
            if (empty($error_message)) {
                $success_message = "Profile updated successfully!";
                // Refresh counselor data
                $stmt = $conn->prepare("SELECT * FROM counselors WHERE id = ?");
                $stmt->bind_param("i", $counselor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $counselor = $result->fetch_assoc();
                $stmt->close();
                
                // Update session
                $_SESSION['counselor_name'] = $name;
            }
        } else {
            $error_message = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - UCSC Help Desk</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
    <link rel="stylesheet" href="counselor_dashboard.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: 700;
        }

        .profile-info h1 {
            font-size: 28px;
            margin-bottom: 8px;
            color: #333;
        }

        .profile-info p {
            color: #666;
            font-size: 14px;
        }

        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4285f4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .btn-primary {
            background: linear-gradient(135deg, #4285f4, #34a853);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(66, 133, 244, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .form-help-text {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .password-strength {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-weak { width: 33%; background: #dc3545; }
        .strength-medium { width: 66%; background: #ffc107; }
        .strength-strong { width: 100%; background: #28a745; }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'coun_navbar.html'; ?>

    <header>
        <h2>⚙️ Profile Settings</h2>
        <p>Manage your account information and preferences</p>
    </header>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-large">
                <?= strtoupper(substr($counselor['name'], 0, 1)) ?>
            </div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($counselor['name']) ?></h1>
                <p>Counselor ID: #<?= $counselor['id'] ?></p>
                <p>Member since: <?= date('F Y', strtotime($counselor['created_at'] ?? 'now')) ?></p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <span><?= $success_message ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <span>❌</span>
                <span><?= $error_message ?></span>
            </div>
        <?php endif; ?>

        <!-- Profile Form -->
        <div class="profile-form">
            <form method="POST" action="">
                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="section-title">Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= htmlspecialchars($counselor['name']) ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($counselor['email']) ?>" 
                                   required>
                            <div class="form-help-text">Your institutional email address</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($counselor['phone']) ?>" 
                                   placeholder="07XXXXXXXX">
                        </div>

                        <div class="form-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" id="specialization" name="specialization" 
                                   value="<?= htmlspecialchars($counselor['specialization']) ?>" 
                                   placeholder="e.g., Mental Health Counseling">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="office_location">Office Location</label>
                        <input type="text" id="office_location" name="office_location" 
                               value="<?= htmlspecialchars($counselor['office_location']) ?>" 
                               placeholder="e.g., Building A, Room 201">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio / About Me</label>
                        <textarea id="bio" name="bio" 
                                  placeholder="Tell students about your experience and expertise..."><?= htmlspecialchars($counselor['bio']) ?></textarea>
                        <div class="form-help-text">This will be visible to students seeking counseling</div>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="form-section">
                    <h3 class="section-title">Change Password</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   placeholder="Enter current password">
                            <div class="form-help-text">Leave blank to keep current password</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" 
                                   placeholder="Enter new password">
                            <div class="password-strength">
                                <div class="password-strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="form-help-text">Minimum 6 characters</div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   placeholder="Re-enter new password">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary">💾 Save Changes</button>
                    <a href="coun_dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength-bar';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                strengthBar.className = 'password-strength-bar strength-weak';
            } else if (strength <= 3) {
                strengthBar.className = 'password-strength-bar strength-medium';
            } else {
                strengthBar.className = 'password-strength-bar strength-strong';
            }
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Auto-hide success message
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.opacity = '0';
                    successAlert.style.transition = 'opacity 0.3s';
                    setTimeout(function() {
                        successAlert.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>