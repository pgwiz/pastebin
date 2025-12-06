<?php
include 'db.php';
include 'header.php'; // Header contains session checks if strictly needed, but Auth.php logic inside login handles session start logic manually usually. 
// Login Logic
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginIdentifier = trim($_POST['login_identifier']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$loginIdentifier, $loginIdentifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = $user;
        if ($remember) {
             setcookie('remember_me', "{$user['id']}:{$user['username']}", time() + 86400 * 30, '/', '', false, true);
        }
        header("Location: index.php");
        exit;
    } else {
        $errorMessage = "Invalid credentials.";
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4">
    <div class="glass-panel p-8 rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden">
        <!-- Decor -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-sky-400"></div>

        <h2 class="text-3xl font-bold text-white mb-6 text-center">Welcome Back</h2>
        
        <?php if($errorMessage): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-2 rounded-lg mb-4 text-center text-sm">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <div>
                <label class="block text-slate-300 mb-2 font-medium text-sm">Username or Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-500"><i class="fas fa-user"></i></span>
                    <input type="text" name="login_identifier" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg py-2 pl-10 pr-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder-slate-500" placeholder="Enter your identity" required>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 mb-2 font-medium text-sm">Password</label>
                 <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-500"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="w-full bg-slate-800/50 border border-slate-600 rounded-lg py-2 pl-10 pr-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder-slate-500" placeholder="Enter your password" required>
                </div>
            </div>
            
            <div class="flex items-center justify-between text-sm">
                 <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-300">
                     <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-700 text-blue-500 focus:ring-offset-slate-800">
                     <span>Remember me</span>
                 </label>
                 <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors">Forgot password?</a>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                Sign In
            </button>
        </form>

        <p class="text-center mt-6 text-slate-400 text-sm">
            Don't have an account? <a href="register.php" class="text-sky-400 font-medium hover:text-sky-300">Sign Up</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
