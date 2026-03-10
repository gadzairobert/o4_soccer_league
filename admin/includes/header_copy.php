<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – Ward 24 Community League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; background: #f8f9fa; }
        .sidebar { 
            height: 100vh; 
            position: fixed; 
            top: 0; left: 0; 
            width: 250px; 
            background: #343a40; 
            transition: width .3s; 
            z-index: 1000; 
            overflow-y: auto; 
        }
        .sidebar.collapsed { width: 60px; }

        /* TIGHTER, CLEANER MENU SPACING */
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 9px 12px;
            margin: 3px 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: #495057;
            transform: translateX(4px);
        }
        .sidebar .nav-link .icon {
            min-width: 22px;
            margin-right: 12px;
            font-size: 1.18rem;
        }
        .sidebar.collapsed .nav-link .text { display: none; }
        .sidebar.collapsed .nav-link {
            padding: 10px;
            justify-content: center;
            margin: 3px auto;
        }
        .sidebar.collapsed .nav-link:hover {
            transform: scale(1.15);
        }

        .main-content {
            margin-left: 250px;
            padding: 25px;
            transition: margin-left .3s;
            min-height: 100vh;
        }
        .sidebar.collapsed ~ .main-content { margin-left: 60px; }

        .toggle-btn {
            background: #495057;
            border: none;
            color: #fff;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .sidebar, .sidebar.collapsed { width: 0; opacity: 0; }
            .main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body>

<nav class="sidebar" id="sidebar">

    <div class="p-3 d-flex align-items-center justify-content-between border-bottom">
        <span class="text-white small">Welcome, <?= htmlspecialchars($username ?? 'Admin') ?></span>
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <ul class="nav flex-column px-2 mt-3">
        <li class="nav-item">
            <a class="nav-link <?= $page=='home' ? 'active' : '' ?>" href="?page=home">
                <span class="icon"><i class="bi bi-speedometer2"></i></span>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='league' ? 'active' : '' ?>" href="?page=league">
                <span class="icon"><i class="bi bi-trophy-fill"></i></span>
                <span class="text">League Table</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='clubs' ? 'active' : '' ?>" href="?page=clubs">
                <span class="icon"><i class="bi bi-shield-fill"></i></span>
                <span class="text">Clubs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='players' ? 'active' : '' ?>" href="?page=players">
                <span class="icon"><i class="bi bi-person-badge-fill"></i></span>
                <span class="text">Players</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='competitions' ? 'active' : '' ?>" href="?page=competitions">
                <span class="icon"><i class="bi bi-calendar-event-fill"></i></span>
                <span class="text">Competitions</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='fixtures' ? 'active' : '' ?>" href="?page=fixtures">
                <span class="icon"><i class="bi bi-calendar-event-fill"></i></span>
                <span class="text">League Fixtures</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='stats' ? 'active' : '' ?>" href="?page=stats">
                <span class="icon"><i class="bi bi-bar-chart-fill"></i></span>
                <span class="text">League Stats</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='tournaments' ? 'active' : '' ?>" href="?page=tournaments">
                <span class="icon"><i class="bi bi-calendar-event-fill"></i></span>
                <span class="text">Tournament Fixtures</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='tournament_stats' ? 'active' : '' ?>" href="?page=tournament_stats">
                <span class="icon"><i class="bi bi-bar-chart-fill"></i></span>
                <span class="text">Tournament Stats</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='tournament_images' ? 'active' : '' ?>" href="?page=tournament_images">
                <span class="icon"><i class="bi bi-images"></i></span>
                <span class="text">Tournament Images</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='news' ? 'active' : '' ?>" href="?page=news">
                <span class="icon"><i class="bi bi-newspaper"></i></span>
                <span class="text">News</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='slideshow' ? 'active' : '' ?>" href="?page=slideshow">
                <span class="icon"><i class="bi bi-images"></i></span>
                <span class="text">Slideshow</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='about_us' ? 'active' : '' ?>" href="?page=about_us">
                <span class="icon"><i class="bi bi-people-fill"></i></span>
                <span class="text">About Us</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='logos' ? 'active' : '' ?>" href="?page=logos">
                <span class="icon"><i class="bi bi-image-fill"></i></span>
                <span class="text">Logos</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='social_media' ? 'active' : '' ?>" href="?page=social_media">
                <span class="icon"><i class="bi bi-share-fill"></i></span>
                <span class="text">Social Media</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='gallery' ? 'active' : '' ?>" href="?page=gallery">
                <span class="icon"><i class="bi bi-card-image"></i></span>
                <span class="text">Gallery</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='nav_bar' ? 'active' : '' ?>" href="?page=nav_bar">
                <span class="icon"><i class="bi bi-menu-app-fill"></i></span>
                <span class="text">Frontend Navbar</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page=='smtp_settings' ? 'active' : '' ?>" href="?page=smtp_settings">
                <span class="icon"><i class="bi bi-gear"></i></span>
                <span class="text">SMTP Settings</span>
            </a>
        </li>

        <?php if ($_SESSION['is_admin'] ?? false): ?>
        <li class="nav-item">
            <a class="nav-link <?= $page=='users' ? 'active' : '' ?>" href="?page=users">
                <span class="icon"><i class="bi bi-people-fill"></i></span>
                <span class="text">Manage Users</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <ul class="nav flex-column mt-auto px-2 mb-4">
        <li class="nav-item">
            <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</nav>

<main class="main-content">

    <!-- Success / Error Messages -->
    <?php if (!empty($_SESSION['success'])): $msg = $_SESSION['success']; unset($_SESSION['success']); ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): $msg = $_SESSION['error']; unset($_SESSION['error']); ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- LOGOUT MODAL -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-right text-danger me-2"></i>
                        Confirm Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismisas="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-0">Are you sure you want to log out?</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-danger px-4">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>

<script>
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>