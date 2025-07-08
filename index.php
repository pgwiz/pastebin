<?php
// index.php
include 'db.php';
include 'auth.php';
$user = get_logged_in_user();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Pastes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="src/view.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        /* Card styling for a consistent grid layout */
        .paste-card {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%; /* Ensures cards in the same row have the same height */
        }
        .paste-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }
        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .status-ind {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd;
            margin-top: 5px;
            flex-shrink: 0;
        }
        .text-content {
            margin: 0;
            line-height: 1.6;
            word-break: break-word; /* Prevents long text from overflowing */
        }
        .text-link {
            text-decoration: none;
            font-weight: 500;
            color: #0d6efd;
        }
        .text-link:hover {
            text-decoration: underline;
        }
        .time {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        .button-wrap {
            margin-top: auto; /* Pushes the button to the bottom of the card */
            padding-top: 1rem;
        }
        .primary-cta {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #0d6efd;
            color: white;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .primary-cta:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            background-color: #fff;
            border-radius: .5rem;
        }
    </style>
</head>
<?php include 'navbar.php'; ?>
<body>
    <div class="container py-4">
        <h2 class="page-title">All Pastes</h2>

        <div class="row">
            <?php
            $stmt = $pdo->query("
                SELECT p.id, p.title, p.username, p.created_at, u.username AS registered_username, p.user_id
                FROM pastes p
                LEFT JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
            ");

            if ($stmt->rowCount() === 0): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <h3>No pastes yet</h3>
                        <p>Be the first to create a paste!</p>
                        <a href="manage_paste.php?action=create" class="primary-cta mt-2">Create New Paste</a>
                    </div>
                </div>
            <?php else:
                while ($row = $stmt->fetch()):
                    $pasteId    = intval($row['id']);
                    $pasteTitle = htmlspecialchars($row['title']);
                    $pasteUser  = $row['user_id'] ? htmlspecialchars($row['registered_username']) : htmlspecialchars($row['username'] ?? 'Anonymous');
                    $createdAt  = $row['created_at'];
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="paste-card">
                        <div>
                            <div class="card-header">
                                <div class="status-ind"></div>
                                <div class="text-wrap">
                                    <p class="text-content">
                                        <a class="text-link" href="#">
                                            <?= $pasteUser ?>
                                        </a>
                                        posted
                                        <a class="text-link" href="view.php?id=<?= $pasteId ?>">
                                            <?= $pasteTitle ?>
                                        </a>
                                    </p>
                                    <p class="time"><?= date('M j, Y, g:i a', strtotime($createdAt)) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="button-wrap">
                            <a href="view.php?id=<?= $pasteId ?>" class="primary-cta">View Paste</a>
                            <?php if ($user && isset($row['user_id']) && $user['id'] === $row['user_id']): ?>
                                <a href="manage_paste.php?action=edit&id=<?= $pasteId ?>" class="primary-cta" style="background-color: #6c757d; margin-left: 0.5rem;">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            ?>
        </div>
    </div>

    <div class="container mt-4">
        <?php
        $stmt = $pdo->query("SELECT p.*, u.username AS author FROM pastes p LEFT JOIN users u ON p.user_id = u.id WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 6");
        $featuredPastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="section featured-section">
            <h2 class="page-title">Featured Pastes</h2>
            <?php if (!empty($featuredPastes)): ?>
                <div class="row">
                    <?php foreach ($featuredPastes as $paste): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><a href="view.php?id=<?= $paste['id'] ?>"><?= htmlspecialchars($paste['title']) ?></a></h5>
                                    <p class="text-muted">By: <?= htmlspecialchars($paste['author'] ?? 'Anonymous') ?></p>
                                    <small class="text-muted mt-auto"><?= date('M j, Y', strtotime($paste['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No featured pastes yet.</p>
            <?php endif; ?>
        </div>
    </div>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
