<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verify paste exists
    $stmt = $pdo->prepare("SELECT id FROM pastes WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        // Increment share count
        $stmt = $pdo->prepare("UPDATE pastes SET shares = shares + 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        // Return current share count
        $stmt = $pdo->prepare("SELECT shares FROM pastes WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'shares' => $result['shares']]);
        exit;
    }
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>