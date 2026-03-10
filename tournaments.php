<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
echo '<style>.navbar-brand img { width: 50px !important; height: 50px !important; object-fit: contain; }</style>';
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND - CONSISTENT WITH OTHER PAGES */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }
    .tournament-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }
    /* Tournament Header */
    .tournament-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.6rem 1.8rem;
        text-align: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .tournament-header h1 {
        margin: 0;
        font-size: 2.4rem;
        font-weight: 900;
        letter-spacing: 1px;
        text-shadow: 0 3px 10px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    .tournament-header img {
        width: 72px; height: 72px; object-fit: contain; background: white; padding: 10px;
        border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); border: 4px solid rgba(255,255,255,0.3);
    }
    /* Section Cards */
    .section-card {
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    /* Section Header */
    .section-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 0.9rem 1.4rem;
        font-size: 1.15rem;
        font-weight: 600;
    }
    /* Tabs */
    .tab {
        overflow: hidden;
        background: #1a2530;
        display: flex;
        flex-wrap: nowrap;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .tab button {
        background: transparent;
        border: none;
        color: #bdc3c7;
        padding: 14px 20px;
        font-size: 1.02rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
        white-space: nowrap;
        position: relative;
    }
    .tab button:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 25%; height: 50%; width: 1px; background: rgba(255,255,255,0.2);
    }
    .tab button:hover { background: #34495e; color: white; }
    .tab button.active { background: #0d6efd; color: white; box-shadow: inset 0 -5px 0 #0b5ed7; font-weight: 700; }
    .tabcontent {
        display: none;
        padding: 1.2rem;
        background: #ffffff;
        flex-grow: 1;
        overflow-y: auto;
    }
    .tabcontent.active { display: block; }
    /* Match Items */
    .match-item {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        transition: background 0.3s;
    }
    .match-item:hover { background: #e9ecef; }
    .match-item:last-child { border-bottom: none; }
    .match-row {
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 64px;
    }
    .teams-stack {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        padding-right: 14px;
        position: relative;
    }
    .teams-stack::after {
        content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
        width: 1px; height: 44px; background: #ccc;
    }
    .team-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .team-logo {
        width: 36px; height: 36px; object-fit: contain; background: white; padding: 3px;
        border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.15); border: 1px solid #ccc;
    }
    .team-name {
        font-weight: 600; font-size: 0.95rem; color: #2c3e50;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .team-name:hover { color: #0d6efd; }
    .winner { color: #27ae60 !important; font-weight: 700 !important; }
    .fixture-info-stack, .score-stack {
        text-align: right;
        min-width: 100px;
        font-size: 0.86rem;
        color: #6c757d;
    }
    .fixture-date { font-weight: 600; color: #2c3e50; }
    .score-stack {
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: center;
    }
    .home-score, .away-score {
        font-size: 1.7rem;
        font-weight: bold;
        line-height: 1;
        color: #2c3e50;
    }
    .match-toggle {
        padding: 0.55rem 1.2rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-size: 0.86rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #6c757d;
    }
    .match-toggle:hover { background: #e9ecef; }
    /* Events */
    .event-list {
        background: #f8f9fa;
        padding: 0.8rem 1.2rem;
        font-size: 0.84rem;
        max-height: 280px;
        overflow-y: auto;
        border-top: 1px solid #dee2e6;
    }
    .event-row {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        padding: 4px 0;
        color: #2c3e50;
    }
    .event-team.home { justify-content: flex-start; }
    .event-team.away { justify-content: flex-end; }
    .event-icon {
        width: 26px; height: 26px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; color: white; font-weight: bold; flex-shrink: 0;
    }
    .goal-icon { background: #0111c0; }
    .assist-icon { background: #f39c12; color: #000; }
    .yellow-icon { background: #f1c40f; color: #000; }
    .red-icon { background: #c0392b; }
    .cleansheet-icon { background: #27ae60; }
    .event-detail {
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0 8px;
        color: #2c3e50;
    }
    /* TABLES */
    .table,
    .top-scorers-table {
        background: #ffffff;
        color: #333333;
    }
    .table thead th,
    .top-scorers-table thead th {
        background: #1a2530 !important;
        color: white !important;
    }
    .table tbody td,
    .top-scorers-table tbody td {
        color: #2c3e50 !important;
    }
    .table tbody tr,
    .top-scorers-table tbody tr {
        background: #f8f9fa !important;
    }
    .table tbody tr:hover,
    .top-scorers-table tbody tr:hover {
        background: #e9ecef !important;
    }
    .table a,
    .top-scorers-table a {
        color: #2c3e50 !important;
    }
    .table a:hover,
    .top-scorers-table a:hover {
        color: #0d6efd !important;
    }
    .player-thumb, .club-logo {
        width: 32px; height: 32px; object-fit: cover; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .club-logo { border-radius: 8px; }
    /* Gallery */
    .gallery-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.6rem;
        text-align: center;
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: 1px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        margin: 1.5rem 0 1rem 0;
    }
    .tournament-gallery {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.2rem;
        padding: 1rem 0;
    }
    .gallery-item {
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all .4s ease;
        aspect-ratio: 1/1;
        cursor: pointer;
    }
    .gallery-item:hover {
        transform: translateY(-10px) scale(1.04);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    .gallery-item img {
        width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease;
    }
    .gallery-item:hover img { transform: scale(1.15); }
    /* Empty states */
    .text-muted { color: #6c757d !important; }
    /* Responsive */
    @media (max-width: 992px) {
        .row.g-4[style*="grid"] { grid-template-columns: 1fr 1fr !important; gap:1.2rem; }
        .tournament-gallery { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 768px) {
        .row.g-4[style*="grid"] { grid-template-columns: 1fr !important; gap:1rem; }
        .tournament-gallery { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 576px) {
        .tournament-header h1 { font-size: 1.9rem; flex-direction: column; gap: 0.8rem; }
        .tournament-header img { width: 60px; height: 60px; }
        .tab button { padding: 12px 10px; font-size: 0.95rem; }
        .tournament-gallery { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .gallery-header { font-size: 1.6rem; padding: 1.2rem; }
    }
</style>
<div class="main-content">
    <div class="container tournament-page-wrapper">
<?php
// Get selected cup ID from URL
$selectedCupId = isset($_GET['cup_id']) ? (int)$_GET['cup_id'] : null;
// Fetch tournaments - either one or all active cups
if ($selectedCupId) {
    $stmt = $pdo->prepare("
        SELECT id, name, short_name, competition_name, season, logo, is_current
        FROM competition_seasons
        WHERE id = ? AND type = 'cup'
    ");
    $stmt->execute([$selectedCupId]);
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($tournaments)) {
        die('<div class="container py-5 text-center"><h3>Tournament not found or not active.</h3></div>');
    }
} else {
    $stmt = $pdo->query("
        SELECT id, name, short_name, competition_name, season, logo, is_current
        FROM competition_seasons
        WHERE type = 'cup'
        ORDER BY is_current DESC, season DESC
    ");
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
foreach ($tournaments as $cup):
    $cupId = $cup['id'];
    $cupName = $cup['name'] ?: $cup['competition_name'] . ' ' . $cup['season'];
    $logoPath = $cup['logo'] ? 'uploads/competitions/' . $cup['logo'] : null;
    // Skip if not current and we're in single mode
    if ($selectedCupId && !$cup['is_current']) continue;
?>
<!-- Tournament Header -->
<div class="tournament-header">
    <h1>
        <?php if ($logoPath): ?>
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars($cupName) ?>">
        <?php endif; ?>
        <?= htmlspecialchars($cupName) ?>
        <?php if ($cup['is_current']): ?><?php endif; ?>
    </h1>
</div>
<!-- THREE COLUMN LAYOUT -->
<div class="row g-4" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.6rem;">
    <!-- 1. Fixtures & Results -->
    <div class="section-card">
        <div class="section-header"><span>Fixtures & Results</span></div>
        <div class="tab">
            <button class="tablinks active" onclick="openMatchTab(event, 'upcoming-<?= $cupId ?>')">Upcoming</button>
            <button class="tablinks" onclick="openMatchTab(event, 'results-<?= $cupId ?>')">Results</button>
        </div>
        <div id="upcoming-<?= $cupId ?>" class="tabcontent active">
            <?php
            $stmt = $pdo->prepare("SELECT f.tournament_date, f.venue, h.name AS home_name, h.logo AS home_logo, h.id AS home_id, a.name AS away_name, a.logo AS away_logo, a.id AS away_id FROM tournament_fixtures f JOIN clubs h ON f.home_club_id = h.id JOIN clubs a ON f.away_club_id = a.id WHERE f.competition_season_id = ? AND f.status = 'scheduled' ORDER BY f.tournament_date ASC LIMIT 6");
            $stmt->execute([$cupId]); $fixtures = $stmt->fetchAll();
            if (empty($fixtures)) echo '<p class="text-center text-muted py-4 small">No upcoming fixtures</p>';
            else foreach ($fixtures as $f):
                $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($f['home_name'],0,2);
                $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($f['away_name'],0,2);
                $dt = new DateTime($f['tournament_date']);
            ?>
            <div class="match-item">
                <div class="match-row">
                    <div class="teams-stack">
                        <div class="team-item"><img src="<?= $homeLogo ?>" class="team-logo"><div class="team-name"><?= htmlspecialchars($f['home_name']) ?></div></div>
                        <div class="team-item"><img src="<?= $awayLogo ?>" class="team-logo"><div class="team-name"><?= htmlspecialchars($f['away_name']) ?></div></div>
                    </div>
                    <div class="fixture-info-stack">
                        <div class="fixture-date"><?= $dt->format('j M') ?><br><small><?= $dt->format('H:i') ?></small></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="results-<?= $cupId ?>" class="tabcontent">
            <?php
            $stmt = $pdo->prepare("SELECT tm.id AS match_id, tm.home_score, tm.away_score, tm.match_date, tf.home_club_id, tf.away_club_id, h.name AS home_name, h.logo AS home_logo, a.name AS away_name, a.logo AS away_logo FROM tournament_matches tm JOIN tournament_fixtures tf ON tm.fixture_id = tf.id JOIN clubs h ON tf.home_club_id = h.id JOIN clubs a ON tf.away_club_id = a.id WHERE tf.competition_season_id = ? ORDER BY tm.match_date DESC LIMIT 6");
            $stmt->execute([$cupId]); $results = $stmt->fetchAll();
            if (empty($results)) echo '<p class="text-center text-muted py-4 small">No results yet</p>';
            else foreach ($results as $r):
                $homeWin = $r['home_score'] > $r['away_score'];
                $awayWin = $r['away_score'] > $r['home_score'];
                $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($r['home_name'],0,2);
                $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($r['away_name'],0,2);
                $dateStr = (new DateTime($r['match_date']))->format('j M');
                $matchId = $r['match_id'];
                $eventHtml = '';
                $events = [];
                // Goals & Assists
                $gStmt = $pdo->prepare("SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id, ap.name AS assist
                                        FROM tournament_goals g
                                        JOIN players p ON g.player_id = p.id
                                        LEFT JOIN tournament_assists ta ON g.id = ta.goal_id
                                        LEFT JOIN players ap ON ta.player_id = ap.id
                                        WHERE g.match_id = ? ORDER BY g.minute");
                $gStmt->execute([$matchId]);
                $goals = $gStmt->fetchAll();
                foreach ($goals as $g) {
                    $team = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $events[] = ['team' => $team, 'icon' => 'goal-icon', 'text' => 'G', 'detail' => htmlspecialchars($g['scorer']).($g['is_penalty'] ? ' (P)' : '')];
                    if ($g['assist']) $events[] = ['team' => $team, 'icon' => 'assist-icon', 'text' => 'A', 'detail' => htmlspecialchars($g['assist'])];
                }
                // Cards
                $cStmt = $pdo->prepare("SELECT card_type, p.name, p.club_id FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ?");
                $cStmt->execute([$matchId]);
                $cards = $cStmt->fetchAll();
                foreach ($cards as $c) {
                    $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $icon = $c['card_type'] === 'yellow' ? 'yellow-icon' : 'red-icon';
                    $text = $c['card_type'] === 'yellow' ? 'YC' : 'RC';
                    $events[] = ['team' => $team, 'icon' => $icon, 'text' => $text, 'detail' => htmlspecialchars($c['name'])];
                }
                // Clean Sheets
                $csStmt = $pdo->prepare("SELECT p.name, p.club_id FROM tournament_clean_sheets tcs JOIN players p ON tcs.player_id = p.id WHERE tcs.match_id = ?");
                $csStmt->execute([$matchId]);
                $cleans = $csStmt->fetchAll();
                foreach ($cleans as $cs) {
                    $team = $cs['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $events[] = ['team' => $team, 'icon' => 'cleansheet-icon', 'text' => 'CS', 'detail' => '<strong>'.htmlspecialchars($cs['name']).'</strong>'];
                }
                foreach ($events as $e) {
                    if ($e['team'] === 'home') {
                        $eventHtml .= "<div class='event-row event-team home'>
                            <div class='event-icon {$e['icon']}'>{$e['text']}</div>
                            <div class='event-detail'>{$e['detail']}</div>
                        </div>";
                    } else {
                        $eventHtml .= "<div class='event-row event-team away'>
                            <div class='event-detail'>{$e['detail']}</div>
                            <div class='event-icon {$e['icon']}'>{$e['text']}</div>
                        </div>";
                    }
                }
                if (empty($eventHtml)) $eventHtml = '<div class="text-center text-muted small py-2">No events recorded</div>';
            ?>
            <div class="match-item">
                <div class="match-row">
                    <div class="teams-stack">
                        <div class="team-item"><img src="<?= $homeLogo ?>" class="team-logo"><div class="team-name <?= $homeWin ? 'winner' : '' ?>"><?= htmlspecialchars($r['home_name']) ?></div></div>
                        <div class="team-item"><img src="<?= $awayLogo ?>" class="team-logo"><div class="team-name <?= $awayWin ? 'winner' : '' ?>"><?= htmlspecialchars($r['away_name']) ?></div></div>
                    </div>
                    <div class="score-stack">
                        <span class="home-score"><?= $r['home_score'] ?></span>
                        <span class="away-score"><?= $r['away_score'] ?></span>
                    </div>
                </div>
                <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#ev-<?= $matchId ?>">
                    <span><?= $dateStr ?></span> <i class="bi bi-chevron-down"></i>
                </div>
                <div class="collapse event-list" id="ev-<?= $matchId ?>">
                    <?= $eventHtml ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- 2. Top Performers -->
    <div class="section-card">
        <div class="section-header"><span>Top Performers</span></div>
        <div class="table-responsive" style="flex-grow:1;">
            <?php
            $stmt = $pdo->prepare("
                SELECT p.id AS player_id, p.name, p.photo, c.name AS club_name,
                       COALESCE(goals.g,0) AS goals,
                       COALESCE(assists.a,0) AS assists,
                       COALESCE(cards.yc,0) AS yellow_cards,
                       COALESCE(cards.rc,0) AS red_cards,
                       COALESCE(cs.cs,0) AS clean_sheets
                FROM players p
                JOIN clubs c ON p.club_id = c.id
                LEFT JOIN (SELECT player_id, COUNT(*) AS g FROM tournament_goals tg JOIN tournament_matches tm ON tg.match_id=tm.id JOIN tournament_fixtures tf ON tm.fixture_id=tf.id WHERE tf.competition_season_id=? GROUP BY player_id) goals ON p.id=goals.player_id
                LEFT JOIN (SELECT ta.player_id, COUNT(*) AS a FROM tournament_assists ta JOIN tournament_goals tg ON ta.goal_id=tg.id JOIN tournament_matches tm ON tg.match_id=tm.id JOIN tournament_fixtures tf ON tm.fixture_id=tf.id WHERE tf.competition_season_id=? GROUP BY ta.player_id) assists ON p.id=assists.player_id
                LEFT JOIN (SELECT player_id, SUM(CASE WHEN card_type='yellow' THEN 1 ELSE 0 END) AS yc, SUM(CASE WHEN card_type='red' THEN 1 ELSE 0 END) AS rc FROM tournament_cards tc JOIN tournament_matches tm ON tc.match_id=tm.id JOIN tournament_fixtures tf ON tm.fixture_id=tf.id WHERE tf.competition_season_id=? GROUP BY player_id) cards ON p.id=cards.player_id
                LEFT JOIN (SELECT player_id, COUNT(*) AS cs FROM tournament_clean_sheets tcs JOIN tournament_matches tm ON tcs.match_id=tm.id JOIN tournament_fixtures tf ON tm.fixture_id=tf.id WHERE tf.competition_season_id=? GROUP BY player_id) cs ON p.id=cs.player_id
                WHERE EXISTS (SELECT 1 FROM tournament_fixtures tf2 WHERE tf2.competition_season_id=? AND (tf2.home_club_id=p.club_id OR tf2.away_club_id=p.club_id))
                HAVING goals>0 OR assists>0 OR yellow_cards>0 OR red_cards>0 OR clean_sheets>0
                ORDER BY goals DESC, assists DESC, p.name ASC LIMIT 10
            ");
            $stmt->execute([$cupId,$cupId,$cupId,$cupId,$cupId]);
            $topPlayers = $stmt->fetchAll();
            if (empty($topPlayers)) echo '<p class="text-center text-muted py-5 small">No stats yet</p>';
            else { ?>
            <table class="table table-hover mb-0 top-scorers-table">
                <thead><tr><th>#</th><th>Player</th><th>G</th><th>A</th><th>YC</th><th>RC</th><th>CS</th></tr></thead>
                <tbody>
                    <?php foreach ($topPlayers as $i => $p):
                        $photo = $p['photo'] ? "uploads/players/".$p['photo'] : "https://via.placeholder.com/60/defcfc/333333?text=".substr($p['name'],0,1); ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $i+1 ?></td>
                        <td><div class="d-flex align-items-center gap-2"><img src="<?= $photo ?>" class="player-thumb"><div><a href="player_profile.php?player_id=<?= $p['player_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($p['name']) ?></a><div class="text-muted small"><?= htmlspecialchars($p['club_name']) ?></div></div></div></td>
                        <td class="text-center text-success fw-bold"><?= $p['goals'] ?></td>
                        <td class="text-center text-info fw-bold"><?= $p['assists'] ?></td>
                        <td class="text-center text-warning fw-bold"><?= $p['yellow_cards'] ?></td>
                        <td class="text-center text-danger fw-bold"><?= $p['red_cards'] ?></td>
                        <td class="text-center text-primary fw-bold"><?= $p['clean_sheets'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
    <!-- 3. Tournament Table -->
    <div class="section-card">
        <div class="section-header"><span>Table</span></div>
        <div class="table-responsive" style="flex-grow:1;">
            <?php
            $stmt = $pdo->prepare("
                SELECT c.id, c.name AS club, c.logo,
                       COUNT(tm.id) AS played,
                       SUM(CASE WHEN (tf.home_club_id = c.id AND tm.home_score > tm.away_score) OR (tf.away_club_id = c.id AND tm.away_score > tm.home_score) THEN 1 ELSE 0 END) AS wins,
                       SUM(CASE WHEN tm.home_score = tm.away_score THEN 1 ELSE 0 END) AS draws,
                       SUM(CASE WHEN (tf.home_club_id = c.id AND tm.home_score < tm.away_score) OR (tf.away_club_id = c.id AND tm.away_score > tm.home_score) THEN 1 ELSE 0 END) AS losses,
                       SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.home_score,0) ELSE COALESCE(tm.away_score,0) END) AS gf,
                       SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.away_score,0) ELSE COALESCE(tm.home_score,0) END) AS ga,
                       SUM(CASE WHEN (tf.home_club_id = c.id AND tm.home_score > tm.away_score) OR (tf.away_club_id = c.id AND tm.away_score > tm.home_score) THEN 3 WHEN tm.home_score = tm.away_score THEN 1 ELSE 0 END) AS points,
                       (SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.home_score,0) ELSE COALESCE(tm.away_score,0) END) - SUM(CASE WHEN tf.home_club_id = c.id THEN COALESCE(tm.away_score,0) ELSE COALESCE(tm.home_score,0) END)) AS gd
                FROM tournament_fixtures tf
                JOIN clubs c ON c.id IN (tf.home_club_id, tf.away_club_id)
                LEFT JOIN tournament_matches tm ON tm.fixture_id = tf.id
                WHERE c.name NOT IN ('Loser 1','Loser 2','Winner 1', 'Winner 2') and tf.competition_season_id = ?
                GROUP BY c.id
                ORDER BY points DESC, gd DESC, gf DESC, c.name ASC
            ");
            $stmt->execute([$cupId]);
            $standings = $stmt->fetchAll();
            if (empty($standings)) echo '<p class="text-center text-muted py-5 small">No matches played</p>';
            else { ?>
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Club</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GD</th><th>PTS</th></tr></thead>
                <tbody>
                    <?php foreach ($standings as $i => $row):
                        $logo = $row['logo'] ? 'uploads/clubs/'.$row['logo'] : 'https://via.placeholder.com/40/defcfc/333333?text=C';
                    ?>
                    <tr>
                        <td class="fw-bold"><?= $i+1 ?></td>
                        <td><div class="d-flex align-items-center gap-2"><img src="<?= $logo ?>" class="club-logo"><span><?= htmlspecialchars($row['club']) ?></span></div></td>
                        <td><?= $row['played'] ?></td>
                        <td><?= $row['wins'] ?></td>
                        <td><?= $row['draws'] ?></td>
                        <td><?= $row['losses'] ?></td>
                        <td class="<?= $row['gd'] >= 0 ? 'text-success' : 'text-danger' ?> fw-bold"><?= $row['gd'] >= 0 ? '+'.$row['gd'] : $row['gd'] ?></td>
                        <td class="fw-bold text-primary"><?= $row['points'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
<!-- GALLERY -->
<?php
$stmt = $pdo->query("SELECT image, caption FROM tournament_images ORDER BY id DESC");
$allImages = $stmt->fetchAll();
if (!empty($allImages)): ?>
<div class="gallery-header">
    <h3>Tournament Gallery</h3>
</div>
<div class="tournament-gallery">
    <?php foreach ($allImages as $i => $img):
        $path = 'uploads/tournaments/' . htmlspecialchars($img['image']);
        $caption = htmlspecialchars($img['caption'] ?? '');
    ?>
    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imgModal<?= $i ?>">
        <img src="<?= $path ?>" alt="<?= $caption ?>" loading="lazy">
    </div>
    <div class="modal fade" id="imgModal<?= $i ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="text-end p-3">
                    <button type="button" class="btn-close btn-close-white shadow-lg" data-bs-dismiss="modal"></button>
                </div>
                <img src="<?= $path ?>" class="img-fluid shadow-lg rounded" alt="<?= $caption ?>">
                <?php if ($caption): ?><div class="text-center mt-3"><p class="text-white bg-dark bg-opacity-75 d-inline-block px-4 py-2 rounded"><?= $caption ?></p></div><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
<script>
function openMatchTab(evt, tabId) {
    const card = evt.currentTarget.closest('.section-card');
    card.querySelectorAll('.tabcontent').forEach(t => t.classList.remove('active'));
    card.querySelectorAll('.tablinks').forEach(b => b.classList.remove('active'));
    card.querySelector('#' + tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tablinks.active').forEach(btn => btn.click());
});
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>