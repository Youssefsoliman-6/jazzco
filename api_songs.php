<?php
require_once __DIR__ . '/includes/functions.php';

$ids = trim($_GET['ids'] ?? '');
if ($ids !== '') {
    $rawIds = array_filter(array_map('intval', explode(',', $ids)), fn($id) => $id > 0);
    $rawIds = array_values(array_unique($rawIds));
    if (!$rawIds) json_response(['songs' => [], 'has_more' => false]);

    $placeholders = implode(',', array_fill(0, count($rawIds), '?'));
    $stmt = $pdo->prepare("SELECT s.*, ar.name AS artist_name, al.title AS album_title, g.name AS genre_name
        FROM songs s
        LEFT JOIN artists ar ON ar.id = s.artist_id
        LEFT JOIN albums al ON al.id = s.album_id
        LEFT JOIN genres g ON g.id = s.genre_id
        WHERE s.id IN ($placeholders)");
    $stmt->execute($rawIds);
    $rows = $stmt->fetchAll();
    $byId = [];
    foreach ($rows as $row) $byId[(int)$row['id']] = $row;
    $ordered = [];
    foreach ($rawIds as $id) if (isset($byId[$id])) $ordered[] = song_to_api($byId[$id]);
    json_response(['songs' => $ordered, 'has_more' => false]);
}

$limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
$offset = max(0, (int)($_GET['offset'] ?? 0));
$q = trim($_GET['q'] ?? '');
$songs = get_songs($pdo, $limit + 1, $offset, $q);
$hasMore = count($songs) > $limit;
$songs = array_slice($songs, 0, $limit);
json_response(['songs' => array_map('song_to_api', $songs), 'has_more' => $hasMore]);
?>
