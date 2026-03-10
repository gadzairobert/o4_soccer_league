<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
include 'includes/properties.php';
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND - CONSISTENT WITH PREVIOUS PAGES */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        overflow-x: hidden;
    }
    .container.py-4 {
        margin-top: -38px !important;
        padding-top: 6px !important;
        max-width: 100% !important;
    }
    .result-card {
        background: #ffffff;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        margin-bottom: 2rem;
    }
    .result-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1rem 1.6rem;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .result-header .opacity-90 {
        opacity: 0.9;
        font-size: 0.95rem;
    }
    .result-header .form-select {
        background: #34495e;
        border: 1px solid #444;
        color: white;
        font-size: 0.9rem;
    }
    .result-body { padding: 0; }
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 0;
    }
    @media (max-width: 992px) {
        .results-grid { grid-template-columns: 1fr; }
    }
    .result-item {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        transition: background 0.3s ease;
    }
    .result-item:hover {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .result-item:last-child { border-bottom: none; }
    .match-row {
        padding: 0.85rem 1.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 78px;
    }
    .teams-stack {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        padding-right: 18px;
        position: relative;
    }
    .teams-stack::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 56px;
        background: #ccc;
    }
    .team-item {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .team-logo {
        width: 44px;
        height: 44px;
        object-fit: contain;
        background: white;
        padding: 3px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        border: 1px solid #ccc;
        flex-shrink: 0;
    }
    .team-name {
        font-weight: 600;
        font-size: 1.02rem;
        color: #2c3e50;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .team-name:hover {
        color: #0d6efd;
    }
    .winner {
        color: #27ae60 !important;
        font-weight: 700 !important;
    }
    .score-stack {
        padding-left: 20px;
        text-align: right;
        min-width: 90px;
        flex-shrink: 0;
        line-height: 1.2;
    }
    .home-score, .away-score {
        display: block;
        font-size: 1.9rem;
        font-weight: bold;
        color: #2c3e50;
    }
    .result-toggle {
        padding: 0.65rem 1.6rem;
        background: #f1f3f5;
        border-top: 1px solid #dee2e6;
        font-size: 0.92rem;
        color: #6c757d;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }
    .result-toggle:hover {
        background: #e9ecef;
    }
    .result-toggle .date {
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
    }
    .result-toggle i {
        font-size: 0.95rem;
        transition: transform 0.25s ease;
        color: #6c757d;
    }
    .result-toggle[aria-expanded="true"] i {
        transform: rotate(180deg);
    }
    .event-list {
        background: #ffffff;
        padding: 0.9rem 1.6rem;
        font-size: 0.86rem;
        max-height: 340px;
        overflow-y: auto;
        border-top: 1px solid #dee2e6;
    }
    .event-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        padding: 3px 0;
    }
    .event-team.home { justify-content: flex-start; text-align: left; }
    .event-team.away { justify-content: flex-end; text-align: right; flex-direction: row-reverse; }
    .event-minutes {
        font-weight: bold;
        color: #2c3e50;
        min-width: 38px;
        font-size: 0.88em;
    }
    .event-icon {
        width: 24px; height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        color: white;
    }
    .goal-icon { background: #0111c0; }
    .assist-icon { background: #f39c12; color: #000; }
    .yellow-icon { background: #f1c40f; color: #000; }
    .red-icon { background: #c0392b; }
    .cleansheet-icon { background: #27ae60; }
    .event-detail { flex: 1; min-width: 0; }
    .event-name {
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #2c3e50;
    }
    .event-penalty {
        font-size: 0.8em;
        color: #e74c3c;
    }
    /* EMPTY STATE */
    .alert-info {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
    /* MOBILE */
    @media (max-width: 576px) {
        .container.py-4 { padding-left: 0 !important; padding-right: 0 !important; }
        .result-card { margin: 0 0 2rem; border-left: none; border-right: none; border-radius: 0 !important; }
        .match-row { padding: 0.8rem 1.2rem; }
        .teams-stack { padding-right: 14px; gap: 3px; }
        .teams-stack::after { height: 50px; }
        .team-logo { width: 40px; height: 40px; }
        .team-name { font-size: 0.98rem; }
        .score-stack { padding-left: 16px; }
        .home-score, .away-score { font-size: 1.7rem; }
        .result-toggle { padding: 0.6rem 1.2rem; }
        .result-toggle .date { font-size: 0.98rem; }
    }
</style>
<div class="container py-4 pb-5">
<?php
$selectedYear = (int)($_GET['year'] ?? date('Y'));
$years = $pdo->query("SELECT DISTINCT YEAR(match_date) AS year FROM matches WHERE match_date IS NOT NULL ORDER BY year DESC")
    ->fetchAll(PDO::FETCH_COLUMN);
if (empty($years)) $years = [date('Y')];
$allResults = getAllCompletedMatchesWithEvents($selectedYear);
if (empty($allResults)) {
    echo '<div class="text-center py-5"><div class="alert alert-info d-inline-block">No results available for ' . $selectedYear . '.</div></div>';
} else {
    $grouped = [];
    foreach ($allResults as $r) $grouped[$r['matchday']][] = $r;
    krsort($grouped);
    foreach ($grouped as $matchday => $results):
?>
        <div class="result-card">
            <div class="result-header">
                <span>Matchday <?= $matchday ?></span>
                <div class="d-flex align-items-center gap-3">
                    <span class="opacity-90"><?= count($results) ?> Matches</span>
                    <!-- Year dropdown only (no time displayed) -->
                    <form method="GET" class="d-inline mb-0">
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
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
                        $homeWin = $r['home_score'] > $r['away_score'];
                        $awayWin = $r['away_score'] > $r['home_score'];
                        $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($r['home_name'],0,2);
                        $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/48/defcfc/333333?text=".substr($r['away_name'],0,2);
                        $dateStr = (new DateTime($r['match_date']))->format('D, j M Y');
                        $matchId = $r['match_id'];
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
                            $isHome = $g['club_id'] == $r['home_club_id'];
                            $teamClass = $isHome ? 'home' : 'away';
                            $minute = $g['minute'] . "'";
                            $events[] = [
                                'minute' => $minute,
                                'icon' => 'goal-icon',
                                'text' => 'G',
                                'detail' => '<span class="event-name">' . htmlspecialchars($g['scorer']) . '</span>' . ($g['is_penalty'] ? ' <span class="event-penalty">(P)</span>' : ''),
                                'team' => $teamClass,
                                'sort' => $g['minute'] * 100
                            ];
                            if ($g['assist']) {
                                $events[] = [
                                    'minute' => '',
                                    'icon' => 'assist-icon',
                                    'text' => 'A',
                                    'detail' => '<span class="event-name">' . htmlspecialchars($g['assist']) . '</span>',
                                    'team' => $teamClass,
                                    'sort' => $g['minute'] * 100 + 1
                                ];
                            }
                        }
                        foreach ($cards as $c) {
                            $isHome = $c['club_id'] == $r['home_club_id'];
                            $teamClass = $isHome ? 'home' : 'away';
                            $icon = $c['card_type'] === 'yellow' ? 'yellow-icon' : 'red-icon';
                            $text = $c['card_type'] === 'yellow' ? 'YC' : 'RC';
                            $events[] = [
                                'minute' => $c['minute'] . "'",
                                'icon' => $icon,
                                'text' => $text,
                                'detail' => '<span class="event-name">' . htmlspecialchars($c['name']) . '</span>',
                                'team' => $teamClass,
                                'sort' => $c['minute'] * 100 + 50
                            ];
                        }
                        foreach ($cleanSheets as $cs) {
                            $teamClass = $cs['club_id'] == $r['home_club_id'] ? 'home' : 'away';
                            $events[] = [
                                'minute' => 'CS',
                                'icon' => 'cleansheet-icon',
                                'text' => 'CS',
                                'detail' => '<strong>' . htmlspecialchars($cs['name']) . '</strong>',
                                'team' => $teamClass,
                                'sort' => 9999
                            ];
                        }
                        usort($events, fn($a, $b) => $a['sort'] <=> $b['sort']);
                        if (!empty($events)) {
                            foreach ($events as $e) {
                                $eventHtml .= "<div class='event-row event-team {$e['team']}'>";
                                $eventHtml .= " <div class='event-minutes'>{$e['minute']}</div>";
                                $eventHtml .= " <div class='event-icon {$e['icon']}'>{$e['text']}</div>";
                                $eventHtml .= " <div class='event-detail'>{$e['detail']}</div>";
                                $eventHtml .= "</div>";
                            }
                        } else {
                            $eventHtml = '<div class="text-center text-muted py-3 small">No events recorded</div>';
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
<?php endforeach; } ?>
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