<?php
require_once __DIR__ . '/includes/functions.php';
$user = current_user($pdo);
if (!$user) json_response(['ok'=>false, 'message'=>'Login required'], 401);
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$songId = (int)($input['song_id'] ?? 0);
if ($songId <= 0) json_response(['ok'=>false, 'message'=>'Invalid song'], 400);
$stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id=? AND song_id=? LIMIT 1');
$stmt->execute([$user['id'], $songId]);
$existing = $stmt->fetch();
if ($existing) {
    $pdo->prepare('DELETE FROM favorites WHERE id=?')->execute([$existing['id']]);
    json_response(['ok'=>true, 'favorited'=>false]);
} else {
    $pdo->prepare('INSERT INTO favorites (user_id, song_id) VALUES (?, ?)')->execute([$user['id'], $songId]);
    json_response(['ok'=>true, 'favorited'=>true]);
}
?>
