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

$leagueName = $currentLeague['name'] ?? 'League';
$leagueSeason = $currentLeague['season'] ?? date('Y');
$leagueFullName = trim("$leagueName $leagueSeason");
$leagueSeasonId = $currentLeague['id'] ?? null;
$cupName = $currentCup['name'] ?? 'Cup';
$cupSeasonId = $currentCup['id'] ?? null;

$currentYear = date('Y');
$startDate = "$currentYear-01-01";
$endDate = "$currentYear-12-31";
?>

<style>
    /* PAGE BACKGROUND CHANGED TO #defcfc - SECTIONS UNCHANGED */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        overflow-x: hidden;
    }

    .container { max-width: 1320px; margin: 0 auto; padding: 0 15px; }

    @media (max-width: 576px) {
        .container { padding: 0 !important; }
        .section-card, .table-responsive, .section-body { border-radius: 0 !important; border-left: none !important; border-right: none !important; }
    }

    /* Mini Matches */
    .mini-match {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0.4rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .mini-match:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }
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
    .mini-team { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .mini-logo {
        width: 36px; height: 36px; object-fit: contain;
        background: white; padding: 3px; border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15); border: 1px solid #ccc; flex-shrink: 0;
    }
    .mini-name { font-weight: 600; font-size: 0.95rem; color: #2c3e50; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
    .mini-winner { color: #27ae60 !important; font-weight: 700 !important; }

    .mini-score { display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 70px; flex-shrink: 0; }
    .mini-home-score, .mini-away-score { font-size: 1.6rem; font-weight: bold; line-height: 1; color: #2c3e50; }

    .mini-info { font-size: 0.84rem; font-weight: 600; color: #6c757d; text-align: right; min-width: 100px; flex-shrink: 0; line-height: 1.3; }

    @media (max-width: 576px) {
        .mini-match-row { padding: 0.6rem 0.9rem; min-height: 64px; gap: 8px; }
        .mini-logo { width: 32px; height: 32px; }
        .mini-name { font-size: 0.92rem; }
        .mini-home-score, .mini-away-score { font-size: 1.5rem; }
        .mini-info { font-size: 0.80rem; min-width: 90px; }
        .mini-teams { gap: 4px; }
    }

    /* Top Performers Tabs */
    #topPerformersTabs .nav-link {
        background-color: #2c3e50 !important;
        color: rgba(255, 255, 255, 0.9) !important;
        border: none !important;
        border-radius: 0 !important;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.65rem 0.5rem;
    }
    #topPerformersTabs .nav-link:hover {
        color: white !important;
        background-color: #34495e !important;
    }
    #topPerformersTabs .nav-link.active {
        background-color: #1a2530 !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 -3px 8px rgba(0,0,0,0.2) inset;
    }
    .tab-content { border-top: 1px solid #444; }

    /* Section Cards & Headers - Dark headers kept, card body remains white */
    .section-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        margin-bottom: 1.2rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .section-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 0.65rem 1.1rem;
        font-size: 1.05rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .news-header {
        background: linear-gradient(135deg, #c0392b, #e74c3c) !important;
    }
    .section-body { padding: 0.8rem; flex-grow: 1; }

    .section-header .btn-outline-light {
        border-color: rgba(255,255,255,0.5);
    }
    .section-header .btn-outline-light:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Tournament Controls */
    .tournament-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: nowrap !important;
        width: 100%;
        justify-content: space-between;
    }
    .tournament-controls .form-select {
        flex: 1;
        min-width: 140px;
        background-color: #34495e;
        color: white;
        border-color: #444;
    }
    @media (max-width: 576px) {
        .tournament-controls { gap: 8px; }
        .tournament-controls .form-select { font-size: 0.9rem; padding: 0.375rem 0.75rem; }
    }

    /* News Section */
    .news-item {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 0;
        transition: all 0.3s ease;
    }
    .news-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
        background: #6c757d;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        align-self: center;
    }
    .news-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
        margin: 0.5rem 0;
        line-height: 1.3;
        text-align: center;
    }
    .news-title a { color: inherit; text-decoration: none; }
    .news-title a:hover { color: #0d6efd; }
    .news-image-wrapper {
        flex-shrink: 0;
        width: 180px;
        height: 120px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .news-image { width: 100%; height: 100%; object-fit: cover; }
    @media (max-width: 768px) {
        .news-item { flex-direction: column; }
        .news-content { padding-right: 0; padding-bottom: 1rem; }
        .news-image-wrapper { width: 100%; height: 200px; margin-bottom: 0; border-radius: 8px; }
    }

    /* Tables */
    .league-table .table, .tournament-table .table {
        margin: 0;
        font-size: 0.88rem;
        table-layout: fixed;
        width: 100%;
        background: #ffffff;
    }
    .league-table .table thead th, .tournament-table .table thead th {
        background: #2c3e50;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 0.5rem 0.35rem;
        text-align: center;
    }
    .league-table .table tbody td, .tournament-table .table tbody td {
        padding: 0.45rem 0.35rem;
        vertical-align: middle;
        text-align: center;
        font-size: 0.88rem;
        color: #212529;
    }
    .league-table .table tbody tr:hover, .tournament-table .table tbody tr:hover { background: #f1f3f5; }
    .league-table .pos, .tournament-table .pos { font-weight: bold; font-size: 1rem; color: #212529; width: 12%; }
    .league-table .club-cell, .tournament-table .club-cell { width: 46%; text-align: left !important; padding-left: 0.6rem !important; }
    .league-table .club-name, .tournament-table .club-name {
        font-weight: 600;
        color: #212529;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.92rem;
        display: block;
    }
    /* Unified club logo style */
    .club-logo, .mini-logo, .club-thumb {
        width: 36px;
        height: 36px;
        object-fit: contain;
        background: white;
        padding: 3px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        border: 1px solid #ccc;
        flex-shrink: 0;
    }
    .club-thumb {
        width: 28px;
        height: 28px;
    }
    .league-table .club-logo {
        width: 36px;
        height: 36px;
    }
    .league-table .narrow, .tournament-table .narrow { width: 11%; font-weight: 600; }
    .league-table .points, .tournament-table .points { font-size: 1.2rem; font-weight: bold; color: #e74c3c; width: 15%; }

    .player-stats-table {
        font-size: 0.84rem;
        margin: 0;
        background: #ffffff;
    }
    .player-stats-table thead th {
        background: #2c3e50;
        color: white;
        font-size: 0.74rem;
        padding: 0.45rem 0.3rem;
        text-transform: uppercase;
    }
    .player-stats-table tbody tr {
        height: 46px;
        background: #ffffff;
        transition: all 0.2s;
        cursor: pointer;
    }
    .player-stats-table tbody tr:hover { background: #f1f3f5; }
    .player-stats-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
        color: #212529;
    }
    .player-stats-table .player-thumb {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .player-stats-table a {
        color: #212529 !important;
        font-weight: 600;
    }
    .player-stats-table a:hover {
        color: #0d6efd !important;
    }

    /* Gallery */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.6rem;
        padding: 0.4rem 0;
    }
    @media (max-width: 1200px) { .gallery-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 992px) { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) {
        .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }
    .gallery-item {
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        aspect-ratio: 1 / 1;
    }
    .gallery-item:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 16px 32px rgba(0,0,0,0.2);
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .gallery-item:hover img { transform: scale(1.12); }

    .gif-banner-section {
        margin: 1.5rem 0;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        padding: 12px 0;
    }
    .gif-banner {
        width: 100%;
        height: auto;
        max-height: 180px;
        object-fit: cover;
        display: block;
    }
    @media (max-width: 768px) { .gif-banner { max-height: 120px; } }
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
                    <div class="section-header news-header">
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
                            echo '<p class="text-center text-muted py-5">No news available.</p>';
                        } else {
                            foreach ($news as $n):
                                $imagePath = $n['image'] ? 'uploads/news/' . htmlspecialchars($n['image']) : 'https://via.placeholder.com/600x400/f8f9fa/333333?text=No+Image';
                                $category = 'Club News';
                        ?>
                            <div class="news-item">
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
                        <i class="bi bi-youtube text-danger fs-4"></i>
                    </div>
                    <div class="section-body">
                        <?php
                        $youtubeStmt = $pdo->prepare("SELECT url FROM social_links WHERE platform_name LIKE 'Youtube_Link%' AND is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 2");
                        $youtubeStmt->execute();
                        $youtubeLinks = $youtubeStmt->fetchAll(PDO::FETCH_COLUMN);
                        if (empty($youtubeLinks)) {
                            echo '<p class="text-center text-muted py-5">No videos available.</p>';
                        } else {
                            foreach ($youtubeLinks as $url):
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match);
                                $videoId = $match[1] ?? '';
                                if ($videoId):
                        ?>
                                    <div class="ratio ratio-16x9 mb-3">
                                        <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>?rel=0&modestbranding=1"
                                                title="YouTube video" allowfullscreen loading="lazy"></iframe>
                                    </div>
                                <?php endif; endforeach;
                        } ?>
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
                    <div class="table-responsive section-body">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pos">#</th>
                                    <th class="text-start ps-3">Club</th>
                                    <th class="narrow">P</th>
                                    <th class="narrow">W</th>
                                    <th class="points"><strong>PTS</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!$leagueSeasonId) {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-3">No active league season</td></tr>';
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
                                        $logo = $row['logo'] ? 'uploads/clubs/'.$row['logo'] : 'https://via.placeholder.com/40?text='.substr($row['club'],0,2);
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
    $gifRow = $gifStmt->fetch(PDO::FETCH_ASSOC);
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
                            $stmt = $pdo->prepare("SELECT f.fixture_date, f.venue, h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id, a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
                                FROM fixtures f
                                JOIN clubs h ON f.home_club_id = h.id
                                JOIN clubs a ON f.away_club_id = a.id
                                WHERE f.competition_season_id = ? AND f.status = 'Scheduled'
                                ORDER BY f.fixture_date ASC LIMIT 5");
                            $stmt->execute([$leagueSeasonId]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f):
                                $homeLogo = $f['home_logo'] ? "uploads/clubs/".$f['home_logo'] : "https://via.placeholder.com/44/defcfc/333333?text=".substr($f['home_name'],0,2);
                                $awayLogo = $f['away_logo'] ? "uploads/clubs/".$f['away_logo'] : "https://via.placeholder.com/44/defcfc/333333?text=".substr($f['away_name'],0,2);
                                $dateStr = (new DateTime($f['fixture_date']))->format('D, j M');
                                $venue = $f['venue'] ?? 'TBD';
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
                                        <small style="font-weight:400;opacity:0.9;"><?= htmlspecialchars($venue) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <p class="text-center text-muted py-4">No upcoming fixtures</p>
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
                            $stmt = $pdo->prepare("SELECT m.home_score, m.away_score, m.match_date, h.name AS home_name, h.logo AS home_logo, h.id AS home_club_id, a.name AS away_name, a.logo AS away_logo, a.id AS away_club_id
                                FROM matches m
                                JOIN fixtures f ON m.fixture_id = f.id
                                JOIN clubs h ON f.home_club_id = h.id
                                JOIN clubs a ON f.away_club_id = a.id
                                WHERE f.competition_season_id = ? AND m.home_score IS NOT NULL
                                ORDER BY m.match_date DESC LIMIT 5");
                            $stmt->execute([$leagueSeasonId]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r):
                                $homeWin = $r['home_score'] > $r['away_score'];
                                $awayWin = $r['away_score'] > $r['home_score'];
                                $homeLogo = $r['home_logo'] ? "uploads/clubs/".$r['home_logo'] : "https://via.placeholder.com/44/defcfc/333333?text=".substr($r['home_name'],0,2);
                                $awayLogo = $r['away_logo'] ? "uploads/clubs/".$r['away_logo'] : "https://via.placeholder.com/44/defcfc/333333?text=".substr($r['away_name'],0,2);
                                $dateStr = (new DateTime($r['match_date']))->format('D, j M');
                        ?>
                            <div class="mini-match">
                                <div class="mini-match-row">
                                    <div class="mini-teams">
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $r['home_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $homeLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name <?= $homeWin ? 'mini-winner' : '' ?>">
                                                    <?= htmlspecialchars($r['home_name']) ?>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="mini-team">
                                            <a href="clubs.php?club_id=<?= $r['away_club_id'] ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                                                <img src="<?= $awayLogo ?>" class="mini-logo" alt="">
                                                <div class="mini-name <?= $awayWin ? 'mini-winner' : '' ?>">
                                                    <?= htmlspecialchars($r['away_name']) ?>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mini-score">
                                        <span class="mini-home-score"><?= $r['home_score'] ?></span>
                                        <span class="mini-away-score"><?= $r['away_score'] ?></span>
                                    </div>
                                    <div class="mini-info">
                                        FT<br>
                                        <small style="font-weight:600;opacity:0.9;"><?= $dateStr ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <p class="text-center text-muted py-4">No results yet</p>
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
                                    'goals' => "
                                        SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo,
                                               COUNT(g.id) AS value
                                        FROM players p
                                        LEFT JOIN clubs c ON p.club_id = c.id
                                        LEFT JOIN goals g ON g.player_id = p.id
                                        JOIN matches m ON g.match_id = m.id
                                        JOIN fixtures f ON m.fixture_id = f.id
                                        WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
                                        GROUP BY p.id
                                        HAVING value > 0
                                        ORDER BY value DESC, p.name ASC
                                        LIMIT 10",
                                    'assists' => "
                                        SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo,
                                               COUNT(a.id) AS value
                                        FROM players p
                                        LEFT JOIN clubs c ON p.club_id = c.id
                                        LEFT JOIN assists a ON a.player_id = p.id
                                        JOIN goals g ON a.goal_id = g.id
                                        JOIN matches m ON g.match_id = m.id
                                        JOIN fixtures f ON m.fixture_id = f.id
                                        WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
                                        GROUP BY p.id
                                        HAVING value > 0
                                        ORDER BY value DESC, p.name ASC
                                        LIMIT 10",
                                    'cs' => "
                                        SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo,
                                               COUNT(cs.id) AS value
                                        FROM players p
                                        LEFT JOIN clubs c ON p.club_id = c.id
                                        LEFT JOIN clean_sheets cs ON cs.player_id = p.id
                                        JOIN matches m ON cs.match_id = m.id
                                        JOIN fixtures f ON m.fixture_id = f.id
                                        WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
                                        GROUP BY p.id
                                        HAVING value > 0
                                        ORDER BY value DESC, p.name ASC
                                        LIMIT 10",
                                    'yc' => "
                                        SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo,
                                               COUNT(cards.id) AS value
                                        FROM players p
                                        LEFT JOIN clubs c ON p.club_id = c.id
                                        LEFT JOIN cards ON cards.player_id = p.id AND cards.card_type = 'yellow'
                                        JOIN matches m ON cards.match_id = m.id
                                        JOIN fixtures f ON m.fixture_id = f.id
                                        WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
                                        GROUP BY p.id
                                        HAVING value > 0
                                        ORDER BY value DESC, p.name ASC
                                        LIMIT 10",
                                    'rc' => "
                                        SELECT p.id, p.name, p.photo, c.name AS club_name, c.logo AS club_logo,
                                               COUNT(cards.id) AS value
                                        FROM players p
                                        LEFT JOIN clubs c ON p.club_id = c.id
                                        LEFT JOIN cards ON cards.player_id = p.id AND cards.card_type = 'red'
                                        JOIN matches m ON cards.match_id = m.id
                                        JOIN fixtures f ON m.fixture_id = f.id
                                        WHERE f.competition_season_id = ? AND f.fixture_date BETWEEN ? AND ?
                                        GROUP BY p.id
                                        HAVING value > 0
                                        ORDER BY value DESC, p.name ASC
                                        LIMIT 10"
                                ];
                                if (!isset($queries[$stat])) return [];
                                $stmt = $pdo->prepare($queries[$stat]);
                                $stmt->execute([$leagueSeasonId, $startDate, $endDate]);
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            $tabs = [
                                'goals' => ['label' => 'Goals', 'color' => 'text-success'],
                                'assists' => ['label' => 'Assists', 'color' => 'text-info'],
                                'cs' => ['label' => 'Clean Sheets', 'color' => 'text-primary'],
                                'yc' => ['label' => 'Yellow Cards', 'color' => 'text-warning'],
                                'rc' => ['label' => 'Red Cards', 'color' => 'text-danger']
                            ];

                            $first = true;
                            foreach ($tabs as $key => $info):
                                $players = getTopPlayers($pdo, $key, $leagueSeasonId, $startDate, $endDate);
                                $active = $first ? 'show active' : '';
                                $first = false;
                            ?>
                            <div class="tab-pane fade <?= $active ?>" id="<?= $key ?>" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 player-stats-table">
                                        <thead>
                                            <tr>
                                                <th width="40">#</th>
                                                <th>Player</th>
                                                <th width="70" class="text-center">Club</th>
                                                <th width="60" class="text-center <?= $info['color'] ?>"><?= $info['label'] == 'Clean Sheets' ? 'CS' : substr($info['label'],0,2) ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($players)): ?>
                                                <tr><td colspan="4" class="text-center text-muted py-3">No data yet</td></tr>
                                            <?php else: foreach ($players as $i => $p):
                                                $photo = $p['photo'] ? "uploads/players/{$p['photo']}" : "https://via.placeholder.com/40x40?text=" . substr($p['name'],0,2);
                                                $clubLogo = $p['club_logo'] ? "uploads/clubs/{$p['club_logo']}" : "https://via.placeholder.com/36x36?text=C";
                                            ?>
                                                <tr onclick="window.location='player_profile.php?player_id=<?= $p['id'] ?>'" style="cursor:pointer;">
                                                    <td class="text-center fw-bold"><?= $i + 1 ?></td>
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
                                                        <?php else: ?>—
                                                        <?php endif; ?>
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

        $defaultTableId = 0;
        $defaultTableName = 'Tournament Table';
        foreach ($allTournaments as $t) {
            if ($t['is_current']) {
                $defaultTableId = $t['id'];
                $defaultTableName = $t['display_name'];
                break;
            }
        }
        if ($defaultTableId == 0 && !empty($allTournaments)) {
            $defaultTableId = $allTournaments[0]['id'];
            $defaultTableName = $allTournaments[0]['display_name'];
        }
        ?>
        <script>
        function updateBlock(blockId, cs_id) {
            const target = document.getElementById(blockId);
            if (!target) return;
            target.innerHTML = '<p class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm"></div> Loading...</p>';
            fetch(`ajax_tournament_home.php?type=${blockId.includes('Fixtures') ? 'fixtures' : blockId.includes('Results') ? 'results' : 'table'}&cs_id=${cs_id}&t=${Date.now()}`)
                .then(r => r.text())
                .then(html => {
                    target.innerHTML = html.trim() || '<p class="text-center text-muted py-4">No data</p>';
                })
                .catch(() => {
                    target.innerHTML = '<p class="text-center text-muted py-4">Error loading data</p>';
                });
        }
        function updateTitle(headerId, name) {
            const suffix = headerId.includes('Fixtures') ? ' Fixtures' : headerId.includes('Results') ? ' Results' : ' Table';
            document.getElementById(headerId).textContent = name + suffix;
        }
        function filterByYear(selectElement) {
            const year = selectElement.value;
            const container = selectElement.closest('.section-card');
            const dropdown = container.querySelector('.tournament-select');
            const headerId = container.querySelector('.tournament-header-title').id;
            dropdown.innerHTML = '';
            const filtered = <?= json_encode($allTournaments) ?>.filter(t => !year || t.season == year);
            filtered.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.display_name;
                if (t.id == dropdown.dataset.current) opt.selected = true;
                dropdown.appendChild(opt);
            });
            if (dropdown.options.length > 0 && !dropdown.value) {
                dropdown.value = dropdown.options[0].value;
            }
            const selectedName = dropdown.options[dropdown.selectedIndex]?.text || 'Tournament';
            updateBlock(container.querySelector('[id$="Body"]').id, dropdown.value);
            updateTitle(headerId, selectedName);
            dropdown.dataset.current = dropdown.value;
        }
        function onTournamentChange(selectElement) {
            const container = selectElement.closest('.section-card');
            const cs_id = selectElement.value;
            const bodyId = container.querySelector('[id$="Body"]').id;
            const headerId = container.querySelector('.tournament-header-title').id;
            const name = selectElement.options[selectElement.selectedIndex].text;
            updateBlock(bodyId, cs_id);
            updateTitle(headerId, name);
            selectElement.dataset.current = cs_id;
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tournament-select[data-all="true"]').forEach(sel => {
                const container = sel.closest('.section-card');
                const bodyId = container.querySelector('[id$="Body"]').id;
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
            <!-- Fixtures -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
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
                        <div class="text-center mt-2">
                            <span class="tournament-header-title fw-bold" id="titleFixtures">All Tournaments Fixtures</span>
                        </div>
                    </div>
                    <div class="section-body" id="tournamentFixturesBody">
                        <p class="text-center text-muted py-4">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
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
                        <div class="text-center mt-2">
                            <span class="tournament-header-title fw-bold" id="titleResults">All Tournaments Results</span>
                        </div>
                    </div>
                    <div class="section-body" id="tournamentResultsBody">
                        <p class="text-center text-muted py-4">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="col-lg-4">
                <div class="section-card tournament-table">
                    <div class="section-header">
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
                        <div class="text-center mt-2">
                            <span class="tournament-header-title fw-bold" id="titleTable"><?= htmlspecialchars($defaultTableName) ?> Table</span>
                        </div>
                    </div>
                    <div class="table-responsive section-body" id="tournamentTableBody">
                        <p class="text-center text-muted py-4">Loading...</p>
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
                                    <div class="modal-content bg-transparent border-0 shadow-none">
                                        <div class="text-end p-3">
                                            <button type="button"
                                                    class="btn-close btn-close-white shadow-lg"
                                                    data-bs-dismiss="modal"
                                                    style="font-size:1.8rem; background:white; border-radius:50%; padding:10px;">
                                            </button>
                                        </div>
                                        <img src="<?= $path ?>"
                                             class="img-fluid shadow-lg"
                                             alt="Gallery Image"
                                             style="max-height:90vh; width:auto; margin:0 auto; display:block; border-radius:0;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted py-5">No images in gallery yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>lightbox.option({resizeDuration:300,wrapAround:true,disableScrolling:true,fitImagesInViewport:true});</script>

<?php include 'includes/footer.php'; ob_end_flush(); ?>