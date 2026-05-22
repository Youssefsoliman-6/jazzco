<?php require_once __DIR__ . '/includes/header.php'; ?>
<section class="player-layout">
    <aside class="sidebar glass">
        <a class="active" href="player.php">▶ Now Playing</a>
        <a href="library.php">▦ Library</a>
        <a href="playlists.php">☰ Playlists</a>
        <a href="profile.php">♡ Favorites</a>
        <button class="btn full" data-theme-toggle>Theme Switcher</button>
     
    </aside>

    <section class="player-stage glass">
        <img id="nowCover" class="now-playing-cover" src="assets/images/covers/default-cover.svg" alt="cover">
        <h1 id="nowTitle" class="now-title">Loading...</h1>
        <div id="nowArtist" class="now-artist">JazzCO</div>
        <div class="visualizer" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
        <div class="progress-wrap">
            <span id="currentTime">0:00</span>
            <input id="progress" class="range" type="range" min="0" value="0" step="1">
            <span id="duration">0:00</span>
        </div>
        <div class="controls">
            <button id="shuffleBtn" class="icon-btn" title="Shuffle">⤨</button>
            <button id="prevBtn" class="icon-btn" title="Previous">⏮</button>
            <button id="playBtn" class="icon-btn play" title="Play">▶</button>
            <button id="nextBtn" class="icon-btn" title="Next">⏭</button>
            <button id="repeatBtn" class="icon-btn" title="Repeat">⟲</button>
            <button id="favoriteBtn" class="icon-btn" title="Favorite">♡</button>
            <button id="fullBtn" class="icon-btn" title="Fullscreen mode">⛶</button>
        </div>
        <div class="volume-wrap">
            <span>🔈</span><input id="volume" class="range" type="range" min="0" max="1" step="0.01" value="0.75"><span>🔊</span>
        </div>
        <div class="section">
            <div class="section-head"><div><h2>Library</h2><p>Infinite scrolling loads more songs.</p></div></div>
            <div id="songGrid" class="grid cards"></div>
        </div>
    </section>

    <aside class="queue-panel glass">
        <h2>Queue</h2>
        <div id="queueList" class="queue-list"></div>
    </aside>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
