<?php
require_once __DIR__ . '/includes/functions.php';
$user = current_user($pdo);
if (!$user) json_response(['ok'=>false, 'message'=>'Login required'], 401);
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$songId = (int)($input['song_id'] ?? 0);
if ($songId <= 0) json_response(['ok'=>false, 'message'=>'Invalid song'], 400);
$pdo->prepare('INSERT INTO recently_played (user_id, song_id, played_at) VALUES (?, ?, NOW())')->execute([$user['id'], $songId]);
$pdo->prepare('UPDATE songs SET plays = plays + 1 WHERE id = ?')->execute([$songId]);
json_response(['ok'=>true]);
?>
