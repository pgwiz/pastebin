<?php
include 'db.php';
include 'auth.php';
include 'card_view.php';

$user = get_logged_in_user();
include 'header.php';
?>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-white mb-4 md:mb-0">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-blue-600">All Pastes</span>
        </h2>
        <?php if ($user): ?>
        <a href="manage_paste.php?action=create" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/20 transition-all transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-plus"></i> Create Paste
        </a>
        <?php endif; ?>
    </div>

    <?php
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.content, p.username, p.created_at, u.username AS registered_username, p.user_id
        FROM pastes p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");

    if ($stmt->rowCount() === 0): ?>
        <div class="glass-panel text-center p-12 rounded-2xl max-w-lg mx-auto mt-12">
            <div class="w-20 h-20 bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-clipboard text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No pastes found</h3>
            <p class="text-slate-400 mb-6">Be the first to share something with the world.</p>
            <a href="manage_paste.php?action=create" class="inline-block px-6 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors">Create New Paste</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
            $postData = [
                'id'         => $row['id'],
                'category'   => $row['title'], // Title as category
                'content'    => $row['content'],
                'author'     => $row['user_id'] ? htmlspecialchars($row['registered_username']) : htmlspecialchars($row['username'] ?? 'Anonymous'),
                'created_at' => $row['created_at'],
                'user_id'    => $row['user_id']
            ];
            echo renderCard($postData, $user);
        endwhile;
        ?>
        </div>
    <?php endif; ?>
</div>

<?php renderCardScripts(); ?>
<?php include 'footer.php'; ?>
