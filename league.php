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
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND - CONSISTENT WITH FIXTURES.PHP, INDEX.PHP & CLUBS.PHP */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        overflow-x: hidden;
    }
    .about-page-wrapper { 
        margin-top: -50px; 
        padding-top: 20px; 
    }
    .about-card {
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        border-radius: 12px;
    }
    .about-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .league-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: flex-start;
        justify-content: space-between;
        padding: 2rem 2rem 2.5rem;
    }
    .league-table-wrapper { 
        flex: 2 1 660px; 
        min-width: 0; 
    }
    .league-shield-wrapper {
        flex: 1 1 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
    }
    .league-shield-wrapper img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        transition: transform .3s ease;
    }
    .league-shield-wrapper img:hover { 
        transform: scale(1.05); 
    }
    /* TABLE */
    .table {
        margin: 0;
        font-size: 0.95rem;
        border-collapse: collapse;
        background: #ffffff;
        color: #333333;
    }
    .table thead th {
        background: #1a2530;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border: none;
    }
    .table tbody td {
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    .table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .pos {
        font-weight: bold;
        font-size: 1.15rem;
        color: #0d6efd;
    }
    .club-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        border: 1px solid #ccc;
        transition: transform .25s;
    }
    .club-logo:hover { 
        transform: scale(1.18); 
    }
    .club-name {
        font-weight: 600;
        color: #2c3e50;
    }
    .club-name:hover {
        color: #0d6efd;
    }
    .points {
        font-size: 1.32rem;
        font-weight: bold;
        color: #2c3e50;
    }
    .form-guide {
        display: flex;
        justify-content: center;
        gap: 7px;
        font-size: 1.15rem;
    }
    .form-w { color: #27ae60; }
    .form-l { color: #e74c3c; }
    .form-d { color: #6c757d; }
    .form-none { color: #aaa; opacity: .6; }
    /* TEXT COLORS FOR GD */
    .text-success { color: #27ae60 !important; }
    .text-danger { color: #e74c3c !important; }
    /* EMPTY / ERROR STATES */
    .text-muted { color: #6c757d !important; }
    .text-danger { color: #e74c3c !important; }
    /* MOBILE */
    @media (max-width: 992px) {
        .league-container {
            flex-direction: column;
            padding: 2rem 0 2.5rem 0;
            gap: 2rem;
        }
        .league-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0 1.5rem;
            box-sizing: border-box;
        }
        .league-table-wrapper .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .league-shield-wrapper {
            order: 2;
            width: 100%;
            padding: 0 1.5rem;
            margin: 0;
            background: transparent;
            box-shadow: none;
            border: none;
        }
        .league-shield-wrapper img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            display: block;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    }
    @media (max-width: 768px) {
        .about-header { 
            font-size: 1.35rem; 
            padding: 1.4rem 1rem; 
        }
        .table { 
            font-size: 0.88rem; 
        }
        .club-logo { 
            width: 36px; 
            height: 36px; 
        }
        .league-container { 
            padding-left: 0; 
            padding-right: 0; 
        }
        .league-table-wrapper,
        .league-shield-wrapper { 
            padding-left: 1rem; 
            padding-right: 1rem; 
        }
    }
</style>
<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <div class="about-header">League Table • 2025/26 Season</div>
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
                                    <th class="text-center"><strong>PTS</strong></th>
                                    <th class="text-center text-uppercase text-muted small">Last 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $standings = getLeagueStandings();
                                    $pos = 1;
                                    if (empty($standings)) {
                                        echo '<tr><td colspan="11" class="text-center py-5 text-muted">No matches played yet.</td></tr>';
                                    } else {
                                        foreach ($standings as $row):
                                            $played = $row['wins'] + $row['draws'] + $row['losses'];
                                            $gd = $row['gd'];
                                            $logo = $row['logo']
                                                ? 'uploads/clubs/' . htmlspecialchars($row['logo'])
                                                : 'https://via.placeholder.com/40/defcfc/333333?text=' . substr($row['club'], 0, 2);
                                            $lastFive = getLastFiveResults($row['id']);
                                ?>
                                <tr>
                                    <td class="text-center pos <?= $pos <= 4 ? 'text-primary' : '' ?>"><?= $pos++ ?></td>
                                    <td>
                                        <a href="clubs.php?club_id=<?= $row['id'] ?>" class="text-decoration-none d-flex align-items-center gap-3">
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
                                    <td class="text-center <?= $gd >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                        <?= $gd >= 0 ? '+' . $gd : $gd ?>
                                    </td>
                                    <td class="text-center points"><?= $row['points'] ?></td>
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
                                    echo '<tr><td colspan="11" class="text-center text-danger py-4">Error loading league table.</td></tr>';
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
                        <div class="text-muted fst-italic text-center py-5">No league shield uploaded yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>