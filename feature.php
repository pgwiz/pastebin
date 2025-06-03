<?php
include 'db.php';
include 'auth.php';

$user = get_logged_in_user();
if (!$user || !$user['is_admin']) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paste_id = intval($_POST['paste_id']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE pastes SET is_featured = ? WHERE id = ?");
    $stmt->execute([$is_featured, $paste_id]);

    header("Location: view.php?id=$paste_id");
    exit;
}
?>