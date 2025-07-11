<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginIdentifier = trim($_POST['login_identifier']);
    $password = $_POST['password'];
    $remember  = isset($_POST['remember']);

    // Check if the user exists with either username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$loginIdentifier, $loginIdentifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user'] = $user;

        if ($remember) {
            // The cookie still uses the unique username for consistency
            setcookie(
                'remember_me',
                "{$user['id']}:{$user['username']}",
                time() + 86400 * 30, // 30 days
                '/',
                '',
                false, // set to true if using HTTPS
                true
            );
        }

        header("Location: index.php");
        exit;
    } else {
        $errorMessage = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MyPastebin</title>
    <link rel="stylesheet" href="src/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center">
    <canvas id="background-canvas"></canvas>
    <button class="btn btn-outline-secondary theme-toggle" id="themeToggle">🌙</button>

    <div class="form-container">
        <form method="post" class="form">
            <h2 class="mb-4">Login</h2>

            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger"><?= $errorMessage ?></div>
            <?php endif; ?>

            <div class="flex-column">
                <label>Username or Email</label>
            </div>
            <div class="inputForm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 32 32" height="20"><g data-name="Layer 3" id="Layer_3"><path d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z"></path></g></svg>
                <input placeholder="Enter your Username or Email" class="input" type="text" name="login_identifier" required>
            </div>

            <div class="flex-column">
                <label>Password</label>
            </div>
            <div class="inputForm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="-64 0 512 512" height="20"><path d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0"></path><path d="m304 224c-8.832031 0-16-7.167969-16-16v-80c0-52.929688-43.070312-96-96-96s-96 43.070312-96 96v80c0 8.832031-7.167969 16-16 16s-16-7.167969-16-16v-80c0-70.59375 57.40625-128 128-128s128 57.40625 128 128v80c0 8.832031-7.167969 16-16 16zm0 0"></path></svg>
                <input placeholder="Enter your Password" class="input" type="password" name="password" required>
            </div>

            <div class="flex-row">
                <div>
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <span class="span">Forgot password?</span>
            </div>

            <button class="button-submit" type="submit">Sign In</button>

            <p class="p">
                Don't have an account?
                <a href="register.php" class="span">Sign Up</a>
            </p>
        </form>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Theme toggle logic
        const toggleBtn = document.getElementById('themeToggle');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            toggleBtn.textContent = '☀️';
        }
        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            toggleBtn.textContent = isDark ? '☀️' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new Event('themeChanged'));
        });
    </script>
</body>
</html>
