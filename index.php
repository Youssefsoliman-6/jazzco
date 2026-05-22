<?php require_once __DIR__ . '/includes/header.php'; ?>
<section class="hero">
    <div>
        <div class="eyebrow">Premium sound. Neon soul.</div>
        <h1>Stream your <span class="gradient-text">next obsession</span>.</h1>
        <p class="lead">"Experience seamless discovery, high-fidelity audio, and a limitless library of independent and mainstream tracks. Your ultimate music destination, built for the modern listener."</p>
        <div class="hero-actions">
            <a class="btn primary" href="player.php">Open Web Player</a>
            <a class="btn" href="library.php">Explore Library</a>
            <button class="btn" data-theme-toggle>Switch Theme</button>
        </div>
        <div class="search-shell">
            <input data-live-search type="search" placeholder="Search songs, artists, albums, playlists...">
            <div data-search-results class="search-results"></div>
        </div>
    </div>
    <div class="hero-player">
        <div class="floating-chip chip-1 glass">Live Radio • 24/7</div>
        <div class="floating-chip chip-2 glass">Hi-Fi Mood</div>
        <div class="orbit-card glass">
            <div class="cover-art"></div>
            <div class="visualizer" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
            <div class="track-meta">
                <h3>Midnight Purple</h3>
                <p>JazzCO Sessions</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div><h2>Trending now</h2><p>Fresh tracks moving across JazzCO.</p></div>
        <a href="library.php" class="btn small">See all</a>
    </div>
    <div class="grid cards">
        <?php
        $stmt = $pdo->query("SELECT s.*, ar.name AS artist_name FROM songs s LEFT JOIN artists ar ON ar.id=s.artist_id WHERE s.is_trending=1 ORDER BY s.plays DESC LIMIT 4");
        $songs = $stmt->fetchAll();
        if (!$songs) echo '<div class="card helper">No trending songs yet. Upload songs from admin.</div>';
        foreach ($songs as $song): ?>
            <article class="card">
                <img class="song-cover" src="<?= e(asset_url($song['cover_path'] ?: DEFAULT_COVER)) ?>" alt="cover">
                <div class="card-title"><?= e($song['title']) ?></div>
                <div class="card-sub"><?= e($song['artist_name'] ?? 'Unknown Artist') ?> • <?= format_time($song['duration_seconds']) ?></div>
                <div class="row-actions"><button class="btn small primary" data-play-song="<?= (int)$song['id'] ?>">Play</button><button class="btn small" data-add-queue="<?= (int)$song['id'] ?>">Queue</button><button class="btn small" data-playlist-song="<?= (int)$song['id'] ?>">Playlist</button></div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div><h2>Featured artists</h2><p>Discover voices, producers, and creators.</p></div>
    </div>
    <div class="grid cards">
        <?php foreach ($pdo->query("SELECT * FROM artists ORDER BY created_at DESC LIMIT 4") as $artist): ?>
            <article class="card">
                <img class="artist-avatar" src="<?= e(asset_url($artist['image_path'] ?: DEFAULT_AVATAR)) ?>" alt="artist">
                <div class="card-title"><?= e($artist['name']) ?></div>
                <div class="card-sub"><?= e($artist['bio'] ?: 'Featured JazzCO artist') ?></div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section glass" style="padding:2rem;border-radius:32px;">
    <div class="grid two" style="align-items:center;">
        <div>
            <div class="eyebrow">Create. Upload. Stream.</div>
            <h2>Your ultimate audio hub.</h2>
            <p>A seamless, high-fidelity streaming experience built entirely around how you listen. Manage your library, follow your favorites, and never miss a beat.</p>
        </div>
        <div class="hero-actions">
            <?php if (!$user): ?>
                <a href="register.php" class="btn primary">Create Account</a>
                <a href="login.php" class="btn">Login</a>
            <?php else: ?>
                <a href="profile.php" class="btn primary">View Profile</a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
