<?php
require 'config.php';
$club_id = $_GET['club_id'] ?? 0;
$players = $pdo->prepare("SELECT * FROM players WHERE club_id = ? ORDER BY position");
$players->execute([$club_id]);
?>
<!-- Loop display cards with photo, stats: Goals: <?= $p['goals'] ?>, Assists: <?= $p['assists'] ?>, etc. -->
<!-- For individual: Link to player.php?id=1, fetch goals/assists/cards via joins -->