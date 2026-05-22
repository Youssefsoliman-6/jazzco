<?php
require_once __DIR__ . '/includes_admin.php';
admin_header('Dashboard');
$counts = [
    'Users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'Songs' => $pdo->query('SELECT COUNT(*) FROM songs')->fetchColumn(),
    'Albums' => $pdo->query('SELECT COUNT(*) FROM albums')->fetchColumn(),
    'Artists' => $pdo->query('SELECT COUNT(*) FROM artists')->fetchColumn(),
    'Playlists' => $pdo->query('SELECT COUNT(*) FROM playlists')->fetchColumn(),
    'Genres' => $pdo->query('SELECT COUNT(*) FROM genres')->fetchColumn(),
];
?>
<div class="section-head"><div><h1>Dashboard statistics</h1><p>Manage JazzCO content and users with full admin CRUD.</p></div><a class="btn primary" href="songs.php">Upload song</a></div>
<div class="stats-grid">
<?php foreach($counts as $label=>$value): ?><div class="stat"><span><?= e($label) ?></span><strong><?= (int)$value ?></strong></div><?php endforeach; ?>
</div>
<section class="section card"><h2>Quick CRUD actions</h2><div class="quick-actions"><a class="btn" href="songs.php">Songs CRUD</a><a class="btn" href="artists.php">Artists + Photos</a><a class="btn" href="albums.php">Albums CRUD</a><a class="btn" href="users.php">Users CRUD</a><a class="btn" href="playlists.php">Playlists CRUD</a><a class="btn" href="genres.php">Genres CRUD</a></div></section>
<section class="card"><h2>Recent songs</h2><div class="table-wrap"><table><thead><tr><th>Song</th><th>Artist</th><th>Plays</th><th>Date</th></tr></thead><tbody>
<?php foreach($pdo->query("SELECT s.*, ar.name artist_name FROM songs s LEFT JOIN artists ar ON ar.id=s.artist_id ORDER BY s.created_at DESC LIMIT 8") as $s): ?>
<tr><td><?= e($s['title']) ?></td><td><?= e($s['artist_name'] ?? 'Unknown') ?></td><td><?= (int)$s['plays'] ?></td><td><?= e($s['created_at']) ?></td></tr>
<?php endforeach; ?></tbody></table></div></section>
<?php admin_footer(); ?>
