<?php
include 'db.php';
include 'header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 chars.";

    if (empty($errors)) {
        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) $errors[] = "Username or Email already exists.";

        if(empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_superadmin) VALUES (?, ?, ?, 0)");
            if ($stmt->execute([$username, $email, $hash])) {
                 header("Location: login.php?registered=1"); exit;
            } else {
                $errors[] = "Database error.";
            }
        }
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4">
    <div class="glass-panel p-8 rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-purple-500 to-pink-500"></div>

        <h2 class="text-3xl font-bold text-white mb-6 text-center">Create Account</h2>
        
        <?php if(!empty($errors)): ?>
             <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-2 rounded-lg mb-4 text-center text-sm">
                <?php foreach($errors as $e) echo "<p>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">
            <div>
                <label class="block text-slate-300 mb-1 font-medium text-sm">Username</label>
                <input type="text" name="username" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg py-2 px-4 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all placeholder-slate-500" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-slate-300 mb-1 font-medium text-sm">Email</label>
                <input type="email" name="email" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg py-2 px-4 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all placeholder-slate-500" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-slate-300 mb-1 font-medium text-sm">Password</label>
                <input type="password" name="password" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg py-2 px-4 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all placeholder-slate-500" required>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-bold rounded-lg shadow-lg shadow-purple-500/30 transition-all transform hover:-translate-y-0.5 mt-4">
                Sign Up
            </button>
        </form>

        <p class="text-center mt-6 text-slate-400 text-sm">
            Already have an account? <a href="login.php" class="text-pink-400 font-medium hover:text-pink-300">Log In</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
