<?php
// admin/includes/tournament_stats.php
// FULLY COMPATIBLE with ALL MySQL/MariaDB versions (no LIMIT ? bug)

function getRecentTournamentMatchesWithStats($pdo, $limit = 20) {
    // Cast limit to integer and inject safely using sprintf + intval
    $limit = (int)$limit;
    if ($limit < 1) $limit = 20;
    if ($limit > 100) $limit = 100;

    $sql = "
        SELECT 
            tm.id AS match_id,
            tm.home_score, 
            tm.away_score,
            tf.tournament_date,
            ch.name AS home_name, 
            ca.name AS away_name,
            tf.home_club_id, 
            tf.away_club_id
        FROM tournament_matches tm
        JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
        JOIN clubs ch ON tf.home_club_id = ch.id
        JOIN clubs ca ON tf.away_club_id = ca.id
        ORDER BY tm.match_date DESC
        LIMIT $limit
    ";

    $stmt = $pdo->query($sql);  // Safe because $limit is cast to int
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($matches as &$match) {
        $match['goal_scorers'] = getTournamentMatchGoalScorers($pdo, $match['match_id']);
        $match['yellow_cards'] = getTournamentMatchCards($pdo, $match['match_id'], 'yellow');
        $match['red_cards']    = getTournamentMatchCards($pdo, $match['match_id'], 'red');
        $match['home_cs']      = $match['away_score'] == 0;
        $match['away_cs']      = $match['home_score'] == 0;
    }
    unset($match);

    return $matches;
}

function getTournamentMatchGoalScorers($pdo, $match_id) {
    $stmt = $pdo->prepare("
        SELECT p.name, g.minute, g.is_penalty
        FROM tournament_goals g
        JOIN players p ON g.player_id = p.id
        WHERE g.match_id = ?
        ORDER BY g.minute
    ");
    $stmt->execute([$match_id]);
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $scorers = [];
    foreach ($goals as $g) {
        $scorers[] = $g['name'] . ($g['is_penalty'] ? ' (P)' : '') . " {$g['minute']}'";
    }
    return implode(', ', $scorers) ?: '—';
}

function getTournamentMatchCards($pdo, $match_id, $type = 'yellow') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_cards WHERE match_id = ? AND card_type = ?");
    $stmt->execute([$match_id, $type]);
    return (int)$stmt->fetchColumn();
}
?>