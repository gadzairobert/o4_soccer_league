<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$match_id   = (int)($_POST['match_id'] ?? 0);
$player_id  = (int)($_POST['player_id'] ?? 0);
$card_type  = ($_POST['card_type'] ?? '') === 'red' ? 'red' : 'yellow';
$minute     = (int)($_POST['minute'] ?? 0);

if ($match_id && $player_id && $minute >= 1 && $minute <= 120) {
    $stmt = $pdo->prepare("INSERT INTO tournament_cards (match_id, player_id, card_type, minute) VALUES (?, ?, ?, ?)");
    $stmt->execute([$match_id, $player_id, $card_type, $minute]);
    $_SESSION['success'] = ucfirst($card_type) . ' card added to tournament match!';
} else {
    $_SESSION['error'] = 'Invalid card data';
}
header('Location: ../dashboard.php?page=tournament_stats');
exit;