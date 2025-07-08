<?php
include 'db.php';
include 'auth.php';

$stmt = $pdo->query("SELECT p.*, u.username AS author FROM pastes p LEFT JOIN users u ON p.user_id = u.id WHERE p.is_featured = 1 ORDER BY p.created_at DESC");
$featuredPastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Featured Pastes | MyPastebin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="src/featured.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4">Featured Pastes</h2>
        
        <?php if (empty($featuredPastes)): ?>
            <div class="alert alert-info text-center">
                No featured pastes found.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($featuredPastes as $paste): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="view.php?id=<?= $paste['id'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($paste['title']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted">By: <?= htmlspecialchars($paste['author'] ?: 'Anonymous') ?></p>
                                <small><?= date('M j, Y', strtotime($paste['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>