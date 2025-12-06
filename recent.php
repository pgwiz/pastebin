<?php
include 'db.php';
include 'auth.php';
include 'card_view.php'; // Reuse card function
$user = get_logged_in_user();
include 'header.php';
?>

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-white mb-8">
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">Recent Pastes</span>
    </h2>

    <?php
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.content, p.username, p.created_at, u.username AS registered_username, p.user_id
        FROM pastes p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
        LIMIT 20
    ");

    if ($stmt->rowCount() === 0): ?>
         <div class="glass-panel text-center p-12 rounded-2xl max-w-lg mx-auto mt-12">
            <h3 class="text-xl font-bold text-white mb-2">No pastes yet</h3>
            <p class="text-slate-400 mb-6">Be the first to create one!</p>
            <a href="manage_paste.php?action=create" class="inline-block px-6 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors">Create New Paste</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                $postData = [
                    'id'         => $row['id'],
                    'category'   => $row['title'], 
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
