<?php
require_once __DIR__ . '/includes_admin.php';

$editGenre = null;
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_genre'])) {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') throw new RuntimeException('Genre name is required.');
            $pdo->prepare('INSERT INTO genres (name) VALUES (?)')->execute([$name]);
            flash('success', 'Genre created successfully.');
            admin_redirect('genres.php');
        }
        if (isset($_POST['update_genre'])) {
            $id = (int)($_POST['genre_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if ($id <= 0 || $name === '') throw new RuntimeException('Genre ID and name are required.');
            $pdo->prepare('UPDATE genres SET name=? WHERE id=?')->execute([$name, $id]);
            flash('success', 'Genre updated successfully.');
            admin_redirect('genres.php');
        }
        if (isset($_POST['delete_genre'])) {
            $pdo->prepare('DELETE FROM genres WHERE id=?')->execute([(int)$_POST['genre_id']]);
            flash('success', 'Genre deleted. Songs are kept with no genre.');
            admin_redirect('genres.php');
        }
    }

    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM genres WHERE id=? LIMIT 1');
        $stmt->execute([(int)$_GET['edit']]);
        $editGenre = $stmt->fetch() ?: null;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

admin_header('Genres CRUD');
$genres = $pdo->query('SELECT g.*, (SELECT COUNT(*) FROM songs s WHERE s.genre_id=g.id) song_count FROM genres g ORDER BY g.name')->fetchAll();
?>
<div class="section-head"><div><h1>Genres CRUD</h1><p>Create, read, update, and delete music genres.</p></div><?php if ($editGenre): ?><a class="btn" href="genres.php">Cancel edit</a><?php endif; ?></div>
<?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

<div class="grid two">
<form class="card" method="post">
    <h2><?= $editGenre ? 'Edit genre' : 'Create genre' ?></h2>
    <div class="form-grid">
        <?php if($editGenre): ?><input type="hidden" name="genre_id" value="<?= (int)$editGenre['id'] ?>"><?php endif; ?>
        <div class="input-group"><label>Genre name</label><input name="name" value="<?= e($editGenre['name'] ?? '') ?>" required></div>
        <button class="btn primary" name="<?= $editGenre ? 'update_genre' : 'add_genre' ?>"><?= $editGenre ? 'Save genre changes' : 'Add genre' ?></button>
    </div>
</form>
<section class="card"><h2>CRUD status</h2><p class="helper">This page now supports all CRUD actions instead of only add/delete.</p><div class="mini-feature-list"><span class="badge">Create</span><span class="badge">Read</span><span class="badge">Update</span><span class="badge">Delete</span></div></section>
</div>

<section class="section card"><h2>All genres</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Songs</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php foreach($genres as $g): ?>
<tr><td><?= (int)$g['id'] ?></td><td><?= e($g['name']) ?></td><td><?= (int)$g['song_count'] ?></td><td><?= e($g['created_at']) ?></td><td class="table-actions"><a class="btn small" href="genres.php?edit=<?= (int)$g['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this genre? Songs will stay with no genre.');"><input type="hidden" name="genre_id" value="<?= (int)$g['id'] ?>"><button class="btn small danger" name="delete_genre">Delete</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php admin_footer(); ?>
