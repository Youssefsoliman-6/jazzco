<?php
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user_id'])) {
    $pdo->prepare('UPDATE users SET remember_token_hash = NULL WHERE id = ?')->execute([$_SESSION['user_id']]);
}
setcookie('jazzco_remember', '', time() - 3600, BASE_URL, '', false, true);
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: index.php');
exit;
?>
