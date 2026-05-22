<?php
require_once __DIR__ . '/includes/functions.php';
ensure_albums_user_column($pdo);
$user = current_user($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        $error = 'Please login before creating an album.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        try {
            assert_post_size_not_exceeded();
            $title = trim($_POST['title'] ?? '');
            $artistName = trim($_POST['artist_name'] ?? '');
            $releaseYear = trim($_POST['release_year'] ?? '');
            $releaseYear = $releaseYear === '' ? null : (int)$releaseYear;

            if (strlen($title) < 2) throw new RuntimeException('Album title must be at least 2 characters.');
            if (strlen($artistName) < 2) throw new RuntimeException('Artist name must be at least 2 characters.');
            if ($releaseYear !== null && ($releaseYear < 1900 || $releaseYear > 2099)) throw new RuntimeException('Release year must be between 1900 and 2099.');

            $artistId = find_or_create_artist($pdo, $artistName, 'Community artist created by ' . $user['username'] . '.');
            $coverPath = upload_image_file($_FILES['cover_image'] ?? [], 'covers', 5 * 1024 * 1024) ?: DEFAULT_COVER;
            $stmt = $pdo->prepare('INSERT INTO albums (user_id, title, artist_id, cover_path, release_year) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $title, $artistId, $coverPath, $releaseYear]);
            flash('success', 'Album created successfully.');
            header('Location: album.php?id=' . (int)$pdo->lastInsertId());
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
$albumsStmt = $pdo->query("SELECT al.*, ar.name AS artist_name, u.username AS owner_name, COUNT(s.id) AS song_count
    FROM albums al
    LEFT JOIN artists ar ON ar.id = al.artist_id
    LEFT JOIN users u ON u.id = al.user_id
    LEFT JOIN songs s ON s.album_id = al.id
    GROUP BY al.id
    ORDER BY al.created_at DESC");
$albums = $albumsStmt->fetchAll();
?>
<section class="section">
    <div class="section-head">
        <div>
            <div class="eyebrow">Albums</div>
            <h1>Open, create, and manage albums.</h1>
            <p>Users can now create their own albums, upload cover art, and open each album page.</p>
        </div>
        <a class="btn primary" href="player.php">Open Player</a>
    </div>
    <?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
</section>

<section class="section grid two">
    <?php if ($user): ?>
        <form class="card" method="post" enctype="multipart/form-data">
            <h2>Create album</h2>
            <p class="helper">Add a title, artist name, release year, and optional cover image.</p>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-grid">
                <div class="input-group"><label>Album title</label><input name="title" required minlength="2" placeholder="Example: Neon Nights"></div>
                <div class="input-group"><label>Artist name</label><input name="artist_name" required minlength="2" placeholder="Example: Essam Waves"></div>
                <div class="input-group"><label>Release year</label><input type="number" name="release_year" min="1900" max="2099" placeholder="2026"></div>
                <div class="input-group"><label>Cover image</label><input type="file" name="cover_image" accept="image/*"></div>
                <button class="btn primary">Create album</button>
            </div>
        </form>
    <?php else: ?>
        <div class="card">
            <h2>Create your own albums</h2>
            <p>Login or register to create albums and upload album cover images.</p>
            <div class="hero-actions"><a class="btn primary" href="login.php">Login</a><a class="btn" href="register.php">Register</a></div>
        </div>
    <?php endif; ?>
    <div class="card">
        <h2>What's next?</h2>
        <p>Your new album will instantly publish to your profile. You’ll have full control to build your tracklist, edit metadata, or safely remove the release whenever you need.</p>
        <div class="mini-feature-list">
            <span class="badge">Instant publish</span>
            <span class="badge">Full ownership</span>
            <span class="badge">Smart layout</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-head"><div><h2>All albums</h2><p>Click any album to open the album page.</p></div></div>
    <?php if (!$albums): ?>
        <div class="empty-state">No albums yet. Create the first one.</div>
    <?php else: ?>
        <div class="grid cards">
            <?php foreach ($albums as $album): ?>
                <a class="card link-card" href="album.php?id=<?= (int)$album['id'] ?>">
                    <img class="song-cover" src="<?= e(asset_url($album['cover_path'] ?: DEFAULT_COVER)) ?>" alt="album cover">
                    <div class="card-title"><?= e($album['title']) ?></div>
                    <div class="card-sub"><?= e($album['artist_name'] ?? 'Various Artists') ?> • <?= (int)$album['song_count'] ?> songs</div>
                    <div class="helper"><?= $album['owner_name'] ? 'Created by ' . e($album['owner_name']) : 'Official JazzCO album' ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
