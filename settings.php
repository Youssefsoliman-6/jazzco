<?php
require_once __DIR__ . '/includes_admin.php';

$editPlaylist = null;
$editSongs = [];
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $userId = (int)($_POST['user_id'] ?? 0);
        $isPublic = !empty($_POST['is_public']) ? 1 : 0;
        $songIds = array_map('intval', $_POST['song_ids'] ?? []);

        if (isset($_POST['create_playlist'])) {
            if ($userId <= 0 || $name === '') throw new RuntimeException('User and playlist name are required.');
            $cover = admin_upload_image('cover_image', 'covers') ?: DEFAULT_COVER;
            $stmt = $pdo->prepare('INSERT INTO playlists (user_id, name, description, cover_image, is_public) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $name, $description, $cover, $isPublic]);
            $playlistId = (int)$pdo->lastInsertId();
            $insert = $pdo->prepare('INSERT IGNORE INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)');
            foreach ($songIds as $sid) $insert->execute([$playlistId, $sid]);
            flash('success', 'Playlist created successfully.');
            admin_redirect('playlists.php');
        }

        if (isset($_POST['update_playlist'])) {
            $playlistId = (int)($_POST['playlist_id'] ?? 0);
            if ($playlistId <= 0 || $userId <= 0 || $name === '') throw new RuntimeException('Playlist ID, user, and name are required.');
            $stmt = $pdo->prepare('SELECT cover_image FROM playlists WHERE id=? LIMIT 1');
            $stmt->execute([$playlistId]);
            $current = $stmt->fetch();
            if (!$current) throw new RuntimeException('Playlist not found.');
            $cover = admin_upload_image('cover_image', 'covers') ?: ($current['cover_image'] ?: DEFAULT_COVER);
            $stmt = $pdo->prepare('UPDATE playlists SET user_id=?, name=?, description=?, cover_image=?, is_public=? WHERE id=?');
            $stmt->execute([$userId, $name, $description, $cover, $isPublic, $playlistId]);
            $pdo->prepare('DELETE FROM playlist_songs WHERE playlist_id=?')->execute([$playlistId]);
            $insert = $pdo->prepare('INSERT IGNORE INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)');
            foreach ($songIds as $sid) $insert->execute([$playlistId, $sid]);
            flash('success', 'Playlist updated successfully.');
            admin_redirect('playlists.php');
        }

        if (isset($_POST['delete_playlist'])) {
            $pdo->prepare('DELETE FROM playlists WHERE id=?')->execute([(int)$_POST['playlist_id']]);
            flash('success', 'Playlist deleted.');
            admin_redirect('playlists.php');
        }
    }

    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM playlists WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_GET['edit']]);
        $editPlaylist = $stmt->fetch() ?: null;
        if ($editPlaylist) {
            $stmt = $pdo->prepare('SELECT song_id FROM playlist_songs WHERE playlist_id=?');
            $stmt->execute([(int)$editPlaylist['id']]);
            $editSongs = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_header('Playlists CRUD');
$users = $pdo->query('SELECT id, username, email FROM users ORDER BY username')->fetchAll();
$songs = $pdo->query('SELECT s.id, s.title, ar.name artist_name FROM songs s LEFT JOIN artists ar ON ar.id=s.artist_id ORDER BY s.title')->fetchAll();
$playlists = $pdo->query("SELECT p.*, u.username, (SELECT COUNT(*) FROM playlist_songs ps WHERE ps.playlist_id=p.id) song_count FROM playlists p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC")->fetchAll();
?>
<div class="section-head"><div><h1>Playlists CRUD</h1><p>Create, read, update, and delete playlists. Admin can also change playlist songs.</p></div><?php if ($editPlaylist): ?><a class="btn" href="playlists.php">Cancel edit</a><?php endif; ?></div>
<?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<form class="card" method="post" enctype="multipart/form-data">
    <h2><?= $editPlaylist ? 'Edit playlist' : 'Create playlist' ?></h2>
    <div class="form-grid admin-form-grid">
        <?php if($editPlaylist): ?><input type="hidden" name="playlist_id" value="<?= (int)$editPlaylist['id'] ?>"><?php endif; ?>
        <div class="input-group"><label>Owner user</label><select name="user_id" required><option value="">Choose user</option><?php foreach($users as $u): ?><option value="<?= (int)$u['id'] ?>" <?= (int)($editPlaylist['user_id'] ?? 0)===(int)$u['id']?'selected':'' ?>><?= e($u['username']) ?> — <?= e($u['email']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Playlist name</label><input name="name" value="<?= e($editPlaylist['name'] ?? '') ?>" required></div>
        <div class="input-group"><label>Description</label><textarea name="description" rows="3"><?= e($editPlaylist['description'] ?? '') ?></textarea></div>
        <div class="input-group"><label>Cover image</label><input type="file" name="cover_image" accept="image/*"></div>
        <?php if($editPlaylist): ?><img class="admin-thumb large" src="<?= e(asset_url($editPlaylist['cover_image'] ?: DEFAULT_COVER)) ?>" alt="playlist cover"><?php endif; ?>
        <label class="check-line"><input type="checkbox" name="is_public" <?= !empty($editPlaylist['is_public'])?'checked':'' ?>> Public playlist</label>
    </div>
    <div class="input-group"><label>Playlist songs</label><div class="checkbox-grid">
        <?php foreach($songs as $s): ?><label><input type="checkbox" name="song_ids[]" value="<?= (int)$s['id'] ?>" <?= in_array((int)$s['id'], $editSongs, true)?'checked':'' ?>> <?= e($s['title']) ?> <span><?= e($s['artist_name'] ?? 'Unknown') ?></span></label><?php endforeach; ?>
    </div></div>
    <button class="btn primary" name="<?= $editPlaylist ? 'update_playlist' : 'create_playlist' ?>"><?= $editPlaylist ? 'Save playlist changes' : 'Add playlist' ?></button>
</form>

<section class="section card"><h2>All playlists</h2><div class="table-wrap"><table><thead><tr><th>Cover</th><th>Name</th><th>User</th><th>Privacy</th><th>Songs</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php foreach($playlists as $p): ?>
<tr><td><img class="admin-thumb" src="<?= e(asset_url($p['cover_image'] ?: DEFAULT_COVER)) ?>" alt="cover"></td><td><?= e($p['name']) ?></td><td><?= e($p['username']) ?></td><td><?= $p['is_public'] ? 'Public' : 'Private' ?></td><td><?= (int)$p['song_count'] ?></td><td><?= e($p['created_at']) ?></td><td class="table-actions"><a class="btn small" href="playlists.php?edit=<?= (int)$p['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this playlist?')"><input type="hidden" name="playlist_id" value="<?= (int)$p['id'] ?>"><button class="btn small danger" name="delete_playlist">Delete</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php admin_footer(); ?>
