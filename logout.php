<?php
require_once __DIR__ . '/../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username=? OR email=? LIMIT 1');
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        header('Location: index.php'); exit;
    }
    $error = 'Invalid admin credentials.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>JazzCO Admin Login</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><main class="page-wrap auth-shell"><form class="auth-card glass" method="post"><a class="brand" href="../index.php"><span class="brand-orb"></span>JazzCO Admin</a><h1>Admin Login</h1><?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?><div class="form-grid"><div class="input-group"><label>Username or email</label><input name="username" required></div><div class="input-group"><label>Password</label><input type="password" name="password" required></div><button class="btn primary full">Login</button></div><p class="helper">Default: admin / admin123</p></form></main></body></html>
