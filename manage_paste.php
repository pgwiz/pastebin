<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
$languages = ['plaintext', 'php', 'javascript', 'html', 'css', 'python', 'sql', 'bash', 'json', 'xml'];

// Determine the action: create, edit, or delete
$action = $_GET['action'] ?? 'create';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$paste = null;
$pageTitle = 'Create Paste';
$submitButtonText = 'Create Paste';

// Authorization and Data Fetching for Edit/Delete
if ($id > 0 && ($action === 'edit' || $action === 'delete')) {
    $stmt = $pdo->prepare("SELECT * FROM pastes WHERE id = ?");
    $stmt->execute([$id]);
    $paste = $stmt->fetch();

    if (!$paste) {
        die("Paste not found.");
    }

    // Authorization Check: User must be the owner to edit or delete
    if (!$user || $user['id'] !== $paste['user_id']) {
        die("You are not authorized to perform this action.");
    }
}

// Handle Form Submissions and Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'create':
            $title = $_POST['title'];
            $content = $_POST['content'];
            $syntax = $_POST['syntax'] ?? 'plaintext';

            if ($user) {
                $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $syntax, $user['id']]);
            } else {
                $username = trim($_POST['username'] ?? '');
                if (empty($username)) {
                    die("Username is required for anonymous pastes.");
                }
                $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, username) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $syntax, $username]);
            }
            header("Location: index.php");
            exit;

        case 'edit':
            if (!$paste) die("Invalid paste ID."); // Re-check paste existence
            $title = $_POST['title'];
            $content = $_POST['content'];
            $syntax = $_POST['syntax'] ?? 'plaintext';

            $stmt = $pdo->prepare("UPDATE pastes SET title = ?, content = ?, syntax_language = ? WHERE id = ?");
            $stmt->execute([$title, $content, $syntax, $id]);

            header("Location: view.php?id=$id");
            exit;

        case 'delete':
             if (!$paste) die("Invalid paste ID."); // Re-check paste existence
            $stmt = $pdo->prepare("DELETE FROM pastes WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: index.php");
            exit;
    }
}

// Prepare view for edit and delete actions
if ($action === 'edit' && $paste) {
    $pageTitle = 'Edit Paste';
    $submitButtonText = 'Update Paste';
} elseif ($action === 'delete' && $paste) {
    // Display a confirmation page for deletion
    $pageTitle = 'Delete Paste';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($pageTitle) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Confirm Deletion</h1>
        <p>Are you sure you want to delete the paste titled "<strong><?= htmlspecialchars($paste['title']) ?></strong>"?</p>
        <p>This action cannot be undone.</p>
        <form method="post" action="manage_paste.php?action=delete&id=<?= $id ?>">
            <a href="history.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-danger">Delete Paste</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit; // Stop executing for delete GET request
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h1>
    <form method="post" action="manage_paste.php?action=<?= $action ?><?= $id > 0 ? '&id='.$id : '' ?>">
        <?php if (!$user && $action === 'create'): ?>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
        <?php elseif ($user): ?>
            <div class="mb-3">
                <label class="form-label">Logged in as</label>
                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" disabled>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($paste['title'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Syntax</label>
            <select name="syntax" class="form-select">
                <?php foreach ($languages as $lang): ?>
                    <option value="<?= $lang ?>" <?= isset($paste['syntax_language']) && $lang === $paste['syntax_language'] ? 'selected' : '' ?>>
                        <?= ucfirst($lang) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="10" required><?= htmlspecialchars($paste['content'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitButtonText) ?></button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
