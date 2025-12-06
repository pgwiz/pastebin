<?php
include 'db.php';

$username = 'dimono';
// Generate 16 char random password
$password = bin2hex(random_bytes(8)); 
// Or use specific complex one if needed, but requirements said "generate"

$hash = password_hash($password, PASSWORD_DEFAULT);
$email = 'dimono@example.com';

try {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE users SET password = ?, is_superadmin = 1 WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "User 'dimono' updated. \n";
    } else {
        // Insert new
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_superadmin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$username, $email, $hash]);
        echo "User 'dimono' created. \n";
    }
    
    echo "--------------------------\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
    echo "--------------------------\n";
    echo "SAVE THIS PASSWORD NOW. IT WILL NOT BE SHOWN AGAIN.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
