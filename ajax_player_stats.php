<?php
require_once 'config.php';
header('Content-Type: text/html; charset=utf-8');

$type  = $_GET['type'] ?? 'league';
$year  = (int)($_GET['year'] ?? date('Y'));
$cs_id = !empty($_GET['cs_id']) ? (int)$_GET['cs_id'] : null;

$start_date = "$year-01-01";
$end_date   = "$year-12-31";

/* ==================================================
   GET CURRENT LEAGUE SEASON ID
   ================================================== */
$leagueSeasonId = null;
if ($type === 'league') {
    $stmt = $pdo->query("SELECT id FROM competition_seasons WHERE is_current = 1 AND type = 'league' LIMIT 1");
    $leagueSeasonId = $stmt->fetchColumn();

    if (!$leagueSeasonId) {
        $stmt = $pdo->prepare("SELECT id FROM competition_seasons WHERE type = 'league' AND season <= ? AND season + 1 >= ? ORDER BY season DESC LIMIT 1");
        $stmt->execute([$year, $year]);
        $leagueSeasonId = $stmt->fetchColumn();
    }

    if (!$leagueSeasonId) {
        echo '<div class="text-center py-5 text-muted"><h5>No active league season found</h5></div>';
        exit;
    }
}

/* ==================================================
   GET PLAYER IDS WITH STATS
   ================================================== */
$playerIds = [];

if ($type === 'league') {
    $sql = "
        SELECT DISTINCT player_id FROM (
            SELECT g.player_id FROM goals g
            JOIN matches m ON g.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?

            UNION
            SELECT a.player_id FROM assists a
            JOIN goals g ON a.goal_id = g.id
            JOIN matches m ON g.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?

            UNION
            SELECT c.player_id FROM cards c
            JOIN matches m ON c.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?

            UNION
            SELECT cs.player_id FROM clean_sheets cs
            JOIN matches m ON cs.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
        ) t
    ";

    $stmt = $pdo->prepare($sql);
    $params = array_fill(0, 4, [$leagueSeasonId, $start_date, $end_date]);
    $params = array_merge(...$params);
    $stmt->execute($params);

} else { // TOURNAMENT
    $csFilter = $cs_id ? "AND tf.competition_season_id = ?" : "";
    $sql = "
        SELECT DISTINCT COALESCE(tg.player_id, ta.player_id, tc.player_id, tcs.player_id) AS player_id
        FROM tournament_fixtures tf
        LEFT JOIN tournament_matches tm ON tm.fixture_id = tf.id
        LEFT JOIN tournament_goals tg ON tg.match_id = tm.id
        LEFT JOIN tournament_assists ta ON ta.goal_id IN (SELECT id FROM tournament_goals WHERE match_id = tm.id)
        LEFT JOIN tournament_cards tc ON tc.match_id = tm.id
        LEFT JOIN tournament_clean_sheets tcs ON tcs.match_id = tm.id
        WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
    ";

    $params = [$start_date, $end_date];
    if ($cs_id) $params[] = $cs_id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

$playerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($playerIds)) {
    echo '<div class="text-center py-5 text-muted">
            <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
            <h5>No player statistics found</h5>
            <p class="text-muted">No recorded actions in the selected period.</p>
          </div>';
    exit;
}

$placeholders = str_repeat('?,', count($playerIds) - 1) . '?';

/* ==================================================
   MAIN QUERY – LEAGUE (accurate)
   ================================================== */
if ($type === 'league') {
    $sql = "
        SELECT 
            p.id, p.name, p.photo, p.position, p.jersey_number,
            c.name AS club_name, c.logo AS club_logo, c.id AS club_id,
            COALESCE(g_stats.goals, 0) AS goals,
            COALESCE(a_stats.assists, 0) AS assists,
            COALESCE(g_stats.goals, 0) + COALESCE(a_stats.assists, 0) AS ga,
            COALESCE(yc.yellow_cards, 0) AS yellow_cards,
            COALESCE(rc.red_cards, 0) AS red_cards,
            COALESCE(cs_stats.clean_sheets, 0) AS clean_sheets
        FROM players p
        LEFT JOIN clubs c ON p.club_id = c.id

        LEFT JOIN (
            SELECT player_id, COUNT(*) AS goals
            FROM goals g JOIN matches m ON g.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
            GROUP BY player_id
        ) g_stats ON g_stats.player_id = p.id

        LEFT JOIN (
            SELECT a.player_id, COUNT(*) AS assists
            FROM assists a
            JOIN goals g ON a.goal_id = g.id
            JOIN matches m ON g.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
            GROUP BY a.player_id
        ) a_stats ON a_stats.player_id = p.id

        LEFT JOIN (
            SELECT player_id, COUNT(*) AS yellow_cards
            FROM cards
            WHERE card_type = 'yellow'
              AND match_id IN (SELECT m.id FROM matches m JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?)
            GROUP BY player_id
        ) yc ON yc.player_id = p.id

        LEFT JOIN (
            SELECT player_id, COUNT(*) AS red_cards
            FROM cards
            WHERE card_type = 'red'
              AND match_id IN (SELECT m.id FROM matches m JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?)
            GROUP BY player_id
        ) rc ON rc.player_id = p.id

        LEFT JOIN (
            SELECT player_id, COUNT(*) AS clean_sheets
            FROM clean_sheets cs
            JOIN matches m ON cs.match_id = m.id
            JOIN fixtures f ON m.fixture_id = f.id
            WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
            GROUP BY player_id
        ) cs_stats ON cs_stats.player_id = p.id

        WHERE p.id IN ($placeholders)
        ORDER BY ga DESC, goals DESC, assists DESC, clean_sheets DESC, p.name ASC
    ";

    $common = [$leagueSeasonId, $start_date, $end_date];
    $params = array_merge($common, $common, $common, $common, $common, $playerIds);

} else {
    /* ==================================================
       MAIN QUERY – TOURNAMENT (fixed & accurate)
       ================================================== */
    $csFilter = $cs_id ? "AND tf.competition_season_id = ?" : "";
    $sql = "
        SELECT 
            p.id, p.name, p.photo, p.position, p.jersey_number,
            c.name AS club_name, c.logo AS club_logo, c.id AS club_id,
            COALESCE(tg.goals, 0) AS goals,
            COALESCE(ta.assists, 0) AS assists,
            COALESCE(tg.goals, 0) + COALESCE(ta.assists, 0) AS ga,
            COALESCE(tc_yc.yellow_cards, 0) AS yellow_cards,
            COALESCE(tc_rc.red_cards, 0) AS red_cards,
            COALESCE(tcs.clean_sheets, 0) AS clean_sheets
        FROM players p
        LEFT JOIN clubs c ON p.club_id = c.id

        LEFT JOIN (
            SELECT tg.player_id, COUNT(*) AS goals
            FROM tournament_goals tg
            JOIN tournament_matches tm ON tg.match_id = tm.id
            JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
            WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
            GROUP BY tg.player_id
        ) tg ON tg.player_id = p.id

        LEFT JOIN (
            SELECT ta.player_id, COUNT(*) AS assists
            FROM tournament_assists ta
            JOIN tournament_goals tg ON ta.goal_id = tg.id
            JOIN tournament_matches tm ON tg.match_id = tm.id
            JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
            WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
            GROUP BY ta.player_id
        ) ta ON ta.player_id = p.id

        LEFT JOIN (
            SELECT tc.player_id, COUNT(*) AS yellow_cards
            FROM tournament_cards tc
            WHERE tc.card_type = 'yellow'
              AND tc.match_id IN (
                SELECT tm.id FROM tournament_matches tm
                JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
                WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
              )
            GROUP BY tc.player_id
        ) tc_yc ON tc_yc.player_id = p.id

        LEFT JOIN (
            SELECT tc.player_id, COUNT(*) AS red_cards
            FROM tournament_cards tc
            WHERE tc.card_type = 'red'
              AND tc.match_id IN (
                SELECT tm.id FROM tournament_matches tm
                JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
                WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
              )
            GROUP BY tc.player_id
        ) tc_rc ON tc_rc.player_id = p.id

        LEFT JOIN (
            SELECT tcs.player_id, COUNT(*) AS clean_sheets
            FROM tournament_clean_sheets tcs
            JOIN tournament_matches tm ON tcs.match_id = tm.id
            JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
            WHERE tf.tournament_date BETWEEN ? AND ? $csFilter
            GROUP BY tcs.player_id
        ) tcs ON tcs.player_id = p.id

        WHERE p.id IN ($placeholders)
        ORDER BY ga DESC, goals DESC, assists DESC, clean_sheets DESC, p.name ASC
    ";

    $params = [$start_date, $end_date];
    if ($cs_id) $params[] = $cs_id;
    $params = array_merge($params, [$start_date, $end_date]);
    if ($cs_id) $params[] = $cs_id;
    $params = array_merge($params, [$start_date, $end_date]);
    if ($cs_id) $params[] = $cs_id;
    $params = array_merge($params, [$start_date, $end_date]);
    if ($cs_id) $params[] = $cs_id;
    $params = array_merge($params, [$start_date, $end_date]);
    if ($cs_id) $params[] = $cs_id;
    $params = array_merge($params, $playerIds);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- TABLE (unchanged) -->
<div class="table-responsive">
    <table class="table table-hover align-middle stats-table">
        <thead class="table-dark">
            <tr>
                <th width="45">#</th>
                <th>Player</th>
                <th width="72" class="text-center">Pos / #</th>
                <th width="72" class="text-center">Club</th>
                <th width="48" class="text-center text-success">G</th>
                <th width="48" class="text-center text-info">A</th>
                <th width="58" class="text-center text-white fw-bold" style="background:#0d6efd;">G/A</th>
                <th width="48" class="text-center text-warning">YC</th>
                <th width="48" class="text-center text-danger">RC</th>
                <th width="48" class="text-center text-primary">CS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $i => $p):
                $photo    = $p['photo'] ? "uploads/players/{$p['photo']}" : "https://via.placeholder.com/40x40?text=" . substr($p['name'],0,2);
                $clubLogo = $p['club_logo'] ? "uploads/clubs/{$p['club_logo']}" : "https://via.placeholder.com/36x36?text=C";
                $jersey   = $p['jersey_number'] ?: '—';
                $pos      = strtoupper(substr($p['position'] ?? 'N/A', 0, 3));
                $ga       = (int)$p['goals'] + (int)$p['assists'];
            ?>
                <tr onclick="openPlayerPanel(<?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>', '<?= addslashes(htmlspecialchars($p['club_name'] ?? '')) ?>', <?= $p['club_id'] ?: 'null' ?>, '<?= $type ?>', <?= $year ?>, <?= $cs_id ?: 'null' ?>)">
                    <td class="rank text-center fw-bold"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= htmlspecialchars($photo) ?>" class="player-thumb" alt="">
                            <div class="fw-semibold" style="font-size:0.94rem;line-height:1.2;">
                                <?= htmlspecialchars($p['name']) ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="pos-jersey">
                            <div class="pos-badge"><?= $pos ?></div>
                            <small class="d-block text-muted fw-bold">#<?= $jersey ?></small>
                        </div>
                    </td>
                    <td class="text-center">
                        <?= $p['club_name'] ? '<img src="'.htmlspecialchars($clubLogo).'" class="club-thumb" alt="">' : '—' ?>
                    </td>
                    <td class="text-center text-success fw-bold"><?= (int)$p['goals'] ?></td>
                    <td class="text-center text-info fw-bold"><?= (int)$p['assists'] ?></td>
                    <td class="text-center text-white fw-bold" style="background:#0d6efd; font-size:1.05rem;">
                        <?= $ga ?: '—' ?>
                    </td>
                    <td class="text-center text-warning fw-bold"><?= (int)$p['yellow_cards'] ?></td>
                    <td class="text-center text-danger fw-bold"><?= (int)$p['red_cards'] ?></td>
                    <td class="text-center text-primary fw-bold">
                        <?= in_array(strtoupper($p['position'] ?? ''), ['GK','GOALKEEPER']) ? (int)$p['clean_sheets'] : '—' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>