<?php
require_once __DIR__ . '/includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (strlen($username) < 3) $error = 'Username must be at least 3 characters.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Enter a valid email address.';
        elseif (strlen($password) < 8) $error = 'Password must be at least 8 characters.';
        elseif ($password !== $confirm) $error = 'Passwords do not match.';
        else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
                $stmt->execute([$username, $email, $hash]);
                $_SESSION['user_id'] = (int)$pdo->lastInsertId();
                flash('success', 'Account created. Welcome to JazzCO.');
                header('Location: profile.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Email or username already exists.';
            }
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
    <form class="auth-card glass" method="post">
        <h1>Create account</h1>
        <p class="helper">Join JazzCO and build your playlists.</p>
        <?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
            <div class="input-group"><label>Username</label><input name="username" minlength="3" required></div>
            <div class="input-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="input-group"><label>Password</label><input type="password" name="password" minlength="8" required></div>
            <div class="input-group"><label>Confirm Password</label><input type="password" name="confirm_password" minlength="8" required></div>
            <button class="btn primary full">Register</button>
            <p class="helper">Already have an account? <a class="gradient-text" href="login.php">Login</a>.</p>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
