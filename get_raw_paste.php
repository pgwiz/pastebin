<?php
// Always start with your database connection and any necessary includes
include 'db.php'; 

// 1. Basic Input Validation
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo "Invalid paste ID.";
    exit;
}

$pasteId = (int)$_GET['id'];

// 2. Fetch the content from the database using a prepared statement
$stmt = $pdo->prepare("SELECT content FROM pastes WHERE id = ?");
$stmt->execute([$pasteId]);
$paste = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paste) {
    http_response_code(404); // Not Found
    echo "Paste not found.";
    exit;
}

// 3. Set Critical Security Headers
// This tells the browser to treat the response as plain text, not HTML.
header('Content-Type: text/plain; charset=utf-8'); 
// This is an extra layer of protection, suggesting the browser download the file.
// For a copy function, it's not strictly necessary but adds security.
// header('Content-Disposition: attachment; filename="paste.txt"'); 

// 4. Output the raw content
echo $paste['content'];

?>