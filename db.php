<?php
$host = 'localhost';
$db   = 'pastebin';
$user = 'root';     // default user for XAMPP/WAMP
$pass = '';         // default password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
