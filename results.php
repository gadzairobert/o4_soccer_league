<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
include 'includes/properties.php';
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    /* ── Design Tokens ── */
    :root {
        --ink:        #1a1a2e;
        --gold:       #c9a84c;
        --gold-light: #f0d080;
        --cream:      #fdf8ef;
        --muted:      rgba(255,255,255,0.45);
        --border:     rgba(201,168,76,0.2);
        --card-bg:    rgba(255,255,255,0.04);
    }

    html, body {
        background-color: #1a1a2e !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.06) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(123,45,139,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: #eee;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── matches original margin/padding ── */
    .results-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .results-page-wrapper {
            margin-top: 0;
            padding: 1rem 0 3rem;
            width: 100%;
        }
    }

    /* ── Result Card ── */
    .result-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        margin-bottom: 2rem;
    }
    @media (max-width: 767px) {
        .result-card {
            border-radius: 0;
            border-left: none;
            border-right: none;
            margin-bottom: 1.2rem;
        }
    }

    /* ── Result Header ── */
    .result-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .result-header .matchday-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0;
    }
    .result-header .header-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .result-header .match-count {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--gold);
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.3);
        padding: 0.25rem 0.85rem;
        border-radius: 20px;
        white-space: nowrap;
    }

    /* Year dropdown */
    .year-select {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        padding: 0.3rem 2rem 0.3rem 0.7rem;
        font-size: 0.82rem;
        border-radius: 6px;
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23c9a84c'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 11px;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
    }
    .year-select:focus { outline: none; border-color: var(--gold); }
    .year-select option { background: #1a1a2e; color: #eee; }

    /* ── Results Grid ── */
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 0;
    }
    @media (max-width: 992px) {
        .results-grid { grid-template-columns: 1fr; }
    }

    /* ── Result Item ── */
    .result-item {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        transition: background 0.25s ease;
        position: relative;
    }
    .result-item::after {
        content: '';
        position: absolute;
        right: 0; top: 16px; bottom: 16px;
        width: 1px;
        background: rgba(255,255,255,0.06);
        display: none;
    }
    @media (min-width: 993px) {
        .result-item:nth-child(odd)::after { display: block; }
    }
    .result-item:hover { background: rgba(201,168,76,0.04); }
    .result-item:last-child { border-bottom: none; }

    /* ── Match Row ── */
    .match-row {
        padding: 0.85rem 1.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 78px;
    }

    /* ── Teams Stack ── */
    .teams-stack {
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex: 1;
        padding-right: 18px;
        position: relative;
    }
    .teams-stack::after {
        content: '';
        position: absolute;
        right: 0; top: 50%;
        transform: translateY(-50%);
        width: 1px; height: 56px;
        background: rgba(255,255,255,0.12);
    }
    .team-item {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    /* ── Team Logo ── */
    .team-logo {
        width: 44px; height: 44px;
        object-fit: contain;
        background: #ffffff;
        padding: 3px;
        border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.25);
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        flex-shrink: 0;
        transition: border-color 0.2s;
    }
    .result-item:hover .team-logo { border-color: rgba(201,168,76,0.5); }

    /* ── Team Name ── */
    .team-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.8);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-decoration: none;
        transition: color 0.2s;
    }
    .team-name:hover { color: var(--gold-light); }
    .winner { color: #4ade80 !important; font-weight: 700 !important; }

    /* ── Score Stack ── */
    .score-stack {
        padding-left: 20px;
        text-align: right;
        min-width: 70px;
        flex-shrink: 0;
        line-height: 1.1;
    }
    .home-score, .away-score {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--cream);
    }

    /* ── Result Toggle (date bar) ── */
    .result-toggle {
        padding: 0.5rem 1.6rem;
        background: rgba(0,0,0,0.2);
        border-top: 1px solid rgba(255,255,255,0.06);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        color: var(--muted);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }
    .result-toggle:hover { background: rgba(255,255,255,0.05); }
    .result-toggle .date {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--cream);
    }
    .result-toggle i {
        font-size: 0.85rem;
        color: rgba(201,168,76,0.6);
        transition: transform 0.25s;
    }
    .result-toggle[aria-expanded="true"] i { transform: rotate(180deg); }

    /* ── Event List ── */
    .event-list {
        background: rgba(0,0,0,0.25);
        padding: 0.75rem 1.6rem;
        font-size: 0.84rem;
        max-height: 340px;
        overflow-y: auto;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .event-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        padding: 3px 0;
    }
    .event-team.away { justify-content: flex-end; text-align: right; flex-direction: row-reverse; }
    .event-minutes {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        color: var(--cream);
        min-width: 38px;
        font-size: 0.82em;
    }
    .event-icon {
        width: 22px; height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: bold;
        color: white;
        flex-shrink: 0;
    }
    .goal-icon        { background: #2563eb; }
    .assist-icon      { background: #c9a84c; color: #000; }
    .yellow-icon      { background: #eab308; color: #000; }
    .red-icon         { background: #dc2626; }
    .cleansheet-icon  { background: #16a34a; }
    .event-detail { flex: 1; min-width: 0; }
    .event-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: rgba(255,255,255,0.75);
        display: block;
    }
    .event-penalty { font-size: 0.8em; color: #f87171; }

    /* ── Empty State ── */
    .results-empty {
        text-align: center;
        padding: 5rem 2rem;
        font-family: 'DM Sans', sans-serif;
    }
    .results-empty i {
        font-size: 3.5rem;
        color: rgba(201,168,76,0.3);
        display: block;
        margin-bottom: 1rem;
    }
    .results-empty p { color: var(--muted); font-size: 1rem; margin: 0; }

    /* ── Mobile ── */
    @media (max-width: 576px) {
        .match-row        { padding: 0.8rem 1rem; min-height: 72px; }
        .teams-stack      { padding-right: 14px; gap: 3px; }
        .teams-stack::after { height: 50px; }
        .team-logo        { width: 38px; height: 38px; }
        .team-name        { font-size: 0.88rem; }
        .score-stack      { padding-left: 14px; min-width: 56px; }
        .home-score,
        .away-score       { font-size: 1.6rem; }
        .result-toggle    { padding: 0.5rem 1rem; }
        .result-toggle .date { font-size: 0.84rem; }
        .event-list       { padding: 0.6rem 1rem; }
    }
    @media (max-width: 360px) {
        .team-logo  { width: 34px; height: 34px; }
        .team-name  { font-size: 0.84rem; }
        .score-stack { min-width: 48px; }
    }
</style>

<div class="results-page-wrapper">
<?php
$selectedYear = (int)($_GET['year'] ?? date('Y'));
$years = $pdo->query("
    SELECT DISTINCT YEAR(match_date) AS year
    FROM matches
    WHERE match_date IS NOT NULL
    ORDER BY year DESC
")->fetchAll(PDO::FETCH_COLUMN);
if (empty($years)) $years = [date('Y')];

$allResults = getAllCompletedMatchesWithEvents($selectedYear);

if (empty($allResults)): ?>
    <div class="results-empty">
        <i class="bi bi-calendar-x"></i>
        <p>No results available for <?= $selectedYear ?>.</p>
    </div>
<?php else:
    $grouped = [];
    foreach ($allResults as $r) $grouped[$r['matchday']][] = $r;
    krsort($grouped);
    foreach ($grouped as $matchday => $results):
?>
    <div class="result-card">
        <div class="result-header">
            <span class="matchday-title">Matchday <?= htmlspecialchars($matchday) ?></span>
            <div class="header-right">
                <span class="match-count"><?= count($results) ?> Match<?= count($results) !== 1 ? 'es' : '' ?></span>
                <form method="GET" class="d-inline mb-0">
                    <select name="year" class="year-select" onchange="this.form.submit()">
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="result-body">
            <div class="results-grid">
                <?php foreach ($results as $r):
                    $homeWin  = $r['home_score'] > $r['away_score'];
                    $awayWin  = $r['away_score'] > $r['home_score'];
                    $homeLogo = $r['home_logo'] ? 'uploads/clubs/' . $r['home_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c?text=' . urlencode(substr($r['home_name'], 0, 2));
                    $awayLogo = $r['away_logo'] ? 'uploads/clubs/' . $r['away_logo'] : 'https://via.placeholder.com/48/1a1a2e/c9a84c?text=' . urlencode(substr($r['away_name'], 0, 2));
                    $dateStr  = (new DateTime($r['match_date']))->format('D, j M Y');
                    $matchId  = $r['match_id'];

                    // === FULL EVENT BUILDING CODE (unchanged) ===
                    $eventHtml = '';
                    $goalsStmt = $pdo->prepare("SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id, ap.name AS assist FROM goals g JOIN players p ON g.player_id = p.id LEFT JOIN assists a ON g.id = a.goal_id LEFT JOIN players ap ON a.player_id = ap.id WHERE g.match_id = ? ORDER BY g.minute");
                    $goalsStmt->execute([$matchId]);
                    $goals = $goalsStmt->fetchAll(PDO::FETCH_ASSOC);

                    $cardsStmt = $pdo->prepare("SELECT c.card_type, c.minute, p.name, p.club_id FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ? ORDER BY c.minute");
                    $cardsStmt->execute([$matchId]);
                    $cards = $cardsStmt->fetchAll(PDO::FETCH_ASSOC);

                    $csStmt = $pdo->prepare("SELECT p.name, p.club_id FROM clean_sheets cs JOIN players p ON cs.player_id = p.id WHERE cs.match_id = ?");
                    $csStmt->execute([$matchId]);
                    $cleanSheets = $csStmt->fetchAll(PDO::FETCH_ASSOC);

                    $events = [];
                    foreach ($goals as $g) {
                        $isHome    = $g['club_id'] == $r['home_club_id'];
                        $teamClass = $isHome ? 'home' : 'away';
                        $events[]  = [
                            'minute' => $g['minute'] . "'",
                            'icon'   => 'goal-icon',
                            'text'   => 'G',
                            'detail' => '<span class="event-name">' . htmlspecialchars($g['scorer']) . '</span>' . ($g['is_penalty'] ? ' <span class="event-penalty">(P)</span>' : ''),
                            'team'   => $teamClass,
                            'sort'   => $g['minute'] * 100,
                        ];
                        if ($g['assist']) {
                            $events[] = [
                                'minute' => '',
                                'icon'   => 'assist-icon',
                                'text'   => 'A',
                                'detail' => '<span class="event-name">' . htmlspecialchars($g['assist']) . '</span>',
                                'team'   => $teamClass,
                                'sort'   => $g['minute'] * 100 + 1,
                            ];
                        }
                    }
                    foreach ($cards as $c) {
                        $isHome    = $c['club_id'] == $r['home_club_id'];
                        $teamClass = $isHome ? 'home' : 'away';
                        $icon      = $c['card_type'] === 'yellow' ? 'yellow-icon' : 'red-icon';
                        $text      = $c['card_type'] === 'yellow' ? 'YC' : 'RC';
                        $events[]  = [
                            'minute' => $c['minute'] . "'",
                            'icon'   => $icon,
                            'text'   => $text,
                            'detail' => '<span class="event-name">' . htmlspecialchars($c['name']) . '</span>',
                            'team'   => $teamClass,
                            'sort'   => $c['minute'] * 100 + 50,
                        ];
                    }
                    foreach ($cleanSheets as $cs) {
                        $teamClass = $cs['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                        $events[]  = [
                            'minute' => 'CS',
                            'icon'   => 'cleansheet-icon',
                            'text'   => 'CS',
                            'detail' => '<strong>' . htmlspecialchars($cs['name']) . '</strong>',
                            'team'   => $teamClass,
                            'sort'   => 9999,
                        ];
                    }
                    usort($events, fn($a, $b) => $a['sort'] <=> $b['sort']);
                    if (!empty($events)) {
                        foreach ($events as $e) {
                            $eventHtml .= "<div class='event-row event-team {$e['team']}'>";
                            $eventHtml .= "<div class='event-minutes'>{$e['minute']}</div>";
                            $eventHtml .= "<div class='event-icon {$e['icon']}'>{$e['text']}</div>";
                            $eventHtml .= "<div class='event-detail'>{$e['detail']}</div>";
                            $eventHtml .= "</div>";
                        }
                    } else {
                        $eventHtml = '<div class="text-center py-3" style="font-family:\'DM Sans\',sans-serif;font-size:0.8rem;color:rgba(255,255,255,0.3);">No events recorded</div>';
                    }
                    // === END EVENT CODE ===
                ?>
                    <div class="result-item">
                        <div class="match-row">
                            <div class="teams-stack">
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>">
                                        <img src="<?= $homeLogo ?>" class="team-logo" alt="<?= htmlspecialchars($r['home_name']) ?>">
                                    </a>
                                    <div class="team-name <?= $homeWin ? 'winner' : '' ?>">
                                        <?= htmlspecialchars($r['home_name']) ?>
                                    </div>
                                </div>
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>">
                                        <img src="<?= $awayLogo ?>" class="team-logo" alt="<?= htmlspecialchars($r['away_name']) ?>">
                                    </a>
                                    <div class="team-name <?= $awayWin ? 'winner' : '' ?>">
                                        <?= htmlspecialchars($r['away_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="score-stack">
                                <span class="home-score"><?= $r['home_score'] ?></span>
                                <span class="away-score"><?= $r['away_score'] ?></span>
                            </div>
                        </div>
                        <div class="result-toggle"
                             data-bs-toggle="collapse"
                             data-bs-target="#events-<?= $r['match_id'] ?>"
                             aria-expanded="false">
                            <span class="date"><?= $dateStr ?></span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="collapse event-list" id="events-<?= $r['match_id'] ?>">
                            <?= $eventHtml ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>
</div>

<!-- ONLY ONE DROPDOWN OPEN AT A TIME -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll('.result-toggle[data-bs-toggle="collapse"]');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            document.querySelectorAll('.collapse.show').forEach(openCollapse => {
                if (openCollapse.id !== this.getAttribute('data-bs-target').substring(1)) {
                    const bsCollapse = bootstrap.Collapse.getInstance(openCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            });
        });
    });
});
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>