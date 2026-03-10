<?php
// admin/actions/add_card.php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request';
    header('Location: ../dashboard.php?page=stats');
    exit;
}

$match_id   = (int)($_POST['match_id'] ?? 0);
$player_id  = (int)($_POST['player_id'] ?? 0);
$card_type  = ($_POST['card_type'] ?? '') === 'red' ? 'red' : 'yellow';
$minute     = (int)($_POST['minute'] ?? 0);

if ($match_id <= 0 || $player_id <= 0 || $minute < 1 || $minute > 120) {
    $_SESSION['error'] = 'Invalid card data.';
} else {
    try {
        $stmt = $pdo->prepare("INSERT INTO cards (match_id, player_id, card_type, minute) VALUES (?, ?, ?, ?)");
        $stmt->execute([$match_id, $player_id, $card_type, $minute]);
        $_SESSION['success'] = ucfirst($card_type) . ' card added.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to add card.';
    }
}

header('Location: ../dashboard.php?page=stats');
exit;