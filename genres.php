<?php
require_once __DIR__ . '/includes_admin.php';

$editArtist = null;
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['create_artist'])) {
            $name = trim($_POST['name'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            if ($name === '') throw new RuntimeException('Artist name is required.');
            $imagePath = admin_upload_image('artist_image', 'artists') ?: DEFAULT_AVATAR;
            $stmt = $pdo->prepare('INSERT INTO artists (name, bio, image_path) VALUES (?, ?, ?)');
            $stmt->execute([$name, $bio, $imagePath]);
            flash('success', 'Artist created successfully.');
            admin_redirect('artists.php');
        }

        if (isset($_POST['update_artist'])) {
            $id = (int)($_POST['artist_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            if ($id <= 0 || $name === '') throw new RuntimeException('Artist ID and name are required.');
            $stmt = $pdo->prepare('SELECT image_path FROM artists WHERE id=? LIMIT 1');
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            if (!$current) throw new RuntimeException('Artist not found.');
            $imagePath = admin_upload_image('artist_image', 'artists') ?: ($current['image_path'] ?: DEFAULT_AVATAR);
            $stmt = $pdo->prepare('UPDATE artists SET name=?, bio=?, image_path=? WHERE id=?');
            $stmt->execute([$name, $bio, $imagePath, $id]);
            flash('success', 'Artist updated successfully.');
            admin_redirect('artists.php');
        }

        if (isset($_POST['delete_artist'])) {
            $id = (int)($_POST['artist_id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('Invalid artist.');
            $pdo->prepare('DELETE FROM artists WHERE id=?')->execute([$id]);
            flash('success', 'Artist deleted. Related songs/albums are kept with empty artist.');
            admin_redirect('artists.php');
        }
    }

    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM artists WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_GET['edit']]);
        $editArtist = $stmt->fetch() ?: null;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_header('Artists CRUD');
$artists = $pdo->query('SELECT a.*, (SELECT COUNT(*) FROM songs s WHERE s.artist_id=a.id) song_count, (SELECT COUNT(*) FROM albums al WHERE al.artist_id=a.id) album_count FROM artists a ORDER BY a.created_at DESC')->fetchAll();
?>
<div class="section-head">
    <div><h1>Artists CRUD</h1><p>Create, read, update, and delete artists. You can also upload artist photos.</p></div>
    <?php if ($editArtist): ?><a class="btn" href="artists.php">Cancel edit</a><?php endif; ?>
</div>
<?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<div class="grid two">
<form class="card" method="post" enctype="multipart/form-data">
    <h2><?= $editArtist ? 'Edit artist' : 'Create artist' ?></h2>
    <div class="form-grid">
        <?php if($editArtist): ?><input type="hidden" name="artist_id" value="<?= (int)$editArtist['id'] ?>"><?php endif; ?>
        <div class="input-group"><label>Artist name</label><input name="name" value="<?= e($editArtist['name'] ?? '') ?>" required></div>
        <div class="input-group"><label>Artist bio</label><textarea name="bio" rows="5" placeholder="Short artist bio"><?= e($editArtist['bio'] ?? '') ?></textarea></div>
        <div class="input-group"><label>Artist photo</label><input type="file" name="artist_image" accept="image/*"><small class="helper">JPG, PNG, WEBP, or GIF. Max 5 MB.</small></div>
        <?php if($editArtist): ?><img class="admin-thumb large" src="<?= e(asset_url($editArtist['image_path'] ?: DEFAULT_AVATAR)) ?>" alt="artist photo"><?php endif; ?>
        <button class="btn primary" name="<?= $editArtist ? 'update_artist' : 'create_artist' ?>"><?= $editArtist ? 'Save artist changes' : 'Add artist' ?></button>
    </div>
</form>

<section class="card">
    <h2>Artist photo tips</h2>
    <p class="helper">Use square images for the best circular avatar result on the website. Uploaded photos are saved in <strong>uploads/artists</strong> and their paths are stored in MySQL.</p>
    <div class="mini-feature-list">
        <span class="badge">Create</span><span class="badge">Read</span><span class="badge">Update</span><span class="badge">Delete</span><span class="badge">Photo upload</span>
    </div>
</section>
</div>

<section class="section card">
    <h2>All artists</h2>
    <div class="table-wrap"><table><thead><tr><th>Photo</th><th>Name</th><th>Bio</th><th>Songs</th><th>Albums</th><th>Created</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($artists as $a): ?>
        <tr>
            <td><img class="admin-thumb" src="<?= e(asset_url($a['image_path'] ?: DEFAULT_AVATAR)) ?>" alt="artist"></td>
            <td><?= e($a['name']) ?></td>
            <td><?= e(short_text($a['bio'] ?? '', 70)) ?></td>
            <td><?= (int)$a['song_count'] ?></td>
            <td><?= (int)$a['album_count'] ?></td>
            <td><?= e($a['created_at']) ?></td>
            <td class="table-actions"><a class="btn small" href="artists.php?edit=<?= (int)$a['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this artist? Songs and albums will stay, but the artist field becomes empty.');"><input type="hidden" name="artist_id" value="<?= (int)$a['id'] ?>"><button class="btn small danger" name="delete_artist">Delete</button></form></td>
        </tr>
    <?php endforeach; ?>
    </tbody></table></div>
</section>
<?php admin_footer(); ?>
