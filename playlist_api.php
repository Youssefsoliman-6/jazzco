<?php
require_once __DIR__ . '/includes/functions.php';
$user = current_user($pdo);
if (!$user) json_response(['ok'=>false, 'message'=>'Login required'], 401);

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$input = [];
if (stripos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
} else {
    $input = $_POST;
}
$action = $input['action'] ?? $_GET['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->prepare('SELECT p.*, (SELECT COUNT(*) FROM playlist_songs ps WHERE ps.playlist_id = p.id) AS song_count
        FROM playlists p
        WHERE p.user_id=?
        ORDER BY p.created_at DESC');
    $stmt->execute([$user['id']]);
    $playlists = array_map(function($p) {
        $p['cover'] = asset_url($p['cover_image'] ?: DEFAULT_COVER);
        $p['song_count'] = (int)($p['song_count'] ?? 0);
        return $p;
    }, $stmt->fetchAll());
    json_response(['ok'=>true, 'playlists'=>$playlists]);
}

if ($action === 'create') {
    try {
        assert_post_size_not_exceeded();
        $name = trim($input['name'] ?? '');
        $isPublic = !empty($input['is_public']) ? 1 : 0;
        if (strlen($name) < 2) json_response(['ok'=>false, 'message'=>'Playlist name is too short'], 400);
        $cover = DEFAULT_COVER;
        if (!empty($_FILES['cover_image']['name'])) {
            $cover = upload_image_file($_FILES['cover_image'], 'covers', 5000000);
        }
        $pdo->prepare('INSERT INTO playlists (user_id, name, description, cover_image, is_public) VALUES (?, ?, ?, ?, ?)')
            ->execute([$user['id'], $name, trim($input['description'] ?? ''), $cover, $isPublic]);
        json_response(['ok'=>true, 'id'=>(int)$pdo->lastInsertId()]);
    } catch (Throwable $e) {
        json_response(['ok'=>false, 'message'=>$e->getMessage()], 400);
    }
}

if ($action === 'update') {
    try {
        assert_post_size_not_exceeded();
        $id = (int)($input['playlist_id'] ?? 0);
        $name = trim($input['name'] ?? '');
        if ($id <= 0 || strlen($name) < 2) json_response(['ok'=>false, 'message'=>'Invalid playlist details'], 400);
        $stmt = $pdo->prepare('SELECT * FROM playlists WHERE id=? AND user_id=?');
        $stmt->execute([$id, $user['id']]);
        $playlist = $stmt->fetch();
        if (!$playlist) json_response(['ok'=>false, 'message'=>'Playlist not found'], 404);
        $cover = $playlist['cover_image'];
        if (!empty($_FILES['cover_image']['name'])) {
            $cover = upload_image_file($_FILES['cover_image'], 'covers', 5000000);
        }
        $isPublic = !empty($input['is_public']) ? 1 : 0;
        $pdo->prepare('UPDATE playlists SET name=?, description=?, cover_image=?, is_public=? WHERE id=? AND user_id=?')
            ->execute([$name, trim($input['description'] ?? ''), $cover, $isPublic, $id, $user['id']]);
        json_response(['ok'=>true]);
    } catch (Throwable $e) {
        json_response(['ok'=>false, 'message'=>$e->getMessage()], 400);
    }
}

if ($action === 'delete') {
    $id = (int)($input['playlist_id'] ?? 0);
    $pdo->prepare('DELETE FROM playlists WHERE id=? AND user_id=?')->execute([$id, $user['id']]);
    json_response(['ok'=>true]);
}

if ($action === 'add_song') {
    $playlistId = (int)($input['playlist_id'] ?? 0);
    $songId = (int)($input['song_id'] ?? 0);
    $own = $pdo->prepare('SELECT id FROM playlists WHERE id=? AND user_id=?');
    $own->execute([$playlistId, $user['id']]);
    if (!$own->fetch()) json_response(['ok'=>false, 'message'=>'Playlist not found'], 404);
    $song = $pdo->prepare('SELECT id FROM songs WHERE id=?');
    $song->execute([$songId]);
    if (!$song->fetch()) json_response(['ok'=>false, 'message'=>'Song not found'], 404);
    $pdo->prepare('INSERT IGNORE INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)')->execute([$playlistId, $songId]);
    json_response(['ok'=>true]);
}

if ($action === 'remove_song') {
    $playlistId = (int)($input['playlist_id'] ?? 0);
    $songId = (int)($input['song_id'] ?? 0);
    $own = $pdo->prepare('SELECT id FROM playlists WHERE id=? AND user_id=?');
    $own->execute([$playlistId, $user['id']]);
    if (!$own->fetch()) json_response(['ok'=>false, 'message'=>'Playlist not found'], 404);
    $pdo->prepare('DELETE FROM playlist_songs WHERE playlist_id=? AND song_id=?')->execute([$playlistId, $songId]);
    json_response(['ok'=>true]);
}

if ($action === 'toggle_privacy') {
    $id = (int)($input['playlist_id'] ?? 0);
    $pdo->prepare('UPDATE playlists SET is_public = 1 - is_public WHERE id=? AND user_id=?')->execute([$id, $user['id']]);
    json_response(['ok'=>true]);
}

json_response(['ok'=>false, 'message'=>'Unknown action'], 400);
?>
