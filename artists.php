<?php
require_once __DIR__ . '/includes_admin.php';

$editAlbum = null;
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $artistId = (int)($_POST['artist_id'] ?? 0) ?: null;
        $releaseYear = trim($_POST['release_year'] ?? '');
        $releaseYear = $releaseYear === '' ? null : (int)$releaseYear;

        if (isset($_POST['create_album'])) {
            if ($title === '') throw new RuntimeException('Album title is required.');
            $coverPath = admin_upload_image('cover_image', 'covers') ?: DEFAULT_COVER;
            $stmt = $pdo->prepare('INSERT INTO albums (title, artist_id, cover_path, release_year) VALUES (?, ?, ?, ?)');
            $stmt->execute([$title, $artistId, $coverPath, $releaseYear]);
            flash('success', 'Album created successfully.');
            admin_redirect('albums.php');
        }

        if (isset($_POST['update_album'])) {
            $id = (int)($_POST['album_id'] ?? 0);
            if ($id <= 0 || $title === '') throw new RuntimeException('Album ID and title are required.');
            $stmt = $pdo->prepare('SELECT cover_path FROM albums WHERE id=? LIMIT 1');
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            if (!$current) throw new RuntimeException('Album not found.');
            $coverPath = admin_upload_image('cover_image', 'covers') ?: ($current['cover_path'] ?: DEFAULT_COVER);
            $stmt = $pdo->prepare('UPDATE albums SET title=?, artist_id=?, cover_path=?, release_year=? WHERE id=?');
            $stmt->execute([$title, $artistId, $coverPath, $releaseYear, $id]);
            flash('success', 'Album updated successfully.');
            admin_redirect('albums.php');
        }

        if (isset($_POST['delete_album'])) {
            $id = (int)($_POST['album_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('Invalid album.');
            $pdo->prepare('DELETE FROM albums WHERE id=?')->execute([$id]);
            flash('success', 'Album deleted. Related songs are kept with no album.');
            admin_redirect('albums.php');
        }
    }

    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM albums WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_GET['edit']]);
        $editAlbum = $stmt->fetch() ?: null;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_header('Albums CRUD');
$artists = $pdo->query('SELECT * FROM artists ORDER BY name')->fetchAll();
$albums = $pdo->query('SELECT al.*, ar.name artist_name, (SELECT COUNT(*) FROM songs s WHERE s.album_id=al.id) song_count FROM albums al LEFT JOIN artists ar ON ar.id=al.artist_id ORDER BY al.created_at DESC')->fetchAll();
?>
<div class="section-head"><div><h1>Albums CRUD</h1><p>Create, read, update, and delete albums with cover images.</p></div><?php if ($editAlbum): ?><a class="btn" href="albums.php">Cancel edit</a><?php endif; ?></div>
<?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<form class="card" method="post" enctype="multipart/form-data">
    <h2><?= $editAlbum ? 'Edit album' : 'Create album' ?></h2>
    <div class="form-grid admin-form-grid">
        <?php if($editAlbum): ?><input type="hidden" name="album_id" value="<?= (int)$editAlbum['id'] ?>"><?php endif; ?>
        <div class="input-group"><label>Album title</label><input name="title" value="<?= e($editAlbum['title'] ?? '') ?>" required></div>
        <div class="input-group"><label>Artist</label><select name="artist_id"><option value="">No artist</option><?php foreach($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (int)($editAlbum['artist_id'] ?? 0)===(int)$a['id']?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Release year</label><input type="number" name="release_year" min="1900" max="2099" value="<?= e($editAlbum['release_year'] ?? '') ?>"></div>
        <div class="input-group"><label>Cover image</label><input type="file" name="cover_image" accept="image/*"></div>
        <?php if($editAlbum): ?><img class="admin-thumb large" src="<?= e(asset_url($editAlbum['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover"><?php endif; ?>
        <button class="btn primary" name="<?= $editAlbum ? 'update_album' : 'create_album' ?>"><?= $editAlbum ? 'Save album changes' : 'Add album' ?></button>
    </div>
</form>

<section class="section card"><h2>All albums</h2><div class="table-wrap"><table><thead><tr><th>Cover</th><th>Title</th><th>Artist</th><th>Year</th><th>Songs</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php foreach($albums as $a): ?>
<tr><td><img class="admin-thumb" src="<?= e(asset_url($a['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover"></td><td><?= e($a['title']) ?></td><td><?= e($a['artist_name'] ?? '-') ?></td><td><?= e($a['release_year'] ?? '-') ?></td><td><?= (int)$a['song_count'] ?></td><td><?= e($a['created_at']) ?></td><td class="table-actions"><a class="btn small" href="albums.php?edit=<?= (int)$a['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this album? Songs will stay but become singles/no album.');"><input type="hidden" name="album_id" value="<?= (int)$a['id'] ?>"><button class="btn small danger" name="delete_album">Delete</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php admin_footer(); ?>
