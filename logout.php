<?php
setcookie('remember_me', '', time()-3600, '/');
session_start();
session_destroy();
header("Location: index.php");
exit;
?>