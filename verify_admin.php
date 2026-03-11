<?php
/**
 * verify_admin.php
 * Accepts POST: username, password
 * Returns JSON: { success: true, token: "..." } or { success: false, message: "..." }
 * Token is a short-lived signed session token stored in $_SESSION
 */
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

// Rate limiting: max 5 attempts per minute per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rateKey = 'admin_verify_' . md5($ip);
if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'first' => time()];
}
$rate = &$_SESSION[$rateKey];
if (time() - $rate['first'] > 60) {
    $rate = ['count' => 0, 'first' => time()];
}
$rate['count']++;
if ($rate['count'] > 5) {
    echo json_encode(['success' => false, 'message' => 'Too many attempts. Please wait a minute.']);
    exit;
}

// Look up admin user
try {
    $stmt = $pdo->prepare(
        "SELECT id, password FROM users WHERE (username = ? OR email = ?) AND role = 'admin' LIMIT 1"
    );
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials or insufficient permissions.']);
    exit;
}

// Verify password — supports both password_hash() and plain MD5 legacy
$valid = false;
if (password_verify($password, $user['password'])) {
    $valid = true;
} elseif (md5($password) === $user['password']) {
    $valid = true;
} elseif ($password === $user['password']) {
    // plain text fallback (legacy)
    $valid = true;
}

if (!$valid) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials or insufficient permissions.']);
    exit;
}

// Issue a short-lived token (valid 5 minutes)
$token = bin2hex(random_bytes(24));
$_SESSION['admin_card_token']    = $token;
$_SESSION['admin_card_token_ts'] = time();
// Reset rate limit on success
$rate = ['count' => 0, 'first' => time()];

echo json_encode(['success' => true, 'token' => $token]);
exit;