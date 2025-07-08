<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

// Fetch all pastes by the user
$stmt = $pdo->prepare("
    SELECT p.*, u.username AS author 
    FROM pastes p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user['id']]);
$pastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paste History | MyPastebin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="src/history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4">Paste History</h2>
        
        <?php if (empty($pastes)): ?>
            <div class="alert alert-info text-center">
                You haven't created any pastes yet.
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Date</th>
                        <?php if (!empty($user['is_admin'])): ?>
                            <th>Status</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pastes as $paste): ?>
                        <tr>
                            <td><?= htmlspecialchars($paste['title']) ?></td>
                            <td><?= htmlspecialchars($paste['author'] ?: 'Anonymous') ?></td>
                            <td><?= date('M j, Y', strtotime($paste['created_at'])) ?></td>
                            <?php if (!empty($user['is_admin'])): ?>
                                <td>
                                    <?php if ($paste['is_featured']): ?>
                                        <span class="badge bg-success">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Standard</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <a href="view.php?id=<?= $paste['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="manage_paste.php?action=edit&id=<?= $paste['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="manage_paste.php?action=delete&id=<?= $paste['id'] ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php if (!empty($user['is_admin'])): ?>
                                    <form action="feature.php" method="post" class="d-inline">
                                        <input type="hidden" name="paste_id" value="<?= $paste['id'] ?>">
                                        <?php if ($paste['is_featured']): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Unfeature Paste">
                                                <i class="fas fa-star-half-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="is_featured" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Feature Paste">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
