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
    die("You are not authorized to delete this paste.");
}

$stmt = $pdo->prepare("DELETE FROM pastes WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>