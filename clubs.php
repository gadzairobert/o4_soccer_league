<?php
ob_start();
header('Content-Type: text/html; charset=UTF-8');
require 'config.php';
include 'includes/header.php';
include 'includes/properties.php';
$club_id = (int)($_GET['club_id'] ?? 0);
// ==================================================================
// SEASON FILTER
// ==================================================================
$selected_season = $_GET['season'] ?? null;
global $pdo;
$seasons_stmt = $pdo->query("
    SELECT DISTINCT YEAR(fixture_date) AS year
    FROM fixtures
    WHERE competition_season_id IN (SELECT id FROM competition_seasons WHERE type = 'league')
    ORDER BY year DESC
");
$available_seasons = $seasons_stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($available_seasons)) $available_seasons = [date('Y'), date('Y')-1];
if (!$selected_season || !in_array($selected_season, $available_seasons)) {
    $selected_season = $available_seasons[0];
}
// ==================================================================
// ALL CLUBS LANDING PAGE
// ==================================================================
if ($club_id === 0) {
    $clubs = getAllClubs();
    ?>
    <style>
        /* LIGHT THEME WITH #defcfc BACKGROUND FOR ALL CLUBS PAGE */
        html, body {
            background-color: #defcfc !important;
            color: #333333;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .main-content { flex: 1 0 auto; }
        footer { flex-shrink: 0; }
        .clubs-page-wrapper { margin-top: -50px; padding-top: 20px; }
        .page-header {
            background: linear-gradient(135deg, #1a2530, #2c3e50);
            color: white;
            padding: 1.6rem 1.8rem;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .clubs-card {
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
            border-radius: 12px;
        }
        .all-clubs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.8rem;
            padding: 2.5rem 0;
        }
        .club-card-big {
            background: #f8f9fa;
            text-align: center;
            padding: 1.8rem 1rem;
            transition: all .3s;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .club-card-big:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 32px rgba(0,0,0,0.15);
            background: #ffffff;
        }
        .club-card-big img {
            width: 140px; height: 140px;
            object-fit: contain;
            background: white;
            padding: 12px;
            border-radius: 50%;
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            margin-bottom: 1rem;
        }
        .club-card-big h5 {
            color: #2c3e50;
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .all-clubs-grid { grid-template-columns: 1fr 1fr; gap: 1.2rem; padding: 1.5rem 0; }
            .club-card-big img { width: 110px; height: 110px; }
            .page-header { font-size: 1.3rem; padding: 1.4rem 1rem; }
        }
    </style>
    <div class="main-content">
        <div class="container clubs-page-wrapper">
            <div class="clubs-card">
                <div class="page-header">All Clubs</div>
                <div class="all-clubs-grid">
                    <?php foreach ($clubs as $club):
                        $logo = $club['logo'] ? 'uploads/clubs/' . htmlspecialchars($club['logo']) : 'https://via.placeholder.com/150/defcfc/333333?text=' . substr($club['name'],0,2);
                    ?>
                        <a href="clubs.php?club_id=<?= $club['id'] ?>" class="text-decoration-none">
                            <div class="club-card-big">
                                <img src="<?= $logo ?>" alt="<?= htmlspecialchars($club['name']) ?>">
                                <h5 class="mt-2 mb-0"><?= htmlspecialchars($club['name']) ?></h5>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ob_end_flush(); exit;
}
// ==================================================================
// SINGLE CLUB PAGE
// ==================================================================
$club = getClubById($club_id);
if (!$club) {
    echo "<div class='alert alert-warning text-center py-5'>Club not found.</div>";
    include 'includes/footer.php'; ob_end_flush(); exit;
}
$standings = getFullLeagueStandings();
$position = 'N/A'; $points = $gf = $ga = $gd = 0;
foreach ($standings as $i => $s) {
    if ($s['id'] == $club_id) {
        $position = $i + 1;
        $points = $s['points'];
        $gf = $s['gf'];
        $ga = $s['ga'];
        $gd = $s['gd'];
        break;
    }
}
$recent_results = getClubRecentLeagueResults($club_id, 100);
$upcoming_fixtures = getClubUpcomingLeagueFixtures($club_id, 100);
$tournament_results = getClubTournamentResults($club_id);
$tournament_upcoming = getClubTournamentFixtures($club_id);
$players = getClubPlayersWithStats($club_id, $selected_season);
$recent_results = array_filter($recent_results, fn($m) => (new DateTime($m['match_date']))->format('Y') == $selected_season);
$upcoming_fixtures = array_filter($upcoming_fixtures, fn($f) => (new DateTime($f['fixture_date']))->format('Y') == $selected_season);
$logo_url = $club['logo'] ? 'uploads/clubs/'.htmlspecialchars($club['logo']) : 'https://via.placeholder.com/280/defcfc/333333?text='.substr($club['name'],0,2);
// Fetch management staff
$management_stmt = $pdo->prepare("
    SELECT id, full_name, role, date_of_birth, photo
    FROM management
    WHERE club_id = ? AND is_active = 1
    ORDER BY
        FIELD(role, 'Coach', 'Assistant Coach', 'Referee', 'Secretary', 'Treasurer', 'Committee Member', 'Medical Aid', 'Councillor'),
        full_name ASC
");
$management_stmt->execute([$club_id]);
$management = $management_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND FOR SINGLE CLUB PAGE */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }
    .clubs-page-wrapper {
        margin-top: -50px;
        padding-top: 90px;
    }
    @media (max-width: 767.98px) {
        .clubs-page-wrapper { padding-top: 20px; }
        .container { max-width: 100% !important; padding-left: 0 !important; padding-right: 0 !important; }
        .club-card, .club-overview-compact, .club-panel { border-radius: 0 !important; }
        .club-content-row { margin: 0; padding: 0 0.75rem; gap: 1rem; }
    }
    .season-select {
        background: #34495e;
        border: 1px solid #444;
        color: white;
        padding: 0.4rem 2.2rem 0.4rem 0.8rem;
        font-size: 0.9rem;
        border-radius: 6px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 11px;
        cursor: pointer;
    }
    @media (min-width: 992px) {
        .club-content-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 1.8rem;
        }
        .club-panel-league { flex: 0 0 calc(70% - 0.9rem) !important; max-width: calc(70% - 0.9rem) !important; }
        .club-panel-players { flex: 0 0 calc(30% - 0.9rem) !important; max-width: calc(30% - 0.9rem) !important; }
    }
    @media (min-width: 992px) {
        .matches-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 1rem !important; }
    }
    .club-overview-compact {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #dee2e6;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        margin: 1.5rem 0;
        border-radius: 12px;
        overflow: hidden;
    }
    @media (min-width: 768px) {
        .club-overview-compact { flex-direction: row; height: 260px; }
    }
    .club-logo-left {
        background: linear-gradient(135deg,#1a2530,#2c3e50);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        width: 100%;
    }
    @media (min-width: 768px) { .club-logo-left { width: 280px; flex-shrink: 0; } }
    .club-logo-circle {
        width: 180px; height: 180px; background: white; border-radius: 50%; padding: 12px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center;
    }
    @media (min-width: 768px) { .club-logo-circle { width: 210px; height: 210px; } }
    .club-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
    .club-info-right { flex: 1; display: flex; flex-direction: column; padding: 1.5rem; }
    .club-header-compact {
        background: linear-gradient(135deg,#1a2530,#2c3e50);
        color: white;
        padding: 1.2rem 1.5rem;
        text-align: center;
        border-radius: 12px 12px 0 0;
    }
    .club-name-main { font-size: 2rem; font-weight: 900; margin: 0; line-height: 1.2; color: white; }
    @media (min-width: 768px) { .club-name-main { font-size: 2.4rem; } }
    .club-meta-horizontal {
        display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;
        margin-top: 0.8rem; font-size: 1rem; color: #bdc3c7;
    }
    .badge-pos { background: #0d6efd; padding: 0.5rem 1rem; font-weight: 700; border-radius: 6px; }
    .club-stats-compact {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: #f8f9fa;
        margin-top: auto;
    }
    @media (max-width: 576px) { .club-stats-compact { grid-template-columns: 1fr 1fr; } }
    .stat-item-compact { text-align: center; padding: 1rem 0.5rem; border-right: 1px solid #dee2e6; }
    .stat-item-compact:last-child { border-right: none; }
    .stat-value-compact { font-size: 2.2rem; font-weight: 800; color: #2c3e50; }
    .stat-label-compact { font-size: 0.8rem; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
    .gd-positive { color: #27ae60; } .gd-negative { color: #e74c3c; }
    .club-content-row { display: flex; flex-wrap: wrap; gap: 1.8rem; margin-top: 1.5rem; }
    .club-panel {
        flex: 1; min-width: 320px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        border-radius: 12px;
    }
    .panel-tabs { display: flex; background: linear-gradient(135deg,#1a2530,#2c3e50); }
    .panel-tabs a {
        flex: 1; text-align: center; padding: 1rem 1.6rem;
        color: white; text-decoration: none; font-weight: 600;
        transition: background 0.3s;
    }
    .panel-tabs a.active { background: #0d6efd; }
    .panel-tabs a:hover:not(.active) { background: rgba(255,255,255,0.1); }
    .panel-header {
        background: linear-gradient(135deg,#9c27b0,#6a1b9a);
        color: white;
        padding: 1rem 1.6rem;
        font-size: 1.25rem;
        font-weight: 600;
        text-align: center;
    }
    .panel-body { padding: 0.6rem; }
    .matches-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 992px) { .matches-grid { grid-template-columns: 1fr 1fr !important; } }
    .match-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
        display: flex; flex-direction: column; overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .match-item:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        transform: translateY(-3px);
        background: #ffffff;
    }
    .match-row {
        padding: 0.75rem 1rem;
        display: flex; align-items: center;
        justify-content: space-between; flex: 1; min-height: 70px;
    }
    .teams-stack {
        display: flex; flex-direction: column; gap: 5px; flex: 1; padding-right: 12px;
        position: relative;
    }
    .teams-stack::after {
        content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
        width: 1px; height: 48px; background: #ccc;
    }
    .team-item { display: flex; align-items: center; gap: 9px; min-width: 0; }
    .team-logo {
        width: 36px; height: 36px; object-fit: contain;
        background: white; padding: 3px; border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15); border: 1px solid #ccc;
    }
    .team-name {
        font-weight: 600; font-size: 0.9rem; color: #2c3e50;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .team-name:hover { color: #0d6efd; }
    .team-name.bold { font-weight: 800 !important; }
    .winner { color: #27ae60 !important; font-weight: 700 !important; }
    .score-stack, .fixture-info {
        text-align: center; min-width: 80px; flex-shrink: 0; line-height: 1.2;
    }
    .home-score, .away-score {
        display: block; font-size: 1.6rem; font-weight: bold; color: #2c3e50;
    }
    .fixture-info { font-size: 0.8rem; color: #6c757d; text-align: right; }
    .fixture-info .date { font-weight: 600; font-size: 0.85rem; }
    .match-toggle {
        padding: 0.4rem 1rem; background: #f1f3f5; border-top: 1px solid #dee2e6;
        font-size: 0.82rem; color: #6c757d; cursor: pointer; display: flex;
        justify-content: space-between; align-items: center; transition: background 0.2s;
    }
    .match-toggle:hover { background: #e9ecef; }
    .match-toggle i { font-size: 0.9rem; transition: transform 0.25s; color: #6c757d; }
    .match-toggle[aria-expanded="true"] i { transform: rotate(180deg); }
    .event-list {
        background: #ffffff; padding: 0.6rem 1rem; font-size: 0.82rem;
        max-height: 300px; overflow-y: auto; border-top: 1px solid #dee2e6;
    }
    .event-row {
        display: flex; align-items: flex-start; gap: 8px; margin-bottom: 7px; padding: 3px 0; line-height: 1.3;
    }
    .event-team.home { justify-content: flex-start; text-align: left; }
    .event-team.away { justify-content: flex-end; text-align: right; flex-direction: row-reverse; }
    .event-minutes { font-weight: bold; color: #2c3e50; min-width: 36px; font-size: 0.85em; }
    .event-icon {
        width: 20px; height: 20px; border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center; font-size: 8px; font-weight: bold; color: white; flex-shrink: 0;
    }
    .goal-icon { background: #0111c0; }
    .assist-icon { background: #f1c40f; color: #000; }
    .yellow-icon { background: #f1c40f; color: #000; }
    .red-icon { background: #c0392b; }
    .event-detail { flex: 1; min-width: 0; }
    .event-name { font-weight: 600; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #2c3e50; }
    /* Players & Management Table */
    .table { background: #ffffff; color: #333333; }
    .table thead th { background: #2c3e50; color: white; }
    .table tbody tr { background: #f8f9fa; }
    .table tbody tr:hover { background: #e9ecef; }
    .table a { color: #333333; text-decoration: none; }
    .table a:hover { color: #0d6efd; }
</style>
<div class="main-content">
    <div class="container clubs-page-wrapper">
        <div class="club-card">
            <div class="club-overview-compact">
                <div class="club-logo-left">
                    <div class="club-logo-circle">
                        <img src="<?= $logo_url ?>" alt="<?= htmlspecialchars($club['name']) ?>">
                    </div>
                </div>
                <div class="club-info-right">
                    <div class="club-header-compact">
                        <h1 class="club-name-main"><?= htmlspecialchars($club['name']) ?></h1>
                        <div class="club-meta-horizontal">
                            <span><strong><?= htmlspecialchars($club['stadium'] ?? 'TBD') ?></strong></span>
                            <?php if (!empty($club['description'])): ?> • <?= htmlspecialchars($club['description']) ?><?php endif; ?>
                            <span class="badge-pos">Position #<?= $position ?></span>
                        </div>
                    </div>
                    <div class="club-stats-compact">
                        <div class="stat-item-compact"><div class="stat-value-compact text-primary"><?= $points ?></div><div class="stat-label-compact">Points</div></div>
                        <div class="stat-item-compact"><div class="stat-value-compact"><?= $gf ?></div><div class="stat-label-compact">GF</div></div>
                        <div class="stat-item-compact"><div class="stat-value-compact"><?= $ga ?></div><div class="stat-label-compact">GA</div></div>
                        <div class="stat-item-compact"><div class="stat-value-compact <?= $gd >= 0 ? 'gd-positive' : 'gd-negative' ?>"><?= $gd >= 0 ? '+' : '' ?><?= $gd ?></div><div class="stat-label-compact">GD</div></div>
                    </div>
                </div>
            </div>
            <div class="club-content-row">
                <div class="club-panel club-panel-league">
                    <div class="panel-tabs">
                        <a href="#" class="active d-flex align-items-center justify-content-center gap-3" data-tab="results">
                            <form method="get" class="me-3">
                                <input type="hidden" name="club_id" value="<?= $club_id ?>">
                                <select name="season" onchange="this.form.submit()" class="season-select">
                                    <?php foreach ($available_seasons as $year): ?>
                                        <option value="<?= $year ?>" <?= $year == $selected_season ? 'selected' : '' ?>><?= $year ?>/<?= $year + 1 ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            Results
                        </a>
                        <a href="#" data-tab="fixtures">Fixtures</a>
                    </div>
                    <div class="panel-body">
                        <div id="results" class="tab-pane active">
                            <div class="matches-grid">
                                <?php if (empty($recent_results)): ?>
                                    <div class="text-center text-muted py-5 w-100">No results this season</div>
                                <?php else: foreach ($recent_results as $r):
                                    $isHome = $r['home_club_id'] == $club_id;
                                    $homeWin = $r['home_score'] > $r['away_score'];
                                    $awayWin = $r['away_score'] > $r['home_score'];
                                    $homeLogo = $r['home_logo'] ? 'uploads/clubs/'.$r['home_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $awayLogo = $r['away_logo'] ? 'uploads/clubs/'.$r['away_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $dateStr = (new DateTime($r['match_date']))->format('j M Y');
                                    $events = getLeagueMatchEvents($r['match_id']);
                                    $eventHtml = '';
                                    foreach ($events['goals'] as $g) {
                                        $team = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                        $penalty = $g['is_penalty'] ? ' (P)' : '';
                                        $eventHtml .= "<div class='event-row event-team {$team}'>
                                            <div class='event-minutes'>{$g['minute']}'</div>
                                            <div class='event-icon goal-icon'>G</div>
                                            <div class='event-detail'><span class='event-name'>{$g['scorer']}{$penalty}</span></div>
                                        </div>";
                                        if (!empty($g['assist'])) {
                                            $eventHtml .= "<div class='event-row event-team {$team}'>
                                                <div class='event-minutes'></div>
                                                <div class='event-icon assist-icon'>A</div>
                                                <div class='event-detail'><span class='event-name'>{$g['assist']}</span></div>
                                            </div>";
                                        }
                                    }
                                    foreach ($events['cards'] as $c) {
                                        $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                        $icon = $c['card_type'] == 'yellow' ? 'yellow-icon' : 'red-icon';
                                        $text = $c['card_type'] == 'yellow' ? 'YC' : 'RC';
                                        $eventHtml .= "<div class='event-row event-team {$team}'>
                                            <div class='event-minutes'>{$c['minute']}'</div>
                                            <div class='event-icon {$icon}'>{$text}</div>
                                            <div class='event-detail'><span class='event-name'>{$c['name']}</span></div>
                                        </div>";
                                    }
                                    $eventHtml = $eventHtml ?: '<div class="text-center text-muted py-3 small">No events recorded</div>';
                                ?>
                                    <div class="match-item">
                                        <div class="match-row">
                                            <div class="teams-stack">
                                                <div class="team-item">
                                                    <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $isHome ? 'bold' : '' ?> <?= $homeWin ? 'winner' : '' ?>">
                                                            <?= htmlspecialchars($r['home_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="team-item">
                                                    <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= !$isHome ? 'bold' : '' ?> <?= $awayWin ? 'winner' : '' ?>">
                                                            <?= htmlspecialchars($r['away_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="score-stack">
                                                <span class="home-score"><?= $r['home_score'] ?></span>
                                                <span class="away-score"><?= $r['away_score'] ?></span>
                                            </div>
                                        </div>
                                        <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#ev<?= $r['match_id'] ?>">
                                            <span class="info"><?= $dateStr ?> • <?= htmlspecialchars($r['venue'] ?? 'TBD') ?></span>
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                        <div class="collapse event-list" id="ev<?= $r['match_id'] ?>">
                                            <?= $eventHtml ?>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <div id="fixtures" class="tab-pane" style="display:none;">
                            <div class="matches-grid">
                                <?php if (empty($upcoming_fixtures)): ?>
                                    <div class="text-center text-muted py-5 w-100">No upcoming fixtures</div>
                                <?php else: foreach ($upcoming_fixtures as $f):
                                    $homeLogo = $f['home_logo'] ? 'uploads/clubs/'.$f['home_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $awayLogo = $f['away_logo'] ? 'uploads/clubs/'.$f['away_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $dt = new DateTime($f['fixture_date']);
                                    $shortDate = $dt->format('D d M');
                                    $time = $dt->format('H:i');
                                ?>
                                    <div class="match-item">
                                        <div class="match-row">
                                            <div class="teams-stack">
                                                <div class="team-item">
                                                    <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $f['home_club_id'] == $club_id ? 'bold' : '' ?>">
                                                            <?= htmlspecialchars($f['home_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="team-item">
                                                    <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $f['away_club_id'] == $club_id ? 'bold' : '' ?>">
                                                            <?= htmlspecialchars($f['away_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="fixture-info">
                                                <div class="date"><?= $shortDate ?></div>
                                                <div class="time"><?= $time ?></div>
                                                <div class="venue"><?= htmlspecialchars($f['venue'] ?? 'TBD') ?></div>
                                            </div>
                                        </div>
                                        <div class="match-toggle">
                                            <span class="info">Upcoming Match</span>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="club-panel club-panel-players">
                    <div class="panel-tabs">
                        <a href="#" class="active" data-tab="players-tab">Players</a>
                        <a href="#" data-tab="management-tab">Management</a>
                    </div>
                    <div class="panel-body">
                        <div id="players-tab" class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" style="font-size:0.84rem;">
                                    <thead>
                                        <tr><th>#</th><th>Player</th><th>Age</th><th>G</th><th>A</th><th>Y</th><th>R</th><th>CS</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($players as $i => $p):
                                            $playerLink = 'player_profile.php?player_id=' . $p['id'];
                                            $status = $p['status'] ?? '';
                                            $statusClasses = [
                                                'Active' => 'bg-success text-white',
                                                'Inactive' => 'bg-danger text-white',
                                                'Injured' => 'bg-warning text-dark',
                                                'Suspended' => 'bg-danger text-white',
                                            ];
                                            $badgeClass = $statusClasses[$status] ?? 'bg-secondary text-white';
                                            $displayStatus = $status ?: '—';
                                        ?>
                                        <tr style="cursor:pointer;" onclick="window.location='<?= $playerLink ?>'">
                                            <td><?= $i + 1 ?></td>
                                            <td>
                                                <a href="<?= $playerLink ?>" class="text-decoration-none d-flex align-items-center gap-1">
                                                    <img src="<?= $p['photo'] ? 'uploads/players/'.$p['photo'] : 'https://via.placeholder.com/32/defcfc/333333' ?>"
                                                        class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                                                    <div>
                                                        <div class="fw-600" style="font-size:0.86rem;"><?= htmlspecialchars($p['name']) ?></div>
                                                        <small class="d-none d-lg-block">
                                                            <span class="badge rounded-pill <?= $badgeClass ?>" style="font-size:0.7rem;">
                                                                <?= htmlspecialchars($displayStatus) ?>
                                                            </span>
                                                        </small>
                                                    </div>
                                                </a>
                                            </td>
                                            <td><?= $p['age'] ?? '-' ?></td>
                                            <td><strong><?= $p['goals'] ?? 0 ?></strong></td>
                                            <td><?= $p['assists'] ?? 0 ?></td>
                                            <td><?= $p['yellow_cards'] ?? 0 ?></td>
                                            <td><?= $p['red_cards'] ?? 0 ?></td>
                                            <td><?= $p['clean_sheets'] ?? 0 ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="management-tab" class="tab-pane" style="display:none;">
                            <?php if (empty($management)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-people display-4 text-secondary mb-3"></i>
                                    <p class="lead">No management or staff registered for this club yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" style="font-size:0.84rem;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Photo</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Date of Birth</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($management as $i => $m):
                                                $photo_url = $m['photo']
                                                    ? 'uploads/management/' . htmlspecialchars($m['photo'])
                                                    : 'https://via.placeholder.com/40/defcfc/333333?text=' . substr(explode(' ', $m['full_name'])[0] ?? 'S', 0, 2);
                                            ?>
                                            <tr style="cursor:pointer;" onclick="window.location='management_profile.php?staff_id=<?= $m['id'] ?>'">
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <img src="<?= $photo_url ?>"
                                                         class="rounded-circle shadow-sm"
                                                         width="40" height="40"
                                                         style="object-fit:cover;"
                                                         alt="<?= htmlspecialchars($m['full_name']) ?>">
                                                </td>
                                                <td class="fw-600"><?= htmlspecialchars($m['full_name']) ?></td>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($m['role']) ?></span></td>
                                                <td class="text-muted small">
                                                    <?= $m['date_of_birth'] ? (new DateTime($m['date_of_birth']))->format('j M Y') : '—' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($tournament_results) || !empty($tournament_upcoming)): ?>
            <div class="mt-5">
                <?php $tHeaderName = $tournament_results[0]['competition_name'] ?? ($tournament_upcoming[0]['competition_name'] ?? 'Tournament'); ?>
                <h4 class="fw-bold text-center mb-4" style="color:#9c27b0;"><?= htmlspecialchars($tHeaderName) ?></h4>
                <div class="club-content-row">
                    <?php if (!empty($tournament_results)): ?>
                    <div class="club-panel">
                        <div class="panel-header">Results</div>
                        <div class="panel-body">
                            <div class="matches-grid">
                                <?php foreach ($tournament_results as $r):
                                    $isHome = $r['home_club_id'] == $club_id;
                                    $homeWin = $r['home_score'] > $r['away_score'];
                                    $awayWin = $r['away_score'] > $r['home_score'];
                                    $homeLogo = $r['home_logo'] ? 'uploads/clubs/'.$r['home_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $awayLogo = $r['away_logo'] ? 'uploads/clubs/'.$r['away_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $dateStr = (new DateTime($r['match_date']))->format('j M Y');
                                    $tournName = htmlspecialchars($r['competition_name'] ?? 'Tournament');
                                    $events = getTournamentMatchEvents($r['match_id']);
                                    $eventHtml = '';
                                    foreach ($events['goals'] as $g) {
                                        $team = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                        $penalty = $g['is_penalty'] ? ' (P)' : '';
                                        $eventHtml .= "<div class='event-row event-team {$team}'>
                                            <div class='event-minutes'>{$g['minute']}'</div>
                                            <div class='event-icon goal-icon'>G</div>
                                            <div class='event-detail'><span class='event-name'>{$g['scorer']}{$penalty}</span></div>
                                        </div>";
                                        if (!empty($g['assist'])) {
                                            $eventHtml .= "<div class='event-row event-team {$team}'>
                                                <div class='event-minutes'></div>
                                                <div class='event-icon assist-icon'>A</div>
                                                <div class='event-detail'><span class='event-name'>{$g['assist']}</span></div>
                                            </div>";
                                        }
                                    }
                                    foreach ($events['cards'] as $c) {
                                        $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                        $icon = $c['card_type'] == 'yellow' ? 'yellow-icon' : 'red-icon';
                                        $text = $c['card_type'] == 'yellow' ? 'YC' : 'RC';
                                        $eventHtml .= "<div class='event-row event-team {$team}'>
                                            <div class='event-minutes'>{$c['minute']}'</div>
                                            <div class='event-icon {$icon}'>{$text}</div>
                                            <div class='event-detail'><span class='event-name'>{$c['name']}</span></div>
                                        </div>";
                                    }
                                    $eventHtml = $eventHtml ?: '<div class="text-center text-muted py-3 small">No events recorded</div>';
                                ?>
                                    <div class="match-item">
                                        <div class="match-row">
                                            <div class="teams-stack">
                                                <div class="team-item">
                                                    <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $isHome ? 'bold' : '' ?> <?= $homeWin ? 'winner' : '' ?>">
                                                            <?= htmlspecialchars($r['home_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="team-item">
                                                    <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= !$isHome ? 'bold' : '' ?> <?= $awayWin ? 'winner' : '' ?>">
                                                            <?= htmlspecialchars($r['away_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="score-stack">
                                                <span class="home-score"><?= $r['home_score'] ?></span>
                                                <span class="away-score"><?= $r['away_score'] ?></span>
                                            </div>
                                        </div>
                                        <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#tev<?= $r['match_id'] ?>">
                                            <span class="info"><?= $dateStr ?> • <?= $tournName ?></span>
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                        <div class="collapse event-list" id="tev<?= $r['match_id'] ?>">
                                            <?= $eventHtml ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($tournament_upcoming)): ?>
                    <div class="club-panel">
                        <div class="panel-header">Fixtures</div>
                        <div class="panel-body">
                            <div class="matches-grid">
                                <?php foreach ($tournament_upcoming as $f):
                                    $homeLogo = $f['home_logo'] ? 'uploads/clubs/'.$f['home_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $awayLogo = $f['away_logo'] ? 'uploads/clubs/'.$f['away_logo'] : 'https://via.placeholder.com/48/defcfc/333333';
                                    $dt = new DateTime($f['tournament_date'] ?? $f['fixture_date'] ?? 'now');
                                    $shortDate = $dt->format('D d M');
                                    $time = $dt->format('H:i');
                                    $tournName = htmlspecialchars($f['competition_name'] ?? 'Tournament');
                                ?>
                                    <div class="match-item">
                                        <div class="match-row">
                                            <div class="teams-stack">
                                                <div class="team-item">
                                                    <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $f['home_club_id']==$club_id?'bold':'' ?>">
                                                            <?= htmlspecialchars($f['home_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="team-item">
                                                    <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                    <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="text-decoration-none">
                                                        <div class="team-name <?= $f['away_club_id']==$club_id?'bold':'' ?>">
                                                            <?= htmlspecialchars($f['away_name']) ?>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="fixture-info">
                                                <div class="date"><?= $shortDate ?></div>
                                                <div class="time"><?= $time ?></div>
                                                <div class="venue"><?= htmlspecialchars($f['venue'] ?? 'TBD') ?></div>
                                            </div>
                                        </div>
                                        <div class="match-toggle">
                                            <span class="info"><?= $tournName ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.panel-tabs a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const tab = this.dataset.tab;
        const panel = this.closest('.club-panel');
        panel.querySelectorAll('.tab-pane').forEach(pane => {
            pane.style.display = 'none';
            pane.classList.remove('active');
        });
        panel.querySelectorAll('.panel-tabs a').forEach(a => a.classList.remove('active'));
        const target = document.getElementById(tab);
        if (target) {
            target.style.display = 'block';
            target.classList.add('active');
        }
        this.classList.add('active');
    });
});
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.match-toggle[data-bs-toggle="collapse"]').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const targetId = this.getAttribute('data-bs-target');
            const targetCollapse = document.querySelector(targetId);
            this.closest('.matches-grid').querySelectorAll('.collapse.show').forEach(open => {
                if (open !== targetCollapse) {
                    bootstrap.Collapse.getInstance(open)?.hide();
                }
            });
        });
    });
});
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>