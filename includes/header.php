<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
// === DYNAMIC LOGO (header + favicon) ===
$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'frontend_header' AND is_active = 1 LIMIT 1");
$stmt->execute();
$headerLogo = $stmt->fetchColumn();
$logoSrc = $headerLogo ? 'uploads/admin/logos/' . $headerLogo : 'uploads/logo.png';
// Absolute URL for favicon
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . $host . rtrim($scriptPath, '/') . '/';
$faviconUrl = $baseUrl . $logoSrc;
// === SOCIAL ICONS ===
$socialStmt = $pdo->prepare("SELECT icon_class, url FROM social_links WHERE is_active = 1 AND display_in_header = 1 ORDER BY sort_order ASC");
$socialStmt->execute();
$social_links = $socialStmt->fetchAll(PDO::FETCH_ASSOC);
// === LEAGUE NAME ===
$league_name = "04 SOCCER LEAGUE";
$stmt = $pdo->query("SELECT value FROM settings WHERE key_name = 'league_name' LIMIT 1");
if ($row = $stmt->fetchColumn()) {
    $league_name = $row ?: $league_name;
}
// === MAIN NAV ITEMS ===
$navStmt = $pdo->prepare("SELECT * FROM nav_items WHERE parent_id = 0 AND is_active = 1 ORDER BY sort_order ASC, id ASC");
$navStmt->execute();
$main_nav_items = $navStmt->fetchAll(PDO::FETCH_ASSOC);
// === DROPDOWN SUB-ITEMS ===
$dropdownStmt = $pdo->prepare("
    SELECT ni.*
    FROM nav_items ni
    INNER JOIN nav_items parent ON ni.parent_id = parent.id
    WHERE parent.parent_id = 0
      AND parent.is_active = 1
      AND ni.is_active = 1
    ORDER BY ni.parent_id ASC, ni.sort_order ASC
");
$dropdownStmt->execute();
$dropdown_items = $dropdownStmt->fetchAll(PDO::FETCH_ASSOC);
$nav_dropdowns = [];
foreach ($dropdown_items as $item) {
    $nav_dropdowns[$item['parent_id']][] = $item;
}
// === CLUBS ===
$clubsStmt = $pdo->query("SELECT id, name, logo FROM clubs WHERE name NOT IN ('Loser 1','Loser 2','Winner 1', 'Winner 2') ORDER BY name ASC");
$clubs = $clubsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? $league_name) ?></title>
    <!-- FAVICONS -->
    <link rel="icon" href="<?= htmlspecialchars($faviconUrl) ?>" type="image/png">
    <link rel="shortcut icon" href="<?= htmlspecialchars($faviconUrl) ?>" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= htmlspecialchars($faviconUrl) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars($faviconUrl) ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= htmlspecialchars($faviconUrl) ?>">
    <link rel="icon" href="<?= $baseUrl ?>uploads/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html { scroll-behavior: smooth; scroll-padding-top: 160px; }
        /* Desktop Header Styles */
        .hero-logos {
            position: fixed; top: 0; left: 0; right: 0; background: #f8f9fa; height: 70px;
            padding: 8px 0; overflow-x: auto; white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1050; display: flex; align-items: center; transition: transform 0.4s ease;
        }
        .hero-logos.scrolled { transform: translateY(-100%); }
        .hero-logos-container { display: flex; justify-content: center; gap: 16px; padding: 0 15px; flex: 1; }
        .club-logo { width: 56px; height: 56px; object-fit: contain; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: transform 0.2s; }
        .club-logo:hover { transform: scale(1.2); }
        .hero-right-icons {
            position: absolute; right: 15px; display: flex; gap: 16px; align-items: center;
        }
        .hero-right-icons a { color: #333; font-size: 1.4rem; transition: all 0.3s; }
        .hero-right-icons a:hover { color: #007bff; transform: scale(1.3); }
        .navbar {
            position: fixed; top: 70px; left: 0; right: 0;
            background: linear-gradient(135deg, #1a1a1a, #2c2c2c) !important;
            padding: 0.35rem 0 !important; min-height: 56px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.45); z-index: 1040;
            transition: top 0.4s ease;
        }
        .navbar.scrolled { top: 0; }
        .main-logo-circle {
            position: fixed; top: 8px; left: 16px; width: 115px; height: 115px;
            background: white; border-radius: 50%; padding: 8px; border: 6px solid #fff;
            box-shadow: 0 8px 32px rgba(0,0,0,0.38); z-index: 1060; transition: all 0.4s ease;
            cursor: pointer;
        }
        .main-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
        .main-logo-circle.scrolled { top: 6px; transform: scale(0.78); left: 12px; }
        .navbar-brand {
            position: absolute; left: 140px !important; top: 50%; transform: translateY(-50%);
            color: #fff !important; font-weight: 800; font-size: 1.75rem;
            white-space: nowrap; margin: 0; z-index: 1055; letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.7); transition: all 0.4s ease;
        }
        .navbar.scrolled .navbar-brand { left: 90px !important; font-size: 1.38rem; }
        .navbar-nav { margin-left: auto !important; margin-right: 20px !important; }
        .nav-item:not(:last-child)::after {
            content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            height: 26px; width: 1px; background: rgba(255,255,255,0.3);
        }
        .nav-link {
            color: #eee !important; font-weight: 600; font-size: 0.96rem;
            padding: 0.5rem 1.1rem !important; transition: all 0.25s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important; background: rgba(255,255,255,0.2) !important; border-radius: 6px;
        }
        /* RED BACKGROUND FOR "News" BUTTON */
        .nav-link.news-item {
            background: #dc3545 !important;
            color: #fff !important;
            border-radius: 6px;
            font-weight: 700 !important;
        }
        .nav-link.news-item:hover,
        .nav-link.news-item.active {
            background: #c82333 !important;
            color: #fff !important;
        }
        .dropdown-menu {
            background: #2c2c2c; border: none; border-radius: 12px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.5); margin-top: 10px;
        }
        .dropdown-item { color: #ddd; padding: 10px 22px; font-weight: 500; }
        .dropdown-item:hover { background: rgba(255,255,255,0.18); color: #fff; }
        .dropdown-item img { width: 26px; height: 26px; object-fit: contain; border-radius: 6px; margin-right: 8px; }
        .hero-search-input {
            position: absolute; top: 50%; right: 50px; transform: translateY(-50%);
            width: 0; padding: 0; background: white; border-radius: 30px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.18); transition: all 0.35s ease; overflow: hidden;
        }
        .hero-search-input.active { width: 290px; padding: 11px 20px; border: 2.5px solid #007bff; }
        .hero-search-input input { border: none; outline: none; width: 100%; font-size: 1rem; }
        body { padding-top: 150px; background: #f5f5f5; }
        body.scrolled { padding-top: 70px; }
        /* Mobile Styles */
        @media (max-width: 991.98px) {
            .hero-logos, .main-logo-circle { display: none !important; }
            .navbar { top: 0 !important; padding: 0.4rem 1rem !important; }
            .navbar-brand {
                position: static !important; transform: none !important;
                font-size: 1.5rem !important; font-weight: 800; margin: 0 12px; flex: 1; text-align: center;
            }
            .mobile-logo-circle {
                width: 48px; height: 48px; background: white; border-radius: 50%; padding: 4px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3); flex-shrink: 0; cursor: pointer;
            }
            .mobile-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
            .mobile-club-bar {
                position: fixed; top: 56px; left: 0; right: 0; height: 76px;
                background: #f8f9fa; z-index: 1030; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transition: transform 0.4s ease; display: flex; align-items: center; overflow: hidden;
            }
            .mobile-club-bar.scrolled { transform: translateY(-100%); }
            .mobile-club-scroll { flex: 1; overflow-x: auto; white-space: nowrap; padding: 0 12px; }
            .mobile-club-scroll::-webkit-scrollbar { display: none; }
            .mobile-club-scroll { -ms-overflow-style: none; scrollbar-width: none; }
            .mobile-club-container { display: inline-flex; gap: 16px; padding: 10px 0; align-items: center; }
            .mobile-club-logo { width: 56px; height: 56px; object-fit: contain; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
            .mobile-top-icons { display: flex; gap: 16px; padding: 0 16px; background: #f8f9fa; }
            .mobile-top-icons a, .mobile-top-icons i { color: #333; font-size: 1.5rem; transition: all 0.3s; }
            .mobile-top-icons a:hover, .mobile-top-icons i:hover { color: #007bff; transform: scale(1.2); }
            body { padding-top: 132px !important; }
            body.scrolled { padding-top: 76px !important; }

            /* Move membership icons to bottom of mobile menu */
            .mobile-membership-icons {
                display: none; /* Hide in top bar on mobile */
            }
        }
        @media (max-width: 480px) {
            .mobile-logo-circle { width: 42px; height: 42px; }
            .navbar-brand { font-size: 1.35rem !important; }
            .mobile-club-logo { width: 48px; height: 48px; }
        }
        /* Banner Popup */
        #bannerPopup .modal-content { background: transparent; border: none; box-shadow: none; }
        #bannerPopup .modal-dialog { max-width: 94%; margin: 1.5rem auto; }
        #bannerPopup .btn-close {
            position: absolute; top: 10px; right: 10px; z-index: 10;
            background: rgba(0,0,0,0.8); color: white !important; border-radius: 50%;
            width: 36px; height: 36px; font-size: 1.4rem; opacity: 1;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5); transition: all 0.25s ease;
        }
        #bannerPopup .btn-close:hover { background: #dc3545; transform: scale(1.2); }
        #bannerPopup img { border-radius: 1.3rem; transition: transform 0.3s ease; }
        #bannerPopup img:hover { transform: scale(1.04); }
        @media (max-width: 576px) {
            #bannerPopup img { border-radius: 1rem; max-width: 360px !important; }
            #bannerPopup .btn-close { width: 32px; height: 32px; font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<?php
$showBanner = false;
$bannerImage = '';
if (!isset($_COOKIE['popup_banner_shown']) || $_COOKIE['popup_banner_shown'] < time()) {
    $stmt = $pdo->prepare("SELECT filename FROM logos WHERE title = 'banner' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
    $stmt->execute();
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($banner && !empty($banner['filename']) && file_exists('uploads/admin/logos/' . $banner['filename'])) {
        $bannerImage = 'uploads/admin/logos/' . htmlspecialchars($banner['filename']);
        $showBanner = true;
        setcookie('popup_banner_shown', time() + 86400, time() + 86400, "/");
    }
}
?>
<?php if ($showBanner): ?>
<div class="modal fade" id="bannerPopup" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            <a href="#" target="_blank" rel="noopener">
                <img src="<?= $bannerImage ?>" class="img-fluid w-100" style="max-width:420px;margin:0 auto;display:block;border-radius:1.3rem;box-shadow:0 16px 50px rgba(0,0,0,0.45);" alt="Promotion Banner">
            </a>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bannerModal = new bootstrap.Modal(document.getElementById('bannerPopup'), {backdrop:'static',keyboard:false});
    bannerModal.show();
});
</script>
<?php endif; ?>
<!-- DESKTOP: Clickable Logo -->
<a href="index.php" class="main-logo-circle d-none d-lg-block" id="main-logo">
    <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($league_name) ?>">
</a>
<!-- DESKTOP: Club Bar -->
<div class="hero-logos d-none d-lg-flex" id="hero-logos">
    <div class="hero-logos-container">
        <?php foreach ($clubs as $club):
            $logoPath = $club['logo'] ? "uploads/clubs/{$club['logo']}" : "https://via.placeholder.com/60?text=" . urlencode(substr($club['name'], 0, 1));
        ?>
            <a href="clubs.php?club_id=<?= $club['id'] ?>" title="<?= htmlspecialchars($club['name']) ?>">
                <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars($club['name']) ?>" class="club-logo">
            </a>
        <?php endforeach; ?>
    </div>
    <div class="hero-right-icons">
        <div class="hero-search-wrapper">
            <i class="bi bi-search" id="search-toggle" style="cursor:pointer;"></i>
            <div class="hero-search-input" id="search-input">
                <form action="search_results.php" method="GET">
                    <input type="text" name="q" placeholder="Search..." autocomplete="off">
                </form>
            </div>
        </div>
        <?php foreach ($social_links as $social): ?>
            <a href="<?= htmlspecialchars($social['url'] ?? '#') ?>" target="_blank" rel="noopener">
                <i class="<?= htmlspecialchars($social['icon_class']) ?>"></i>
            </a>
        <?php endforeach; ?>
        <!-- Membership Icons (Desktop only) -->
        <?php if (isset($_SESSION['member_id'])): ?>
            <a href="user_portal/dashboard.php" title="My Contributions"><i class="bi bi-person-circle" style="font-size:1.6rem;"></i></a>
            <a href="user_portal/logout.php" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
        <?php else: ?>
            <a href="user_portal/login.php" title="Login"><i class="bi bi-box-arrow-in-right"></i></a>
            <a href="user_portal/register.php" title="Register"><i class="bi bi-person-plus"></i></a>
        <?php endif; ?>
    </div>
</div>
<!-- MOBILE: Club Bar -->
<div class="mobile-club-bar d-lg-none" id="mobile-club-bar">
    <div class="mobile-club-scroll">
        <div class="mobile-club-container">
            <?php foreach ($clubs as $club):
                $logoPath = $club['logo'] ? "uploads/clubs/{$club['logo']}" : "https://via.placeholder.com/60?text=" . urlencode(substr($club['name'], 0, 1));
            ?>
                <a href="clubs.php?club_id=<?= $club['id'] ?>" title="<?= htmlspecialchars($club['name']) ?>">
                    <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars($club['name']) ?>" class="mobile-club-logo">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="mobile-top-icons">
        <i class="bi bi-search" id="mobile-search-toggle" style="cursor:pointer;"></i>
        <?php foreach ($social_links as $social): ?>
            <a href="<?= htmlspecialchars($social['url'] ?? '#') ?>" target="_blank" rel="noopener">
                <i class="<?= htmlspecialchars($social['icon_class']) ?>"></i>
            </a>
        <?php endforeach; ?>
        <!-- Membership icons hidden here on mobile - moved to navbar -->
        <div class="mobile-membership-icons">
            <?php if (isset($_SESSION['member_id'])): ?>
                <a href="user_portal/dashboard.php"><i class="bi bi-person-circle" style="font-size:1.8rem;"></i></a>
                <a href="user_portal/logout.php"><i class="bi bi-box-arrow-right" style="font-size:1.8rem;"></i></a>
            <?php else: ?>
                <a href="user_portal/login.php"><i class="bi bi-box-arrow-in-right" style="font-size:1.8rem;"></i></a>
                <a href="user_portal/register.php"><i class="bi bi-person-plus" style="font-size:1.8rem;"></i></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark" id="main-navbar">
    <div class="container-fluid position-relative px-4">
        <div class="d-lg-none d-flex align-items-center w-100">
            <a href="index.php" class="mobile-logo-circle">
                <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($league_name) ?>">
            </a>
            <div class="navbar-brand"><?= htmlspecialchars($league_name) ?></div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="navbar-brand d-none d-lg-block"><?= htmlspecialchars($league_name) ?></div>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php foreach ($main_nav_items as $item):
                    $has_regular_dropdown = isset($nav_dropdowns[$item['id']]) && !empty($nav_dropdowns[$item['id']]);
                    $is_tournaments = ($item['name'] === 'Tournaments');
                    $is_news = ($item['name'] === 'News');
                ?>
                    <li class="nav-item <?= ($has_regular_dropdown || $is_tournaments) ? 'dropdown' : '' ?>">
                        <?php if ($is_tournaments): ?>
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($item['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item fw-bold" href="tournaments.php">All Tournaments</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php
                                $cupStmt = $pdo->query("SELECT id, name, short_name, competition_name, season, logo FROM competition_seasons WHERE type = 'cup' AND is_current = 1 ORDER BY season DESC, name ASC");
                                $activeCups = $cupStmt->fetchAll(PDO::FETCH_ASSOC);
                                if (!empty($activeCups)):
                                    foreach ($activeCups as $cup):
                                        $cupName = $cup['name'] ?: ($cup['competition_name'] ?? '') . ' ' . $cup['season'];
                                ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="tournaments.php?cup_id=<?= $cup['id'] ?>">
                                            <?php if ($cup['logo']): ?>
                                                <img src="uploads/competitions/<?= htmlspecialchars($cup['logo']) ?>" alt="">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($cupName) ?></span>
                                            <span class="badge bg-success ms-auto">LIVE</span>
                                        </a>
                                    </li>
                                <?php
                                    endforeach;
                                else: ?>
                                    <li><span class="dropdown-item text-muted">No active tournaments</span></li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <a class="nav-link <?= $has_regular_dropdown ? 'dropdown-toggle' : '' ?> <?= $is_news ? 'news-item' : '' ?>"
                               href="<?= $has_regular_dropdown ? '#' : htmlspecialchars($item['link']) ?>"
                               <?= $has_regular_dropdown ? 'data-bs-toggle="dropdown" aria-expanded="false"' : '' ?>
                               <?= $item['target_blank'] ? 'target="_blank" rel="noopener"' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </a>
                            <?php if ($has_regular_dropdown): ?>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php foreach ($nav_dropdowns[$item['id']] as $sub): ?>
                                        <li><a class="dropdown-item" href="<?= htmlspecialchars($sub['link']) ?>"
                                               <?= $sub['target_blank'] ? 'target="_blank" rel="noopener"' : '' ?>>
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <!-- Membership Icons - Only shown on mobile, at bottom of menu -->
                <li class="nav-item d-lg-none border-top border-secondary mt-3 pt-3">
                    <div class="d-flex justify-content-around px-3">
                        <?php if (isset($_SESSION['member_id'])): ?>
                            <a href="user_portal/dashboard.php" class="text-light text-center">
                                <i class="bi bi-person-circle fs-3 d-block mb-1"></i>
                                <small>Dashboard</small>
                            </a>
                            <a href="user_portal/logout.php" class="text-light text-center">
                                <i class="bi bi-box-arrow-right fs-3 d-block mb-1"></i>
                                <small>Logout</small>
                            </a>
                        <?php else: ?>
                            <a href="user_portal/login.php" class="text-light text-center">
                                <i class="bi bi-box-arrow-in-right fs-3 d-block mb-1"></i>
                                <small>Login</small>
                            </a>
                            <a href="user_portal/register.php" class="text-light text-center">
                                <i class="bi bi-person-plus fs-3 d-block mb-1"></i>
                                <small>Register</small>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4 pb-5" id="main-content">
<script>
document.addEventListener("DOMContentLoaded", function() {
    const heroLogos = document.getElementById("hero-logos");
    const mobileClubBar = document.getElementById("mobile-club-bar");
    const navbar = document.getElementById("main-navbar");
    const mainLogo = document.getElementById("main-logo");
    const body = document.body;
    let lastScroll = 0;
    window.addEventListener("scroll", function() {
        const current = window.pageYOffset;
        if (current > 100 && current > lastScroll) {
            heroLogos?.classList.add("scrolled");
            mobileClubBar?.classList.add("scrolled");
            navbar.classList.add("scrolled");
            mainLogo?.classList.add("scrolled");
            body.classList.add("scrolled");
        } else if (current < lastScroll && current <= 100) {
            heroLogos?.classList.remove("scrolled");
            mobileClubBar?.classList.remove("scrolled");
            navbar.classList.remove("scrolled");
            mainLogo?.classList.remove("scrolled");
            body.classList.remove("scrolled");
        }
        lastScroll = current;
    });
    document.getElementById("search-toggle")?.addEventListener("click", (e) => {
        e.stopPropagation();
        document.getElementById("search-input").classList.toggle("active");
    });
    document.getElementById("mobile-search-toggle")?.addEventListener("click", () => {
        const q = prompt("Search...");
        if (q) location.href = "search_results.php?q=" + encodeURIComponent(q);
    });
    document.addEventListener("click", (e) => {
        const input = document.getElementById("search-input");
        if (input && !document.querySelector(".hero-search-wrapper")?.contains(e.target)) {
            input.classList.remove("active");
        }
    });
});
</script>
</body>
</html>