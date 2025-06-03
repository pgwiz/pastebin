<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
if (!$user) {
    header("Location: login.php");
    exit;
}


if (isset($_POST['feature_paste'])) {
    $paste_id = intval($_POST['paste_id']);
    $stmt = $pdo->prepare("UPDATE pastes SET is_featured = 1 WHERE id = ?");
    $stmt->execute([$paste_id]);
}

// Get user statistics
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_pastes,
        SUM(views) AS total_views,
        SUM(shares) AS total_shares,
        SUM(is_featured) AS featured_count
    FROM pastes 
    WHERE user_id = ?
");
$stmt->execute([$user['id']]);
$stats = $stmt->fetch();

// Format numbers
$total_pastes = $stats['total_pastes'] ?? 0;
$total_views = number_format($stats['total_views'] ?? 0);
$total_shares = number_format($stats['total_shares'] ?? 0);
$featured_count = $stats['featured_count'] ?? 0;

$success = $error = '';
$avatar_dir = 'uploads/avatars/';

$default_avatar = 'https://cdn1.iconfinder.com/data/icons/basic-and-universal/64/Male_avatar-512.png'; 

// Create avatar directory if needed
if (!is_dir($avatar_dir)) {
    mkdir($avatar_dir, 0755, true);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Username update
    if (isset($_POST['update_username'])) {
        $new_username = trim($_POST['new_username']);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$new_username]);
        $existing = $stmt->fetch();

        if ($existing && $existing['id'] != $user['id']) {
            $error = "Username already taken";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$new_username, $user['id']]);
            
            $_SESSION['user']['username'] = $new_username;
            $success = "Username updated successfully";
        }
    }

    // Password update
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $stored_password = $stmt->fetch()['password'];

        if (!password_verify($current_password, $stored_password)) {
            $error = "Current password is incorrect";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);
            $success = "Password updated successfully";
        }
    }

    // Avatar upload
    if (isset($_POST['update_avatar'])) {
        if (!empty($_FILES['avatar']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $detected_type = mime_content_type($_FILES['avatar']['tmp_name']);
            
            if (!in_array($detected_type, $allowed_types)) {
                $error = "Only JPG, PNG, and GIF images are allowed";
            } else {
                $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $user['id'] . '.' . $extension;
                $target = $avatar_dir . $filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                    $avatar_url = $avatar_dir . $filename;
                    
                    // Update database
                    $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
                    $stmt->execute([$avatar_url, $user['id']]);
                    
                    // Update session
                    $_SESSION['user']['avatar_url'] = $avatar_url;
                    $success = "Avatar updated successfully";
                } else {
                    $error = "Error uploading file";
                }
            }
        } else {
            $error = "No file selected";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | MyPastebin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/profile.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="avatar-container">
                            <img src="<?= $user['avatar_url'] ?? $default_avatar ?>" 
                                 alt="Profile Avatar" class="avatar-preview">
                            <div class="avatar-overlay">Click to Change</div>
                        </div>
                        <h1 class="username"><?= htmlspecialchars($user['username']) ?></h1>
                        <p class="member-since">
                            <i class="fas fa-calendar-alt me-2"></i>Member since 
                            <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?>
                        </p>
                    </div>
                    
                    <!-- Profile Body -->
                    <div class="profile-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- User Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <div class="stats-icon">
                                        <i class="fas fa-file-code"></i>
                                    </div>
                                    <div class="stats-number"><?= $total_pastes ?></div>
                                    <div class="stats-label">Total Pastes</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <div class="stats-icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="stats-number"><?= $total_views ?></div>
                                    <div class="stats-label">Total Views</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <div class="stats-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="stats-number"><?= $featured_count ?></div>
                                    <div class="stats-label">Featured</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <div class="stats-icon">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <div class="stats-number"><?= $total_shares ?></div>
                                    <div class="stats-label">Shares</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Avatar Update Form -->
                        <form method="post" enctype="multipart/form-data" class="mb-5">
                            <h4 class="section-title">
                                <i class="fas fa-camera"></i>Profile Picture
                            </h4>
                            
                           <div class="avatar-upload-container">
                                <img id="avatarPreview" class="avatar-preview" src="" alt="Avatar Preview">
                                <input type="file" name="avatar" id="avatar" accept="image/*" onchange="previewAvatar(event)">
                                <label for="avatar">Upload New Avatar</label>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" name="update_avatar" class="btn-submit">
                                    <i class="fas fa-save me-2"></i>Update Avatar
                                </button>
                            </div>
                        </form>
                        
                        <div class="divider"></div>
                        
                        <!-- Username Update Form -->
                        <form method="post" class="mb-5">
                            <h4 class="section-title">
                                <i class="fas fa-user"></i>Account Information
                            </h4>
                            
                            <div class="form-group">
                                <input type="text" name="new_username" id="username" 
                                       class="form-control" value="<?= htmlspecialchars($user['username']) ?>" 
                                       required>
                                <label for="username" class="form-label">Username</label>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" name="update_username" class="btn-submit">
                                    <i class="fas fa-save me-2"></i>Update Username
                                </button>
                            </div>
                        </form>
                        
                        <div class="divider"></div>
                        
                        <!-- Password Update Form -->
                        <form method="post">
                            <h4 class="section-title">
                                <i class="fas fa-lock"></i>Security
                            </h4>
                            
                            <div class="form-group">
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control" required>
                                <label for="current_password" class="form-label">Current Password</label>
                            </div>
                            
                            <div class="form-group">
                                <input type="password" name="new_password" id="new_password" 
                                       class="form-control" required>
                                <label for="new_password" class="form-label">New Password</label>
                            </div>
                            
                            <div class="form-group">
                                <input type="password" id="confirm_password" class="form-control" required>
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" name="update_password" class="btn-submit">
                                    <i class="fas fa-save me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Avatar preview function
        function previewAvatar(event) {
    const input = event.target;
    const preview = document.getElementById('avatarPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
        
        // Password confirmation validation
        document.querySelector('form[method="post"]:last-of-type').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New password and confirmation do not match');
            }
        });
        
        // Add animation to form elements
        document.querySelectorAll('.form-group').forEach((group, index) => {
            setTimeout(() => {
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, 200 * index);
        });
    </script>
</body>
</html>