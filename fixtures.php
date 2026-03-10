<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
include 'includes/properties.php';
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND - CONSISTENT WITH INDEX.PHP & CLUBS.PHP */
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
    .matchday-card {
        background: #ffffff;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        margin-bottom: 2rem;
    }
    .matchday-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1rem 1.6rem;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .matchday-header span.opacity-90 {
        opacity: 0.9;
        font-size: 0.95rem;
    }
    .matchday-body { padding: 0; }
    .fixtures-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 0;
    }
    @media (max-width: 992px) {
        .fixtures-grid { grid-template-columns: 1fr; }
    }
    .fixture-card {
        background: #f8f9fa;
        padding: 1rem 1.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 92px;
        border-bottom: 1px solid #dee2e6;
        transition: background 0.3s ease;
    }
    .fixture-card:hover {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .fixture-card:last-child { border-bottom: none; }
    /* LEFT – Teams */
    .teams-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        padding-right: 20px;
        position: relative;
    }
    .teams-stack::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 68px;
        background: #ccc;
    }
    .team-item {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }
    .team-logo {
        width: 48px;
        height: 48px;
        object-fit: contain;
        background: white;
        padding: 4px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        border: 1px solid #ccc;
        flex-shrink: 0;
    }
    .team-name {
        font-weight: 600;
        font-size: 1.05rem;
        color: #2c3e50;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .team-name:hover {
        color: #0d6efd;
    }
    /* RIGHT – Date + Venue */
    .fixture-details {
        padding-left: 24px;
        text-align: right;
        color: #6c757d;
        font-size: 0.94rem;
        width: 135px;
        flex-shrink: 0;
        line-height: 1.5;
    }
    .fixture-details .date {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.02rem;
        display: block;
        margin-bottom: 3px;
    }
    .fixture-details .venue {
        font-size: 0.88rem;
        color: #95a5a6;
    }
    /* MOBILE OPTIMISATIONS */
    @media (max-width: 576px) {
        .container.py-4 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .matchday-card {
            margin: 0 0 2rem;
            border-left: none;
            border-right: none;
            border-radius: 0 !important;
        }
        .fixture-card {
            padding: 1rem 1.2rem;
        }
        .teams-stack { padding-right: 16px; }
        .fixture-details { padding-left: 20px; width: 125px; }
    }
    @media (max-width: 360px) {
        .team-logo { width: 44px; height: 44px; }
        .team-name { font-size: 1rem; }
        .fixture-details { width: 115px; padding-left: 16px; }
        .fixture-details .date { font-size: 0.98rem; }
    }
    /* EMPTY STATE */
    .alert-info {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
</style>
<div class="container py-4 pb-5">
    <?php
    $allFixtures = getAllUpcomingFixtures();
    if (empty($allFixtures)) {
        echo '<div class="text-center py-5"><div class="alert alert-info d-inline-block">No upcoming fixtures scheduled yet.</div></div>';
    } else {
        $grouped = [];
        foreach ($allFixtures as $f) {
            $grouped[$f['matchday']][] = $f;
        }
        foreach ($grouped as $matchday => $fixtures):
    ?>
        <div class="matchday-card">
            <div class="matchday-header">
                <span>Matchday <?= $matchday ?> Fixtures</span>
                <span class="opacity-90"><?= count($fixtures) ?> Matches</span>
            </div>
            <div class="matchday-body">
                <div class="fixtures-grid">
                    <?php foreach ($fixtures as $f):
                        $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/50/defcfc/333333?text=".substr($f['home_name'],0,2);
                        $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/50/defcfc/333333?text=".substr($f['away_name'],0,2);
                        $date = new DateTime($f['fixture_date']);
                        $dateStr = $date->format('D, j M');
                        $venue = htmlspecialchars($f['venue'] ?? 'TBD');
                    ?>
                        <div class="fixture-card">
                            <div class="teams-stack">
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>">
                                        <img src="<?= $homeLogo ?>" class="team-logo" alt="<?= htmlspecialchars($f['home_name']) ?>">
                                    </a>
                                    <div class="team-name"><?= htmlspecialchars($f['home_name']) ?></div>
                                </div>
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>">
                                        <img src="<?= $awayLogo ?>" class="team-logo" alt="<?= htmlspecialchars($f['away_name']) ?>">
                                    </a>
                                    <div class="team-name"><?= htmlspecialchars($f['away_name']) ?></div>
                                </div>
                            </div>
                            <div class="fixture-details">
                                <span class="date"><?= $dateStr ?></span>
                                <span class="venue"><?= $venue ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; } ?>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>