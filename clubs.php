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
if (empty($available_seasons)) $available_seasons = [date('Y'), date('Y') - 1];
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
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --gold:       #c9a84c;
        --gold-light: #f0d080;
        --cream:      #fdf8ef;
        --dark-panel: #1a1a2e;
        --dark-tab:   #16152b;
        --border:     rgba(201,168,76,0.25);
        --muted:      #6b7280;
        --card-bg:    #ffffff;
        --card-hover: #f9f7f2;
    }

    html, body {
        background-color: #f0ede8 !important;
        background-image:
            radial-gradient(ellipse at 20% 20%, rgba(201,168,76,0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 80%, rgba(180,160,120,0.06) 0%, transparent 50%);
        background-attachment: fixed;
        color: #1a1a2e;
        margin: 0; padding: 0;
    }

    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    .clubs-page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem 4rem;
    }
    @media (min-width: 992px) { .clubs-page-wrapper { padding-top: 4.5rem; } }
    @media (max-width: 767px) { .clubs-page-wrapper { padding: 1.5rem 0.75rem 3rem; } }
    @media (max-width: 480px) {
        .clubs-page-wrapper { padding: 1rem 0 3rem; }
        .clubs-page-header  { padding: 0 0.75rem 0; }
        .all-clubs-grid     { padding: 0 0.5rem; }
    }

    .clubs-page-header {
        text-align: center;
        margin-bottom: 2.5rem;
        position: relative;
    }
    .clubs-page-header::before {
        content: '';
        display: block;
        width: 60px; height: 3px;
        background: var(--gold);
        margin: 0 auto 1.2rem;
        border-radius: 2px;
    }
    .clubs-page-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.6rem, 4vw, 2.8rem);
        font-weight: 900;
        color: #1a1a2e;
        letter-spacing: -0.5px;
        margin: 0 0 0.5rem;
    }
    .clubs-page-header p {
        font-family: 'DM Sans', sans-serif;
        color: var(--muted);
        font-size: 0.95rem;
        margin: 0;
    }
    .clubs-page-header::after {
        content: '';
        display: block;
        width: 40px; height: 2px;
        background: var(--gold);
        opacity: 0.5;
        margin: 1.2rem auto 0;
        border-radius: 2px;
    }

    .all-clubs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.6rem;
    }

    .club-card-big {
        background: var(--card-bg);
        border: 1px solid rgba(201,168,76,0.2);
        border-radius: 14px;
        text-align: center;
        padding: 2rem 1rem 1.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    .club-card-big:hover {
        transform: translateY(-6px);
        border-color: rgba(201,168,76,0.5);
        background: var(--card-hover);
        box-shadow: 0 16px 40px rgba(0,0,0,0.13);
    }

    .club-logo-ring {
        width: 120px; height: 120px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid rgba(201,168,76,0.35);
        padding: 10px;
        margin: 0 auto 1.2rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transition: border-color 0.3s, box-shadow 0.3s;
        overflow: hidden;
        flex-shrink: 0;
    }
    .club-card-big:hover .club-logo-ring {
        border-color: var(--gold);
        box-shadow: 0 8px 24px rgba(201,168,76,0.2);
    }
    .club-logo-ring img {
        width: 100%; height: 100%;
        object-fit: contain;
        border-radius: 50%;
        display: block;
        background: #ffffff;
    }
    .club-card-name {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.92rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        line-height: 1.3;
    }
    .club-card-big:hover .club-card-name { color: #9a6f1e; }

    @media (max-width: 767px) {
        .clubs-page-wrapper {
            padding-left: 0 !important; padding-right: 0 !important;
            max-width: 100% !important; width: 100% !important;
        }
        .clubs-page-header { padding: 0 1rem; }
        .all-clubs-grid    { padding: 0 0.75rem; }
    }
    @media (max-width: 576px) {
        .all-clubs-grid { grid-template-columns: 1fr 1fr; gap: 0.85rem; }
        .club-logo-ring { width: 88px; height: 88px; padding: 8px; }
    }
    @media (max-width: 360px) { .all-clubs-grid { grid-template-columns: 1fr; } }
</style>

<div class="main-content">
    <div class="clubs-page-wrapper">
        <div class="clubs-page-header">
            <h1>All Clubs</h1>
            <p>Member Clubs of the League</p>
        </div>
        <div class="all-clubs-grid">
            <?php foreach ($clubs as $club):
                $logo = $club['logo']
                    ? 'uploads/clubs/' . htmlspecialchars($club['logo'])
                    : 'https://via.placeholder.com/120/1a1a2e/c9a84c?text=' . urlencode(substr($club['name'], 0, 2));
            ?>
                <a href="clubs.php?club_id=<?= $club['id'] ?>" class="club-card-big">
                    <div class="club-logo-ring">
                        <img src="<?= $logo ?>" alt="<?= htmlspecialchars($club['name']) ?>">
                    </div>
                    <div class="club-card-name"><?= htmlspecialchars($club['name']) ?></div>
                </a>
            <?php endforeach; ?>
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
        $points   = $s['points'];
        $gf       = $s['gf'];
        $ga       = $s['ga'];
        $gd       = $s['gd'];
        break;
    }
}

$recent_results      = getClubRecentLeagueResults($club_id, 100);
$upcoming_fixtures   = getClubUpcomingLeagueFixtures($club_id, 100);
$tournament_results  = getClubTournamentResults($club_id);
$tournament_upcoming = getClubTournamentFixtures($club_id);
$players             = getClubPlayersWithStats($club_id, $selected_season);

$recent_results    = array_filter($recent_results,    fn($m) => (new DateTime($m['match_date']))->format('Y')   == $selected_season);
$upcoming_fixtures = array_filter($upcoming_fixtures, fn($f) => (new DateTime($f['fixture_date']))->format('Y') == $selected_season);

$logo_url = $club['logo']
    ? 'uploads/clubs/' . htmlspecialchars($club['logo'])
    : 'https://via.placeholder.com/280/1a1a2e/c9a84c?text=' . urlencode(substr($club['name'], 0, 2));

$management_stmt = $pdo->prepare("
    SELECT id, full_name, role, date_of_birth, photo
    FROM management
    WHERE club_id = ? AND is_active = 1
    ORDER BY
        FIELD(role,'Coach','Assistant Coach','Referee','Secretary','Treasurer','Committee Member','Medical Aid','Councillor'),
        full_name ASC
");
$management_stmt->execute([$club_id]);
$management = $management_stmt->fetchAll(PDO::FETCH_ASSOC);
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
        margin: 0; padding: 0;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Wrapper ── */
    .clubs-page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem 4rem;
    }
    @media (min-width: 992px) { .clubs-page-wrapper { padding-top: 4.5rem; } }
    @media (max-width: 767.98px) {
        .clubs-page-wrapper {
            padding-left: 0 !important; padding-right: 0 !important;
            max-width: 100% !important; width: 100% !important;
        }
        .club-content-row { padding: 0 0.75rem; gap: 1rem; margin-top: 1rem; }
    }

    /* ── Club Overview Banner ── */
    .club-overview-compact {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.09);
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) { .club-overview-compact { flex-direction: row; min-height: 240px; } }
    @media (max-width: 767.98px) { .club-overview-compact { border-radius: 0; border-left: none; border-right: none; } }

    /* Left: logo panel — DARK */
    .club-logo-left {
        background: linear-gradient(160deg, #0f0f1e 0%, #1e1a40 100%);
        display: flex; align-items: center; justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .club-logo-left::after {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at center, rgba(201,168,76,0.1) 0%, transparent 70%);
    }
    @media (min-width: 768px) { .club-logo-left { width: 260px; flex-shrink: 0; } }

    .club-logo-circle {
        width: 160px; height: 160px;
        background: #ffffff;
        border: 3px solid rgba(201,168,76,0.4);
        border-radius: 50%;
        padding: 12px;
        box-shadow: 0 0 40px rgba(201,168,76,0.15), 0 12px 32px rgba(0,0,0,0.4);
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 1;
        overflow: hidden;
    }
    @media (min-width: 768px) { .club-logo-circle { width: 190px; height: 190px; } }
    .club-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; background: #ffffff; }

    /* Right: info panel — LIGHT */
    .club-info-right { flex: 1; display: flex; flex-direction: column; background: #ffffff; }

    /* Club name header — DARK */
    .club-header-compact {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1.4rem 1.8rem;
        text-align: center;
    }
    .club-name-main {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.5rem, 4vw, 2.4rem);
        font-weight: 900;
        color: var(--cream);
        margin: 0 0 0.5rem;
        line-height: 1.2;
        text-shadow: 0 2px 16px rgba(0,0,0,0.5);
    }
    .club-meta-horizontal {
        display: flex; flex-wrap: wrap; justify-content: center;
        gap: 0.5rem 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.55);
        margin-top: 0.3rem;
    }
    .badge-pos {
        background: rgba(201,168,76,0.15);
        border: 1px solid rgba(201,168,76,0.4);
        color: var(--gold);
        padding: 0.25rem 0.85rem;
        font-weight: 700;
        border-radius: 20px;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
    }

    /* Stats strip — LIGHT background */
    .club-stats-compact {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-top: auto;
        border-top: 1px solid #e5e7eb;
        background: #f9f7f2;
    }
    @media (max-width: 480px) { .club-stats-compact { grid-template-columns: 1fr 1fr; } }

    .stat-item-compact {
        text-align: center;
        padding: 1.1rem 0.5rem;
        border-right: 1px solid #e5e7eb;
        position: relative;
    }
    .stat-item-compact:last-child { border-right: none; }
    .stat-value-compact {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-main);
        line-height: 1;
    }
    .stat-value-compact.pts { color: var(--gold-dark); }
    .gd-positive { color: #16a34a !important; }
    .gd-negative { color: #dc2626 !important; }
    .stat-label-compact {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.7rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-top: 4px;
    }

    /* ── Content Layout ── */
    .club-content-row {
        display: flex;
        flex-direction: column;
        gap: 1.6rem;
        margin-top: 1.6rem;
    }
    @media (min-width: 992px) {
        .club-content-row { flex-direction: row; flex-wrap: nowrap; }
        .club-panel-league  { flex: 0 0 calc(70% - 0.8rem); max-width: calc(70% - 0.8rem); }
        .club-panel-players { flex: 0 0 calc(30% - 0.8rem); max-width: calc(30% - 0.8rem); }
    }
    @media (max-width: 991.98px) {
        .club-content-row { gap: 1rem; margin-top: 1rem; }
        .club-panel, .club-panel-league, .club-panel-players { flex: 0 0 100%; max-width: 100%; width: 100%; }
    }
    @media (max-width: 767.98px) {
        .club-content-row { gap: 0.75rem; margin-top: 0.75rem; padding: 0 0.5rem; }
    }

    /* ── Panel Card — LIGHT body ── */
    .club-panel {
        flex: 1; min-width: 0; width: 100%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    @media (max-width: 767.98px) { .club-panel { border-radius: 10px; } }

    /* Panel tab bar — DARK */
    .panel-tabs {
        display: flex;
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        border-bottom: 2px solid var(--border);
    }
    .panel-tabs a {
        flex: 1; text-align: center;
        padding: 0.9rem 1rem;
        color: rgba(255,255,255,0.5);
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        letter-spacing: 0.3px;
        transition: all 0.25s;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
    }
    .panel-tabs a.active {
        color: var(--gold);
        border-bottom-color: var(--gold);
        background: rgba(201,168,76,0.07);
    }
    .panel-tabs a:hover:not(.active) {
        color: #ffffff;
        background: rgba(255,255,255,0.06);
    }

    /* Season selector — dark to match tab bar */
    .season-select {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(201,168,76,0.35);
        color: var(--gold);
        padding: 0.3rem 2rem 0.3rem 0.7rem;
        font-size: 0.82rem;
        border-radius: 6px;
        -webkit-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23c9a84c'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 11px;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
    }
    .season-select:focus { outline: none; border-color: var(--gold); }
    .season-select option { background: #1a1a2e; color: var(--gold); }

    /* Panel header (tournament) — DARK */
    .panel-header {
        background: linear-gradient(135deg, #3a1a5c, #6a1b9a);
        border-bottom: 2px solid rgba(201,168,76,0.3);
        color: var(--cream);
        padding: 1rem 1.6rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        text-align: center;
    }

    /* Panel body — LIGHT */
    .panel-body { padding: 0.75rem; background: #ffffff; }

    /* ── Match Items — LIGHT ── */
    .matches-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.85rem;
    }
    @media (min-width: 992px) { .matches-grid { grid-template-columns: 1fr 1fr; } }

    .match-item {
        background: #f9f8f5;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        display: flex; flex-direction: column;
        transition: all 0.25s ease;
    }
    .match-item:hover {
        border-color: rgba(201,168,76,0.4);
        background: #fdf9f0;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .match-row {
        padding: 0.75rem 1rem;
        display: flex; align-items: center;
        justify-content: space-between;
        min-height: 72px;
        background: #f9f8f5;
    }

    .teams-stack {
        display: flex; flex-direction: column;
        gap: 6px; flex: 1;
        padding-right: 14px;
        position: relative;
    }
    .teams-stack::after {
        content: '';
        position: absolute; right: 0; top: 50%;
        transform: translateY(-50%);
        width: 1px; height: 46px;
        background: #d1d5db;
    }

    .team-item { display: flex; align-items: center; gap: 9px; min-width: 0; }

    .team-logo {
        width: 34px; height: 34px;
        object-fit: contain;
        background: #ffffff;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .team-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        font-size: 0.88rem;
        color: var(--text-soft);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-decoration: none;
        transition: color 0.2s;
    }
    .team-name:hover { color: var(--gold-dark); }
    .team-name.bold  { font-weight: 700; color: var(--text-main); }
    .winner { color: #15803d !important; font-weight: 700 !important; }

    .score-stack {
        text-align: center;
        min-width: 52px;
        flex-shrink: 0;
        line-height: 1.1;
    }
    .home-score, .away-score {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .fixture-info {
        text-align: right;
        min-width: 80px;
        flex-shrink: 0;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        color: var(--muted);
        line-height: 1.5;
    }
    .fixture-info .date { font-weight: 600; color: var(--text-soft); font-size: 0.82rem; }

    /* Match toggle footer — DARK */
    .match-toggle {
        padding: 0.38rem 1rem;
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        border-top: 1px solid rgba(201,168,76,0.15);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        color: rgba(255,255,255,0.5);
        cursor: pointer;
        display: flex; justify-content: space-between; align-items: center;
        transition: background 0.2s;
    }
    .match-toggle:hover { background: linear-gradient(135deg, #1a1830, #2a2650); }
    .match-toggle i { font-size: 0.85rem; color: rgba(201,168,76,0.7); transition: transform 0.25s; }
    .match-toggle[aria-expanded="true"] i { transform: rotate(180deg); }

    /* Event list — slightly off-white */
    .event-list {
        background: #f3f1ec;
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        max-height: 260px;
        overflow-y: auto;
        border-top: 1px solid #e5e7eb;
    }
    .event-row {
        display: flex; align-items: flex-start;
        gap: 8px; margin-bottom: 6px;
        padding: 2px 0; line-height: 1.3;
    }
    .event-team.away { justify-content: flex-end; text-align: right; flex-direction: row-reverse; }
    .event-minutes {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700; color: var(--text-main);
        min-width: 34px; font-size: 0.8em;
    }
    .event-icon {
        width: 20px; height: 20px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 8px; font-weight: bold; color: white; flex-shrink: 0;
    }
    .goal-icon   { background: #2563eb; }
    .assist-icon { background: #c9a84c; color: #000; }
    .yellow-icon { background: #eab308; color: #000; }
    .red-icon    { background: #dc2626; }
    .event-detail { flex: 1; min-width: 0; }
    .event-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600; display: block;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: var(--text-soft);
    }

    /* ── Tables — LIGHT ── */
    .table {
        background: #ffffff;
        color: var(--text-main);
        font-family: 'DM Sans', sans-serif;
        margin: 0;
    }
    /* Table header — DARK */
    .table thead th {
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        color: var(--gold);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-color: rgba(201,168,76,0.2);
        padding: 0.65rem 0.6rem;
    }
    .table tbody tr {
        background: #ffffff;
        border-color: #f3f4f6;
        transition: background 0.2s;
    }
    .table tbody tr:hover { background: #fdf9f0; }
    .table td { border-color: #f3f4f6; vertical-align: middle; padding: 0.55rem 0.6rem; }
    .table a { color: var(--text-soft); text-decoration: none; transition: color 0.2s; }
    .table a:hover { color: var(--gold-dark); }

    /* Status badges */
    .badge-status {
        font-size: 0.65rem; font-weight: 700;
        padding: 2px 7px; border-radius: 10px;
        letter-spacing: 0.3px;
    }

    /* Role badge */
    .badge-role {
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.35);
        color: var(--gold-dark);
        font-size: 0.7rem; font-weight: 600;
        padding: 2px 8px; border-radius: 10px;
        letter-spacing: 0.3px;
        font-family: 'DM Sans', sans-serif;
    }

    /* Tournament section title */
    .tournament-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--gold-dark);
        text-align: center;
        margin: 2.5rem 0 1.2rem;
        position: relative;
    }
    .tournament-section-title::before,
    .tournament-section-title::after {
        content: '';
        position: absolute;
        top: 50%; transform: translateY(-50%);
        height: 1px; width: 15%;
        opacity: 0.4;
    }
    .tournament-section-title::before { left: 10%; background: linear-gradient(to right, transparent, var(--gold)); }
    .tournament-section-title::after  { right: 10%; background: linear-gradient(to left, transparent, var(--gold)); }

    /* Empty states */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        font-family: 'DM Sans', sans-serif;
        color: var(--muted);
        font-size: 0.9rem;
    }
    .empty-state i { font-size: 2rem; color: rgba(201,168,76,0.4); display: block; margin-bottom: 0.75rem; }
</style>

<div class="main-content">
    <div class="clubs-page-wrapper">

        <!-- ══ CLUB OVERVIEW BANNER ══ -->
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
                        <?php if (!empty($club['stadium'])): ?>
                            <span><i class="bi bi-geo-alt" style="color:var(--gold);margin-right:4px;"></i><?= htmlspecialchars($club['stadium']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($club['description'])): ?>
                            <span><?= htmlspecialchars($club['description']) ?></span>
                        <?php endif; ?>
                        <span class="badge-pos">Position #<?= $position ?></span>
                    </div>
                </div>
                <div class="club-stats-compact">
                    <div class="stat-item-compact">
                        <div class="stat-value-compact pts"><?= $points ?></div>
                        <div class="stat-label-compact">Points</div>
                    </div>
                    <div class="stat-item-compact">
                        <div class="stat-value-compact"><?= $gf ?></div>
                        <div class="stat-label-compact">GF</div>
                    </div>
                    <div class="stat-item-compact">
                        <div class="stat-value-compact"><?= $ga ?></div>
                        <div class="stat-label-compact">GA</div>
                    </div>
                    <div class="stat-item-compact">
                        <div class="stat-value-compact <?= $gd >= 0 ? 'gd-positive' : 'gd-negative' ?>">
                            <?= $gd >= 0 ? '+' : '' ?><?= $gd ?>
                        </div>
                        <div class="stat-label-compact">GD</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ RESULTS / FIXTURES + PLAYERS / MANAGEMENT ══ -->
        <div class="club-content-row">

            <!-- LEFT: Results & Fixtures -->
            <div class="club-panel club-panel-league">
                <div class="panel-tabs">
                    <a href="#" class="active d-flex align-items-center justify-content-center gap-2" data-tab="results">
                        <form method="get" style="margin:0;">
                            <input type="hidden" name="club_id" value="<?= $club_id ?>">
                            <select name="season" onchange="this.form.submit()" class="season-select">
                                <?php foreach ($available_seasons as $year): ?>
                                    <option value="<?= $year ?>" <?= $year == $selected_season ? 'selected' : '' ?>>
                                        <?= $year ?>/<?= $year + 1 ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        Results
                    </a>
                    <a href="#" data-tab="fixtures">Fixtures</a>
                </div>

                <div class="panel-body">

                    <!-- Results Tab -->
                    <div id="results" class="tab-pane active">
                        <div class="matches-grid">
                            <?php if (empty($recent_results)): ?>
                                <div class="empty-state" style="grid-column:1/-1;">
                                    <i class="bi bi-calendar-x"></i>No results this season
                                </div>
                            <?php else: foreach ($recent_results as $r):
                                $isHome  = $r['home_club_id'] == $club_id;
                                $homeWin = $r['home_score'] > $r['away_score'];
                                $awayWin = $r['away_score'] > $r['home_score'];
                                $homeLogo = $r['home_logo'] ? 'uploads/clubs/' . $r['home_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $awayLogo = $r['away_logo'] ? 'uploads/clubs/' . $r['away_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $dateStr  = (new DateTime($r['match_date']))->format('j M Y');
                                $events   = getLeagueMatchEvents($r['match_id']);
                                $eventHtml = '';
                                foreach ($events['goals'] as $g) {
                                    $team    = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                    $penalty = $g['is_penalty'] ? ' (P)' : '';
                                    $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'>{$g['minute']}'</div><div class='event-icon goal-icon'>G</div><div class='event-detail'><span class='event-name'>{$g['scorer']}{$penalty}</span></div></div>";
                                    if (!empty($g['assist'])) {
                                        $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'></div><div class='event-icon assist-icon'>A</div><div class='event-detail'><span class='event-name'>{$g['assist']}</span></div></div>";
                                    }
                                }
                                foreach ($events['cards'] as $c) {
                                    $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                    $icon = $c['card_type'] == 'yellow' ? 'yellow-icon' : 'red-icon';
                                    $text = $c['card_type'] == 'yellow' ? 'YC' : 'RC';
                                    $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'>{$c['minute']}'</div><div class='event-icon {$icon}'>{$text}</div><div class='event-detail'><span class='event-name'>{$c['name']}</span></div></div>";
                                }
                                $eventHtml = $eventHtml ?: '<div class="empty-state" style="padding:0.8rem;"><span style="font-size:0.78rem;">No events recorded</span></div>';
                            ?>
                                <div class="match-item">
                                    <div class="match-row">
                                        <div class="teams-stack">
                                            <div class="team-item">
                                                <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="team-name <?= $isHome ? 'bold' : '' ?> <?= $homeWin ? 'winner' : '' ?>">
                                                    <?= htmlspecialchars($r['home_name']) ?>
                                                </a>
                                            </div>
                                            <div class="team-item">
                                                <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="team-name <?= !$isHome ? 'bold' : '' ?> <?= $awayWin ? 'winner' : '' ?>">
                                                    <?= htmlspecialchars($r['away_name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="score-stack">
                                            <span class="home-score"><?= $r['home_score'] ?></span>
                                            <span class="away-score"><?= $r['away_score'] ?></span>
                                        </div>
                                    </div>
                                    <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#ev<?= $r['match_id'] ?>">
                                        <span><?= $dateStr ?> &bull; <?= htmlspecialchars($r['venue'] ?? 'TBD') ?></span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div class="collapse event-list" id="ev<?= $r['match_id'] ?>">
                                        <?= $eventHtml ?>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Fixtures Tab -->
                    <div id="fixtures" class="tab-pane" style="display:none;">
                        <div class="matches-grid">
                            <?php if (empty($upcoming_fixtures)): ?>
                                <div class="empty-state" style="grid-column:1/-1;">
                                    <i class="bi bi-calendar3"></i>No upcoming fixtures
                                </div>
                            <?php else: foreach ($upcoming_fixtures as $f):
                                $homeLogo  = $f['home_logo'] ? 'uploads/clubs/' . $f['home_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $awayLogo  = $f['away_logo'] ? 'uploads/clubs/' . $f['away_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $dt        = new DateTime($f['fixture_date']);
                                $shortDate = $dt->format('D d M');
                                $time      = $dt->format('H:i');
                            ?>
                                <div class="match-item">
                                    <div class="match-row">
                                        <div class="teams-stack">
                                            <div class="team-item">
                                                <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="team-name <?= $f['home_club_id'] == $club_id ? 'bold' : '' ?>">
                                                    <?= htmlspecialchars($f['home_name']) ?>
                                                </a>
                                            </div>
                                            <div class="team-item">
                                                <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="team-name <?= $f['away_club_id'] == $club_id ? 'bold' : '' ?>">
                                                    <?= htmlspecialchars($f['away_name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="fixture-info">
                                            <div class="date"><?= $shortDate ?></div>
                                            <div><?= $time ?></div>
                                            <div><?= htmlspecialchars($f['venue'] ?? 'TBD') ?></div>
                                        </div>
                                    </div>
                                    <div class="match-toggle">
                                        <span>Upcoming Match</span>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                </div>
            </div><!-- /.club-panel-league -->

            <!-- RIGHT: Players & Management -->
            <div class="club-panel club-panel-players">
                <div class="panel-tabs">
                    <a href="#" class="active" data-tab="players-tab">Players</a>
                    <a href="#" data-tab="management-tab">Management</a>
                </div>
                <div class="panel-body" style="padding:0;">

                    <!-- Players Tab -->
                    <div id="players-tab" class="tab-pane active">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" style="font-size:0.82rem;">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Player</th><th>Age</th>
                                        <th>G</th><th>A</th><th>Y</th><th>R</th><th>CS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($players as $i => $p):
                                        $playerLink = 'player_profile.php?player_id=' . $p['id'];
                                        $status     = $p['status'] ?? '';
                                        $statusColors = [
                                            'Active'    => 'background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.35);color:#15803d;',
                                            'Inactive'  => 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#dc2626;',
                                            'Injured'   => 'background:rgba(234,179,8,0.12);border:1px solid rgba(234,179,8,0.35);color:#b45309;',
                                            'Suspended' => 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#dc2626;',
                                        ];
                                        $badgeStyle = $statusColors[$status] ?? 'background:rgba(100,100,100,0.1);border:1px solid rgba(100,100,100,0.25);color:#6b7280;';
                                    ?>
                                    <tr style="cursor:pointer;" onclick="window.location='<?= $playerLink ?>'">
                                        <td style="color:var(--muted);"><?= $i + 1 ?></td>
                                        <td>
                                            <a href="<?= $playerLink ?>" class="d-flex align-items-center gap-2" style="text-decoration:none;">
                                                <img src="<?= $p['photo'] ? 'uploads/players/' . $p['photo'] : 'https://via.placeholder.com/32/1a1a2e/c9a84c' ?>"
                                                    class="rounded-circle" width="30" height="30"
                                                    style="object-fit:cover;border:1px solid rgba(201,168,76,0.3);">
                                                <div>
                                                    <div style="font-weight:600;color:var(--text-main);font-size:0.84rem;line-height:1.2;">
                                                        <?= htmlspecialchars($p['name']) ?>
                                                    </div>
                                                    <?php if ($status): ?>
                                                    <span class="d-none d-lg-inline badge-status" style="<?= $badgeStyle ?>">
                                                        <?= htmlspecialchars($status) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </td>
                                        <td style="color:var(--muted);"><?= $p['age'] ?? '—' ?></td>
                                        <td style="color:var(--gold-dark);font-weight:700;"><?= $p['goals']       ?? 0 ?></td>
                                        <td style="color:var(--text-soft);"><?= $p['assists']      ?? 0 ?></td>
                                        <td style="color:#b45309;"><?= $p['yellow_cards'] ?? 0 ?></td>
                                        <td style="color:#dc2626;"><?= $p['red_cards']    ?? 0 ?></td>
                                        <td style="color:var(--text-soft);"><?= $p['clean_sheets'] ?? 0 ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Management Tab -->
                    <div id="management-tab" class="tab-pane" style="display:none;">
                        <?php if (empty($management)): ?>
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                No management staff registered yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" style="font-size:0.82rem;">
                                    <thead>
                                        <tr>
                                            <th>#</th><th>Photo</th><th>Name</th><th>Role</th><th>DOB</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($management as $i => $m):
                                            $photo_url = $m['photo']
                                                ? 'uploads/management/' . htmlspecialchars($m['photo'])
                                                : 'https://via.placeholder.com/40/1a1a2e/c9a84c?text=' . urlencode(substr($m['full_name'], 0, 2));
                                        ?>
                                        <tr style="cursor:pointer;" onclick="window.location='management_profile.php?staff_id=<?= $m['id'] ?>'">
                                            <td style="color:var(--muted);"><?= $i + 1 ?></td>
                                            <td>
                                                <img src="<?= $photo_url ?>"
                                                     class="rounded-circle"
                                                     width="36" height="36"
                                                     style="object-fit:cover;border:1px solid rgba(201,168,76,0.3);"
                                                     alt="<?= htmlspecialchars($m['full_name']) ?>">
                                            </td>
                                            <td style="font-weight:600;color:var(--text-main);">
                                                <?= htmlspecialchars($m['full_name']) ?>
                                            </td>
                                            <td><span class="badge-role"><?= htmlspecialchars($m['role']) ?></span></td>
                                            <td style="color:var(--muted);font-size:0.78rem;">
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
            </div><!-- /.club-panel-players -->

        </div><!-- /.club-content-row -->

        <!-- ══ TOURNAMENT SECTION ══ -->
        <?php if (!empty($tournament_results) || !empty($tournament_upcoming)):
            $tHeaderName = $tournament_results[0]['competition_name'] ?? ($tournament_upcoming[0]['competition_name'] ?? 'Tournament');
        ?>
        <div class="mt-4">
            <div class="tournament-section-title"><?= htmlspecialchars($tHeaderName) ?></div>
            <div class="club-content-row">

                <?php if (!empty($tournament_results)): ?>
                <div class="club-panel">
                    <div class="panel-header"><i class="bi bi-trophy me-2"></i>Results</div>
                    <div class="panel-body">
                        <div class="matches-grid">
                            <?php foreach ($tournament_results as $r):
                                $isHome   = $r['home_club_id'] == $club_id;
                                $homeWin  = $r['home_score'] > $r['away_score'];
                                $awayWin  = $r['away_score'] > $r['home_score'];
                                $homeLogo = $r['home_logo'] ? 'uploads/clubs/' . $r['home_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $awayLogo = $r['away_logo'] ? 'uploads/clubs/' . $r['away_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $dateStr  = (new DateTime($r['match_date']))->format('j M Y');
                                $tournName = htmlspecialchars($r['competition_name'] ?? 'Tournament');
                                $events   = getTournamentMatchEvents($r['match_id']);
                                $eventHtml = '';
                                foreach ($events['goals'] as $g) {
                                    $team    = $g['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                    $penalty = $g['is_penalty'] ? ' (P)' : '';
                                    $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'>{$g['minute']}'</div><div class='event-icon goal-icon'>G</div><div class='event-detail'><span class='event-name'>{$g['scorer']}{$penalty}</span></div></div>";
                                    if (!empty($g['assist'])) {
                                        $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'></div><div class='event-icon assist-icon'>A</div><div class='event-detail'><span class='event-name'>{$g['assist']}</span></div></div>";
                                    }
                                }
                                foreach ($events['cards'] as $c) {
                                    $team = $c['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                                    $icon = $c['card_type'] == 'yellow' ? 'yellow-icon' : 'red-icon';
                                    $text = $c['card_type'] == 'yellow' ? 'YC' : 'RC';
                                    $eventHtml .= "<div class='event-row event-team {$team}'><div class='event-minutes'>{$c['minute']}'</div><div class='event-icon {$icon}'>{$text}</div><div class='event-detail'><span class='event-name'>{$c['name']}</span></div></div>";
                                }
                                $eventHtml = $eventHtml ?: '<div class="empty-state" style="padding:0.8rem;"><span style="font-size:0.78rem;">No events recorded</span></div>';
                            ?>
                                <div class="match-item">
                                    <div class="match-row">
                                        <div class="teams-stack">
                                            <div class="team-item">
                                                <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="team-name <?= $isHome ? 'bold' : '' ?> <?= $homeWin ? 'winner' : '' ?>">
                                                    <?= htmlspecialchars($r['home_name']) ?>
                                                </a>
                                            </div>
                                            <div class="team-item">
                                                <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="team-name <?= !$isHome ? 'bold' : '' ?> <?= $awayWin ? 'winner' : '' ?>">
                                                    <?= htmlspecialchars($r['away_name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="score-stack">
                                            <span class="home-score"><?= $r['home_score'] ?></span>
                                            <span class="away-score"><?= $r['away_score'] ?></span>
                                        </div>
                                    </div>
                                    <div class="match-toggle" data-bs-toggle="collapse" data-bs-target="#tev<?= $r['match_id'] ?>">
                                        <span><?= $dateStr ?> &bull; <?= $tournName ?></span>
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
                    <div class="panel-header"><i class="bi bi-calendar-event me-2"></i>Fixtures</div>
                    <div class="panel-body">
                        <div class="matches-grid">
                            <?php foreach ($tournament_upcoming as $f):
                                $homeLogo  = $f['home_logo'] ? 'uploads/clubs/' . $f['home_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $awayLogo  = $f['away_logo'] ? 'uploads/clubs/' . $f['away_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c';
                                $dt        = new DateTime($f['tournament_date'] ?? $f['fixture_date'] ?? 'now');
                                $shortDate = $dt->format('D d M');
                                $time      = $dt->format('H:i');
                                $tournName = htmlspecialchars($f['competition_name'] ?? 'Tournament');
                            ?>
                                <div class="match-item">
                                    <div class="match-row">
                                        <div class="teams-stack">
                                            <div class="team-item">
                                                <img src="<?= $homeLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="team-name <?= $f['home_club_id'] == $club_id ? 'bold' : '' ?>">
                                                    <?= htmlspecialchars($f['home_name']) ?>
                                                </a>
                                            </div>
                                            <div class="team-item">
                                                <img src="<?= $awayLogo ?>" class="team-logo" alt="">
                                                <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="team-name <?= $f['away_club_id'] == $club_id ? 'bold' : '' ?>">
                                                    <?= htmlspecialchars($f['away_name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="fixture-info">
                                            <div class="date"><?= $shortDate ?></div>
                                            <div><?= $time ?></div>
                                            <div><?= htmlspecialchars($f['venue'] ?? 'TBD') ?></div>
                                        </div>
                                    </div>
                                    <div class="match-toggle">
                                        <span><?= $tournName ?></span>
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

    </div><!-- /.clubs-page-wrapper -->
</div><!-- /.main-content -->

<script>
document.querySelectorAll('.panel-tabs a[data-tab]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const tab   = this.dataset.tab;
        const panel = this.closest('.club-panel');
        panel.querySelectorAll('.tab-pane').forEach(p => { p.style.display = 'none'; p.classList.remove('active'); });
        panel.querySelectorAll('.panel-tabs a').forEach(a => a.classList.remove('active'));
        const target = document.getElementById(tab);
        if (target) { target.style.display = 'block'; target.classList.add('active'); }
        this.classList.add('active');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.match-toggle[data-bs-toggle="collapse"]').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const targetId       = this.getAttribute('data-bs-target');
            const targetCollapse = document.querySelector(targetId);
            this.closest('.matches-grid').querySelectorAll('.collapse.show').forEach(open => {
                if (open !== targetCollapse) bootstrap.Collapse.getInstance(open)?.hide();
            });
        });
    });
});
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>