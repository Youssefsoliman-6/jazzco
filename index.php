<?php
require_once __DIR__ . '/../includes/functions.php';

function admin_header($title = 'Admin Dashboard') {
    global $pdo;
    require_admin();
    $admin = admin_user($pdo);
    $page = basename($_SERVER['PHP_SELF']);
    $nav = [
        'index.php' => 'Dashboard',
        'songs.php' => 'Songs',
        'artists.php' => 'Artists',
        'albums.php' => 'Albums',
        'users.php' => 'Users',
        'playlists.php' => 'Playlists',
        'genres.php' => 'Genres',
        'settings.php' => 'Settings',
    ];
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . e($title) . ' - JazzCO</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><script>(function(){try{var t=localStorage.getItem("jazzco_theme");if(t==="light")document.documentElement.setAttribute("data-theme","light");}catch(e){}})();</script><link rel="stylesheet" href="' . BASE_URL . 'assets/css/style.css"></head><body><div class="loading-screen hide" id="loadingScreen"></div><header class="site-header glass"><a class="brand" href="' . BASE_URL . 'admin/index.php"><span class="brand-orb"></span>JazzCO Admin</a><nav class="main-nav show"><a href="' . BASE_URL . 'index.php">Website</a><a class="nav-pill" href="' . BASE_URL . 'admin/logout.php">Logout</a></nav><button class="theme-toggle" type="button" data-theme-toggle>Light mode</button></header><main class="page-wrap admin-shell"><aside class="admin-nav glass">';
    foreach ($nav as $href => $label) {
        $active = $page === $href ? ' active' : '';
        echo '<a class="' . $active . '" href="' . $href . '">' . e($label) . '</a>';
    }
    echo '</aside><section>';
}

function admin_footer() {
    echo '</section></main><div class="toast-stack" id="toastStack"></div>';
    foreach (pull_flashes() as $flash) {
        echo '<script>window.__JAZZ_FLASHES__ = window.__JAZZ_FLASHES__ || []; window.__JAZZ_FLASHES__.push(' . json_encode($flash, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');</script>';
    }
    echo '<script src="' . BASE_URL . 'assets/js/app.js"></script></body></html>';
}


function short_text($value, int $limit = 70) {
    $value = (string)$value;
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($value, 0, $limit, '...', 'UTF-8');
    }
    return strlen($value) > $limit ? substr($value, 0, $limit) . '...' : $value;
}

function admin_redirect(string $page) {
    header('Location: ' . $page);
    exit;
}

function admin_upload_image(string $field, string $subdir = 'covers', int $maxMb = 5) {
    return upload_image_file($_FILES[$field] ?? [], $subdir, $maxMb * 1024 * 1024);
}
?>
