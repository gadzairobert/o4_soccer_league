<?php
// search.php
header('Content-Type: application/json; charset=utf-8');

// Start session if needed (e.g. for auth) – not required for public search
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------------
// 1. Load DB connection
// ------------------------------------------------------------------
require_once 'config.php';   // <-- make sure $pdo is a valid PDO instance

// ------------------------------------------------------------------
// 2. Get & sanitise the search term
// ------------------------------------------------------------------
$query = trim($_GET['q'] ?? '');
if ($query === '' || mb_strlen($query) < 2) {
    echo json_encode(['players' => [], 'clubs' => []]);
    exit;
}

// Escape the % and _ characters that are special in LIKE
$search = '%' . $pdo->quote($query, PDO::PARAM_STR) . '%';
$search = str_replace(['\\%', '\\_'], ['%', '_'], $search); // un-escape our own wildcards

// ------------------------------------------------------------------
// 3. Search Players (name column)
// ------------------------------------------------------------------
$playerStmt = $pdo->prepare("
    SELECT id, name 
    FROM players 
    WHERE name LIKE ? 
    ORDER BY name 
    LIMIT 15
");
$playerStmt->execute([$search]);
$players = $playerStmt->fetchAll(PDO::FETCH_ASSOC);

// ------------------------------------------------------------------
// 4. Search Clubs (name column)
// ------------------------------------------------------------------
$clubStmt = $pdo->prepare("
    SELECT id, name 
    FROM clubs 
    WHERE name LIKE ? 
    ORDER BY name 
    LIMIT 15
");
$clubStmt->execute([$search]);
$clubs = $clubStmt->fetchAll(PDO::FETCH_ASSOC);

// ------------------------------------------------------------------
// 5. Return JSON
// ------------------------------------------------------------------
echo json_encode([
    'players' => $players,
    'clubs'   => $clubs
]);
exit;
?>