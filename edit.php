<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid paste ID.");
}
$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM pastes WHERE id = ?");
$stmt->execute([$id]);
$paste = $stmt->fetch();

if (!$paste || !$user || $user['id'] !== $paste['user_id']) {
    die("You are not authorized to edit this paste.");
}

$languages = ['plaintext', 'php', 'javascript', 'html', 'css', 'python', 'sql', 'bash', 'json', 'xml'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $syntax = $_POST['syntax'] ?? 'plaintext';

    $stmt = $pdo->prepare("UPDATE pastes SET title = ?, content = ?, syntax_language = ? WHERE id = ?");
    $stmt->execute([$title, $content, $syntax, $id]);

    header("Location: view.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Paste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Paste</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($paste['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Syntax</label>
                <select name="syntax" class="form-select">
                    <?php foreach ($languages as $lang): ?>
                        <option value="<?= $lang ?>" <?= $lang === $paste['syntax_language'] ? 'selected' : '' ?>>
                            <?= ucfirst($lang) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="10" required><?= htmlspecialchars($paste['content']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Paste</button>
        </form>
    </div>
</body>
</html>