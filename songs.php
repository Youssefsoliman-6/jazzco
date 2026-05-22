<?php
require_once __DIR__ . '/includes_admin.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['site_name','tagline','allow_registration'] as $key) {
        $value = trim($_POST[$key] ?? '');
        $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        $stmt->execute([$key, $value]);
    }
    flash('success', 'Settings saved.'); header('Location: settings.php'); exit;
}
$settings = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
admin_header('Settings');
?>
<form class="card" method="post">
    <h1>Website settings</h1>
    <div class="form-grid">
        <div class="input-group"><label>Site name</label><input name="site_name" value="<?= e($settings['site_name'] ?? 'JazzCO') ?>"></div>
        <div class="input-group"><label>Tagline</label><input name="tagline" value="<?= e($settings['tagline'] ?? 'Premium music streaming') ?>"></div>
        <div class="input-group"><label>Allow registration</label><select name="allow_registration"><option value="1" <?= ($settings['allow_registration'] ?? '1')==='1'?'selected':'' ?>>Yes</option><option value="0" <?= ($settings['allow_registration'] ?? '1')==='0'?'selected':'' ?>>No</option></select></div>
        <button class="btn primary">Save settings</button>
    </div>
</form>
<?php admin_footer(); ?>
