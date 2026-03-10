<?php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$match_id = (int)($_POST['match_id'] ?? 0);
$club_id  = (int)($_POST['club_id'] ?? 0);
$player_id = (int)($_POST['player_id'] ?? 0);

if ($match_id <= 0 || $club_id <= 0) {
    $_SESSION['error'] = 'Invalid data';
    header('Location: ../dashboard.php?page=stats');
    exit;
}

$stmt = $pdo->prepare("
    SELECT m.home_score, m.away_score, f.home_club_id, f.away_club_id
    FROM matches m JOIN fixtures f ON m.fixture_id = f.id WHERE m.id = ?
");
$stmt->execute([$match_id]);
$match = $stmt->fetch();

$conceded = ($club_id == $match['home_club_id']) ? $match['away_score'] : $match['home_score'];

if ($conceded > 0) {
    $_SESSION['error'] = 'Cannot award clean sheet — opponent scored!';
} else {
    if (!$player_id) {
        $stmt = $pdo->prepare("SELECT id FROM players WHERE club_id = ? AND position = 'GK' LIMIT 1");
        $stmt->execute([$club_id]);
        $player_id = $stmt->fetchColumn();
    }
    if ($player_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO clean_sheets (match_id, player_id) VALUES (?, ?)");
        $stmt->execute([$match_id, $player_id]);
        $_SESSION['success'] = 'Clean sheet awarded successfully!';
    } else {
        $_SESSION['error'] = 'No goalkeeper found';
    }
}

header('Location: ../dashboard.php?page=stats');
exit;