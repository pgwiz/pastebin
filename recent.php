<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

// Fetch recent pastes (last 10)
$stmt = $pdo->prepare("
    SELECT p.*, u.username AS author 
    FROM pastes p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
$stmt->execute([$user['id']]);
$recentPastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <style>
        .recent-list {
    max-height: 400px;
    overflow-y: auto;
}
    </style>
<head>
    <meta charset="UTF-8">
    <title>Recent Pastes | MyPastebin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="src/recent.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4">Recent Pastes</h2>
        
        <?php if (empty($recentPastes)): ?>
            <div class="alert alert-info text-center">
                No recent pastes found.
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($recentPastes as $paste): ?>
                    <a href="view.php?id=<?= $paste['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold"><?= htmlspecialchars($paste['title']) ?></div>
                            <small>By: <?= htmlspecialchars($paste['author'] ?: 'Anonymous') ?></small>
                        </div>
                        <span class="badge bg-primary rounded-pill">
                            <?= date('M j, Y', strtotime($paste['created_at'])) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>