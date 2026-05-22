<?php
require_once __DIR__ . '/includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $userRow = $stmt->fetch();
        if ($userRow && password_verify($password, $userRow['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$userRow['id'];
            if (!empty($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $hash = hash('sha256', $token);
                $pdo->prepare('UPDATE users SET remember_token_hash = ? WHERE id = ?')->execute([$hash, $userRow['id']]);
                setcookie('jazzco_remember', $token, time() + (86400 * 30), BASE_URL, '', false, true);
            }
            flash('success', 'Welcome back to JazzCO.');
            header('Location: player.php');
            exit;
        }
        $error = 'Invalid email or password.';
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
    <form class="auth-card glass" method="post">
        <h1>Login</h1>
        <p class="helper">Enter your JazzCO account.</p>
        <?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
            <div class="input-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="input-group"><label>Password</label><input type="password" name="password" required></div>
            <label style="display:flex;gap:.6rem;align-items:center;"><input style="width:auto" type="checkbox" name="remember"> Remember me</label>
            <button class="btn primary full">Login</button>
            <p class="helper">No account? <a class="gradient-text" href="register.php">Register now</a>.</p>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
