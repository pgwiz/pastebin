<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
$languages = ['plaintext', 'php', 'javascript', 'html', 'css', 'python', 'sql', 'bash', 'json', 'xml'];

$action = $_GET['action'] ?? 'create';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$paste = null;
$pageTitle = 'Create Paste';
$submitButtonText = 'Create Paste';
$share_message = '';
$shared_users = [];

// --- Authorization Helper Function ---
function can_edit_paste($user, $paste, $pdo) {
    if (!$user || !$paste) return false;
    // Rule 1: User is a superadmin
    if (!empty($user['is_superadmin'])) return true;
    // Rule 2: User is the owner of the paste
    if ($user['id'] === $paste['user_id']) return true;
    // Rule 3: User has been granted 'edit' permissions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM paste_permissions WHERE paste_id = ? AND user_id = ? AND permission_type = 'edit'");
    $stmt->execute([$paste['id'], $user['id']]);
    return $stmt->fetchColumn() > 0;
}

// --- Fetch Paste and Check Initial Authorization ---
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pastes WHERE id = ?");
    $stmt->execute([$id]);
    $paste = $stmt->fetch();

    if (!$paste) die("Paste not found.");

    // Authorization check for any action on an existing paste
    if ($action === 'edit' || $action === 'delete' || $action === 'share') {
        if (!can_edit_paste($user, $paste, $pdo)) {
            die("You are not authorized to perform this action.");
        }
    }
    
    // Fetch users this paste is shared with
    $shareStmt = $pdo->prepare("SELECT u.username FROM users u JOIN paste_permissions pp ON u.id = pp.user_id WHERE pp.paste_id = ?");
    $shareStmt->execute([$id]);
    $shared_users = $shareStmt->fetchAll(PDO::FETCH_COLUMN);
}


// --- Handle Form Submissions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine which form was submitted
    $form_action = $_POST['form_action'] ?? $action;

    switch ($form_action) {
        case 'share':
            $username_to_share = trim($_POST['username_to_share']);
            $user_to_share_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $user_to_share_stmt->execute([$username_to_share]);
            $user_to_share = $user_to_share_stmt->fetch();

            if ($user_to_share) {
                if ($user_to_share['id'] == $paste['user_id']) {
                    $share_message = "<div class='alert alert-warning'>You cannot share a paste with its owner.</div>";
                } else {
                    $insert_perm_stmt = $pdo->prepare("INSERT INTO paste_permissions (paste_id, user_id, permission_type) VALUES (?, ?, 'edit') ON DUPLICATE KEY UPDATE permission_type='edit'");
                    $insert_perm_stmt->execute([$id, $user_to_share['id']]);
                    $share_message = "<div class='alert alert-success'>Successfully shared paste with <strong>" . htmlspecialchars($username_to_share) . "</strong>.</div>";
                    // Refresh shared users list
                     $shareStmt = $pdo->prepare("SELECT u.username FROM users u JOIN paste_permissions pp ON u.id = pp.user_id WHERE pp.paste_id = ?");
                    $shareStmt->execute([$id]);
                    $shared_users = $shareStmt->fetchAll(PDO::FETCH_COLUMN);
                }
            } else {
                $share_message = "<div class='alert alert-danger'>User <strong>" . htmlspecialchars($username_to_share) . "</strong> not found.</div>";
            }
            // Fall through to show the edit page again with the message
            $action = 'edit';
            break;

        case 'create':
            // ... (create logic remains the same)
            header("Location: index.php");
            exit;

        case 'edit':
            // ... (edit logic remains the same)
            header("Location: view.php?id=$id");
            exit;

        case 'delete':
            // ... (delete logic remains the same)
            header("Location: index.php");
            exit;
    }
}

// --- Prepare View ---
if ($action === 'edit' && $paste) {
    $pageTitle = 'Edit & Share Paste';
    $submitButtonText = 'Update Paste';
} elseif ($action === 'delete' && $paste) {
    // ... (delete confirmation page remains the same)
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
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h1>
            <form method="post" action="manage_paste.php?action=<?= $action ?><?= $id > 0 ? '&id='.$id : '' ?>">
                <input type="hidden" name="form_action" value="<?= $action ?>">
                <!-- Paste editing form fields here -->
                 <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($paste['title'] ?? '') ?>" required>
                </div>
                <!-- Other fields... -->
                <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitButtonText) ?></button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

        <?php if ($action === 'edit' && $paste): ?>
        <div class="col-md-4">
            <div class="card bg-light p-3">
                <h3>Share this Paste</h3>
                <p>Grant another user permission to edit this paste.</p>
                <?= $share_message ?>
                <form method="post" action="manage_paste.php?action=edit&id=<?= $id ?>">
                    <input type="hidden" name="form_action" value="share">
                    <div class="mb-3">
                        <label for="username_to_share" class="form-label">Username</label>
                        <input type="text" name="username_to_share" id="username_to_share" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-info w-100">Share</button>
                </form>
                <hr>
                <h5>Shared With:</h5>
                <?php if (empty($shared_users)): ?>
                    <p class="text-muted">Not shared with anyone yet.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($shared_users as $shared_user): ?>
                            <li class="list-group-item"><?= htmlspecialchars($shared_user) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
