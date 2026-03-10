<?php
// ajax_tournament_home.php - FINAL WITH RESTRUCTURED RESULTS LIKE IMAGE

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/config.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    echo '<p class="text-center text-muted py-4">Database error</p>';
    exit;
}

$type = $_GET['type'] ?? '';
$cs_id = (int)($_GET['cs_id'] ?? 0);

if (!in_array($type, ['fixtures', 'results', 'table'])) {
    echo '<p class="text-center text-muted py-4">Invalid type</p>';
    exit;
}

$where = "1=1";
$params = [];

if ($cs_id > 0) {
    $stmt = $pdo->prepare("SELECT 1 FROM competition_seasons WHERE id = ? AND type = 'cup'");
    $stmt->execute([$cs_id]);
    if (!$stmt->fetchColumn()) {
        echo '<p class="text-center text-muted py-4">Invalid tournament</p>';
        exit;
    }
    $where .= " AND tf.competition_season_id = ?";
    $params[] = $cs_id;
}

$club_exclude = " AND h.name NOT IN ('Loser 1','Loser 2','Winner 1','Winner 2') AND a.name NOT IN ('Loser 1','Loser 2','Winner 1','Winner 2')";

if ($type === 'fixtures') {
    // (Unchanged - already perfect)
    $sql = "
        SELECT tf.tournament_date, tf.venue, 
               h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id,
               a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
        FROM tournament_fixtures tf 
        JOIN clubs h ON tf.home_club_id = h.id 
        JOIN clubs a ON tf.away_club_id = a.id
        WHERE $where AND LOWER(tf.status) = 'scheduled' $club_exclude
        ORDER BY tf.tournament_date ASC 
        LIMIT 10
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        echo '<p class="text-center text-muted py-4">No upcoming fixtures</p>';
    } else {
        foreach ($items as $f):
            $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/44/2c3e50/white?text=".substr($f['home_name'],0,2);
            $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/44/2c3e50/white?text=".substr($f['away_name'],0,2);
            $dateStr = date('D, j M', strtotime($f['tournament_date']));
            $venue = $f['venue'] ?? 'TBD';
            ?>
            <div class="mini-match">
                <div class="mini-match-row">
                    <div class="mini-teams">
                        <div class="mini-team">
                            <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="<?= $homeLogo ?>" class="mini-logo" alt="">
                                <div class="mini-name"><?= htmlspecialchars($f['home_name']) ?></div>
                            </a>
                        </div>
                        <div class="mini-team">
                            <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="<?= $awayLogo ?>" class="mini-logo" alt="">
                                <div class="mini-name"><?= htmlspecialchars($f['away_name']) ?></div>
                            </a>
                        </div>
                    </div>
                    <div class="mini-info">
                        <?= $dateStr ?><br>
                        <small style="font-weight:400;opacity:0.9;"><?= htmlspecialchars($venue) ?></small>
                    </div>
                </div>
            </div>
            <?php
        endforeach;
    }

} elseif ($type === 'results') {
    $sql = "
        SELECT tm.home_score, tm.away_score, tm.match_date,
               h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id,
               a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
        FROM tournament_matches tm 
        JOIN tournament_fixtures tf ON tm.fixture_id = tf.id 
        JOIN clubs h ON tf.home_club_id = h.id 
        JOIN clubs a ON tf.away_club_id = a.id
        WHERE $where AND tm.home_score IS NOT NULL AND tm.away_score IS NOT NULL $club_exclude
        ORDER BY tm.match_date DESC 
        LIMIT 10
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        echo '<p class="text-center text-muted py-4">No results yet</p>';
    } else {
        foreach ($items as $r):
            $homeWin = $r['home_score'] > $r['away_score'];
            $awayWin = $r['away_score'] > $r['home_score'];
            $draw = $r['home_score'] == $r['away_score'];
            $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/44/2c3e50/white?text=".substr($r['home_name'],0,2);
            $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/44/2c3e50/white?text=".substr($r['away_name'],0,2);
            $dateStr = date('D, j M', strtotime($r['match_date']));
            ?>
            <div class="mini-match">
                <div class="mini-match-row">
                    <div class="mini-teams">
                        <div class="mini-team">
                            <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="<?= $homeLogo ?>" class="mini-logo" alt="">
                                <div class="mini-name <?= $homeWin ? 'mini-winner' : '' ?>">
                                    <?= htmlspecialchars($r['home_name']) ?>
                                </div>
                            </a>
                        </div>
                        <div class="mini-team">
                            <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="<?= $awayLogo ?>" class="mini-logo" alt="">
                                <div class="mini-name <?= $awayWin ? 'mini-winner' : '' ?>">
                                    <?= htmlspecialchars($r['away_name']) ?>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="mini-score">
                        <span class="mini-home-score"><?= $r['home_score'] ?></span>
                        <span class="mini-away-score"><?= $r['away_score'] ?></span>
                    </div>
                    <div class="mini-info">
                        FT<br>
                        <small style="font-weight:600;opacity:0.9;"><?= $dateStr ?></small>
                    </div>
                </div>
            </div>
            <?php
        endforeach;
    }

} elseif ($type === 'table') {
    // (Unchanged - already perfect with clickable clubs)
    $sql = "
        SELECT c.id, c.name AS club, c.logo,
               COUNT(tm.id) AS played,
               SUM(CASE WHEN (tf.home_club_id = c.id AND tm.home_score > tm.away_score) OR (tf.away_club_id = c.id AND tm.away_score > tm.home_score) THEN 1 ELSE 0 END) AS wins,
               SUM(CASE WHEN tm.home_score = tm.away_score THEN 1 ELSE 0 END) AS draws,
               SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.home_score,0) ELSE COALESCE(tm.away_score,0) END) AS gf,
               SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.away_score,0) ELSE COALESCE(tm.home_score,0) END) AS ga,
               SUM(CASE WHEN (tf.home_club_id = c.id AND tm.home_score > tm.away_score) OR (tf.away_club_id = c.id AND tm.away_score > tm.home_score) THEN 3 WHEN tm.home_score = tm.away_score THEN 1 ELSE 0 END) AS points,
               (SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.home_score,0) ELSE COALESCE(tm.away_score,0) END) - 
                SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.away_score,0) ELSE COALESCE(tm.home_score,0) END)) AS gd
        FROM tournament_fixtures tf
        JOIN clubs c ON c.id IN (tf.home_club_id, tf.away_club_id)
        LEFT JOIN tournament_matches tm ON tm.fixture_id = tf.id AND tm.home_score IS NOT NULL
        WHERE c.name NOT IN ('Loser 1','Loser 2','Winner 1', 'Winner 2')
          AND $where
        GROUP BY c.id
        HAVING played > 0
        ORDER BY points DESC, gd DESC, gf DESC, c.name ASC
        LIMIT 15
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo '<p class="text-center text-muted py-4">No matches played yet</p>';
    } else {
        ?>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="pos">#</th>
                    <th class="text-start ps-3">Club</th>
                    <th class="narrow">P</th>
                    <th class="narrow">W</th>
                    <th class="narrow">GD</th>
                    <th class="points"><strong>PTS</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php $pos = 1; foreach ($rows as $row):
                    $gd = $row['gd'];
                    $logo = $row['logo'] ? 'uploads/clubs/'.$row['logo'] : 'https://via.placeholder.com/40?text='.substr($row['club'],0,2);
                ?>
                    <tr>
                        <td class="pos"><?= $pos++ ?></td>
                        <td class="club-cell">
                            <a href="clubs.php?club_id=<?= $row['id'] ?>" class="text-decoration-none d-flex align-items-center gap-2">
                                <img src="<?= $logo ?>" class="club-logo" alt="">
                                <span class="club-name"><?= htmlspecialchars($row['club']) ?></span>
                            </a>
                        </td>
                        <td class="narrow"><?= $row['played'] ?></td>
                        <td class="narrow"><?= $row['wins'] ?></td>
                        <td class="narrow"><?= $gd >= 0 ? '+'.$gd : $gd ?></td>
                        <td class="points"><?= $row['points'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
}
?>