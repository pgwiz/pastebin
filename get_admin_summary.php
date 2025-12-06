<?php
include 'db.php';
include 'auth.php';

header('Content-Type: application/json');

$user = get_logged_in_user();
if (!$user || empty($user['is_superadmin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

$id = intval($_GET['id']);

try {
    // Basic Info
    $stmt = $pdo->prepare("SELECT title, views, is_featured, created_at FROM pastes WHERE id = ?");
    $stmt->execute([$id]);
    $paste = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paste) {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        exit;
    }

    // Reports
    $stmt = $pdo->prepare("SELECT reason, created_at FROM reports WHERE paste_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$id]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $reportCount = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE paste_id = ?");
    $reportCount->execute([$id]);
    $totalReports = $reportCount->fetchColumn();

    // Shares
    $stmt = $pdo->prepare("SELECT u.username FROM users u JOIN paste_permissions pp ON u.id = pp.user_id WHERE pp.paste_id = ?");
    $stmt->execute([$id]);
    $shares = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $totalShares = count($shares);

    echo json_encode([
        'title' => $paste['title'],
        'views' => $paste['views'],
        'is_featured' => (bool)$paste['is_featured'],
        'created_at' => $paste['created_at'],
        'reports' => [
            'count' => $totalReports,
            'recent' => $reports
        ],
        'shares' => [
            'count' => $totalShares,
            'users' => array_slice($shares, 0, 5) // Limit names
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
