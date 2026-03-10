<?php
// admin/includes/stats.php
// Helper functions used in original dashboard.php

function getPlayersWithStats($pdo) {
    $sql = "
        SELECT 
            p.id, p.name, p.jersey_number, p.position, p.photo,
            c.name AS club_name,
            COALESCE(g.goals, 0) AS goals,
            COALESCE(a.assists, 0) AS assists,
            COALESCE(yc.yellow_cards, 0) AS yellow_cards,
            COALESCE(rc.red_cards, 0) AS red_cards,
            COALESCE(cs.clean_sheets, 0) AS clean_sheets
        FROM players p
        JOIN clubs c ON p.club_id = c.id
        LEFT JOIN (
            SELECT player_id, COUNT(*) AS goals
            FROM goals
            GROUP BY player_id
        ) g ON p.id = g.player_id
        LEFT JOIN (
            SELECT player_id, COUNT(*) AS assists
            FROM assists
            GROUP BY player_id
        ) a ON p.id = a.player_id
        LEFT JOIN (
            SELECT player_id, COUNT(*) AS yellow_cards
            FROM cards WHERE card_type = 'yellow'
            GROUP BY player_id
        ) yc ON p.id = yc.player_id
        LEFT JOIN (
            SELECT player_id, COUNT(*) AS red_cards
            FROM cards WHERE card_type = 'red'
            GROUP BY player_id
        ) rc ON p.id = rc.player_id
        LEFT JOIN (
            SELECT 
                CASE 
                    WHEN m.home_score = 0 THEN f.away_club_id
                    WHEN m.away_score = 0 THEN f.home_club_id
                END AS gk_club_id,
                COUNT(*) AS clean_sheets
            FROM matches m
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE m.home_score = 0 OR m.away_score = 0
            GROUP BY gk_club_id
        ) cs ON c.id = cs.gk_club_id
        ORDER BY goals DESC, assists DESC, p.name
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getRecentMatchesWithStats($pdo, $limit = 10) {
    $sql = "
        SELECT 
            m.id,
            m.home_score, m.away_score,
            f.fixture_date,
            ch.name AS home_name, ca.name AS away_name,
            f.home_club_id, f.away_club_id
        FROM matches m
        JOIN fixtures f ON m.fixture_id = f.id
        JOIN clubs ch ON f.home_club_id = ch.id
        JOIN clubs ca ON f.away_club_id = ca.id
        ORDER BY m.match_date DESC
        LIMIT ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($matches as &$match) {
        $match['goal_scorers'] = getMatchGoalScorers($pdo, $match['id']);
        $match['yellow_cards'] = getMatchYellowCards($pdo, $match['id']);
        $match['red_cards'] = getMatchRedCards($pdo, $match['id']);
        $match['home_cs'] = $match['away_score'] == 0;
        $match['away_cs'] = $match['home_score'] == 0;
    }
    return $matches;
}

function getMatchGoalScorers($pdo, $match_id) {
    $stmt = $pdo->prepare("
        SELECT p.name, g.minute, g.is_penalty
        FROM goals g
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

function getMatchYellowCards($pdo, $match_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE match_id = ? AND card_type = 'yellow'");
    $stmt->execute([$match_id]);
    return $stmt->fetchColumn();
}

function getMatchRedCards($pdo, $match_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE match_id = ? AND card_type = 'red'");
    $stmt->execute([$match_id]);
    return $stmt->fetchColumn();
}

function getTournamentGoalScorers($pdo, $match_id) {
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

function getTournamentYellowCards($pdo, $match_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_cards WHERE match_id = ? AND card_type = 'yellow'");
    $stmt->execute([$match_id]);
    return $stmt->fetchColumn();
}

function getTournamentRedCards($pdo, $match_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournament_cards WHERE match_id = ? AND card_type = 'red'");
    $stmt->execute([$match_id]);
    return $stmt->fetchColumn();
}