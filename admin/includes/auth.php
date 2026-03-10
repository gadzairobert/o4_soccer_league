<?php
// admin/includes/auth.php
// Include this in EVERY admin page (after config.php)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FORCE LOGIN - If not logged in → go to login
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// 30 MINUTE IDLE TIMEOUT
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// PREVENT ALL CACHING (CRITICAL)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Session regeneration for security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}
?>