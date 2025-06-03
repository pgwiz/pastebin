<?php
session_start();

function get_logged_in_user() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    
    if (isset($_COOKIE['remember_me'])) {
        list($user_id, $username) = explode(':', $_COOKIE['remember_me']);
        $pdo = include 'db.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user && $user['username'] === $username) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            return $user;
        }
    }
    return null;
}
?>


