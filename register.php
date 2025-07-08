<?php
include 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // --- Validation ---
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = "Username is already taken.";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "An account with this email already exists.";
    }

    // If no errors, create the user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            // Redirect to login page after successful registration
            header("Location: login.php?registration=success");
            exit;
        } else {
            $errors[] = "Could not create account. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MyPastebin</title>
    <link rel="stylesheet" href="src/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center">
    <canvas id="background-canvas"></canvas>
    <button class="btn btn-outline-secondary theme-toggle" id="themeToggle">🌙</button>

    <div class="form-container">
        <form method="post" class="form">
            <h2 class="mb-4">Create Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="flex-column">
                <label>Username</label>
            </div>
            <div class="inputForm">
                <input placeholder="Enter your Username" class="input" type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="flex-column">
                <label>Email</label>
            </div>
            <div class="inputForm">
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 32 32" height="20"><g data-name="Layer 3" id="Layer_3"><path d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z"></path></g></svg>
                <input placeholder="Enter your Email" class="input" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="flex-column">
                <label>Password</label>
            </div>
            <div class="inputForm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="-64 0 512 512" height="20"><path d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0"></path><path d="m304 224c-8.832031 0-16-7.167969-16-16v-80c0-52.929688-43.070312-96-96-96s-96 43.070312-96 96v80c0 8.832031-7.167969 16-16 16s-16-7.167969-16-16v-80c0-70.59375 57.40625-128 128-128s128 57.40625 128 128v80c0 8.832031-7.167969 16-16 16zm0 0"></path></svg>
                <input placeholder="Enter your Password" class="input" type="password" name="password" required>
            </div>

            <button class="button-submit" type="submit">Sign Up</button>

            <p class="p">
                Already have an account?
                <a href="login.php" class="span">Sign In</a>
            </p>
        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
