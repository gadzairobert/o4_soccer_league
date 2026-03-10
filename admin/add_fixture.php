<?php
// ... (Similar to add_player, fetch clubs for home/away dropdowns)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $home_id = $_POST['home_club_id'];
    $away_id = $_POST['away_club_id'];
    $date = $_POST['fixture_date'];
    $venue = $_POST['venue'];
    $stmt = $pdo->prepare("INSERT INTO fixtures (home_club_id, away_club_id, fixture_date, venue) VALUES (?, ?, ?, ?)");
    $stmt->execute([$home_id, $away_id, $date, $venue]);
    // ...
}
?>
<!-- Form with selects for clubs, date input, venue text -->