<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) json_response(['ok' => false, 'message' => 'Invalid song id'], 400);

$stmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name, al.title AS album_title, g.name AS genre_name
    FROM songs s
    LEFT JOIN artists ar ON ar.id = s.artist_id
    LEFT JOIN albums al ON al.id = s.album_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.id = ?
    LIMIT 1");
$stmt->execute([$id]);
$song = $stmt->fetch();

if (!$song) json_response(['ok' => false, 'message' => 'Song not found'], 404);
json_response(['ok' => true, 'song' => song_to_api($song)]);
?>
