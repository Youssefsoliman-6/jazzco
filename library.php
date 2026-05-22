<?php require_once __DIR__ . '/includes/header.php'; ?>
<section class="section">
    <div class="section-head">
        <div><h1>Music Library</h1><p>Albums, artists, genres, singles, recently added, trending, and recommended tracks.</p></div>
        <a class="btn primary" href="player.php">Open Player</a>
    </div>
    <div class="search-shell">
        <input data-live-search type="search" placeholder="Search everything in JazzCO...">
        <div data-search-results class="search-results"></div>
    </div>
</section>

<section class="section">
    <h2>Recently added</h2>
    <div class="grid cards">
        <?php foreach (get_songs($pdo, 8, 0) as $song): ?>
            <article class="card">
                <img class="song-cover" src="<?= e(asset_url($song['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover">
                <div class="card-title"><?= e($song['title']) ?></div>
                <div class="card-sub"><?= e($song['artist_name'] ?? 'Unknown') ?> • <?= e($song['genre_name'] ?? 'Genre') ?></div>
                <div class="row-actions"><button class="btn small primary" data-play-song="<?= (int)$song['id'] ?>">Play</button><button class="btn small" data-add-queue="<?= (int)$song['id'] ?>">Queue</button><button class="btn small" data-playlist-song="<?= (int)$song['id'] ?>">Playlist</button></div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-head"><div><h2>Albums</h2><p>Open album pages or create your own.</p></div><a class="btn small" href="albums.php">See all albums</a></div>
    <div class="grid cards">
        <?php foreach ($pdo->query("SELECT al.*, ar.name artist_name FROM albums al LEFT JOIN artists ar ON ar.id=al.artist_id ORDER BY al.created_at DESC LIMIT 8") as $album): ?>
            <a class="card link-card" href="album.php?id=<?= (int)$album['id'] ?>">
                <img class="song-cover" src="<?= e(asset_url($album['cover_path'] ?: DEFAULT_COVER)) ?>" alt="album">
                <div class="card-title"><?= e($album['title']) ?></div>
                <div class="card-sub"><?= e($album['artist_name'] ?? 'Various Artists') ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2>Genres</h2>
    <div class="grid cards">
        <?php foreach ($pdo->query("SELECT g.*, COUNT(s.id) song_count FROM genres g LEFT JOIN songs s ON s.genre_id=g.id GROUP BY g.id ORDER BY g.name") as $genre): ?>
            <article class="card">
                <div class="eyebrow">Genre</div>
                <div class="card-title"><?= e($genre['name']) ?></div>
                <div class="card-sub"><?= (int)$genre['song_count'] ?> songs</div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
