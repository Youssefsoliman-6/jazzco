<?php
require_once __DIR__ . '/includes/functions.php';
$user = require_login($pdo);
ensure_albums_user_column($pdo);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        try {
            if (isset($_POST['update_profile'])) {
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                if (strlen($username) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Invalid username or email.');
                $avatar = upload_image_file($_FILES['profile_picture'] ?? [], 'profiles', 2 * 1024 * 1024);
                if ($avatar) {
                    $pdo->prepare('UPDATE users SET username=?, email=?, profile_picture=? WHERE id=?')->execute([$username, $email, $avatar, $user['id']]);
                } else {
                    $pdo->prepare('UPDATE users SET username=?, email=? WHERE id=?')->execute([$username, $email, $user['id']]);
                }
                flash('success', 'Profile updated.');
                header('Location: profile.php'); exit;
            }
            if (isset($_POST['change_password'])) {
                $current = $_POST['current_password'] ?? '';
                $new = $_POST['new_password'] ?? '';
                if (!password_verify($current, $user['password_hash'])) throw new RuntimeException('Current password is wrong.');
                if (strlen($new) < 8) throw new RuntimeException('New password must be at least 8 characters.');
                $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
                flash('success', 'Password changed.');
                header('Location: profile.php'); exit;
            }
        } catch (Throwable $ex) { $error = $ex->getMessage(); }
    }
}
$user = current_user($pdo);
require_once __DIR__ . '/includes/header.php';
$favStmt = $pdo->prepare("SELECT s.*, ar.name artist_name FROM favorites f JOIN songs s ON s.id=f.song_id LEFT JOIN artists ar ON ar.id=s.artist_id WHERE f.user_id=? ORDER BY f.created_at DESC LIMIT 8");
$favStmt->execute([$user['id']]);
$recentStmt = $pdo->prepare("SELECT s.*, ar.name artist_name, MAX(r.played_at) last_played FROM recently_played r JOIN songs s ON s.id=r.song_id LEFT JOIN artists ar ON ar.id=s.artist_id WHERE r.user_id=? GROUP BY s.id ORDER BY last_played DESC LIMIT 8");
$recentStmt->execute([$user['id']]);
$stats = [
    'playlists' => $pdo->prepare('SELECT COUNT(*) FROM playlists WHERE user_id=?'),
    'favorites' => $pdo->prepare('SELECT COUNT(*) FROM favorites WHERE user_id=?'),
    'recent' => $pdo->prepare('SELECT COUNT(*) FROM recently_played WHERE user_id=?'),
    'albums' => $pdo->prepare('SELECT COUNT(*) FROM albums WHERE user_id=?')
];
foreach ($stats as $s) $s->execute([$user['id']]);
?>
<section class="profile-head glass">
    <img src="<?= e(asset_url($user['profile_picture'] ?: DEFAULT_AVATAR)) ?>" alt="avatar">
    <div>
        <div class="eyebrow">Member profile</div>
        <h1><?= e($user['username']) ?></h1>
        <p class="helper"><?= e($user['email']) ?></p>
        <div class="hero-actions"><a class="btn primary" href="player.php">Open Player</a><a class="btn" href="playlists.php">Manage Playlists</a><a class="btn" href="albums.php">Create Album</a></div>
    </div>
</section>
<?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
<div class="stats-grid">
    <div class="stat"><span>Playlists</span><strong><?= (int)$stats['playlists']->fetchColumn() ?></strong></div>
    <div class="stat"><span>Favorites</span><strong><?= (int)$stats['favorites']->fetchColumn() ?></strong></div>
    <div class="stat"><span>Recently played</span><strong><?= (int)$stats['recent']->fetchColumn() ?></strong></div>
    <div class="stat"><span>Albums</span><strong><?= (int)$stats['albums']->fetchColumn() ?></strong></div>
</div>
<section class="grid two section">
    <form class="card" method="post" enctype="multipart/form-data">
        <h2>Account settings</h2>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
            <div class="input-group"><label>Username</label><input name="username" value="<?= e($user['username']) ?>" required></div>
            <div class="input-group"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required></div>
            <div class="input-group"><label>Profile picture</label><input type="file" name="profile_picture" accept="image/*"></div>
            <button class="btn primary" name="update_profile">Save profile</button>
        </div>
    </form>
    <form class="card" method="post">
        <h2>Change password</h2>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
            <div class="input-group"><label>Current password</label><input type="password" name="current_password" required></div>
            <div class="input-group"><label>New password</label><input type="password" name="new_password" minlength="8" required></div>
            <button class="btn" name="change_password">Update password</button>
        </div>
    </form>
</section>

<section class="section">
    <div class="section-head"><div><h2>Your albums</h2><p>Albums you created.</p></div><a class="btn small" href="albums.php">Create album</a></div>
    <div class="grid cards">
        <?php
        $albumStmt = $pdo->prepare("SELECT al.*, ar.name artist_name, COUNT(s.id) song_count FROM albums al LEFT JOIN artists ar ON ar.id=al.artist_id LEFT JOIN songs s ON s.album_id=al.id WHERE al.user_id=? GROUP BY al.id ORDER BY al.created_at DESC LIMIT 8");
        $albumStmt->execute([$user['id']]);
        $profileAlbums = $albumStmt->fetchAll();
        if (!$profileAlbums) echo '<div class="empty-state">No albums yet. Create one from the Albums page.</div>';
        foreach ($profileAlbums as $album): ?>
            <a class="card link-card" href="album.php?id=<?= (int)$album['id'] ?>"><img class="song-cover" src="<?= e(asset_url($album['cover_path'] ?: DEFAULT_COVER)) ?>"><div class="card-title"><?= e($album['title']) ?></div><div class="card-sub"><?= e($album['artist_name'] ?? 'Various Artists') ?> • <?= (int)$album['song_count'] ?> songs</div></a>
        <?php endforeach; ?>
    </div>
</section>
<section class="section">
    <div class="section-head"><div><h2>Favorite songs</h2><p>Your liked tracks.</p></div></div>
    <div class="grid cards">
        <?php foreach ($favStmt as $song): ?>
            <article class="card"><img class="song-cover" src="<?= e(asset_url($song['cover_path'] ?: DEFAULT_COVER)) ?>"><div class="card-title"><?= e($song['title']) ?></div><div class="card-sub"><?= e($song['artist_name'] ?? 'Unknown') ?></div></article>
        <?php endforeach; ?>
    </div>
</section>
<section class="section">
    <div class="section-head"><div><h2>Recently played</h2><p>Automatically tracked when you play a song.</p></div></div>
    <div class="grid cards">
        <?php foreach ($recentStmt as $song): ?>
            <article class="card"><img class="song-cover" src="<?= e(asset_url($song['cover_path'] ?: DEFAULT_COVER)) ?>"><div class="card-title"><?= e($song['title']) ?></div><div class="card-sub"><?= e($song['artist_name'] ?? 'Unknown') ?></div></article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
