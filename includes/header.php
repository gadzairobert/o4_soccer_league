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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* ── Design Tokens (shared with all pages) ── */
        :root {
            --gold:       #c9a84c;
            --gold-light: #f0d080;
            --cream:      #fdf8ef;
            --ink:        #1a1a2e;
            --border:     rgba(201,168,76,0.25);
        }

        html { scroll-behavior: smooth; scroll-padding-top: 160px; }

        /* ══════════════════════════════════════════
           DESKTOP — Club Bar  (WHITE background kept)
        ══════════════════════════════════════════ */
        .hero-logos {
            position: fixed; top: 0; left: 0; right: 0;
            background: #ffffff;                        /* WHITE — unchanged */
            border-bottom: 2px solid var(--border);
            height: 70px;
            padding: 8px 0;
            overflow-x: auto; white-space: nowrap;
            box-shadow: 0 3px 14px rgba(0,0,0,0.1);
            z-index: 1050;
            display: flex; align-items: center;
            transition: transform 0.4s ease;
        }
        .hero-logos.scrolled { transform: translateY(-100%); }
        .hero-logos-container {
            display: flex; justify-content: center;
            gap: 16px; padding: 0 15px; flex: 1;
        }

        /* Club logos — gold border ring on hover */
        .club-logo {
            width: 56px; height: 56px; object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            border: 2px solid transparent;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .club-logo:hover {
            transform: scale(1.18);
            border-color: var(--gold);
            box-shadow: 0 4px 14px rgba(201,168,76,0.35);
        }

        /* Right-side icons in club bar */
        .hero-right-icons {
            position: absolute; right: 15px;
            display: flex; gap: 16px; align-items: center;
        }
        .hero-right-icons a {
            color: #555;
            font-size: 1.35rem;
            transition: all 0.25s ease;
        }
        .hero-right-icons a:hover {
            color: var(--gold);
            transform: scale(1.25);
        }
        /* Search icon */
        #search-toggle { color: #555; font-size: 1.35rem; cursor: pointer; transition: color 0.25s, transform 0.25s; }
        #search-toggle:hover { color: var(--gold); transform: scale(1.2); }

        /* Search input */
        .hero-search-input {
            position: absolute; top: 50%; right: 50px;
            transform: translateY(-50%);
            width: 0; padding: 0;
            background: white;
            border-radius: 30px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
            transition: all 0.35s ease;
            overflow: hidden;
        }
        .hero-search-input.active {
            width: 290px; padding: 10px 20px;
            border: 2px solid var(--gold);
            box-shadow: 0 4px 18px rgba(201,168,76,0.2);
        }
        .hero-search-input input { border: none; outline: none; width: 100%; font-size: 1rem; font-family: 'DM Sans', sans-serif; }

        /* ══════════════════════════════════════════
           DESKTOP — Main Logo Circle
        ══════════════════════════════════════════ */
        .main-logo-circle {
            position: fixed; top: 8px; left: 16px;
            width: 115px; height: 115px;
            background: white;
            border-radius: 50%;
            padding: 8px;
            border: 4px solid var(--border);
            box-shadow: 0 8px 28px rgba(0,0,0,0.25), 0 0 0 2px rgba(201,168,76,0.15);
            z-index: 1060;
            transition: all 0.4s ease;
            cursor: pointer;
        }
        .main-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
        .main-logo-circle:hover { box-shadow: 0 10px 32px rgba(201,168,76,0.35), 0 0 0 3px rgba(201,168,76,0.3); }
        .main-logo-circle.scrolled { top: 6px; transform: scale(0.78); left: 12px; }

        /* ══════════════════════════════════════════
           DESKTOP — Navbar  (dark gold luxury)
        ══════════════════════════════════════════ */
        .navbar {
            position: fixed; top: 70px; left: 0; right: 0;
            background: linear-gradient(135deg, #16152b, #24224a) !important;
            border-bottom: 2px solid var(--border);
            padding: 0.35rem 0 !important;
            min-height: 56px !important;
            box-shadow: 0 6px 24px rgba(0,0,0,0.5);
            z-index: 1040;
            transition: top 0.4s ease;
        }
        .navbar.scrolled { top: 0; }

        /* League name */
        .navbar-brand {
            position: absolute; left: 140px !important; top: 50%; transform: translateY(-50%);
            font-family: 'Playfair Display', serif !important;
            color: var(--cream) !important;
            font-weight: 900;
            font-size: 1.6rem;
            white-space: nowrap; margin: 0; z-index: 1055;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.6);
            transition: all 0.4s ease;
            text-decoration: none;
        }
        .navbar.scrolled .navbar-brand { left: 90px !important; font-size: 1.3rem; }

        /* Nav item separator */
        .navbar-nav { margin-left: auto !important; margin-right: 20px !important; }
        .nav-item { position: relative; }
        .nav-item:not(:last-child)::after {
            content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            height: 22px; width: 1px; background: rgba(201,168,76,0.25);
        }

        /* Nav links */
        .nav-link {
            font-family: 'DM Sans', sans-serif !important;
            color: rgba(255,255,255,0.75) !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1.1rem !important;
            letter-spacing: 0.3px;
            transition: all 0.22s ease;
            border-radius: 6px;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--gold-light) !important;
            background: rgba(201,168,76,0.12) !important;
        }

        /* News pill — gold instead of red */
        .nav-link.news-item {
            background: rgba(201,168,76,0.15) !important;
            border: 1px solid rgba(201,168,76,0.4) !important;
            color: var(--gold) !important;
            font-weight: 700 !important;
        }
        .nav-link.news-item:hover, .nav-link.news-item.active {
            background: rgba(201,168,76,0.28) !important;
            color: var(--gold-light) !important;
            border-color: var(--gold) !important;
        }

        /* Dropdown menu */
        .dropdown-menu {
            background: #16152b;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 16px 44px rgba(0,0,0,0.55);
            margin-top: 8px;
            padding: 0.4rem 0;
        }
        .dropdown-item {
            font-family: 'DM Sans', sans-serif;
            color: rgba(255,255,255,0.72);
            padding: 10px 20px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background 0.2s, color 0.2s;
        }
        .dropdown-item:hover {
            background: rgba(201,168,76,0.12);
            color: var(--gold-light);
        }
        .dropdown-item img {
            width: 26px; height: 26px;
            object-fit: contain; border-radius: 6px;
            margin-right: 8px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .dropdown-divider { border-color: var(--border); }

        /* "LIVE" badge */
        .badge.bg-success {
            background: rgba(74,222,128,0.15) !important;
            color: #4ade80 !important;
            border: 1px solid rgba(74,222,128,0.35);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.68rem;
            letter-spacing: 0.5px;
            border-radius: 20px;
            padding: 2px 7px;
        }

        /* ══════════════════════════════════════════
           BODY padding
        ══════════════════════════════════════════ */
        body { padding-top: 150px; }
        body.scrolled { padding-top: 70px; }

        /* ══════════════════════════════════════════
           MOBILE — Club Bar  (WHITE kept)
        ══════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .hero-logos, .main-logo-circle { display: none !important; }
            .navbar {
                top: 0 !important;
                padding: 0.4rem 1rem !important;
                border-bottom: 2px solid var(--border);
            }
            .navbar-brand {
                position: static !important; transform: none !important;
                font-size: 1.4rem !important; margin: 0 12px; flex: 1; text-align: center;
            }

            /* Mobile logo circle */
            .mobile-logo-circle {
                width: 48px; height: 48px;
                background: white;
                border-radius: 50%; padding: 4px;
                border: 2px solid var(--border);
                box-shadow: 0 4px 12px rgba(0,0,0,0.25);
                flex-shrink: 0; cursor: pointer;
            }
            .mobile-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

            /* Mobile club bar — WHITE kept */
            .mobile-club-bar {
                position: fixed; top: 56px; left: 0; right: 0;
                height: 76px;
                background: #ffffff;                    /* WHITE — unchanged */
                border-bottom: 2px solid var(--border);
                z-index: 1030;
                box-shadow: 0 3px 12px rgba(0,0,0,0.1);
                transition: transform 0.4s ease;
                display: flex; align-items: center; overflow: hidden;
            }
            .mobile-club-bar.scrolled { transform: translateY(-100%); }
            .mobile-club-scroll { flex: 1; overflow-x: auto; white-space: nowrap; padding: 0 12px; }
            .mobile-club-scroll::-webkit-scrollbar { display: none; }
            .mobile-club-scroll { -ms-overflow-style: none; scrollbar-width: none; }
            .mobile-club-container { display: inline-flex; gap: 16px; padding: 10px 0; align-items: center; }
            .mobile-club-logo {
                width: 52px; height: 52px; object-fit: contain;
                border-radius: 12px;
                border: 2px solid transparent;
                box-shadow: 0 2px 8px rgba(0,0,0,0.12);
                transition: border-color 0.2s, transform 0.2s;
            }
            .mobile-club-logo:hover { border-color: var(--gold); transform: scale(1.12); }

            /* Mobile top icons */
            .mobile-top-icons { display: flex; gap: 16px; padding: 0 16px; background: #ffffff; }
            .mobile-top-icons a, .mobile-top-icons i {
                color: #555; font-size: 1.45rem; transition: color 0.25s, transform 0.25s;
            }
            .mobile-top-icons a:hover, .mobile-top-icons i:hover { color: var(--gold); transform: scale(1.2); }

            /* Hide membership icons in top bar on mobile */
            .mobile-membership-icons { display: none; }

            body { padding-top: 132px !important; }
            body.scrolled { padding-top: 76px !important; }
        }
        @media (max-width: 480px) {
            .mobile-logo-circle { width: 42px; height: 42px; }
            .navbar-brand { font-size: 1.25rem !important; }
            .mobile-club-logo { width: 44px; height: 44px; }
        }

        /* Mobile nav bottom section — membership links */
        .mobile-member-section {
            border-top: 1px solid var(--border);
            margin-top: 0.75rem; padding-top: 0.75rem;
        }
        .mobile-member-section a {
            font-family: 'DM Sans', sans-serif;
            color: rgba(255,255,255,0.7);
            transition: color 0.2s;
            text-decoration: none;
        }
        .mobile-member-section a:hover { color: var(--gold-light); }
        .mobile-member-section small { font-size: 0.75rem; }

        /* Toggler — gold tint */
        .navbar-toggler { border-color: var(--border) !important; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(201,168,76,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* ══════════════════════════════════════════
           Banner Popup
        ══════════════════════════════════════════ */
        #bannerPopup .modal-content { background: transparent; border: none; box-shadow: none; }
        #bannerPopup .modal-dialog { max-width: 94%; margin: 1.5rem auto; }
        #bannerPopup .btn-close {
            position: absolute; top: 10px; right: 10px; z-index: 10;
            background: rgba(0,0,0,0.75);
            color: white !important;
            border-radius: 50%;
            width: 36px; height: 36px;
            font-size: 1.4rem; opacity: 1;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
            border: 1px solid var(--border);
            transition: all 0.25s ease;
        }
        #bannerPopup .btn-close:hover {
            background: rgba(201,168,76,0.25);
            border-color: var(--gold);
            transform: scale(1.15);
        }
        #bannerPopup img { border-radius: 1.3rem; transition: transform 0.3s ease; }
        #bannerPopup img:hover { transform: scale(1.03); }
        @media (max-width: 576px) {
            #bannerPopup img { border-radius: 1rem; max-width: 360px !important; }
            #bannerPopup .btn-close { width: 30px; height: 30px; font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<?php
/* ── Banner Popup ── */
$showBanner  = false;
$bannerImage = '';
if (!isset($_COOKIE['popup_banner_shown']) || $_COOKIE['popup_banner_shown'] < time()) {
    $stmt = $pdo->prepare("SELECT filename FROM logos WHERE title = 'banner' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
    $stmt->execute();
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($banner && !empty($banner['filename']) && file_exists('uploads/admin/logos/' . $banner['filename'])) {
        $bannerImage = 'uploads/admin/logos/' . htmlspecialchars($banner['filename']);
        $showBanner  = true;
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
                <img src="<?= $bannerImage ?>" class="img-fluid w-100"
                     style="max-width:420px;margin:0 auto;display:block;border-radius:1.3rem;box-shadow:0 20px 56px rgba(0,0,0,0.5);"
                     alt="Promotion Banner">
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

<!-- ── DESKTOP: Clickable Logo Circle ── -->
<a href="index.php" class="main-logo-circle d-none d-lg-block" id="main-logo">
    <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($league_name) ?>">
</a>

<!-- ── DESKTOP: Club Bar ── -->
<div class="hero-logos d-none d-lg-flex" id="hero-logos">
    <div class="hero-logos-container">
        <?php foreach ($clubs as $club):
            $logoPath = $club['logo']
                ? "uploads/clubs/{$club['logo']}"
                : "https://via.placeholder.com/60/f8f9fa/c9a84c?text=" . urlencode(substr($club['name'], 0, 1));
        ?>
            <a href="clubs.php?club_id=<?= $club['id'] ?>" title="<?= htmlspecialchars($club['name']) ?>">
                <img src="<?= htmlspecialchars($logoPath) ?>"
                     alt="<?= htmlspecialchars($club['name']) ?>"
                     class="club-logo">
            </a>
        <?php endforeach; ?>
    </div>
    <div class="hero-right-icons">
        <div class="hero-search-wrapper" style="position:relative;">
            <i class="bi bi-search" id="search-toggle"></i>
            <div class="hero-search-input" id="search-input">
                <form action="search_results.php" method="GET">
                    <input type="text" name="q" placeholder="Search players, clubs…" autocomplete="off">
                </form>
            </div>
        </div>
        <?php foreach ($social_links as $social): ?>
            <a href="<?= htmlspecialchars($social['url'] ?? '#') ?>" target="_blank" rel="noopener">
                <i class="<?= htmlspecialchars($social['icon_class']) ?>"></i>
            </a>
        <?php endforeach; ?>
        <!-- Membership Icons (desktop) -->
        <?php if (isset($_SESSION['member_id'])): ?>
            <a href="user_portal/dashboard.php" title="My Dashboard"><i class="bi bi-person-circle"></i></a>
            <a href="user_portal/logout.php"    title="Logout">      <i class="bi bi-box-arrow-right"></i></a>
        <?php else: ?>
            <a href="user_portal/login.php"    title="Login">   <i class="bi bi-box-arrow-in-right"></i></a>
            <a href="user_portal/register.php" title="Register"><i class="bi bi-person-plus"></i></a>
        <?php endif; ?>
    </div>
</div>

<!-- ── MOBILE: Club Bar ── -->
<div class="mobile-club-bar d-lg-none" id="mobile-club-bar">
    <div class="mobile-club-scroll">
        <div class="mobile-club-container">
            <?php foreach ($clubs as $club):
                $logoPath = $club['logo']
                    ? "uploads/clubs/{$club['logo']}"
                    : "https://via.placeholder.com/60/f8f9fa/c9a84c?text=" . urlencode(substr($club['name'], 0, 1));
            ?>
                <a href="clubs.php?club_id=<?= $club['id'] ?>" title="<?= htmlspecialchars($club['name']) ?>">
                    <img src="<?= htmlspecialchars($logoPath) ?>"
                         alt="<?= htmlspecialchars($club['name']) ?>"
                         class="mobile-club-logo">
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
        <div class="mobile-membership-icons">
            <?php if (isset($_SESSION['member_id'])): ?>
                <a href="user_portal/dashboard.php"><i class="bi bi-person-circle" style="font-size:1.8rem;"></i></a>
                <a href="user_portal/logout.php">   <i class="bi bi-box-arrow-right" style="font-size:1.8rem;"></i></a>
            <?php else: ?>
                <a href="user_portal/login.php">    <i class="bi bi-box-arrow-in-right" style="font-size:1.8rem;"></i></a>
                <a href="user_portal/register.php"> <i class="bi bi-person-plus" style="font-size:1.8rem;"></i></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── MAIN NAVBAR ── -->
<nav class="navbar navbar-expand-lg navbar-dark" id="main-navbar">
    <div class="container-fluid position-relative px-4">

        <!-- Mobile top row -->
        <div class="d-lg-none d-flex align-items-center w-100">
            <a href="index.php" class="mobile-logo-circle">
                <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($league_name) ?>">
            </a>
            <div class="navbar-brand"><?= htmlspecialchars($league_name) ?></div>
            <button class="navbar-toggler ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <!-- Desktop league name -->
        <div class="navbar-brand d-none d-lg-block"><?= htmlspecialchars($league_name) ?></div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php foreach ($main_nav_items as $item):
                    $has_regular_dropdown = isset($nav_dropdowns[$item['id']]) && !empty($nav_dropdowns[$item['id']]);
                    $is_tournaments = ($item['name'] === 'Tournaments');
                    $is_news        = ($item['name'] === 'News');
                ?>
                    <li class="nav-item <?= ($has_regular_dropdown || $is_tournaments) ? 'dropdown' : '' ?>">
                        <?php if ($is_tournaments): ?>
                            <a class="nav-link dropdown-toggle" href="#"
                               data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="tournaments.php?cup_id=<?= $cup['id'] ?>">
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
                                    <li><span class="dropdown-item" style="color:rgba(255,255,255,0.35);">No active tournaments</span></li>
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
                                        <li>
                                            <a class="dropdown-item"
                                               href="<?= htmlspecialchars($sub['link']) ?>"
                                               <?= $sub['target_blank'] ? 'target="_blank" rel="noopener"' : '' ?>>
                                                <?= htmlspecialchars($sub['name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <!-- Mobile: membership links at bottom of menu -->
                <li class="nav-item d-lg-none mobile-member-section">
                    <div class="d-flex justify-content-around px-3">
                        <?php if (isset($_SESSION['member_id'])): ?>
                            <a href="user_portal/dashboard.php" class="text-center">
                                <i class="bi bi-person-circle fs-3 d-block mb-1"></i>
                                <small>Dashboard</small>
                            </a>
                            <a href="user_portal/logout.php" class="text-center">
                                <i class="bi bi-box-arrow-right fs-3 d-block mb-1"></i>
                                <small>Logout</small>
                            </a>
                        <?php else: ?>
                            <a href="user_portal/login.php" class="text-center">
                                <i class="bi bi-box-arrow-in-right fs-3 d-block mb-1"></i>
                                <small>Login</small>
                            </a>
                            <a href="user_portal/register.php" class="text-center">
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
document.addEventListener("DOMContentLoaded", function () {
    const heroLogos    = document.getElementById("hero-logos");
    const mobileBar    = document.getElementById("mobile-club-bar");
    const navbar       = document.getElementById("main-navbar");
    const mainLogo     = document.getElementById("main-logo");
    const body         = document.body;
    let lastScroll     = 0;

    window.addEventListener("scroll", function () {
        const current = window.pageYOffset;
        if (current > 100 && current > lastScroll) {
            heroLogos?.classList.add("scrolled");
            mobileBar?.classList.add("scrolled");
            navbar.classList.add("scrolled");
            mainLogo?.classList.add("scrolled");
            body.classList.add("scrolled");
        } else if (current < lastScroll && current <= 100) {
            heroLogos?.classList.remove("scrolled");
            mobileBar?.classList.remove("scrolled");
            navbar.classList.remove("scrolled");
            mainLogo?.classList.remove("scrolled");
            body.classList.remove("scrolled");
        }
        lastScroll = current;
    });

    // Desktop search toggle
    document.getElementById("search-toggle")?.addEventListener("click", function (e) {
        e.stopPropagation();
        document.getElementById("search-input").classList.toggle("active");
    });
    document.addEventListener("click", function (e) {
        const wrapper = document.querySelector(".hero-search-wrapper");
        const input   = document.getElementById("search-input");
        if (input && wrapper && !wrapper.contains(e.target)) {
            input.classList.remove("active");
        }
    });

    // Mobile search
    document.getElementById("mobile-search-toggle")?.addEventListener("click", function () {
        const q = prompt("Search players, clubs…");
        if (q) location.href = "search_results.php?q=" + encodeURIComponent(q);
    });
});
</script>