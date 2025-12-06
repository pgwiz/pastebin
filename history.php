<?php
include 'db.php';
include 'auth.php';
$user = get_logged_in_user();
if (!$user) { header("Location: login.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM pastes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$pastes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-white mb-6">Your History</h2>

    <?php if(empty($pastes)): ?>
        <div class="glass-panel p-8 text-center rounded-xl">
             <p class="text-slate-400">You haven't created any pastes yet.</p>
             <a href="manage_paste.php?action=create" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg">Create One</a>
        </div>
    <?php else: ?>
        <div class="glass-panel rounded-xl overflow-hidden">
             <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/50 text-slate-300 text-sm uppercase">
                            <th class="p-4">Title</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Views</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-slate-300 text-sm">
                        <?php foreach($pastes as $p): ?>
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 font-medium text-white">
                                <a href="view.php?id=<?= $p['id'] ?>" class="hover:text-blue-400"><?= htmlspecialchars($p['title']) ?></a>
                            </td>
                            <td class="p-4"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                            <td class="p-4"><?= $p['views'] ?></td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <a href="manage_paste.php?action=edit&id=<?= $p['id'] ?>" class="px-3 py-1 bg-blue-600/20 text-blue-400 rounded hover:bg-blue-600/30">Edit</a>
                                    <form method="post" action="manage_paste.php?action=delete&id=<?= $p['id'] ?>" class="inline" onsubmit="return confirm('Delete?')">
                                        <input type="hidden" name="form_action" value="delete">
                                        <button class="px-3 py-1 bg-red-600/20 text-red-400 rounded hover:bg-red-600/30">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
