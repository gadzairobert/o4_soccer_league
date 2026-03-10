<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/properties.php';

$shield = null;
try {
    $stmt = $pdo->prepare("SELECT filename FROM logos WHERE title LIKE 'matchday%' AND purpose = 'league_shield' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
    $stmt->execute();
    $shield = $stmt->fetchColumn();
} catch (Exception $e) {}
$shieldPath = $shield ? "uploads/admin/logos/" . htmlspecialchars($shield) : null;

// Season year filter — defaults to current year
$selectedYear = (int)($_GET['year'] ?? date('Y'));
$years = [];
try {
    $years = $pdo->query("
        SELECT DISTINCT YEAR(match_date) AS year
        FROM matches
        WHERE match_date IS NOT NULL
        ORDER BY year DESC
    ")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
if (empty($years)) $years = [date('Y')];
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
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── matches original margin-top: -50px / padding-top: 20px ── */
    .about-page-wrapper {
        max-width: 1400px;
        margin: -50px auto 0;
        padding: 20px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .about-page-wrapper {
            margin-top: 0;
            padding: 1rem 0 3rem;
            max-width: 100%;
            width: 100%;
        }
    }

    /* ── About Card ── */
    .about-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
    }
    @media (max-width: 767px) {
        .about-card { border-radius: 0; border-left: none; border-right: none; }
    }

    /* ── About Header ── */
    .about-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 1rem 1.8rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.25rem;
        font-weight: 700;
        text-align: center;
    }
    @media (max-width: 767px) {
        .about-header { font-size: 1.1rem; padding: 1rem; }
    }

    /* ── Layout ── */
    .league-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1.5rem;
    }
    .league-table-wrapper { flex: 2 1 660px; min-width: 0; }
    .league-shield-wrapper {
        flex: 1 1 280px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        border-radius: 12px;
    }
    .league-shield-wrapper img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.4);
        transition: transform 0.3s ease;
    }
    .league-shield-wrapper img:hover { transform: scale(1.04); }
    .shield-empty {
        font-family: 'DM Sans', sans-serif;
        color: var(--muted);
        font-style: italic;
        text-align: center;
        padding: 3rem 1rem;
        font-size: 0.9rem;
    }

    /* ── Table ── */
    .table {
        margin: 0;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.92rem;
        border-collapse: collapse;
        background: transparent;
        color: rgba(255,255,255,0.8);
        width: 100%;
    }
    .table thead th {
        background: linear-gradient(135deg, #16152b, #24224a);
        color: var(--gold);
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        padding: 0.75rem 0.6rem;
        white-space: nowrap;
    }
    .table tbody td {
        vertical-align: middle;
        border-top: 1px solid rgba(255,255,255,0.05);
        border-bottom: none;
        padding: 0.6rem 0.6rem;
        /* Ensure text stays visible — override Bootstrap hover colour inheritance */
        color: rgba(255,255,255,0.8);
    }
    .table tbody tr {
        transition: background 0.2s;
    }
    /* Override Bootstrap table-hover which sets --bs-table-hover-bg (dark) */
    .table-hover > tbody > tr:hover > * {
        background-color: rgba(201,168,76,0.08) !important;
        color: rgba(255,255,255,0.9) !important;
        --bs-table-accent-bg: transparent;
    }

    /* Season year dropdown */
    .season-select {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        padding: 0.28rem 2rem 0.28rem 0.7rem;
        font-size: 0.8rem;
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
    .season-select:focus { outline: none; border-color: var(--gold); }
    .season-select option { background: #1a1a2e; color: #eee; }

    /* GD colours — must stay visible on hover too */
    .pos {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--muted);
    }
    .pos.top { color: var(--gold); }

    /* Club logo */
    .club-logo {
        width: 38px; height: 38px;
        object-fit: contain;
        background: #ffffff;          /* solid white circle */
        padding: 3px;
        border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.25);
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        flex-shrink: 0;
        transition: border-color 0.2s, transform 0.25s;
    }
    .club-logo:hover {
        transform: scale(1.15);
        border-color: var(--gold);
    }

    /* Club name */
    .club-name {
        font-weight: 600;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        transition: color 0.2s;
        white-space: nowrap;
    }
    .club-name:hover { color: var(--gold-light); }

    /* Points */
    .points {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--gold);
    }

    /* GD colours */
    .gd-positive { color: #4ade80 !important; font-weight: 700; }
    .gd-negative { color: #f87171 !important; font-weight: 700; }
    .gd-zero     { color: var(--muted); font-weight: 600; }

    /* Form guide */
    .form-guide {
        display: flex;
        justify-content: center;
        gap: 6px;
        font-size: 1.05rem;
    }
    .form-w    { color: #4ade80; }
    .form-l    { color: #f87171; }
    .form-d    { color: rgba(255,255,255,0.35); }
    .form-none { color: rgba(255,255,255,0.15); }

    /* Mobile */
    @media (max-width: 992px) {
        .league-container {
            flex-direction: column;
            padding: 1rem 0 1.5rem;
            gap: 1.5rem;
        }
        .league-table-wrapper {
            width: 100%;
            padding: 0 0.75rem;
            box-sizing: border-box;
        }
        .league-shield-wrapper {
            order: 2;
            width: 100%;
            padding: 0 0.75rem;
            background: transparent;
            border: none;
            box-shadow: none;
        }
        .league-shield-wrapper img {
            width: 100%;
            border-radius: 12px;
        }
    }
    @media (max-width: 768px) {
        .table { font-size: 0.82rem; }
        .club-logo { width: 32px; height: 32px; }
        .points { font-size: 1.05rem; }
    }
</style>

<div class="main-content">
    <div class="about-page-wrapper">

        <div class="about-card">
            <div class="about-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
                <span>League Table &mdash; <?= $selectedYear ?>/<?= $selectedYear + 1 ?> Season</span>
                <form method="GET" style="margin:0;">
                    <select name="year" class="season-select" onchange="this.form.submit()">
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>>
                                <?= $y ?>/<?= $y + 1 ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="league-container">

                <!-- TABLE -->
                <div class="league-table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Club</th>
                                    <th class="text-center">P</th>
                                    <th class="text-center">W</th>
                                    <th class="text-center">D</th>
                                    <th class="text-center">L</th>
                                    <th class="text-center">GF</th>
                                    <th class="text-center">GA</th>
                                    <th class="text-center">GD</th>
                                    <th class="text-center">PTS</th>
                                    <th class="text-center" style="white-space:nowrap;">Last 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $standings = getLeagueStandings($selectedYear);
                                    $pos = 1;
                                    if (empty($standings)) {
                                        echo '<tr><td colspan="11" class="text-center py-5" style="color:var(--muted);font-family:\'DM Sans\',sans-serif;">No matches played yet for ' . $selectedYear . '.</td></tr>';
                                    } else {
                                        foreach ($standings as $row):
                                            $played = $row['wins'] + $row['draws'] + $row['losses'];
                                            $gd     = $row['gd'];
                                            $gdClass = $gd > 0 ? 'gd-positive' : ($gd < 0 ? 'gd-negative' : 'gd-zero');
                                            $logo   = $row['logo']
                                                ? 'uploads/clubs/' . htmlspecialchars($row['logo'])
                                                : 'https://via.placeholder.com/40/1a1a2e/c9a84c?text=' . urlencode(substr($row['club'], 0, 2));
                                            $lastFive = getLastFiveResults($row['id'], $selectedYear);
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="pos <?= $pos <= 4 ? 'top' : '' ?>"><?= $pos++ ?></span>
                                    </td>
                                    <td>
                                        <a href="clubs.php?club_id=<?= $row['id'] ?>" class="text-decoration-none d-flex align-items-center gap-2">
                                            <img src="<?= $logo ?>" class="club-logo" alt="<?= htmlspecialchars($row['club']) ?>">
                                            <span class="club-name"><?= htmlspecialchars($row['club']) ?></span>
                                        </a>
                                    </td>
                                    <td class="text-center"><?= $played ?></td>
                                    <td class="text-center"><?= $row['wins'] ?></td>
                                    <td class="text-center"><?= $row['draws'] ?></td>
                                    <td class="text-center"><?= $row['losses'] ?></td>
                                    <td class="text-center"><?= $row['goals_for'] ?></td>
                                    <td class="text-center"><?= $row['goals_against'] ?></td>
                                    <td class="text-center <?= $gdClass ?> fw-bold">
                                        <?= $gd >= 0 ? '+' . $gd : $gd ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="points"><?= $row['points'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-guide">
                                            <?php foreach ($lastFive as $res): ?>
                                                <i class="bi bi-circle-fill <?= $res === null ? 'form-none' : 'form-' . strtolower($res) ?>"></i>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        endforeach;
                                    }
                                } catch (Exception $e) {
                                    echo '<tr><td colspan="11" class="text-center py-4" style="color:#f87171;font-family:\'DM Sans\',sans-serif;">Error loading league table.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SHIELD IMAGE -->
                <div class="league-shield-wrapper">
                    <?php if ($shieldPath): ?>
                        <img src="<?= $shieldPath ?>?v=<?= time() ?>" alt="League Shield">
                    <?php else: ?>
                        <div class="shield-empty">No league shield uploaded yet</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>