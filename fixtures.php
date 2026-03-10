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
        --ink-light:  #2c3e50;
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

    /* ── Page Wrapper ── */
    .fixtures-page-wrapper {
        max-width: 1400px;
        margin: -38px auto 0;   /* matches original container margin-top after slideshow */
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .fixtures-page-wrapper {
            margin-top: 0;
            padding: 1rem 0 3rem;
            max-width: 100%;
            width: 100%;
        }
    }

    /* ── Matchday Card ── */
    .matchday-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        margin-bottom: 2rem;
    }
    @media (max-width: 767px) {
        .matchday-card {
            border-radius: 0;
            border-left: none;
            border-right: none;
            margin-bottom: 1.2rem;
        }
    }

    /* ── Matchday Header ── */
    .matchday-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .matchday-header .matchday-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0;
    }
    .matchday-header .matchday-count {
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

    /* ── Fixtures Grid ── */
    .fixtures-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 0;
    }
    @media (max-width: 992px) {
        .fixtures-grid { grid-template-columns: 1fr; }
    }

    /* ── Fixture Card ── */
    .fixture-card {
        background: transparent;
        padding: 1rem 1.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 96px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        transition: background 0.25s ease;
        position: relative;
    }
    .fixture-card::after {
        /* vertical divider between grid columns */
        content: '';
        position: absolute;
        right: 0; top: 16px; bottom: 16px;
        width: 1px;
        background: rgba(255,255,255,0.06);
        display: none;
    }
    @media (min-width: 993px) {
        /* show right-side divider only when two-column grid is active */
        .fixture-card:nth-child(odd)::after { display: block; }
    }
    .fixture-card:hover {
        background: rgba(201,168,76,0.04);
    }
    .fixture-card:last-child { border-bottom: none; }

    /* ── Teams Stack ── */
    .teams-stack {
        display: flex;
        flex-direction: column;
        gap: 7px;
        flex: 1;
        padding-right: 20px;
        position: relative;
    }
    .teams-stack::after {
        content: '';
        position: absolute;
        right: 0; top: 50%;
        transform: translateY(-50%);
        width: 1px; height: 60px;
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
        background: #ffffff;           /* solid white circle background */
        padding: 4px;
        border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.25);
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        flex-shrink: 0;
        transition: border-color 0.2s;
    }
    .fixture-card:hover .team-logo {
        border-color: rgba(201,168,76,0.5);
    }

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

    /* ── Fixture Details (date + venue) ── */
    .fixture-details {
        padding-left: 22px;
        text-align: right;
        width: 130px;
        flex-shrink: 0;
        line-height: 1.5;
    }
    .fixture-details .date {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--cream);
        display: block;
        margin-bottom: 3px;
    }
    .fixture-details .time {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        color: var(--gold);
        display: block;
        margin-bottom: 2px;
    }
    .fixture-details .venue {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        color: var(--muted);
        display: block;
    }

    /* ── Empty State ── */
    .fixtures-empty {
        text-align: center;
        padding: 5rem 2rem;
        font-family: 'DM Sans', sans-serif;
    }
    .fixtures-empty i {
        font-size: 3.5rem;
        color: rgba(201,168,76,0.3);
        display: block;
        margin-bottom: 1rem;
    }
    .fixtures-empty p {
        color: var(--muted);
        font-size: 1rem;
        margin: 0;
    }

    /* ── Mobile ── */
    @media (max-width: 576px) {
        .fixture-card { padding: 0.85rem 1rem; min-height: 88px; }
        .teams-stack  { padding-right: 14px; }
        .team-logo    { width: 38px; height: 38px; }
        .team-name    { font-size: 0.88rem; }
        .fixture-details { padding-left: 14px; width: 110px; }
        .fixture-details .date { font-size: 0.82rem; }
    }
    @media (max-width: 360px) {
        .team-logo    { width: 34px; height: 34px; }
        .team-name    { font-size: 0.84rem; }
        .fixture-details { width: 100px; }
    }
</style>

<div class="fixtures-page-wrapper">

    <?php
    $allFixtures = getAllUpcomingFixtures();
    if (empty($allFixtures)): ?>
        <div class="fixtures-empty">
            <i class="bi bi-calendar-x"></i>
            <p>No upcoming fixtures scheduled yet.</p>
        </div>
    <?php else:
        $grouped = [];
        foreach ($allFixtures as $f) {
            $grouped[$f['matchday']][] = $f;
        }
        foreach ($grouped as $matchday => $fixtures):
    ?>
        <div class="matchday-card">
            <div class="matchday-header">
                <span class="matchday-title">Matchday <?= htmlspecialchars($matchday) ?> Fixtures</span>
                <span class="matchday-count"><?= count($fixtures) ?> Match<?= count($fixtures) !== 1 ? 'es' : '' ?></span>
            </div>
            <div class="matchday-body">
                <div class="fixtures-grid">
                    <?php foreach ($fixtures as $f):
                        $homeLogo = $f['home_logo']
                            ? 'uploads/clubs/' . $f['home_logo']
                            : 'https://via.placeholder.com/50/1a1a2e/c9a84c?text=' . urlencode(substr($f['home_name'], 0, 2));
                        $awayLogo = $f['away_logo']
                            ? 'uploads/clubs/' . $f['away_logo']
                            : 'https://via.placeholder.com/50/1a1a2e/c9a84c?text=' . urlencode(substr($f['away_name'], 0, 2));
                        $date    = new DateTime($f['fixture_date']);
                        $dateStr = $date->format('D, j M');
                        $timeStr = $date->format('H:i');
                        $venue   = htmlspecialchars($f['venue'] ?? 'TBD');
                    ?>
                        <div class="fixture-card">
                            <div class="teams-stack">
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>">
                                        <img src="<?= $homeLogo ?>" class="team-logo" alt="<?= htmlspecialchars($f['home_name']) ?>">
                                    </a>
                                    <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="team-name">
                                        <?= htmlspecialchars($f['home_name']) ?>
                                    </a>
                                </div>
                                <div class="team-item">
                                    <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>">
                                        <img src="<?= $awayLogo ?>" class="team-logo" alt="<?= htmlspecialchars($f['away_name']) ?>">
                                    </a>
                                    <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="team-name">
                                        <?= htmlspecialchars($f['away_name']) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="fixture-details">
                                <span class="date"><?= $dateStr ?></span>
                                <span class="time"><?= $timeStr ?></span>
                                <span class="venue"><?= $venue ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>

</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>