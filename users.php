<?php
require_once __DIR__ . '/includes_admin.php';

$editSong = null;
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        assert_post_size_not_exceeded();
        if (isset($_POST['update_song'])) {
            $id = (int)($_POST['song_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $artistId = (int)($_POST['artist_id'] ?? 0) ?: null;
            $albumId = (int)($_POST['album_id'] ?? 0) ?: null;
            $genreId = (int)($_POST['genre_id'] ?? 0) ?: null;
            $duration = max(0, (int)($_POST['duration_seconds'] ?? 0));
            $trending = !empty($_POST['is_trending']) ? 1 : 0;
            if ($id <= 0 || $title === '') throw new RuntimeException('Song ID and title are required.');
            $stmt = $pdo->prepare('SELECT file_path, cover_path FROM songs WHERE id=? LIMIT 1');
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            if (!$current) throw new RuntimeException('Song not found.');
            $songPath = upload_audio_file($_FILES['mp3_file'] ?? [], 'songs', 50 * 1024 * 1024) ?: $current['file_path'];
            $coverPath = admin_upload_image('cover_image', 'covers') ?: ($current['cover_path'] ?: DEFAULT_COVER);
            $stmt = $pdo->prepare('UPDATE songs SET title=?, artist_id=?, album_id=?, genre_id=?, file_path=?, cover_path=?, duration_seconds=?, is_trending=? WHERE id=?');
            $stmt->execute([$title, $artistId, $albumId, $genreId, $songPath, $coverPath, $duration, $trending, $id]);
            flash('success', 'Song updated successfully.');
            admin_redirect('songs.php');
        }

        if (isset($_POST['delete_song'])) {
            $pdo->prepare('DELETE FROM songs WHERE id=?')->execute([(int)$_POST['song_id']]);
            flash('success', 'Song deleted.');
            admin_redirect('songs.php');
        }
    }

    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM songs WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_GET['edit']]);
        $editSong = $stmt->fetch() ?: null;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_header('Songs CRUD');
$artists = $pdo->query('SELECT * FROM artists ORDER BY name')->fetchAll();
$albums = $pdo->query('SELECT * FROM albums ORDER BY title')->fetchAll();
$genres = $pdo->query('SELECT * FROM genres ORDER BY name')->fetchAll();
$songs = $pdo->query("SELECT s.*, ar.name artist_name, g.name genre_name, al.title album_title FROM songs s LEFT JOIN artists ar ON ar.id=s.artist_id LEFT JOIN genres g ON g.id=s.genre_id LEFT JOIN albums al ON al.id=s.album_id ORDER BY s.created_at DESC")->fetchAll();
?>
<div class="section-head"><div><h1>Songs CRUD</h1><p>Upload, view, edit, and delete songs. Optional MP3/cover replacement is supported while editing.</p></div><?php if ($editSong): ?><a class="btn" href="songs.php">Cancel edit</a><?php endif; ?></div>
<?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<div class="grid two">
<form class="card" id="uploadSongForm" enctype="multipart/form-data">
    <h2>Create song</h2>
    <div class="form-grid">
        <div class="input-group"><label>Title</label><input name="title" required></div>
        <div class="input-group"><label>Artist</label><select name="artist_id" required><option value="">Choose artist</option><?php foreach($artists as $a): ?><option value="<?= (int)$a['id'] ?>"><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Album</label><select name="album_id"><option value="">Single/no album</option><?php foreach($albums as $a): ?><option value="<?= (int)$a['id'] ?>"><?= e($a['title']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Genre</label><select name="genre_id"><option value="">Choose genre</option><?php foreach($genres as $g): ?><option value="<?= (int)$g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Duration seconds</label><input type="number" name="duration_seconds" min="0" value="180"></div>
        <div class="input-group"><label>MP3 file</label><input type="file" name="mp3_file" accept="audio/mpeg,audio/mp3,.mp3" required><small class="helper">MP3 only, max 50 MB.</small></div>
        <div class="input-group"><label>Cover image</label><input type="file" name="cover_image" accept="image/*"></div>
        <label class="check-line"><input type="checkbox" name="is_trending"> Mark as trending</label>
        <button class="btn primary">Upload song</button>
    </div>
</form>

<form class="card" method="post" enctype="multipart/form-data">
    <h2><?= $editSong ? 'Edit song' : 'Select a song to edit' ?></h2>
    <?php if($editSong): ?>
    <div class="form-grid">
        <input type="hidden" name="song_id" value="<?= (int)$editSong['id'] ?>">
        <div class="input-group"><label>Title</label><input name="title" value="<?= e($editSong['title']) ?>" required></div>
        <div class="input-group"><label>Artist</label><select name="artist_id"><option value="">No artist</option><?php foreach($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (int)($editSong['artist_id'] ?? 0)===(int)$a['id']?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Album</label><select name="album_id"><option value="">Single/no album</option><?php foreach($albums as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (int)($editSong['album_id'] ?? 0)===(int)$a['id']?'selected':'' ?>><?= e($a['title']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Genre</label><select name="genre_id"><option value="">No genre</option><?php foreach($genres as $g): ?><option value="<?= (int)$g['id'] ?>" <?= (int)($editSong['genre_id'] ?? 0)===(int)$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
        <div class="input-group"><label>Duration seconds</label><input type="number" name="duration_seconds" min="0" value="<?= (int)$editSong['duration_seconds'] ?>"></div>
        <div class="input-group"><label>Replace MP3 file</label><input type="file" name="mp3_file" accept="audio/mpeg,.mp3"><small class="helper">Leave empty to keep current file. MP3 only, max 50 MB.</small></div>
        <div class="input-group"><label>Replace cover image</label><input type="file" name="cover_image" accept="image/*"></div>
        <img class="admin-thumb large" src="<?= e(asset_url($editSong['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover">
        <label class="check-line"><input type="checkbox" name="is_trending" <?= !empty($editSong['is_trending'])?'checked':'' ?>> Mark as trending</label>
        <button class="btn primary" name="update_song">Save song changes</button>
    </div>
    <?php else: ?>
        <p class="helper">Click Edit beside any song in the table below to update its title, artist, album, genre, MP3 file, cover, duration, and trending status.</p>
    <?php endif; ?>
</form>
</div>

<section class="section card"><h2>All songs</h2><div class="table-wrap"><table><thead><tr><th>Cover</th><th>Title</th><th>Artist</th><th>Album</th><th>Genre</th><th>Duration</th><th>Trending</th><th>Plays</th><th>Actions</th></tr></thead><tbody>
<?php foreach($songs as $s): ?>
<tr><td><img class="admin-thumb" src="<?= e(asset_url($s['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover"></td><td><?= e($s['title']) ?></td><td><?= e($s['artist_name'] ?? '-') ?></td><td><?= e($s['album_title'] ?? 'Single') ?></td><td><?= e($s['genre_name'] ?? '-') ?></td><td><?= format_time($s['duration_seconds']) ?></td><td><?= $s['is_trending'] ? 'Yes' : 'No' ?></td><td><?= (int)$s['plays'] ?></td><td class="table-actions"><a class="btn small" href="songs.php?edit=<?= (int)$s['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this song?')"><input type="hidden" name="song_id" value="<?= (int)$s['id'] ?>"><button class="btn small danger" name="delete_song">Delete</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div></section>
<script>
document.getElementById('uploadSongForm')?.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Uploading...';
    try {
        const res = await fetch('../upload_song.php', {method:'POST', body:new FormData(e.target)});
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch { throw new Error('Upload failed before JazzCO could read the response. Check setup_check.php and PHP upload limits.'); }
        window.JazzCO.toast(data.message, data.ok ? 'success' : 'error');
        if(data.ok) setTimeout(()=>location.reload(), 900);
    } catch (err) {
        window.JazzCO.toast(err.message || 'Upload failed. Check setup_check.php.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Upload song';
    }
});
</script>
<?php admin_footer(); ?>
