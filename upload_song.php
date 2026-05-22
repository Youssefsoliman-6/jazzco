<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok'=>false, 'message'=>'POST required'], 405);
    assert_post_size_not_exceeded();
    $title = trim($_POST['title'] ?? '');
    $artistId = (int)($_POST['artist_id'] ?? 0);
    $albumId = (int)($_POST['album_id'] ?? 0) ?: null;
    $genreId = (int)($_POST['genre_id'] ?? 0) ?: null;
    $duration = max(0, (int)($_POST['duration_seconds'] ?? 0));
    $trending = !empty($_POST['is_trending']) ? 1 : 0;
    if ($title === '' || $artistId <= 0) throw new RuntimeException('Title and artist are required.');
    $songPath = upload_audio_file($_FILES['mp3_file'] ?? [], 'songs', 50 * 1024 * 1024);
    if (!$songPath) throw new RuntimeException('MP3 file is required.');
    $coverPath = upload_image_file($_FILES['cover_image'] ?? [], 'covers', 5 * 1024 * 1024);
    $stmt = $pdo->prepare('INSERT INTO songs (title, artist_id, album_id, genre_id, file_path, cover_path, duration_seconds, is_trending) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$title, $artistId, $albumId, $genreId, $songPath, $coverPath, $duration, $trending]);
    json_response(['ok'=>true, 'message'=>'Song uploaded successfully.']);
} catch (Throwable $e) {
    json_response(['ok'=>false, 'message'=>$e->getMessage()], 400);
}
?>
