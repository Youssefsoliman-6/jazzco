<?php
require_once __DIR__ . '/includes/functions.php';
ensure_albums_user_column($pdo);
$user = current_user($pdo);
$albumId = (int)($_GET['id'] ?? 0);
if ($albumId <= 0) {
    flash('error', 'Album not found.');
    header('Location: albums.php');
    exit;
}

$error = '';
$success = '';

function load_album(PDO $pdo, int $albumId) {
    $stmt = $pdo->prepare("SELECT al.*, ar.name AS artist_name, u.username AS owner_name
        FROM albums al
        LEFT JOIN artists ar ON ar.id = al.artist_id
        LEFT JOIN users u ON u.id = al.user_id
        WHERE al.id = ?
        LIMIT 1");
    $stmt->execute([$albumId]);
    return $stmt->fetch() ?: null;
}

$album = load_album($pdo, $albumId);
if (!$album) {
    flash('error', 'Album not found.');
    header('Location: albums.php');
    exit;
}
$isOwner = $user && (int)($album['user_id'] ?? 0) === (int)$user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        $error = 'Please login first.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        try {
            if (!$isOwner) throw new RuntimeException('Only the album owner can change this album.');
            assert_post_size_not_exceeded();
            $action = $_POST['action'] ?? '';

            if ($action === 'update_album') {
                $title = trim($_POST['title'] ?? '');
                $artistName = trim($_POST['artist_name'] ?? '');
                $releaseYear = trim($_POST['release_year'] ?? '');
                $releaseYear = $releaseYear === '' ? null : (int)$releaseYear;
                if (strlen($title) < 2) throw new RuntimeException('Album title must be at least 2 characters.');
                if (strlen($artistName) < 2) throw new RuntimeException('Artist name must be at least 2 characters.');
                if ($releaseYear !== null && ($releaseYear < 1900 || $releaseYear > 2099)) throw new RuntimeException('Release year must be between 1900 and 2099.');
                $artistId = find_or_create_artist($pdo, $artistName, 'Community artist created by ' . $user['username'] . '.');
                $coverPath = upload_image_file($_FILES['cover_image'] ?? [], 'covers', 5 * 1024 * 1024) ?: ($album['cover_path'] ?: DEFAULT_COVER);
                $stmt = $pdo->prepare('UPDATE albums SET title=?, artist_id=?, cover_path=?, release_year=? WHERE id=? AND user_id=?');
                $stmt->execute([$title, $artistId, $coverPath, $releaseYear, $albumId, $user['id']]);
                flash('success', 'Album updated.');
                header('Location: album.php?id=' . $albumId); exit;
            }

            if ($action === 'delete_album') {
                $stmt = $pdo->prepare('DELETE FROM albums WHERE id=? AND user_id=?');
                $stmt->execute([$albumId, $user['id']]);
                flash('success', 'Album deleted. Songs were kept as singles/no album.');
                header('Location: albums.php'); exit;
            }

            if ($action === 'add_song') {
                $songId = (int)($_POST['song_id'] ?? 0);
                if ($songId <= 0) throw new RuntimeException('Choose a valid song.');
                $stmt = $pdo->prepare('UPDATE songs SET album_id=? WHERE id=?');
                $stmt->execute([$albumId, $songId]);
                flash('success', 'Song added to album.');
                header('Location: album.php?id=' . $albumId); exit;
            }

            if ($action === 'remove_song') {
                $songId = (int)($_POST['song_id'] ?? 0);
                if ($songId <= 0) throw new RuntimeException('Choose a valid song.');
                $stmt = $pdo->prepare('UPDATE songs SET album_id=NULL WHERE id=? AND album_id=?');
                $stmt->execute([$songId, $albumId]);
                flash('success', 'Song removed from album.');
                header('Location: album.php?id=' . $albumId); exit;
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$album = load_album($pdo, $albumId);
$isOwner = $user && (int)($album['user_id'] ?? 0) === (int)$user['id'];
$songStmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name, g.name AS genre_name
    FROM songs s
    LEFT JOIN artists ar ON ar.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.album_id = ?
    ORDER BY s.created_at DESC");
$songStmt->execute([$albumId]);
$albumSongs = $songStmt->fetchAll();
$songIds = implode(',', array_map(fn($s) => (int)$s['id'], $albumSongs));

$availableSongs = [];
if ($isOwner) {
    $stmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name
        FROM songs s
        LEFT JOIN artists ar ON ar.id = s.artist_id
        WHERE s.album_id IS NULL OR s.album_id <> ?
        ORDER BY s.created_at DESC
        LIMIT 80");
    $stmt->execute([$albumId]);
    $availableSongs = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="album-hero glass">
    <img src="<?= e(asset_url($album['cover_path'] ?: DEFAULT_COVER)) ?>" alt="album cover">
    <div>
        <div class="eyebrow">Album</div>
        <h1><?= e($album['title']) ?></h1>
        <div class="album-meta-row">
            <span><?= e($album['artist_name'] ?? 'Various Artists') ?></span>
            <span>•</span>
            <span><?= e($album['release_year'] ?: 'No year') ?></span>
            <span>•</span>
            <span><?= count($albumSongs) ?> songs</span>
            <span class="badge"><?= $album['owner_name'] ? 'By ' . e($album['owner_name']) : 'Official' ?></span>
        </div>
        <div class="hero-actions">
            <?php if ($songIds ?? ''): ?><button class="btn primary" data-play-playlist="<?= e($songIds) ?>">Play album</button><button class="btn" data-add-playlist-queue="<?= e($songIds) ?>">Add all to queue</button><?php else: ?><a class="btn primary" href="player.php">Open Player</a><?php endif; ?>
            <a class="btn" href="albums.php">All Albums</a>
            <button class="btn" data-theme-toggle>Light mode</button>
        </div>
    </div>
</section>

<?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<section class="section">
    <div class="section-head"><div><h2>Songs in this album</h2><p>Open the player to listen to tracks from JazzCO.</p></div></div>
    <?php if (!$albumSongs): ?>
        <div class="empty-state">This album has no songs yet.</div>
    <?php else: ?>
        <div class="queue-list">
            <?php foreach ($albumSongs as $song): ?>
                <div class="song-row">
                    <img src="<?= e(asset_url($song['cover_path'] ?: $album['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover">
                    <div>
                        <h4><?= e($song['title']) ?></h4>
                        <p><?= e($song['artist_name'] ?? $album['artist_name'] ?? 'Unknown Artist') ?> • <?= e($song['genre_name'] ?? 'Genre') ?> • <?= format_time($song['duration_seconds']) ?></p>
                    </div>
                    <div class="table-actions">
                        <button class="btn small primary" data-play-song="<?= (int)$song['id'] ?>">Play</button><button class="btn small" data-add-queue="<?= (int)$song['id'] ?>">Queue</button><button class="btn small" data-playlist-song="<?= (int)$song['id'] ?>">Playlist</button>
                        <?php if ($isOwner): ?>
                            <form method="post" onsubmit="return confirm('Remove this song from the album?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="remove_song">
                                <input type="hidden" name="song_id" value="<?= (int)$song['id'] ?>">
                                <button class="btn small danger">Remove</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($isOwner): ?>
<section class="section grid two">
    <form class="card" method="post" enctype="multipart/form-data">
        <h2>Edit album</h2>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="update_album">
        <div class="form-grid">
            <div class="input-group"><label>Album title</label><input name="title" value="<?= e($album['title']) ?>" required minlength="2"></div>
            <div class="input-group"><label>Artist name</label><input name="artist_name" value="<?= e($album['artist_name'] ?? '') ?>" required minlength="2"></div>
            <div class="input-group"><label>Release year</label><input type="number" name="release_year" min="1900" max="2099" value="<?= e($album['release_year'] ?? '') ?>"></div>
            <div class="input-group"><label>Replace cover image</label><input type="file" name="cover_image" accept="image/*"></div>
            <button class="btn primary">Save changes</button>
        </div>
    </form>

    <div class="card">
        <h2>Add existing song</h2>
        <p class="helper">This small project uses songs uploaded by the admin. Choose one to attach it to your album.</p>
        <?php if (!$availableSongs): ?>
            <p class="helper">No available songs to add.</p>
        <?php else: ?>
            <form method="post" class="form-grid">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="add_song">
                <div class="input-group"><label>Song</label><select name="song_id" required><?php foreach ($availableSongs as $song): ?><option value="<?= (int)$song['id'] ?>"><?= e($song['title']) ?> — <?= e($song['artist_name'] ?? 'Unknown Artist') ?></option><?php endforeach; ?></select></div>
                <button class="btn">Add song to album</button>
            </form>
        <?php endif; ?>
        <hr style="border:0;border-top:1px solid var(--border);margin:1.4rem 0;">
        <form method="post" onsubmit="return confirm('Delete this album? Songs will stay but become singles/no album.');">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_album">
            <button class="btn danger">Delete album</button>
        </form>
    </div>
</section>
<?php elseif ($user): ?>
<section class="section"><div class="card"><p class="helper">Only the album owner can edit this album.</p></div></section>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
