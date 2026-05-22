<?php
require_once __DIR__ . '/includes/functions.php';
$user = current_user($pdo);
$playlistId = (int)($_GET['id'] ?? 0);
if ($playlistId <= 0) { header('Location: playlists.php'); exit; }

function load_playlist(PDO $pdo, int $playlistId) {
    $stmt = $pdo->prepare('SELECT p.*, u.username AS owner_name
        FROM playlists p
        JOIN users u ON u.id = p.user_id
        WHERE p.id = ?
        LIMIT 1');
    $stmt->execute([$playlistId]);
    return $stmt->fetch();
}

$playlist = load_playlist($pdo, $playlistId);
if (!$playlist) { header('Location: playlists.php'); exit; }
$isOwner = $user && (int)$playlist['user_id'] === (int)$user['id'];
if (!$isOwner && (int)$playlist['is_public'] !== 1) { header('Location: playlists.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isOwner) {
    try {
        assert_post_size_not_exceeded();
        if (!verify_csrf($_POST['csrf_token'] ?? '')) throw new RuntimeException('Security token expired. Refresh and try again.');
        $action = $_POST['action'] ?? '';

        if ($action === 'update_playlist') {
            $name = trim($_POST['name'] ?? '');
            if (strlen($name) < 2) throw new RuntimeException('Playlist name must be at least 2 characters.');
            $cover = $playlist['cover_image'];
            if (!empty($_FILES['cover_image']['name'])) {
                $cover = upload_image_file($_FILES['cover_image'], 'covers', 5000000);
            }
            $isPublic = !empty($_POST['is_public']) ? 1 : 0;
            $stmt = $pdo->prepare('UPDATE playlists SET name=?, description=?, cover_image=?, is_public=? WHERE id=? AND user_id=?');
            $stmt->execute([$name, trim($_POST['description'] ?? ''), $cover, $isPublic, $playlistId, $user['id']]);
            flash('success', 'Playlist updated.');
            header('Location: playlist.php?id=' . $playlistId); exit;
        }

        if ($action === 'delete_playlist') {
            $stmt = $pdo->prepare('DELETE FROM playlists WHERE id=? AND user_id=?');
            $stmt->execute([$playlistId, $user['id']]);
            flash('success', 'Playlist deleted.');
            header('Location: playlists.php'); exit;
        }

        if ($action === 'add_song') {
            $songId = (int)($_POST['song_id'] ?? 0);
            if ($songId <= 0) throw new RuntimeException('Choose a valid song.');
            $stmt = $pdo->prepare('INSERT IGNORE INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)');
            $stmt->execute([$playlistId, $songId]);
            flash('success', 'Song added to playlist.');
            header('Location: playlist.php?id=' . $playlistId); exit;
        }

        if ($action === 'remove_song') {
            $songId = (int)($_POST['song_id'] ?? 0);
            if ($songId <= 0) throw new RuntimeException('Choose a valid song.');
            $stmt = $pdo->prepare('DELETE FROM playlist_songs WHERE playlist_id=? AND song_id=?');
            $stmt->execute([$playlistId, $songId]);
            flash('success', 'Song removed from playlist.');
            header('Location: playlist.php?id=' . $playlistId); exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$playlist = load_playlist($pdo, $playlistId);
$isOwner = $user && (int)$playlist['user_id'] === (int)$user['id'];

$songStmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name, al.title AS album_title, g.name AS genre_name
    FROM playlist_songs ps
    JOIN songs s ON s.id = ps.song_id
    LEFT JOIN artists ar ON ar.id = s.artist_id
    LEFT JOIN albums al ON al.id = s.album_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE ps.playlist_id = ?
    ORDER BY ps.added_at ASC");
$songStmt->execute([$playlistId]);
$playlistSongs = $songStmt->fetchAll();
$songIds = implode(',', array_map(fn($s) => (int)$s['id'], $playlistSongs));

$availableSongs = [];
if ($isOwner) {
    $stmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name
        FROM songs s
        LEFT JOIN artists ar ON ar.id = s.artist_id
        WHERE s.id NOT IN (SELECT song_id FROM playlist_songs WHERE playlist_id = ?)
        ORDER BY s.created_at DESC");
    $stmt->execute([$playlistId]);
    $availableSongs = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="album-hero glass">
    <img src="<?= e(asset_url($playlist['cover_image'] ?: DEFAULT_COVER)) ?>" alt="playlist cover">
    <div>
        <div class="eyebrow">Playlist</div>
        <h1><?= e($playlist['name']) ?></h1>
        <div class="album-meta-row">
            <span>By <?= e($playlist['owner_name']) ?></span>
            <span>•</span>
            <span><?= count($playlistSongs) ?> songs</span>
            <span class="badge"><?= (int)$playlist['is_public'] === 1 ? 'Public' : 'Private' ?></span>
        </div>
        <?php if ($playlist['description']): ?><p class="lead small-lead"><?= e($playlist['description']) ?></p><?php endif; ?>
        <div class="hero-actions">
            <?php if ($songIds): ?>
                <button class="btn primary" data-play-playlist="<?= e($songIds) ?>">Play playlist</button>
                <button class="btn" data-add-playlist-queue="<?= e($songIds) ?>">Add all to queue</button>
            <?php endif; ?>
            <a class="btn" href="playlists.php">All Playlists</a>
        </div>
    </div>
</section>

<?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<section class="section">
    <div class="section-head"><div><h2>Songs in this playlist</h2><p>Play, queue, or save tracks to another playlist.</p></div></div>
    <?php if (!$playlistSongs): ?>
        <div class="empty-state">This playlist has no songs yet.</div>
    <?php else: ?>
        <div class="queue-list long-list">
            <?php foreach ($playlistSongs as $song): ?>
                <div class="song-row">
                    <img src="<?= e(asset_url($song['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover">
                    <div>
                        <h4><?= e($song['title']) ?></h4>
                        <p><?= e($song['artist_name'] ?? 'Unknown Artist') ?> • <?= e($song['album_title'] ?? 'Single') ?> • <?= format_time($song['duration_seconds']) ?></p>
                    </div>
                    <div class="table-actions">
                        <button class="btn small primary" data-play-song="<?= (int)$song['id'] ?>">Play</button>
                        <button class="btn small" data-add-queue="<?= (int)$song['id'] ?>">Queue</button>
                        <button class="btn small" data-playlist-song="<?= (int)$song['id'] ?>">Playlist</button>
                        <?php if ($isOwner): ?>
                            <form method="post" onsubmit="return confirm('Remove this song from the playlist?');">
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
        <h2>Edit playlist</h2>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="update_playlist">
        <div class="form-grid">
            <div class="input-group"><label>Playlist name</label><input name="name" value="<?= e($playlist['name']) ?>" required minlength="2"></div>
            <div class="input-group"><label>Description</label><textarea name="description"><?= e($playlist['description'] ?? '') ?></textarea></div>
            <div class="input-group"><label>Replace cover image</label><input type="file" name="cover_image" accept="image/*"></div>
            <label class="check-line"><input type="checkbox" name="is_public" <?= (int)$playlist['is_public'] === 1 ? 'checked' : '' ?>> Public playlist</label>
            <button class="btn primary">Save changes</button>
        </div>
    </form>

    <div class="card">
        <h2>Add existing song</h2>
        <p class="helper">Choose any uploaded song and attach it to this playlist.</p>
        <?php if (!$availableSongs): ?>
            <p class="helper">No available songs to add.</p>
        <?php else: ?>
            <form method="post" class="form-grid">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="add_song">
                <div class="input-group"><label>Song</label><select name="song_id" required><?php foreach ($availableSongs as $song): ?><option value="<?= (int)$song['id'] ?>"><?= e($song['title']) ?> — <?= e($song['artist_name'] ?? 'Unknown Artist') ?></option><?php endforeach; ?></select></div>
                <button class="btn">Add song to playlist</button>
            </form>
        <?php endif; ?>
        <hr style="border:0;border-top:1px solid var(--border);margin:1.4rem 0;">
        <form method="post" onsubmit="return confirm('Delete this playlist?');">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_playlist">
            <button class="btn danger">Delete playlist</button>
        </form>
    </div>
</section>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
