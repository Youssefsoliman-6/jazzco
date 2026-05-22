<?php
require_once __DIR__ . '/includes/header.php';
$user = current_user($pdo);
?>
<section class="section">
    <div class="section-head">
        <div><h1>Playlists</h1><p>Create playlists, open playlist pages, and add songs from anywhere in JazzCO.</p></div>
    </div>
    <?php if (!$user): ?>
        <div class="card"><p>Please <a class="gradient-text" href="login.php">login</a> to create playlists.</p></div>
    <?php else: ?>
        <div class="grid two">
            <form class="card" id="playlistForm" enctype="multipart/form-data">
                <h2>Create playlist</h2>
                <div class="form-grid">
                    <input type="hidden" name="action" value="create">
                    <div class="input-group"><label>Name</label><input name="name" required minlength="2"></div>
                    <div class="input-group"><label>Description</label><textarea name="description"></textarea></div>
                    <div class="input-group"><label>Cover image</label><input type="file" name="cover_image" accept="image/*"></div>
                    <label class="check-line"><input type="checkbox" name="is_public"> Public playlist</label>
                    <button class="btn primary">Create Playlist</button>
                </div>
            </form>
            <div class="card">
                <h2>Your playlists</h2>
                <div id="playlistList" class="queue-list"></div>
            </div>
        </div>
    <?php endif; ?>
</section>
<section class="section">
    <h2>Public playlists</h2>
    <div class="grid cards">
        <?php foreach ($pdo->query("SELECT p.*, u.username, (SELECT COUNT(*) FROM playlist_songs ps WHERE ps.playlist_id=p.id) song_count FROM playlists p JOIN users u ON u.id=p.user_id WHERE p.is_public=1 ORDER BY p.created_at DESC LIMIT 8") as $pl): ?>
            <a class="card link-card" href="playlist.php?id=<?= (int)$pl['id'] ?>">
                <img class="playlist-cover" src="<?= e(asset_url($pl['cover_image'] ?: DEFAULT_COVER)) ?>" alt="playlist">
                <div class="card-title"><?= e($pl['name']) ?></div>
                <div class="card-sub">By <?= e($pl['username']) ?> • <?= (int)$pl['song_count'] ?> songs</div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<script>
const form = document.getElementById('playlistForm');
const list = document.getElementById('playlistList');
async function loadPlaylists(){
    if(!list) return;
    const res = await fetch('playlist_api.php?action=list');
    const data = await res.json();
    list.innerHTML = (data.playlists||[]).map(p => `<div class="queue-item"><img src="${p.cover}" alt=""><div><h4><a href="playlist.php?id=${p.id}">${escapeHtml(p.name)}</a></h4><p>${p.is_public==1?'Public':'Private'} • ${p.song_count||0} songs</p><div class="queue-actions"><a class="micro-btn" href="playlist.php?id=${p.id}">Open</a><button class="micro-btn danger" data-del="${p.id}">Delete</button></div></div></div>`).join('') || '<p class="helper">No playlists yet.</p>';
}
form?.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(form);
    const res = await fetch('playlist_api.php',{method:'POST',body:fd});
    const data = await res.json();
    window.JazzCO.toast(data.ok ? 'Playlist created.' : data.message, data.ok ? 'success' : 'error');
    if(data.ok){ form.reset(); loadPlaylists(); }
});
list?.addEventListener('click', async e => {
    const btn = e.target.closest('[data-del]');
    if(!btn) return;
    if(!confirm('Delete this playlist?')) return;
    const res = await fetch('playlist_api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'delete', playlist_id:btn.dataset.del})});
    const data = await res.json();
    window.JazzCO.toast(data.ok ? 'Playlist deleted.' : 'Delete failed.', data.ok ? 'success' : 'error');
    loadPlaylists();
});
function escapeHtml(str){ return String(str ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c])); }
loadPlaylists();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
