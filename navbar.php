<?php
include_once 'db.php';
include_once 'auth.php';
$user = get_logged_in_user();
?>
<nav class="glass-nav fixed w-full z-50 top-0 start-0 border-b border-gray-700">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <!-- Brand -->
    <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse group">
        <div class="p-2 bg-blue-500/10 rounded-lg group-hover:bg-blue-500/20 transition-all duration-300">
            <i class="fas fa-code text-2xl text-blue-400"></i>
        </div>
        <span class="self-center text-2xl font-bold whitespace-nowrap text-white tracking-tight">MyPastebin</span>
    </a>

    <!-- Mobile Menu Button -->
    <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
    </button>

    <!-- Navbar Content -->
    <div class="hidden w-full md:block md:w-auto" id="navbar-default">
      <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-700 rounded-lg bg-gray-800/50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-transparent">
        
        <li>
          <a href="index.php" class="block py-2 px-3 text-white rounded hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-blue-400 md:p-0 transition-colors">
            <i class="fas fa-home me-2"></i>Home
          </a>
        </li>
        <li>
          <a href="manage_paste.php?action=create" class="block py-2 px-3 text-white rounded hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-blue-400 md:p-0 transition-colors">
            <i class="fas fa-plus-circle me-2"></i>New Paste
          </a>
        </li>
        <li>
          <a href="recent.php" class="block py-2 px-3 text-white rounded hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-blue-400 md:p-0 transition-colors">
             <i class="fas fa-history me-2"></i>Recent
          </a>
        </li>
        <li>
          <a href="featured.php" class="block py-2 px-3 text-white rounded hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-blue-400 md:p-0 transition-colors">
            <i class="fas fa-star me-2"></i>Featured
          </a>
        </li>
        
        <?php if ($user && !empty($user['is_superadmin'])): ?>
        <li>
            <a href="admin_dashboard.php" class="block py-2 px-3 text-red-400 rounded hover:bg-gray-700 md:hover:bg-transparent md:border-0 md:hover:text-red-300 md:p-0 transition-colors">
                <i class="fas fa-cog me-2"></i>Admin
            </a>
        </li>
        <?php endif; ?>

        <!-- User Section (Mobile: stacked in list, Desktop: separate implementation could be better but keeping inline for simplicity) -->
        <li class="md:hidden border-t border-gray-700 my-2 pt-2"></li>
        
        <?php if ($user): ?>
            <li class="md:hidden">
                <div class="flex items-center space-x-3 px-3 py-2">
                    <img class="w-8 h-8 rounded-full border border-blue-400" src="<?= $user['avatar_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($user['username']) ?>" alt="User avatar">
                    <span class="text-white"><?= htmlspecialchars($user['username']) ?></span>
                </div>
            </li>
            <li class="md:hidden">
                <a href="logout.php" class="block py-2 px-3 text-gray-400 hover:text-white">Sign out</a>
            </li>
        <?php else: ?>
            <li class="md:hidden">
                <a href="login.php" class="block py-2 px-3 text-white">Login</a>
            </li>
            <li class="md:hidden">
                <a href="register.php" class="block py-2 px-3 text-white">Register</a>
            </li>
        <?php endif; ?>

      </ul>
    </div>

    <!-- Desktop User Actions (hidden on mobile) -->
    <div class="hidden md:flex md:items-center md:space-x-4">
        <?php if ($user): ?>
            <span class="text-gray-300 text-sm">Hello, <span class="text-blue-400 font-semibold"><?= htmlspecialchars($user['username']) ?></span></span>
            <a href="profile.php" class="relative group">
                <img class="w-10 h-10 rounded-full border-2 border-transparent group-hover:border-blue-400 transition-all cursor-pointer object-cover" src="<?= $user['avatar_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($user['username']) ?>" alt="User avatar">
            </a>
            <a href="logout.php" class="text-gray-400 hover:text-white transition-colors" title="Logout">
                <i class="fas fa-sign-out-alt text-lg"></i>
            </a>
        <?php else: ?>
            <a href="login.php" class="text-gray-300 hover:text-white font-medium transition-colors">Login</a>
            <a href="register.php" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all shadow-lg shadow-blue-500/30">Register</a>
        <?php endif; ?>
    </div>
  </div>
</nav>
<!-- Spacer for fixed navbar -->
<!-- Spacer for fixed navbar -->
<div class="h-32"></div>

<script>
    // Simple toggle for mobile menu
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.querySelector('[data-collapse-toggle="navbar-default"]');
        const nav = document.getElementById('navbar-default');
        
        if(toggle && nav) {
            toggle.addEventListener('click', () => {
                nav.classList.toggle('hidden');
            });
        }
    });
</script>