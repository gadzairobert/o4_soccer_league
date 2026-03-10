<?php
ob_start();
require 'config.php';
require 'includes/properties.php';
include 'includes/header.php';

// GET CURRENT LEAGUE & CUP SEASONS
$currentLeague = $pdo->query("
    SELECT id, name, season
    FROM competition_seasons
    WHERE is_current = 1 AND type = 'league'
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$currentCup = $pdo->query("
    SELECT id, name
    FROM competition_seasons
    WHERE is_current = 1 AND type = 'cup'
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$leagueName     = $currentLeague['name']   ?? 'League';
$leagueSeason   = $currentLeague['season'] ?? date('Y');
$leagueFullName = trim("$leagueName $leagueSeason");
$leagueSeasonId = $currentLeague['id']     ?? null;
$cupName        = $currentCup['name']      ?? 'Cup';
$cupSeasonId    = $currentCup['id']        ?? null;

$currentYear = date('Y');
$startDate   = "$currentYear-01-01";
$endDate     = "$currentYear-12-31";
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
        --card-hover: rgba(255,255,255,0.07);
        --panel-dark: linear-gradient(135deg, #16152b, #24224a);
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

    .container { max-width: 1320px; margin: 0 auto; padding: 0 15px; }

    @media (max-width: 576px) {
        .container { padding: 0 !important; }
        .section-card, .table-responsive, .section-body {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
        }
    }

    /* ── Section Card ── */
    .section-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        margin-bottom: 1.2rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* ── Section Header ── */
    .section-header {
        background: var(--panel-dark);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 0.7rem 1.1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .section-header span,
    .section-header .tournament-header-title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        color: var(--cream);
    }
    .news-header { border-bottom-color: #e74c3c !important; }
    .news-header::before {
        /* keep the red accent visible */
        background: linear-gradient(135deg, #2a0a0a, #4a1010) !important;
    }
    .section-header .btn-outline-light {
        border-color: rgba(201,168,76,0.5);
        color: var(--gold);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .section-header .btn-outline-light:hover {
        background: rgba(201,168,76,0.15);
        border-color: var(--gold);
        color: var(--gold-light);
    }

    .section-body { padding: 0.8rem; flex-grow: 1; }

    /* ── Mini Matches (fixtures + results) ── */
    .mini-match {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0.45rem;
        transition: all 0.2s ease;
    }
    .mini-match:hover {
        transform: translateY(-2px);
        border-color: rgba(201,168,76,0.3);
        background: rgba(255,255,255,0.06);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .mini-match:last-child { margin-bottom: 0; }
    .mini-match-row {
        padding: 0.65rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 68px;
        gap: 12px;
    }
    .mini-teams { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 0; }
    .mini-team  { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .mini-logo {
        width: 34px; height: 34px;
        object-fit: contain;
        background: #ffffff;
        padding: 3px;
        border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.2);
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        flex-shrink: 0;
        transition: border-color 0.2s;
    }
    .mini-match:hover .mini-logo { border-color: rgba(201,168,76,0.4); }
    .mini-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.8);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        text-decoration: none;
        transition: color 0.2s;
    }
    a .mini-name:hover, .mini-name:hover { color: var(--gold-light); }
    .mini-winner { color: #4ade80 !important; font-weight: 700 !important; }
    .mini-score {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 52px;
        flex-shrink: 0;
    }
    .mini-home-score, .mini-away-score {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
        color: var(--cream);
    }
    .mini-info {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--muted);
        text-align: right;
        min-width: 90px;
        flex-shrink: 0;
        line-height: 1.4;
    }
    @media (max-width: 576px) {
        .mini-match-row { padding: 0.6rem 0.75rem; min-height: 62px; gap: 8px; }
        .mini-logo { width: 30px; height: 30px; }
        .mini-name { font-size: 0.84rem; }
        .mini-home-score, .mini-away-score { font-size: 1.35rem; }
        .mini-info { font-size: 0.74rem; min-width: 78px; }
    }

    /* ── Top Performers Tabs ── */
    #topPerformersTabs .nav-link {
        background: rgba(255,255,255,0.04) !important;
        color: var(--muted) !important;
        border: none !important;
        border-radius: 0 !important;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 0.6rem 0.4rem;
        transition: all 0.2s;
    }
    #topPerformersTabs .nav-link:hover {
        color: var(--cream) !important;
        background: rgba(255,255,255,0.08) !important;
    }
    #topPerformersTabs .nav-link.active {
        background: rgba(201,168,76,0.12) !important;
        color: var(--gold) !important;
        font-weight: 700;
        border-bottom: 2px solid var(--gold) !important;
    }
    .tab-content { border-top: 1px solid rgba(255,255,255,0.07); }

    /* ── League / Tournament Tables ── */
    .league-table .table,
    .tournament-table .table {
        margin: 0;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.86rem;
        table-layout: fixed;
        width: 100%;
        background: transparent;
        color: rgba(255,255,255,0.8);
    }
    .league-table .table thead th,
    .tournament-table .table thead th {
        background: var(--panel-dark);
        color: var(--gold);
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0.5rem 0.35rem;
        text-align: center;
        border: none;
    }
    .league-table .table tbody td,
    .tournament-table .table tbody td {
        padding: 0.45rem 0.35rem;
        vertical-align: middle;
        text-align: center;
        font-size: 0.86rem;
        color: rgba(255,255,255,0.8);
        border-top: 1px solid rgba(255,255,255,0.05);
        border-bottom: none;
    }
    /* Fix Bootstrap table-hover dark bg */
    .league-table .table-hover > tbody > tr:hover > *,
    .tournament-table .table-hover > tbody > tr:hover > * {
        background-color: rgba(201,168,76,0.07) !important;
        color: rgba(255,255,255,0.95) !important;
        --bs-table-accent-bg: transparent;
    }
    .league-table .pos, .tournament-table .pos {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1rem;
        color: var(--muted);
        width: 12%;
    }
    .league-table .club-cell, .tournament-table .club-cell { width: 46%; text-align: left !important; padding-left: 0.6rem !important; }
    .league-table .club-name, .tournament-table .club-name {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        color: rgba(255,255,255,0.85);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.88rem;
        display: block;
        text-decoration: none;
        transition: color 0.2s;
    }
    .league-table .club-name:hover, .tournament-table .club-name:hover { color: var(--gold-light); }

    /* Club logos — solid white circle */
    .club-logo, .mini-logo, .club-thumb {
        object-fit: contain;
        background: #ffffff;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .club-logo {
        width: 32px; height: 32px;
        padding: 3px;
        border: 2px solid rgba(201,168,76,0.2);
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .club-thumb {
        width: 26px; height: 26px;
        padding: 2px;
        border: 1px solid rgba(201,168,76,0.2);
    }
    .league-table .narrow, .tournament-table .narrow { width: 11%; font-weight: 600; }
    .league-table .points, .tournament-table .points {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--gold);
        width: 15%;
    }

    /* ── Player Stats Table ── */
    .player-stats-table {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        margin: 0;
        background: transparent;
        color: rgba(255,255,255,0.8);
        width: 100%;
    }
    .player-stats-table thead th {
        background: var(--panel-dark);
        color: var(--gold);
        font-size: 0.7rem;
        padding: 0.45rem 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: none;
        font-weight: 700;
    }
    .player-stats-table tbody tr {
        height: 46px;
        background: transparent;
        transition: background 0.2s;
        cursor: pointer;
    }
    .player-stats-table .table-hover > tbody > tr:hover > * {
        background-color: rgba(201,168,76,0.07) !important;
        color: rgba(255,255,255,0.95) !important;
        --bs-table-accent-bg: transparent;
    }
    .player-stats-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
        color: rgba(255,255,255,0.8);
        border-top: 1px solid rgba(255,255,255,0.05);
        border-bottom: none;
    }
    .player-stats-table .player-thumb {
        width: 30px; height: 30px;
        object-fit: cover;
        border: 2px solid rgba(201,168,76,0.3);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .player-stats-table a {
        color: rgba(255,255,255,0.8) !important;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .player-stats-table a:hover { color: var(--gold-light) !important; }

    /* ── News ── */
    .news-item {
        display: flex;
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        padding: 0.9rem 0;
        transition: all 0.25s ease;
    }
    .news-item:hover {
        background: rgba(255,255,255,0.04);
        transform: translateY(-1px);
    }
    .news-item:last-child { border-bottom: none; }
    .news-content {
        flex: 1;
        padding-right: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }
    .news-category {
        display: inline-block;
        background: rgba(201,168,76,0.15);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.18rem 0.6rem;
        border-radius: 20px;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        align-self: center;
        letter-spacing: 0.5px;
    }
    .news-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0.4rem 0;
        line-height: 1.3;
        text-align: center;
    }
    .news-title a { color: inherit; text-decoration: none; transition: color 0.2s; }
    .news-title a:hover { color: var(--gold-light); }
    .news-image-wrapper {
        flex-shrink: 0;
        width: 160px; height: 110px;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .news-image { width: 100%; height: 100%; object-fit: cover; }
    @media (max-width: 768px) {
        .news-item { flex-direction: column; }
        .news-content { padding-right: 0; padding-bottom: 0.75rem; }
        .news-image-wrapper { width: 100%; height: 180px; }
    }

    /* ── Tournament Controls ── */
    .tournament-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap !important;
        width: 100%;
        justify-content: space-between;
    }
    .tournament-controls .form-select {
        flex: 1;
        min-width: 130px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 6px;
        -webkit-appearance: none;
        appearance: none;
        padding: 0.35rem 1.8rem 0.35rem 0.6rem;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23c9a84c'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.4rem center;
        background-size: 11px;
        cursor: pointer;
    }
    .tournament-controls .form-select:focus { outline: none; border-color: var(--gold); }
    .tournament-controls .form-select option { background: #1a1a2e; color: #eee; }
    @media (max-width: 576px) {
        .tournament-controls { gap: 6px; }
        .tournament-controls .form-select { font-size: 0.76rem; }
    }

    /* ── GIF Banner ── */
    .gif-banner-section {
        margin: 1.2rem 0;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,0.02);
        padding: 10px 0;
        border-radius: 8px;
    }
    .gif-banner { width: 100%; height: auto; max-height: 180px; object-fit: cover; display: block; border-radius: 8px; }
    @media (max-width: 768px) { .gif-banner { max-height: 120px; } }

    /* ── Gallery (home section) ── */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.6rem;
        padding: 0.4rem 0;
    }
    @media (max-width: 1200px) { .gallery-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 992px)  { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 0.8rem; } }

    .gallery-item {
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
        aspect-ratio: 1 / 1;
        cursor: pointer;
        background: rgba(255,255,255,0.03);
    }
    .gallery-item:hover {
        transform: translateY(-6px) scale(1.02);
        border-color: rgba(201,168,76,0.5);
        box-shadow: 0 16px 36px rgba(0,0,0,0.5);
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; display: block; }
    .gallery-item:hover img { transform: scale(1.1); }

    /* Modal close button */
    .gallery-modal-close {
        position: absolute; top: 12px; right: 12px;
        width: 38px; height: 38px;
        background: rgba(0,0,0,0.7);
        border: 1px solid rgba(201,168,76,0.4);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--gold);
        font-size: 1.3rem;
        z-index: 10;
        transition: all 0.2s;
        line-height: 1;
    }
    .gallery-modal-close:hover { background: rgba(201,168,76,0.2); border-color: var(--gold); }

    /* Spinner */
    .spinner-border { color: var(--gold) !important; }

    /* Text helpers */
    .text-muted   { color: var(--muted) !important; }
    .text-success { color: #4ade80 !important; }
    .text-info    { color: #67e8f9 !important; }
    .text-warning { color: #fbbf24 !important; }
    .text-danger  { color: #f87171 !important; }
    .text-primary { color: var(--gold) !important; }
</style>

<!-- HERO SLIDESHOW -->
<div class="hero-bleed">
    <?php include 'includes/slideshow.php'; ?>
</div>

<div class="container">

    <!-- TOP ROW: News | Videos | League Table -->
    <section class="mb-3">
        <div class="row g-3 g-lg-4">

            <!-- Latest News -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header news-header" style="background:linear-gradient(135deg,#2a0808,#5a1010);border-bottom-color:#e74c3c;">
                        <span>Latest News</span>
                        <a href="news.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                    <div class="section-body p-0">
                        <?php
                        $news = $pdo->query("
                            SELECT id, title, image, publish_date
                            FROM news
                            WHERE is_published = 1
                            ORDER BY publish_date DESC
                            LIMIT 3
                        ")->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($news)) {
                            echo '<p class="text-center py-5" style="color:var(--muted);font-family:\'DM Sans\',sans-serif;">No news available.</p>';
                        } else {
                            foreach ($news as $n):
                                $imagePath = $n['image'] ? 'uploads/news/' . htmlspecialchars($n['image']) : 'https://via.placeholder.com/600x400/1a1a2e/c9a84c?text=News';
                                $category  = 'Club News';
                        ?>
                            <div class="news-item" style="padding:0.9rem 0.9rem;">
                                <div class="news-content">
                                    <div class="news-category"><?= htmlspecialchars($category) ?></div>
                                    <h3 class="news-title">
                                        <a href="news_article.php?id=<?= $n['id'] ?>"><?= htmlspecialchars($n['title']) ?></a>
                                    </h3>
                                </div>
                                <div class="news-image-wrapper">
                                    <img src="<?= $imagePath ?>" class="news-image" alt="<?= htmlspecialchars($n['title']) ?>">
                                </div>
                            </div>
                        <?php endforeach; } ?>
                    </div>
                </div>
            </div>

            <!-- YouTube Highlights -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <span>Watch Highlights</span>
                        <i class="bi bi-youtube" style="color:#f87171;font-size:1.4rem;"></i>
                    </div>
                    <div class="section-body">
                        <?php
                        $youtubeStmt = $pdo->prepare("SELECT url FROM social_links WHERE platform_name LIKE 'Youtube_Link%' AND is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 2");
                        $youtubeStmt->execute();
                        $youtubeLinks = $youtubeStmt->fetchAll(PDO::FETCH_COLUMN);
                        if (empty($youtubeLinks)) {
                            echo '<p class="text-center py-5" style="color:var(--muted);font-family:\'DM Sans\',sans-serif;">No videos available.</p>';
                        } else {
                            foreach ($youtubeLinks as $url):
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match);
                                $videoId = $match[1] ?? '';
                                if ($videoId):
                        ?>
                                <div class="ratio ratio-16x9 mb-3" style="border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                                    <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>?rel=0&modestbranding=1"
                                            title="YouTube video" allowfullscreen loading="lazy"></iframe>
                                </div>
                        <?php endif; endforeach; } ?>
                    </div>
                </div>
            </div>

            <!-- League Table -->
            <div class="col-lg-4">
                <div class="section-card league-table">
                    <div class="section-header">
                        <span><?= htmlspecialchars($leagueFullName) ?></span>
                        <a href="league.php" class="btn btn-sm btn-outline-light">Full Table</a>
                    </div>
                    <div class="table-responsive section-body" style="padding:0;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pos">#</th>
                                    <th class="text-start ps-2">Club</th>
                                    <th class="narrow">P</th>
                                    <th class="narrow">W</th>
                                    <th class="points">PTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!$leagueSeasonId) {
                                    echo '<tr><td colspan="5" class="text-center py-3" style="color:var(--muted);">No active league season</td></tr>';
                                } else {
                                    $stmt = $pdo->prepare("
                                        SELECT c.id, c.name AS club, c.logo,
                                            COUNT(m.id) AS played,
                                            SUM(CASE WHEN (f.home_club_id = c.id AND m.home_score > m.away_score) OR
                                                          (f.away_club_id = c.id AND m.away_score > m.home_score) THEN 3
                                                     WHEN m.home_score = m.away_score THEN 1 ELSE 0 END) AS points
                                        FROM clubs c
                                        LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id) AND f.competition_season_id = ?
                                        LEFT JOIN matches m ON m.fixture_id = f.id AND m.home_score IS NOT NULL
                                        GROUP BY c.id
                                        ORDER BY points DESC, played ASC, club ASC
                                        LIMIT 10
                                    ");
                                    $stmt->execute([$leagueSeasonId]);
                                    $pos = 1;
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                        $logo = $row['logo'] ? 'uploads/clubs/'.$row['logo'] : 'https://via.placeholder.com/40/1a1a2e/c9a84c?text='.urlencode(substr($row['club'],0,2));
                                ?>
                                    <tr>
                                        <td class="pos"><?= $pos++ ?></td>
                                        <td class="club-cell">
                                            <a href="clubs.php?club_id=<?= $row['id'] ?>" class="text-decoration-none d-flex align-items-center gap-2">
                                                <img src="<?= $logo ?>" class="club-logo" alt="">
                                                <span class="club-name"><?= htmlspecialchars($row['club']) ?></span>
                                            </a>
                                        </td>
                                        <td class="narrow"><?= $row['played'] ?? 0 ?></td>
                                        <td class="narrow"><?= floor(($row['points'] ?? 0) / 3) ?></td>
                                        <td class="points"><?= $row['points'] ?? 0 ?></td>
                                    </tr>
                                <?php endwhile; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GIF BANNER -->
    <?php
    $gifStmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'footer_logo' AND title = 'gif' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
    $gifStmt->execute();
    $gifRow  = $gifStmt->fetch(PDO::FETCH_ASSOC);
    $gifPath = $gifRow ? 'uploads/admin/logos/' . htmlspecialchars($gifRow['filename']) : null;
    ?>
    <?php if ($gifPath && file_exists($gifPath)): ?>
    <div class="gif-banner-section">
        <img src="<?= $gifPath ?>" alt="Sponsor Banner" class="gif-banner" loading="lazy">
    </div>
    <?php endif; ?>

    <!-- LEAGUE: FIXTURES + RESULTS + TOP PERFORMERS -->
    <section class="mb-4">
        <div class="row g-3 g-lg-4">

            <!-- League Fixtures -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <span><?= htmlspecialchars($leagueName) ?> Fixtures</span>
                        <a href="fixtures.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                    <div class="section-body">
                        <?php if ($leagueSeasonId):
                            $stmt = $pdo->prepare("SELECT f.fixture_date, f.venue,
                                h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id,
                                a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
                                FROM fixtures f
                                JOIN clubs h ON f.home_club_id = h.id
                                JOIN clubs a ON f.away_club_id = a.id
                                WHERE f.competition_season_id = ? AND f.status = 'Scheduled'
                                ORDER BY f.fixture_date ASC LIMIT 5");
                            $stmt->execute([$leagueSeasonId]);
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($rows)) echo '<p class="text-center py-4" style="color:var(--muted);font-family:\'DM Sans\',sans-serif;">No upcoming fixtures</p>';
                            foreach ($rows as $f):
                                $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/44/1a1a2e/c9a84c?text=".urlencode(substr($f['home_name'],0,2));
                                $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/44/1a1a2e/c9a84c?text=".urlencode(substr($f['away_name'],0,2));
                                $dateStr  = (new DateTime($f['fixture_date']))->format('D, j M');
                                $venue    = $f['venue'] ?? 'TBD';
                        ?>
                            <div class="mini-match">
                                <div class="mini-match-row">
                                    <div class="mini-teams">
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $f['home_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $homeLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name"><?= htmlspecialchars($f['home_name']) ?></div>
                                            </a>
                                        </div>
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $f['away_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $awayLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name"><?= htmlspecialchars($f['away_name']) ?></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mini-info">
                                        <?= $dateStr ?><br>
                                        <small style="font-weight:400;opacity:0.8;"><?= htmlspecialchars($venue) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <p class="text-center py-4" style="color:var(--muted);font-family:'DM Sans',sans-serif;">No upcoming fixtures</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- League Results -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <span><?= htmlspecialchars($leagueName) ?> Results</span>
                        <a href="results.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                    <div class="section-body">
                        <?php if ($leagueSeasonId):
                            $stmt = $pdo->prepare("SELECT m.home_score, m.away_score, m.match_date,
                                h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id,
                                a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
                                FROM matches m
                                JOIN fixtures f ON m.fixture_id = f.id
                                JOIN clubs h ON f.home_club_id = h.id
                                JOIN clubs a ON f.away_club_id = a.id
                                WHERE f.competition_season_id = ? AND m.home_score IS NOT NULL
                                ORDER BY m.match_date DESC LIMIT 5");
                            $stmt->execute([$leagueSeasonId]);
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($rows)) echo '<p class="text-center py-4" style="color:var(--muted);font-family:\'DM Sans\',sans-serif;">No results yet</p>';
                            foreach ($rows as $r):
                                $homeWin  = $r['home_score'] > $r['away_score'];
                                $awayWin  = $r['away_score'] > $r['home_score'];
                                $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/44/1a1a2e/c9a84c?text=".urlencode(substr($r['home_name'],0,2));
                                $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/44/1a1a2e/c9a84c?text=".urlencode(substr($r['away_name'],0,2));
                                $dateStr  = (new DateTime($r['match_date']))->format('D, j M');
                        ?>
                            <div class="mini-match">
                                <div class="mini-match-row">
                                    <div class="mini-teams">
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $homeLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name <?= $homeWin ? 'mini-winner' : '' ?>"><?= htmlspecialchars($r['home_name']) ?></div>
                                            </a>
                                        </div>
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $awayLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name <?= $awayWin ? 'mini-winner' : '' ?>"><?= htmlspecialchars($r['away_name']) ?></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mini-score">
                                        <span class="mini-home-score"><?= $r['home_score'] ?></span>
                                        <span class="mini-away-score"><?= $r['away_score'] ?></span>
                                    </div>
                                    <div class="mini-info">
                                        FT<br>
                                        <small style="font-weight:600;"><?= $dateStr ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <p class="text-center py-4" style="color:var(--muted);font-family:'DM Sans',sans-serif;">No results yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <span>Top Performers</span>
                        <a href="player_stats.php" class="btn btn-sm btn-outline-light">Full Stats</a>
                    </div>
                    <div class="section-body p-0">
                        <ul class="nav nav-tabs nav-fill" id="topPerformersTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="goals-tab" data-bs-toggle="tab" data-bs-target="#goals" type="button" role="tab">Goals</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="assists-tab" data-bs-toggle="tab" data-bs-target="#assists" type="button" role="tab">Assists</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cs-tab" data-bs-toggle="tab" data-bs-target="#cs" type="button" role="tab">CS</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="yc-tab" data-bs-toggle="tab" data-bs-target="#yc" type="button" role="tab">YC</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rc-tab" data-bs-toggle="tab" data-bs-target="#rc" type="button" role="tab">RC</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <?php
                            function getTopPlayers($pdo, $stat, $leagueSeasonId, $startDate, $endDate) {
                                $queries = [
                                    'goals'   => "SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo, COUNT(g.id) AS value FROM players p LEFT JOIN clubs c ON p.club_id = c.id LEFT JOIN goals g ON g.player_id = p.id JOIN matches m ON g.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ? GROUP BY p.id HAVING value > 0 ORDER BY value DESC, p.name ASC LIMIT 10",
                                    'assists' => "SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo, COUNT(a.id) AS value FROM players p LEFT JOIN clubs c ON p.club_id = c.id LEFT JOIN assists a ON a.player_id = p.id JOIN goals g ON a.goal_id = g.id JOIN matches m ON g.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ? GROUP BY p.id HAVING value > 0 ORDER BY value DESC, p.name ASC LIMIT 10",
                                    'cs'      => "SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo, COUNT(cs.id) AS value FROM players p LEFT JOIN clubs c ON p.club_id = c.id LEFT JOIN clean_sheets cs ON cs.player_id = p.id JOIN matches m ON cs.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ? GROUP BY p.id HAVING value > 0 ORDER BY value DESC, p.name ASC LIMIT 10",
                                    'yc'      => "SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo, COUNT(cards.id) AS value FROM players p LEFT JOIN clubs c ON p.club_id = c.id LEFT JOIN cards ON cards.player_id = p.id AND cards.card_type = 'yellow' JOIN matches m ON cards.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ? GROUP BY p.id HAVING value > 0 ORDER BY value DESC, p.name ASC LIMIT 10",
                                    'rc'      => "SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo, COUNT(cards.id) AS value FROM players p LEFT JOIN clubs c ON p.club_id = c.id LEFT JOIN cards ON cards.player_id = p.id AND cards.card_type = 'red' JOIN matches m ON cards.match_id = m.id JOIN fixtures f ON m.fixture_id = f.id WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ? GROUP BY p.id HAVING value > 0 ORDER BY value DESC, p.name ASC LIMIT 10"
                                ];
                                if (!isset($queries[$stat])) return [];
                                $stmt = $pdo->prepare($queries[$stat]);
                                $stmt->execute([$leagueSeasonId, $startDate, $endDate]);
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            $tabs  = [
                                'goals'   => ['label' => 'Goals',        'color' => 'text-success'],
                                'assists' => ['label' => 'Assists',       'color' => 'text-info'],
                                'cs'      => ['label' => 'Clean Sheets',  'color' => 'text-primary'],
                                'yc'      => ['label' => 'Yellow Cards',  'color' => 'text-warning'],
                                'rc'      => ['label' => 'Red Cards',     'color' => 'text-danger'],
                            ];
                            $first = true;
                            foreach ($tabs as $key => $info):
                                $players = getTopPlayers($pdo, $key, $leagueSeasonId, $startDate, $endDate);
                                $active  = $first ? 'show active' : '';
                                $first   = false;
                            ?>
                            <div class="tab-pane fade <?= $active ?>" id="<?= $key ?>" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 player-stats-table">
                                        <thead>
                                            <tr>
                                                <th width="36">#</th>
                                                <th>Player</th>
                                                <th width="60" class="text-center">Club</th>
                                                <th width="56" class="text-center <?= $info['color'] ?>"><?= $info['label'] === 'Clean Sheets' ? 'CS' : substr($info['label'],0,2) ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($players)): ?>
                                                <tr><td colspan="4" class="text-center py-3" style="color:var(--muted);">No data yet</td></tr>
                                            <?php else: foreach ($players as $i => $p):
                                                $photo   = $p['photo']     ? "uploads/players/{$p['photo']}"   : "https://via.placeholder.com/40x40/1a1a2e/c9a84c?text=".urlencode(substr($p['name'],0,2));
                                                $clubLogo = $p['club_logo'] ? "uploads/clubs/{$p['club_logo']}" : "https://via.placeholder.com/36x36/1a1a2e/c9a84c?text=C";
                                            ?>
                                                <tr onclick="window.location='player_profile.php?player_id=<?= $p['id'] ?>'" style="cursor:pointer;">
                                                    <td class="text-center fw-bold" style="color:var(--muted);"><?= $i + 1 ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="<?= htmlspecialchars($photo) ?>" class="player-thumb rounded-circle" alt="">
                                                            <a href="player_profile.php?player_id=<?= $p['id'] ?>" class="text-decoration-none fw-semibold">
                                                                <?= htmlspecialchars($p['name']) ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($p['club_name']): ?>
                                                            <img src="<?= htmlspecialchars($clubLogo) ?>" class="club-thumb" alt="" title="<?= htmlspecialchars($p['club_name']) ?>">
                                                        <?php else: ?>—<?php endif; ?>
                                                    </td>
                                                    <td class="text-center <?= $info['color'] ?> fw-bold"><?= (int)$p['value'] ?></td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TOURNAMENT SECTION -->
    <section class="mb-5">
        <?php
        $yearStmt = $pdo->query("SELECT DISTINCT season AS year FROM competition_seasons WHERE type = 'cup' ORDER BY season DESC");
        $availableYears = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

        $tournamentsStmt = $pdo->query("
            SELECT id, COALESCE(name, CONCAT(competition_name, ' ', season)) AS display_name, is_current, season
            FROM competition_seasons
            WHERE type = 'cup'
            ORDER BY is_current DESC, season DESC, display_name ASC
        ");
        $allTournaments = $tournamentsStmt->fetchAll(PDO::FETCH_ASSOC);

        $defaultTableId   = 0;
        $defaultTableName = 'Tournament Table';
        foreach ($allTournaments as $t) {
            if ($t['is_current']) { $defaultTableId = $t['id']; $defaultTableName = $t['display_name']; break; }
        }
        if ($defaultTableId == 0 && !empty($allTournaments)) {
            $defaultTableId   = $allTournaments[0]['id'];
            $defaultTableName = $allTournaments[0]['display_name'];
        }
        ?>

        <script>
        function updateBlock(blockId, cs_id) {
            const target = document.getElementById(blockId);
            if (!target) return;
            target.innerHTML = '<p class="text-center py-4" style="color:rgba(255,255,255,0.3);font-family:\'DM Sans\',sans-serif;"><span class="spinner-border spinner-border-sm"></span> Loading...</p>';
            fetch(`ajax_tournament_home.php?type=${blockId.includes('Fixtures') ? 'fixtures' : blockId.includes('Results') ? 'results' : 'table'}&cs_id=${cs_id}&t=${Date.now()}`)
                .then(r => r.text())
                .then(html => {
                    target.innerHTML = html.trim() || '<p class="text-center py-4" style="color:rgba(255,255,255,0.3);font-family:\'DM Sans\',sans-serif;">No data</p>';
                })
                .catch(() => {
                    target.innerHTML = '<p class="text-center py-4" style="color:#f87171;font-family:\'DM Sans\',sans-serif;">Error loading data</p>';
                });
        }
        function updateTitle(headerId, name) {
            const suffix = headerId.includes('Fixtures') ? ' Fixtures' : headerId.includes('Results') ? ' Results' : ' Table';
            document.getElementById(headerId).textContent = name + suffix;
        }
        function filterByYear(selectElement) {
            const year      = selectElement.value;
            const container = selectElement.closest('.section-card');
            const dropdown  = container.querySelector('.tournament-select');
            const headerId  = container.querySelector('.tournament-header-title').id;
            dropdown.innerHTML = '';
            const filtered  = <?= json_encode($allTournaments) ?>.filter(t => !year || t.season == year);
            filtered.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id; opt.textContent = t.display_name;
                if (t.id == dropdown.dataset.current) opt.selected = true;
                dropdown.appendChild(opt);
            });
            if (dropdown.options.length > 0 && !dropdown.value) dropdown.value = dropdown.options[0].value;
            const selectedName = dropdown.options[dropdown.selectedIndex]?.text || 'Tournament';
            updateBlock(container.querySelector('[id$="Body"]').id, dropdown.value);
            updateTitle(headerId, selectedName);
            dropdown.dataset.current = dropdown.value;
        }
        function onTournamentChange(selectElement) {
            const container = selectElement.closest('.section-card');
            const cs_id     = selectElement.value;
            const bodyId    = container.querySelector('[id$="Body"]').id;
            const headerId  = container.querySelector('.tournament-header-title').id;
            const name      = selectElement.options[selectElement.selectedIndex].text;
            updateBlock(bodyId, cs_id);
            updateTitle(headerId, name);
            selectElement.dataset.current = cs_id;
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tournament-select[data-all="true"]').forEach(sel => {
                const container = sel.closest('.section-card');
                const bodyId    = container.querySelector('[id$="Body"]').id;
                updateBlock(bodyId, 0);
            });
            const tableSelect = document.querySelector('#tableSelect');
            if (tableSelect) {
                tableSelect.dataset.current = <?= $defaultTableId ?>;
                updateBlock('tournamentTableBody', <?= $defaultTableId ?>);
            }
        });
        </script>

        <div class="row g-3 g-lg-4">

            <!-- Tournament Fixtures -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header" style="flex-direction:column;gap:0.4rem;">
                        <div class="tournament-controls">
                            <select class="form-select tournament-select" data-all="true" onchange="onTournamentChange(this)">
                                <option value="0" selected>All Tournaments</option>
                                <?php foreach ($allTournaments as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['display_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select" onchange="filterByYear(this)">
                                <?php foreach ($availableYears as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                                <option value="">All Years</option>
                            </select>
                        </div>
                        <div class="text-center w-100">
                            <span class="tournament-header-title" id="titleFixtures">All Tournaments Fixtures</span>
                        </div>
                    </div>
                    <div class="section-body" id="tournamentFixturesBody">
                        <p class="text-center py-4" style="color:var(--muted);">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Tournament Results -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header" style="flex-direction:column;gap:0.4rem;">
                        <div class="tournament-controls">
                            <select class="form-select tournament-select" data-all="true" onchange="onTournamentChange(this)">
                                <option value="0" selected>All Tournaments</option>
                                <?php foreach ($allTournaments as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['display_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select" onchange="filterByYear(this)">
                                <?php foreach ($availableYears as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                                <option value="">All Years</option>
                            </select>
                        </div>
                        <div class="text-center w-100">
                            <span class="tournament-header-title" id="titleResults">All Tournaments Results</span>
                        </div>
                    </div>
                    <div class="section-body" id="tournamentResultsBody">
                        <p class="text-center py-4" style="color:var(--muted);">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Tournament Table -->
            <div class="col-lg-4">
                <div class="section-card tournament-table">
                    <div class="section-header" style="flex-direction:column;gap:0.4rem;">
                        <div class="tournament-controls">
                            <select class="form-select tournament-select" id="tableSelect" onchange="onTournamentChange(this)">
                                <?php foreach ($allTournaments as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= $t['id'] == $defaultTableId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['display_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select" onchange="filterByYear(this)">
                                <?php foreach ($availableYears as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                                <option value="">All Years</option>
                            </select>
                        </div>
                        <div class="text-center w-100">
                            <span class="tournament-header-title" id="titleTable"><?= htmlspecialchars($defaultTableName) ?> Table</span>
                        </div>
                    </div>
                    <div class="table-responsive section-body" id="tournamentTableBody">
                        <p class="text-center py-4" style="color:var(--muted);">Loading...</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- GALLERY -->
    <section class="mb-5">
        <div class="section-card">
            <div class="section-header">
                <span>Gallery Highlights</span>
                <a href="gallery.php" class="btn btn-sm btn-outline-light">View Full Gallery</a>
            </div>
            <div class="section-body p-3">
                <?php
                $galleryImages = $pdo->query("SELECT image FROM gallery ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($galleryImages)): ?>
                    <div class="gallery-grid">
                        <?php foreach ($galleryImages as $index => $img):
                            $path = 'uploads/gallery/' . htmlspecialchars($img);
                        ?>
                            <div class="gallery-item"
                                 data-bs-toggle="modal"
                                 data-bs-target="#homeGalleryModal<?= $index ?>">
                                <img src="<?= $path ?>" alt="Gallery Image" loading="lazy">
                            </div>
                            <div class="modal fade" id="homeGalleryModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content bg-transparent border-0 shadow-none position-relative">
                                        <button type="button"
                                                class="gallery-modal-close"
                                                data-bs-dismiss="modal">&times;</button>
                                        <img src="<?= $path ?>"
                                             class="img-fluid shadow-lg"
                                             alt="Gallery Image"
                                             style="max-height:88vh;width:auto;margin:0 auto;display:block;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.7);">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center py-5" style="color:var(--muted);font-family:'DM Sans',sans-serif;">No images in gallery yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</div><!-- /.container -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>lightbox.option({resizeDuration:300,wrapAround:true,disableScrolling:true,fitImagesInViewport:true});</script>

<?php include 'includes/footer.php'; ob_end_flush(); ?>