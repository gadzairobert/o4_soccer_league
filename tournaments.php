<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
echo '<style>.navbar-brand img { width: 50px !important; height: 50px !important; object-fit: contain; }</style>';
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --gold:        #c9a84c;
        --gold-light:  #f0d080;
        --gold-dark:   #9a6f1e;
        --cream:       #fdf8ef;
        --dark-panel:  #1a1a2e;
        --dark-tab:    #16152b;
        --dark-deeper: #0f0e22;
        --border:      rgba(201,168,76,0.22);
        --muted:       #6b7280;
        --text-main:   #1a1a2e;
        --text-soft:   #4b5563;
    }

    /* ── PAGE BACKGROUND: LIGHT ── */
    html, body {
        background-color: #f0ede8 !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.07) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(180,160,120,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: var(--text-main);
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .tournament-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .tournament-page-wrapper { margin-top: 0; padding: 1rem 0 3rem; width: 100%; }
    }

    /* ── Tournament Header — DARK ── */
    .tournament-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1.4rem 1.8rem;
        text-align: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        margin-bottom: 1.8rem;
        border-radius: 14px 14px 0 0;
    }
    .tournament-header h1 {
        font-family: 'Playfair Display', serif;
        margin: 0;
        font-size: 2.1rem;
        font-weight: 900;
        color: var(--cream);
        letter-spacing: 1px;
        text-shadow: 0 3px 10px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    .tournament-header img {
        width: 66px; height: 66px; object-fit: contain;
        background: rgba(255,255,255,0.06);
        padding: 8px;
        border-radius: 16px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.3);
        border: 2px solid var(--border);
    }

    /* ── Three-Column Grid ── */
    .tournament-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.4rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1100px) { .tournament-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 700px)  { .tournament-grid { grid-template-columns: 1fr; } }

    /* ── Section Card — LIGHT ── */
    .section-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
    }

    /* ── Section Header — DARK ── */
    .section-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 1px solid var(--border);
        padding: 0.85rem 1.4rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--cream);
    }

    /* ── Tabs — DARK ── */
    .tab {
        overflow: hidden;
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        display: flex;
        flex-wrap: nowrap;
        border-bottom: 1px solid var(--border);
    }
    .tab button {
        background: transparent;
        border: none;
        color: rgba(255,255,255,0.45);
        padding: 12px 18px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        flex: 1;
        text-align: center;
        white-space: nowrap;
        position: relative;
        letter-spacing: 0.4px;
    }
    .tab button:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 25%; height: 50%;
        width: 1px; background: var(--border);
    }
    .tab button:hover { background: rgba(201,168,76,0.08); color: var(--gold-light); }
    .tab button.active {
        background: rgba(201,168,76,0.12);
        color: var(--gold);
        box-shadow: inset 0 -2px 0 var(--gold);
        font-weight: 700;
    }

    /* ── Tab Content ── */
    .tabcontent { display: none; flex-grow: 1; overflow-y: auto; }
    .tabcontent.active { display: block; }

    /* ── Match Item — LIGHT ── */
    .match-item {
        background: transparent;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
    }
    .match-item:hover { background: #fdf9f0; }
    .match-item:last-child { border-bottom: none; }

    .match-row {
        padding: 0.75rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 68px;
    }

    /* ── Teams Stack ── */
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
        width: 1px; height: 48px; background: #d1d5db;
    }
    .team-item { display: flex; align-items: center; gap: 10px; }

    .team-logo {
        width: 36px; height: 36px; object-fit: contain;
        background: #fff; padding: 3px; border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.25);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        flex-shrink: 0; transition: border-color 0.2s;
    }
    .match-item:hover .team-logo { border-color: rgba(201,168,76,0.5); }

    .team-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600; font-size: 0.9rem;
        color: var(--text-soft);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-decoration: none; transition: color 0.2s;
    }
    .team-name:hover { color: var(--gold-dark); }
    .winner { color: #15803d !important; font-weight: 700 !important; }

    /* ── Fixture Info ── */
    .fixture-info-stack {
        text-align: right;
        min-width: 80px;
        flex-shrink: 0;
    }
    .fixture-date {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--gold-dark);
        line-height: 1.3;
    }

    /* ── Score Stack ── */
    .score-stack {
        padding-left: 14px;
        text-align: right;
        min-width: 56px;
        flex-shrink: 0;
        line-height: 1.1;
    }
    .home-score, .away-score {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--text-main);
    }

    /* ── Match Toggle — DARK ── */
    .match-toggle {
        padding: 0.45rem 1.2rem;
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        border-top: 1px solid rgba(201,168,76,0.15);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        color: rgba(255,255,255,0.5);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }
    .match-toggle:hover { background: linear-gradient(135deg, #1a1830, #2a2650); }
    .match-toggle span { font-weight: 700; font-size: 0.84rem; color: var(--cream); }
    .match-toggle i { color: rgba(201,168,76,0.6); transition: transform 0.25s; }
    .match-toggle[aria-expanded="true"] i { transform: rotate(180deg); }

    /* ── Event List — LIGHT ── */
    .event-list {
        background: #f3f1ec;
        padding: 0.7rem 1.2rem;
        font-size: 0.82rem;
        max-height: 280px;
        overflow-y: auto;
        border-top: 1px solid #e5e7eb;
    }
    .event-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
        padding: 2px 0;
    }
    .event-team.away { justify-content: flex-end; flex-direction: row-reverse; }
    .event-icon {
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: bold; color: white; flex-shrink: 0;
    }
    .goal-icon       { background: #2563eb; }
    .assist-icon     { background: var(--gold); color: #000; }
    .yellow-icon     { background: #eab308; color: #000; }
    .red-icon        { background: #dc2626; }
    .cleansheet-icon { background: #16a34a; }
    .event-detail {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: var(--text-soft);
    }

    /* ── Tables — LIGHT ── */
    .table {
        color: var(--text-main) !important;
        margin: 0;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.87rem;
        background: #ffffff;
    }
    .table thead th {
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab)) !important;
        color: var(--gold) !important;
        border-bottom: 1px solid var(--border) !important;
        font-weight: 700;
        letter-spacing: 0.4px;
        font-size: 0.78rem;
        text-transform: uppercase;
        padding: 0.7rem 0.6rem;
        white-space: nowrap;
    }
    .table tbody td {
        color: var(--text-main) !important;
        border-bottom: 1px solid #f3f4f6 !important;
        padding: 0.6rem 0.6rem;
        vertical-align: middle;
    }
    .table tbody tr { background: #ffffff !important; transition: background 0.2s; }
    .table tbody tr:hover { background: #fdf9f0 !important; }
    .table a { color: var(--text-soft) !important; text-decoration: none; transition: color 0.2s; }
    .table a:hover { color: var(--gold-dark) !important; }

    .player-thumb, .club-logo {
        width: 30px; height: 30px; object-fit: cover;
        border-radius: 50%; border: 2px solid rgba(201,168,76,0.25);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .club-logo { border-radius: 6px; object-fit: contain; background: #fff; padding: 2px; }

    /* Stat badges */
    .text-success { color: #15803d !important; }
    .text-info    { color: #0e7490 !important; }
    .text-warning { color: #b45309 !important; }
    .text-danger  { color: #dc2626 !important; }
    .text-primary { color: var(--gold-dark) !important; }

    /* ── Gallery — LIGHT ── */
    .gallery-section {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-top: 0.5rem;
    }
    .gallery-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 0.9rem 1.6rem;
    }
    .gallery-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0;
    }
    .tournament-gallery {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        padding: 1.2rem;
    }
    .gallery-item {
        overflow: hidden;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        transition: all 0.35s ease;
        aspect-ratio: 1/1;
        cursor: pointer;
    }
    .gallery-item:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 16px 36px rgba(0,0,0,0.12);
        border-color: rgba(201,168,76,0.45);
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .gallery-item:hover img { transform: scale(1.12); }

    /* Empty states */
    .text-muted { color: var(--muted) !important; font-family: 'DM Sans', sans-serif; font-size: 0.88rem; }

    /* Modal */
    .modal-content.bg-transparent { background: transparent !important; }

    @media (max-width: 992px) { .tournament-gallery { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 768px)  { .tournament-gallery { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 576px) {
        .tournament-header h1 { font-size: 1.6rem; flex-direction: column; gap: 0.7rem; }
        .tournament-header img { width: 56px; height: 56px; }
        .tab button { padding: 10px 10px; font-size: 0.82rem; }
        .tournament-grid { gap: 1rem; }
        .tournament-gallery { grid-template-columns: repeat(2, 1fr); gap: 0.8rem; }
    }
</style>

<div class="main-content">
    <div class="tournament-page-wrapper container">

<?php
$selectedCupId = isset($_GET['cup_id']) ? (int)$_GET['cup_id'] : null;
if ($selectedCupId) {
    $stmt = $pdo->prepare("SELECT id, name, short_name, competition_name, season, logo, is_current FROM competition_seasons WHERE id = ? AND type = 'cup'");
    $stmt->execute([$selectedCupId]);
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($tournaments)) die('<div class="container py-5 text-center"><h3 style="color:var(--text-main);">Tournament not found.</h3></div>');
} else {
    $stmt = $pdo->query("SELECT id, name, short_name, competition_name, season, logo, is_current FROM competition_seasons WHERE type = 'cup' ORDER BY is_current DESC, season DESC");
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

foreach ($tournaments as $cup):
    $cupId   = $cup['id'];
    $cupName = $cup['name'] ?: $cup['competition_name'] . ' ' . $cup['season'];
    $logoPath = $cup['logo'] ? 'uploads/competitions/' . $cup['logo'] : null;
    if ($selectedCupId && !$cup['is_current']) continue;
?>

<!-- ── Tournament Header ── -->
<div class="tournament-header">
    <h1>
        <?php if ($logoPath): ?>
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars($cupName) ?>">
        <?php endif; ?>
        <?= htmlspecialchars($cupName) ?>
    </h1>
</div>

<!-- ── Three Columns ── -->
<div class="tournament-grid">

    <!-- 1. Fixtures & Results -->
    <div class="section-card">
        <div class="section-header">Fixtures &amp; Results</div>
        <div class="tab">
            <button class="tablinks active" onclick="openMatchTab(event, 'upcoming-<?= $cupId ?>')">Upcoming</button>
            <button class="tablinks" onclick="openMatchTab(event, 'results-<?= $cupId ?>')">Results</button>
        </div>

        <div id="upcoming-<?= $cupId ?>" class="tabcontent active">
            <?php
            $stmt = $pdo->prepare("SELECT f.tournament_date, f.venue, h.name AS home_name, h.logo AS home_logo, h.id AS home_id, a.name AS away_name, a.logo AS away_logo, a.id AS away_id FROM tournament_fixtures f JOIN clubs h ON f.home_club_id = h.id JOIN clubs a ON f.away_club_id = a.id WHERE f.competition_season_id = ? AND f.status = 'scheduled' ORDER BY f.tournament_date ASC LIMIT 6");
            $stmt->execute([$cupId]); $fixtures = $stmt->fetchAll();
            if (empty($fixtures)) echo '<p class="text-center text-muted py-4">No upcoming fixtures</p>';
            else foreach ($fixtures as $f):
                $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/48/f0ede8/9a6f1e?text=".urlencode(substr($f['home_name'],0,2));
                $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/48/f0ede8/9a6f1e?text=".urlencode(substr($f['away_name'],0,2));
                $dt = new DateTime($f['tournament_date']);
            ?>
            <div class="match-item">
                <div class="match-row">
                    <div class="teams-stack">
                        <div class="team-item"><img src="<?= $homeLogo ?>" class="team-logo"><span class="team-name"><?= htmlspecialchars($f['home_name']) ?></span></div>
                        <div class="team-item"><img src="<?= $awayLogo ?>" class="team-logo"><span class="team-name"><?= htmlspecialchars($f['away_name']) ?></span></div>
                    </div>
                    <div class="fixture-info-stack">
                        <div class="fixture-date"><?= $dt->format('j M') ?><br><small style="font-weight:500;color:var(--muted);"><?= $dt->format('H:i') ?></small></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="results-<?= $cupId ?>" class="tabcontent">
            <?php
            $stmt = $pdo->prepare("SELECT tm.id AS match_id, tm.home_score, tm.away_score, tm.match_date, tf.home_club_id, tf.away_club_id, h.name AS home_name, h.logo AS home_logo, a.name AS away_name, a.logo AS away_logo FROM tournament_matches tm JOIN tournament_fixtures tf ON tm.fixture_id = tf.id JOIN clubs h ON tf.home_club_id = h.id JOIN clubs a ON tf.away_club_id = a.id WHERE tf.competition_season_id = ? ORDER BY tm.match_date DESC LIMIT 6");
            $stmt->execute([$cupId]); $results = $stmt->fetchAll();
            if (empty($results)) echo '<p class="text-center text-muted py-4">No results yet</p>';
            else foreach ($results as $r):
                $homeWin  = $r['home_score'] > $r['away_score'];
                $awayWin  = $r['away_score'] > $r['home_score'];
                $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/48/f0ede8/9a6f1e?text=".urlencode(substr($r['home_name'],0,2));
                $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/48/f0ede8/9a6f1e?text=".urlencode(substr($r['away_name'],0,2));
                $dateStr  = (new DateTime($r['match_date']))->format('j M');
                $matchId  = $r['match_id'];
                $eventHtml = ''; $events = [];
                $gStmt = $pdo->prepare("SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id, ap.name AS assist FROM tournament_goals g JOIN players p ON g.player_id = p.id LEFT JOIN tournament_assists ta ON g.id = ta.goal_id LEFT JOIN players ap ON ta.player_id = ap.id WHERE g.match_id = ? ORDER BY g.minute");
                $gStmt->execute([$matchId]); $goals = $gStmt->fetchAll();
                foreach ($goals as $g) {
                    $team = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $events[] = ['team'=>$team,'icon'=>'goal-icon','text'=>'G','detail'=>htmlspecialchars($g['scorer']).($g['is_penalty']?' (P)':'')];
                    if ($g['assist']) $events[] = ['team'=>$team,'icon'=>'assist-icon','text'=>'A','detail'=>htmlspecialchars($g['assist'])];
                }
                $cStmt = $pdo->prepare("SELECT card_type, p.name, p.club_id FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ?");
                $cStmt->execute([$matchId]); $cards = $cStmt->fetchAll();
                foreach ($cards as $c) {
                    $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $icon = $c['card_type'] === 'yellow' ? 'yellow-icon' : 'red-icon';
                    $events[] = ['team'=>$team,'icon'=>$icon,'text'=>($c['card_type']==='yellow'?'YC':'RC'),'detail'=>htmlspecialchars($c['name'])];
                }
                $csStmt = $pdo->prepare("SELECT p.name, p.club_id FROM tournament_clean_sheets tcs JOIN players p ON tcs.player_id = p.id WHERE tcs.match_id = ?");
                $csStmt->execute([$matchId]); $cleans = $csStmt->fetchAll();
                foreach ($cleans as $cs) {
                    $team = $cs['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                    $events[] = ['team'=>$team,'icon'=>'cleansheet-icon','text'=>'CS','detail'=>'<strong>'.htmlspecialchars($cs['name']).'</strong>'];
                }
                foreach ($events as $e) {
                    if ($e['team'] === 'home') {
                        $eventHtml .= "<div class='event-row event-team home'><div class='event-icon {$e['icon']}'>{$e['text']}</div><div class='event-detail'>{$e['detail']}</div></div>";
                    } else {
                        $eventHtml .= "<div class='event-row event-team away'><div class='event-detail'>{$e['detail']}</div><div class='event-icon {$e['icon']}'>{$e['text']}</div></div>";
                    }
                }
                if (empty($eventHtml)) $eventHtml = '<div class="text-center text-muted py-2" style="font-size:0.8rem;">No events recorded</div>';
            ?>
            <div class="match-item">
                <div class="match-row">
                    <div class="teams-stack">
                        <div class="team-item"><img src="<?= $homeLogo ?>" class="team-logo"><span class="team-name <?= $homeWin ? 'winner' : '' ?>"><?= htmlspecialchars($r['home_name']) ?></span></div>
                        <div class="team-item"><img src="<?= $awayLogo ?>" class="team-logo"><span class="team-name <?= $awayWin ? 'winner' : '' ?>"><?= htmlspecialchars($r['away_name']) ?></span></div>
                    </div>
                    <div class="score-stack">
                        <span class="home-score"><?= $r['home_score'] ?></span>
                        <span class="away-score"><?= $r['away_score'] ?></span>
                    </div>
                </div>
                <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#ev-<?= $matchId ?>">
                    <span><?= $dateStr ?></span><i class="bi bi-chevron-down"></i>
                </div>
                <div class="collapse event-list" id="ev-<?= $matchId ?>"><?= $eventHtml ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. Top Performers -->
    <div class="section-card">
        <div class="section-header">Top Performers</div>
        <div class="table-responsive" style="flex-grow:1;">
            <?php
            $stmt = $pdo->prepare("
                SELECT p.id AS player_id, p.name, p.photo, c.name AS club_name,
                       COALESCE(goals.g,0) AS goals, COALESCE(assists.a,0) AS assists,
                       COALESCE(cards.yc,0) AS yellow_cards, COALESCE(cards.rc,0) AS red_cards,
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
            if (empty($topPlayers)) echo '<p class="text-center text-muted py-5">No stats yet</p>';
            else { ?>
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Player</th><th>G</th><th>A</th><th>YC</th><th>RC</th><th>CS</th></tr></thead>
                <tbody>
                    <?php foreach ($topPlayers as $i => $p):
                        $photo = $p['photo'] ? "uploads/players/".$p['photo'] : "https://via.placeholder.com/60/f0ede8/9a6f1e?text=".urlencode(substr($p['name'],0,1));
                    ?>
                    <tr>
                        <td class="text-center fw-bold" style="color:var(--muted);"><?= $i+1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= $photo ?>" class="player-thumb">
                                <div>
                                    <a href="player_profile.php?player_id=<?= $p['player_id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                                    <div style="font-size:0.75rem;color:var(--muted);"><?= htmlspecialchars($p['club_name']) ?></div>
                                </div>
                            </div>
                        </td>
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
        <div class="section-header">Table</div>
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
                WHERE c.name NOT IN ('Loser 1','Loser 2','Winner 1','Winner 2') AND tf.competition_season_id = ?
                GROUP BY c.id
                ORDER BY points DESC, gd DESC, gf DESC, c.name ASC
            ");
            $stmt->execute([$cupId]);
            $standings = $stmt->fetchAll();
            if (empty($standings)) echo '<p class="text-center text-muted py-5">No matches played</p>';
            else { ?>
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Club</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GD</th><th>PTS</th></tr></thead>
                <tbody>
                    <?php foreach ($standings as $i => $row):
                        $logo = $row['logo'] ? 'uploads/clubs/'.$row['logo'] : 'https://via.placeholder.com/40/f0ede8/9a6f1e?text=C';
                    ?>
                    <tr>
                        <td class="fw-bold" style="color:var(--muted);"><?= $i+1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= $logo ?>" class="club-logo">
                                <span><?= htmlspecialchars($row['club']) ?></span>
                            </div>
                        </td>
                        <td><?= $row['played'] ?></td>
                        <td><?= $row['wins'] ?></td>
                        <td><?= $row['draws'] ?></td>
                        <td><?= $row['losses'] ?></td>
                        <td class="fw-bold <?= $row['gd'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= $row['gd'] >= 0 ? '+'.$row['gd'] : $row['gd'] ?></td>
                        <td class="fw-bold text-primary"><?= $row['points'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>

</div><!-- /.tournament-grid -->
<?php endforeach; ?>

<!-- ── Gallery ── -->
<?php
$stmt = $pdo->query("SELECT image, caption FROM tournament_images ORDER BY id DESC");
$allImages = $stmt->fetchAll();
if (!empty($allImages)): ?>
<div class="gallery-section">
    <div class="gallery-header">
        <h3>Tournament Gallery</h3>
    </div>
    <div class="tournament-gallery">
        <?php foreach ($allImages as $i => $img):
            $path    = 'uploads/tournaments/' . htmlspecialchars($img['image']);
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
                    <?php if ($caption): ?>
                        <div class="text-center mt-3">
                            <p class="text-white d-inline-block px-4 py-2 rounded" style="background:rgba(0,0,0,0.7);"><?= $caption ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

    </div><!-- /.tournament-page-wrapper -->
</div><!-- /.main-content -->

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