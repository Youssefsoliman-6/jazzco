<?php
require_once __DIR__ . '/../includes/config.php';
unset($_SESSION['admin_id']);
header('Location: login.php');
exit;
?>
