<?php
include 'db.php';
include 'auth.php';

$languages = ['plaintext', 'php', 'javascript', 'html', 'css', 'python', 'sql', 'bash', 'json', 'xml'];
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $syntax = $_POST['syntax'] ?? 'plaintext';
    
    if ($user) {
        // Registered user
        $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $syntax, $user['id']]);
    } else {
        // Anonymous user
        $username = trim($_POST['username']);
        if (empty($username)) {
            die("Username is required for anonymous pastes");
        }
        $stmt = $pdo->prepare("INSERT INTO pastes (title, content, syntax_language, username) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $syntax, $username]);
    }
    
    header("Location: index.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Paste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Create New Paste</h1>
        <form method="post">
            <?php if (!$user): ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Logged in as</label>
                    <input type="text" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" disabled>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Syntax</label>
                <select name="syntax" class="form-select">
                    <?php foreach ($languages as $lang): ?>
                        <option value="<?= $lang ?>"><?= ucfirst($lang) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="10" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
