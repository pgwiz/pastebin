<?php
include_once 'db.php';
include_once 'auth.php';
$user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPastebin | Advanced Navbar with SVG Effects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"   rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
    <link rel="stylesheet" href="src/navbar.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-code brand-logo"></i>
                MyPastebin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">   
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active star-button" href="index.php">
                            <i class="fas fa-home me-2"></i>Home
                            <div class="star-1"><?php include __DIR__ . '/src/star-1.svg'; ?></div>
                            <div class="star-2"><?php include __DIR__ . '/src/star-2.svg'; ?></div>
                            <div class="star-3"><?php include __DIR__ . '/src/star-3.svg'; ?></div>
                            <div class="star-4"><?php include __DIR__ . '/src/star-4.svg'; ?></div>
                            <div class="star-5"><?php include __DIR__ . '/src/star-5.svg'; ?></div>
                            <div class="star-6"><?php include __DIR__ . '/src/star-6.svg'; ?></div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link star-button" href="create.php">
                            <i class="fas fa-plus-circle me-2"></i>New Paste
                            <div class="star-1"><?php include __DIR__ . '/src/star-1.svg'; ?></div>
                            <div class="star-2"><?php include __DIR__ . '/src/star-2.svg'; ?></div>
                            <div class="star-3"><?php include __DIR__ . '/src/star-3.svg'; ?></div>
                            <div class="star-4"><?php include __DIR__ . '/src/star-4.svg'; ?></div>
                            <div class="star-5"><?php include __DIR__ . '/src/star-5.svg'; ?></div>
                            <div class="star-6"><?php include __DIR__ . '/src/star-6.svg'; ?></div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link star-button" href="recent.php">
                            <i class="fas fa-history me-2"></i>Recent
                            <div class="star-1"><?php include __DIR__ . '/src/star-1.svg'; ?></div>
                            <div class="star-2"><?php include __DIR__ . '/src/star-2.svg'; ?></div>
                            <div class="star-3"><?php include __DIR__ . '/src/star-3.svg'; ?></div>
                            <div class="star-4"><?php include __DIR__ . '/src/star-4.svg'; ?></div>
                            <div class="star-5"><?php include __DIR__ . '/src/star-5.svg'; ?></div>
                            <div class="star-6"><?php include __DIR__ . '/src/star-6.svg'; ?></div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link star-button" href="featured.php">
                            <i class="fas fa-star me-2"></i>Featured
                            <div class="star-1"><?php include __DIR__ . '/src/star-1.svg'; ?></div>
                            <div class="star-2"><?php include __DIR__ . '/src/star-2.svg'; ?></div>
                            <div class="star-3"><?php include __DIR__ . '/src/star-3.svg'; ?></div>
                            <div class="star-4"><?php include __DIR__ . '/src/star-4.svg'; ?></div>
                            <div class="star-5"><?php include __DIR__ . '/src/star-5.svg'; ?></div>
                            <div class="star-6"><?php include __DIR__ . '/src/star-6.svg'; ?></div>
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if ($user): ?>
                        <div class="user-section d-flex align-items-center">
                            <span class="text-white me-3">Hello, <?= htmlspecialchars($user['username']) ?></span>
                            <a href="profile.php" class="me-3">
                                <img src="<?= $user['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80' ?>" 
                                     alt="Profile" class="user-avatar">
                            </a>
                            <a href="logout.php" class="nav-btn">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="user-section d-flex align-items-center">
                            <a href="login.php" class="nav-btn login me-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                            <a href="register.php" class="nav-btn">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const loggedInSection = document.querySelector('.user-section.d-flex');
            const loggedOutSection = document.querySelector('.user-section.d-none');
            setTimeout(() => {
                if (loggedInSection) loggedInSection.classList.add('d-none');
                if (loggedOutSection) loggedOutSection.classList.remove('d-none');
            }, 3000);
            setTimeout(() => {
                if (loggedInSection) loggedInSection.classList.remove('d-none');
                if (loggedOutSection) loggedOutSection.classList.add('d-none');
            }, 6000);
        });
    </script>
</body>
</html>