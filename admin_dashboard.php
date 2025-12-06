<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
if (!$user || empty($user['is_superadmin'])) {
    header("Location: index.php"); exit;
}

// Admin Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if($act === 'toggle_featured') {
        $stmt = $pdo->prepare("UPDATE pastes SET is_featured=NOT is_featured WHERE id=?");
        $stmt->execute([$_POST['paste_id']]);
    } elseif($act === 'delete_paste') {
        $stmt = $pdo->prepare("DELETE FROM pastes WHERE id=?");
        $stmt->execute([$_POST['paste_id']]);
    } elseif($act === 'toggle_admin') {
        $stmt = $pdo->prepare("UPDATE users SET is_superadmin=NOT is_superadmin WHERE id=?");
        $stmt->execute([$_POST['user_id']]);
    } elseif($act === 'delete_user') {
        if($_POST['user_id'] != $user['id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$_POST['user_id']]);
        }
    } elseif($act === 'dismiss_report') {
        $stmt = $pdo->prepare("DELETE FROM reports WHERE id=?");
        $stmt->execute([$_POST['report_id']]);
    } elseif($act === 'delete_reported_paste') {
        // Delete paste and cascade will kill report
        $stmt = $pdo->prepare("DELETE FROM pastes WHERE id=?");
        $stmt->execute([$_POST['paste_id']]);
    }
    header("Location: admin_dashboard.php"); exit;
}

// Stats
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'pastes' => $pdo->query("SELECT COUNT(*) FROM pastes")->fetchColumn(),
    'views' => $pdo->query("SELECT SUM(views) FROM pastes")->fetchColumn() ?: 0,
    'featured' => $pdo->query("SELECT COUNT(*) FROM pastes WHERE is_featured=1")->fetchColumn(),
    'reports' => $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn()
];

// Data
$pastes = $pdo->query("SELECT p.*, u.username as registered_username FROM pastes p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$reports = $pdo->query("SELECT r.id as report_id, r.reason, r.created_at as report_date, p.id as paste_id, p.title, p.content, u.username as reporter FROM reports r JOIN pastes p ON r.paste_id = p.id LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Admin Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="glass-panel p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm font-medium">Total Users</p>
                <p class="text-2xl font-bold text-white"><?= $stats['users'] ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-xl"><i class="fas fa-users"></i></div>
        </div>
        <div class="glass-panel p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm font-medium">Total Pastes</p>
                <p class="text-2xl font-bold text-white"><?= $stats['pastes'] ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 text-xl"><i class="fas fa-code"></i></div>
        </div>
        <div class="glass-panel p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm font-medium">Total Views</p>
                <p class="text-2xl font-bold text-white"><?= number_format($stats['views']) ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 text-xl"><i class="fas fa-eye"></i></div>
        </div>
        <div class="glass-panel p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm font-medium">Featured</p>
                <p class="text-2xl font-bold text-white"><?= $stats['featured'] ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-400 text-xl"><i class="fas fa-star"></i></div>
        </div>
        <div class="glass-panel p-6 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-sm font-medium">Reports</p>
                <p class="text-2xl font-bold text-white"><?= $stats['reports'] ?></p>
            </div>
            <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center text-red-400 text-xl"><i class="fas fa-flag"></i></div>
        </div>
    </div>

    <!-- Tabs (Client-side simple toggle) -->
    <div x-data="{ activeTab: 'pastes' }">
        <div class="flex border-b border-slate-700 mb-6">
            <button onclick="switchTab('pastes')" id="tab-pastes" class="px-6 py-3 text-white border-b-2 border-blue-500 font-medium transition-colors">Pastes</button>
            <button onclick="switchTab('users')" id="tab-users" class="px-6 py-3 text-slate-400 hover:text-white font-medium transition-colors">Users</button>
            <button onclick="switchTab('reports')" id="tab-reports" class="px-6 py-3 text-slate-400 hover:text-white font-medium transition-colors">Reports <span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?= count($reports) ?></span></button>
        </div>

        <!-- Pastes Tab -->
        <div id="content-pastes">
            <div class="glass-panel rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/50 text-slate-300 text-sm uppercase">
                                <th class="p-4">Title</th>
                                <th class="p-4">Author</th>
                                <th class="p-4">Views</th>
                                <th class="p-4">Featured</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700 text-slate-300 text-sm">
                            <?php foreach($pastes as $p): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="p-4 font-medium text-white truncate max-w-xs">
                                    <a href="view.php?id=<?= $p['id'] ?>" class="hover:text-blue-400"><?= htmlspecialchars($p['title']) ?></a>
                                </td>
                                <td class="p-4"><?= htmlspecialchars($p['user_id']?$p['registered_username']:$p['username']) ?></td>
                                <td class="p-4"><?= $p['views'] ?></td>
                                <td class="p-4">
                                    <?php if($p['is_featured']): ?>
                                        <span class="px-2 py-1 rounded bg-green-500/20 text-green-400 text-xs">Featured</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 rounded bg-slate-600/30 text-slate-400 text-xs">Standard</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="toggle_featured">
                                            <input type="hidden" name="paste_id" value="<?= $p['id'] ?>">
                                            <button class="text-yellow-400 hover:text-yellow-300" title="Toggle Feature"><i class="fas fa-star"></i></button>
                                        </form>
                                        <form method="post" class="inline" onsubmit="return confirm('Delete?')">
                                            <input type="hidden" name="action" value="delete_paste">
                                            <input type="hidden" name="paste_id" value="<?= $p['id'] ?>">
                                            <button class="text-red-400 hover:text-red-300" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div id="content-users" class="hidden">
            <div class="glass-panel rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                     <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/50 text-slate-300 text-sm uppercase">
                                <th class="p-4">Username</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Role</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700 text-slate-300 text-sm">
                            <?php foreach($users as $u): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="p-4 font-medium text-white">
                                    <?= htmlspecialchars($u['username']) ?>
                                    <?php if($u['id']===$user['id']): ?><span class="ml-2 px-2 py-0.5 bg-blue-500/20 text-blue-400 text-xs rounded">You</span><?php endif; ?>
                                </td>
                                <td class="p-4"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="p-4">
                                     <?php if($u['is_superadmin']): ?>
                                        <span class="px-2 py-1 rounded bg-purple-500/20 text-purple-400 text-xs">Admin</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 rounded bg-slate-600/30 text-slate-400 text-xs">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <?php if($u['id'] !== $user['id']): ?>
                                    <div class="flex gap-2">
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="toggle_admin">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button class="text-purple-400 hover:text-purple-300" title="Toggle Admin"><i class="fas fa-user-shield"></i></button>
                                        </form>
                                        <form method="post" class="inline" onsubmit="return confirm('Delete user?')">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button class="text-red-400 hover:text-red-300" title="Delete User"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="content-reports" class="hidden">
            <div class="glass-panel rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                     <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/50 text-slate-300 text-sm uppercase">
                                <th class="p-4">Paste</th>
                                <th class="p-4">Reported By</th>
                                <th class="p-4">Reason</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700 text-slate-300 text-sm">
                            <?php if(empty($reports)): ?>
                            <tr><td colspan="5" class="p-4 text-center text-slate-400">No reports found.</td></tr>
                            <?php else: ?>
                                <?php foreach($reports as $r): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="p-4 font-medium text-white truncate max-w-xs">
                                        <a href="view.php?id=<?= $r['paste_id'] ?>" class="text-blue-400 hover:underline" target="_blank"><?= htmlspecialchars($r['title']) ?></a>
                                    </td>
                                    <td class="p-4"><?= htmlspecialchars($r['reporter'] ?? 'Guest') ?></td>
                                    <td class="p-4"><span class="text-red-300"><?= htmlspecialchars($r['reason']) ?></span></td>
                                    <td class="p-4"><?= date('M j, Y', strtotime($r['report_date'])) ?></td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <form method="post" class="inline">
                                                <input type="hidden" name="action" value="dismiss_report">
                                                <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                                                <button class="px-3 py-1 bg-slate-600 hover:bg-slate-500 text-white rounded text-xs">Dismiss</button>
                                            </form>
                                            <form method="post" class="inline" onsubmit="return confirm('Delete paste and report?')">
                                                <input type="hidden" name="action" value="delete_reported_paste">
                                                <input type="hidden" name="paste_id" value="<?= $r['paste_id'] ?>">
                                                <button class="px-3 py-1 bg-red-600 hover:bg-red-500 text-white rounded text-xs">Delete Paste</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const tabs = ['pastes', 'users', 'reports'];
    tabs.forEach(t => {
        document.getElementById('content-' + t).classList.add('hidden');
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('border-b-2', 'border-blue-500', 'text-white');
        btn.classList.add('text-slate-400');
    });

    document.getElementById('content-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.remove('text-slate-400');
    activeBtn.classList.add('border-b-2', 'border-blue-500', 'text-white');
}
</script>

<?php include 'footer.php'; ?>
