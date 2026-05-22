<?php
require_once __DIR__ . '/includes/config.php';

function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function status_badge($ok) { return $ok ? '<span class="ok">OK</span>' : '<span class="bad">Fix needed</span>'; }
function make_dir_if_missing($dir) { if (!is_dir($dir)) @mkdir($dir, 0775, true); @chmod($dir, 0775); }

$uploadDirs = [
    'uploads' => UPLOAD_DIR,
    'uploads/songs' => UPLOAD_DIR . DIRECTORY_SEPARATOR . 'songs',
    'uploads/covers' => UPLOAD_DIR . DIRECTORY_SEPARATOR . 'covers',
    'uploads/artists' => UPLOAD_DIR . DIRECTORY_SEPARATOR . 'artists',
    'uploads/profiles' => UPLOAD_DIR . DIRECTORY_SEPARATOR . 'profiles',
];
foreach ($uploadDirs as $dir) make_dir_if_missing($dir);

$dbOk = false;
$dbMsg = '';
try {
    $pdoTest = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $dbOk = true;
    $dbMsg = 'Connected to ' . DB_NAME;
} catch (Throwable $e) {
    $dbMsg = 'Database connection failed. Import database.sql and check includes/config.php.';
}

$uploadSettingsOk = filter_var(ini_get('file_uploads'), FILTER_VALIDATE_BOOLEAN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JazzCO Setup Check</title>
<style>
:root{color-scheme:dark;--bg:#090811;--card:#161425;--text:#f6f0ff;--muted:#b9accf;--gold:#ffd166;--purple:#8b5cf6;--bad:#ff6b6b;--ok:#5ef0a4}*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at top left,#28185f,transparent 35%),var(--bg);font-family:Inter,Arial,sans-serif;color:var(--text);padding:32px}.wrap{max-width:980px;margin:auto}.card{background:rgba(22,20,37,.78);border:1px solid rgba(255,255,255,.12);border-radius:22px;padding:22px;margin:18px 0;box-shadow:0 18px 60px rgba(0,0,0,.3)}h1{font-size:36px;margin:0 0 8px}h2{margin-top:0}.muted{color:var(--muted)}table{width:100%;border-collapse:collapse;overflow:hidden;border-radius:16px}td,th{padding:14px;border-bottom:1px solid rgba(255,255,255,.08);text-align:left}code{background:#080710;border:1px solid rgba(255,255,255,.12);padding:3px 6px;border-radius:7px}.ok{color:#05140c;background:var(--ok);padding:5px 9px;border-radius:999px;font-weight:800}.bad{color:#200;background:var(--bad);padding:5px 9px;border-radius:999px;font-weight:800}.cmd{display:block;white-space:pre-wrap;background:#080710;border-radius:14px;padding:16px;border:1px solid rgba(255,255,255,.12);color:var(--gold)}a{color:var(--gold)}</style>
</head>
<body>
<div class="wrap">
    <h1>JazzCO Setup Check</h1>
    <p class="muted">Use this page on Mac/Windows XAMPP to diagnose upload problems.</p>

    <div class="card">
        <h2>PHP upload settings</h2>
        <table>
            <tr><th>Setting</th><th>Value</th><th>Status</th></tr>
            <tr><td>file_uploads</td><td><?= h(ini_get('file_uploads')) ?></td><td><?= status_badge($uploadSettingsOk) ?></td></tr>
            <tr><td>upload_max_filesize</td><td><?= h(ini_get('upload_max_filesize')) ?></td><td><?= status_badge(true) ?></td></tr>
            <tr><td>post_max_size</td><td><?= h(ini_get('post_max_size')) ?></td><td><?= status_badge(true) ?></td></tr>
            <tr><td>max_file_uploads</td><td><?= h(ini_get('max_file_uploads')) ?></td><td><?= status_badge(true) ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h2>Upload folder permissions</h2>
        <table>
            <tr><th>Folder</th><th>Server path</th><th>Status</th></tr>
            <?php foreach ($uploadDirs as $label => $dir): $ok = is_dir($dir) && is_writable($dir); ?>
            <tr><td><?= h($label) ?></td><td><code><?= h($dir) ?></code></td><td><?= status_badge($ok) ?></td></tr>
            <?php endforeach; ?>
        </table>
        <p class="muted">If any folder says “Fix needed” on macOS XAMPP, run this in Terminal:</p>
        <code class="cmd">chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/jazzco/uploads</code>
    </div>

    <div class="card">
        <h2>Database</h2>
        <p><?= status_badge($dbOk) ?> <?= h($dbMsg) ?></p>
    </div>

    <div class="card">
        <h2>Recommended Mac XAMPP php.ini values</h2>
        <p class="muted">If uploads fail for large songs, open <code>/Applications/XAMPP/xamppfiles/etc/php.ini</code>, set these values, then restart Apache:</p>
        <code class="cmd">file_uploads = On
upload_max_filesize = 100M
post_max_size = 120M
max_execution_time = 300
memory_limit = 256M</code>
    </div>

    <p><a href="<?= h(BASE_URL) ?>admin/songs.php">Back to Admin Songs</a></p>
</div>
</body>
</html>
