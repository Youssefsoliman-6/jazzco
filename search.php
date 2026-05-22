<?php
require_once __DIR__ . '/includes/functions.php';
$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) json_response(['songs'=>[], 'artists'=>[], 'albums'=>[], 'playlists'=>[]]);
$term = '%' . $q . '%';

$songStmt = $pdo->prepare("SELECT s.id, s.title, s.cover_path, ar.name artist_name FROM songs s LEFT JOIN artists ar ON ar.id=s.artist_id WHERE s.title LIKE ? OR ar.name LIKE ? ORDER BY s.plays DESC LIMIT 8");
$songStmt->execute([$term, $term]);
$songs = array_map(fn($s) => [
    'id'=>(int)$s['id'], 'title'=>$s['title'], 'subtitle'=>$s['artist_name'] ?: 'Unknown Artist', 'cover'=>asset_url($s['cover_path'] ?: DEFAULT_COVER)
], $songStmt->fetchAll());

$artistStmt = $pdo->prepare("SELECT id, name, image_path, bio FROM artists WHERE name LIKE ? ORDER BY name LIMIT 6");
$artistStmt->execute([$term]);
$artists = array_map(fn($a) => ['id'=>(int)$a['id'], 'name'=>$a['name'], 'subtitle'=>'Artist', 'image'=>asset_url($a['image_path'] ?: DEFAULT_AVATAR), 'url'=>BASE_URL . 'library.php'], $artistStmt->fetchAll());

$albumStmt = $pdo->prepare("SELECT id, title, cover_path FROM albums WHERE title LIKE ? ORDER BY title LIMIT 6");
$albumStmt->execute([$term]);
$albums = array_map(fn($a) => ['id'=>(int)$a['id'], 'title'=>$a['title'], 'subtitle'=>'Album', 'cover'=>asset_url($a['cover_path'] ?: DEFAULT_COVER), 'url'=>BASE_URL . 'album.php?id=' . (int)$a['id']], $albumStmt->fetchAll());

$playlistStmt = $pdo->prepare("SELECT id, name, cover_image FROM playlists WHERE is_public = 1 AND name LIKE ? ORDER BY created_at DESC LIMIT 6");
$playlistStmt->execute([$term]);
$playlists = array_map(fn($p) => ['id'=>(int)$p['id'], 'title'=>$p['name'], 'subtitle'=>'Public Playlist', 'cover'=>asset_url($p['cover_image'] ?: DEFAULT_COVER), 'url'=>BASE_URL . 'playlist.php?id=' . (int)$p['id']], $playlistStmt->fetchAll());

json_response(['songs'=>$songs, 'artists'=>$artists, 'albums'=>$albums, 'playlists'=>$playlists]);
?>
