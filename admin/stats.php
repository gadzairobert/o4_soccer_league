<?php
// admin/stats.php - Reusable functions for player and match stats

function getPlayersWithStats($pdo) {
    $sql = "
        SELECT 
            p.*, 
            c.name as club_name,
            COALESCE((SELECT COUNT(*) FROM goals g WHERE g.player_id = p.id), 0) as goals,
            COALESCE((SELECT COUNT(*) FROM assists a WHERE a.player_id = p.id), 0) as assists,
            COALESCE((SELECT COUNT(*) FROM cards c WHERE c.player_id = p.id AND c.card_type = 'yellow'), 0) as yellow_cards,
            COALESCE((SELECT COUNT(*) FROM cards c WHERE c.player_id = p.id AND c.card_type = 'red'), 0) as red_cards,
            CASE 
                WHEN p.position = 'GK' THEN 
                    COALESCE((
                        SELECT COUNT(DISTINCT m.id) 
                        FROM matches m 
                        JOIN fixtures f ON m.fixture_id = f.id 
                        WHERE ((f.home_club_id = p.club_id AND m.away_score = 0) OR 
                               (f.away_club_id = p.club_id AND m.home_score = 0))
                    ), 0) 
                ELSE 0 
            END as clean_sheets
        FROM players p 
        JOIN clubs c ON p.club_id = c.id 
        ORDER BY c.name, p.position, p.jersey_number
    ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchGoalScorers($pdo, $match_id) {
    $sql = "
        SELECT 
            p.name as scorer, 
            g.minute, 
            g.is_penalty, 
            pa.name as assister
        FROM goals g 
        JOIN players p ON g.player_id = p.id 
        LEFT JOIN assists a ON a.goal_id = g.id 
        LEFT JOIN players pa ON a.player_id = pa.id 
        WHERE g.match_id = ? 
        ORDER BY g.minute ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$match_id]);
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $scorers_list = [];
    foreach ($goals as $goal) {
        $str = $goal['scorer'];
        if ($goal['minute'] !== null) {
            $str .= ' (' . $goal['minute'] . ')';
        }
        if ($goal['is_penalty']) {
            $str .= ' PEN';
        }
        if (!empty($goal['assister'])) {
            $str .= ' ass. ' . $goal['assister'];
        }
        $scorers_list[] = $str;
    }
    return !empty($scorers_list) ? implode(', ', $scorers_list) : 'No goals';
}

// Tournament-specific goal scorers (no assists for now)
function getTournamentGoalScorers($pdo, $match_id) {
    $sql = "
        SELECT 
            p.name as scorer, 
            g.minute, 
            g.is_penalty
        FROM tournament_goals g 
        JOIN players p ON g.player_id = p.id 
        WHERE g.match_id = ? 
        ORDER BY g.minute ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$match_id]);
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $scorers_list = [];
    foreach ($goals as $goal) {
        $str = $goal['scorer'];
        if ($goal['minute'] !== null) {
            $str .= ' (' . $goal['minute'] . ')';
        }
        if ($goal['is_penalty']) {
            $str .= ' PEN';
        }
        $scorers_list[] = $str;
    }
    return !empty($scorers_list) ? implode(', ', $scorers_list) : 'No goals';
}

function getMatchCardCount($pdo, $match_id, $card_type) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM cards 
        WHERE match_id = ? AND card_type = ?
    ");
    $stmt->execute([$match_id, $card_type]);
    return (int) $stmt->fetchColumn();
}

function getTournamentCardCount($pdo, $match_id, $card_type) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM tournament_cards 
        WHERE match_id = ? AND card_type = ?
    ");
    $stmt->execute([$match_id, $card_type]);
    return (int) $stmt->fetchColumn();
}

function getMatchYellowCards($pdo, $match_id) {
    return getMatchCardCount($pdo, $match_id, 'yellow');
}

function getMatchRedCards($pdo, $match_id) {
    return getMatchCardCount($pdo, $match_id, 'red');
}

function getTournamentYellowCards($pdo, $match_id) {
    return getTournamentCardCount($pdo, $match_id, 'yellow');
}

function getTournamentRedCards($pdo, $match_id) {
    return getTournamentCardCount($pdo, $match_id, 'red');
}

// Optional: Dedicated stats page data fetcher (for ?page=stats) - league only for now
function getRecentMatchesWithStats($pdo, $limit = 10) {
    $sql = "
        SELECT m.*, f.fixture_date, ch.name as home_name, ca.name as away_name, f.home_club_id, f.away_club_id 
        FROM matches m 
        JOIN fixtures f ON m.fixture_id = f.id 
        JOIN clubs ch ON f.home_club_id = ch.id 
        JOIN clubs ca ON f.away_club_id = ca.id 
        ORDER BY m.match_date DESC 
        LIMIT ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($matches as &$match) {
        $match['goal_scorers'] = getMatchGoalScorers($pdo, $match['id']);
        $match['yellow_cards'] = getMatchYellowCards($pdo, $match['id']);
        $match['red_cards'] = getMatchRedCards($pdo, $match['id']);
        $match['home_cs'] = $match['away_score'] == 0 ? 'Yes' : 'No';
        $match['away_cs'] = $match['home_score'] == 0 ? 'Yes' : 'No';
    }
    return $matches;
}
?>