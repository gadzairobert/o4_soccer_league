<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin – Ward 24 Community League</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
body{ overflow-x:hidden; background:#f8f9fa; }

/* SIDEBAR */
.sidebar{
    height:100vh;
    position:fixed;
    top:0; left:0;
    width:250px;
    background:#343a40;
    transition:all .3s ease;
    z-index:1040;
    overflow-y:auto;
}
.sidebar.collapsed{ width:60px; }

.sidebar .nav-link{
    color:#adb5bd;
    padding:9px 12px;
    margin:3px 10px;
    border-radius:8px;
    display:flex;
    align-items:center;
    font-size:.95rem;
    transition:.25s;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active{
    color:#fff;
    background:#495057;
    transform:translateX(4px);
}
.sidebar .icon{
    min-width:22px;
    margin-right:12px;
    font-size:1.1rem;
}
.sidebar.collapsed .text{ display:none; }
.sidebar.collapsed .nav-link{
    justify-content:center;
    margin:3px auto;
}
.sidebar.collapsed .nav-link:hover{
    transform:scale(1.15);
}
.dropdown-toggle::after{ margin-left:auto; }
.sub-menu .nav-link{
    font-size:.9rem;
    margin-left:32px;
}

/* MAIN CONTENT */
.main-content{
    margin-left:250px;
    padding:25px;
    transition:.3s;
}
.sidebar.collapsed ~ .main-content{ margin-left:60px; }
@media(max-width:768px){
    .sidebar{ transform:translateX(-100%); pointer-events:none; }
    .sidebar.show{ transform:translateX(0); pointer-events:auto; }
    .main-content{ margin-left:0!important; padding-top:70px; }
}

/* TOGGLE BUTTON */
.toggle-btn{
    background:#495057;
    border:none;
    color:#fff;
    padding:6px 10px;
    border-radius:6px;
    cursor:pointer;
}

/* MOBILE TOP BAR */
.mobile-topbar{
    display:none;
    position:fixed;
    top:0; left:0; right:0;
    height:60px;
    background:#343a40;
    z-index:1100;
    padding:0 15px;
    align-items:center;
    justify-content:space-between;
}
.mobile-topbar span{ color:#fff; font-size:.9rem; }
@media(max-width:768px){ .mobile-topbar{ display:flex; } }

/* LOGOUT */
.nav-bottom{ position:relative; padding:0 15px 15px 15px; z-index:1051; }
</style>
</head>
<body>

<!-- MOBILE TOP BAR -->
<div class="mobile-topbar">
    <button class="toggle-btn" id="mobileToggle" type="button">
        <i class="bi bi-list"></i>
    </button>
    <span>Admin Panel</span>
</div>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">

<div class="p-3 d-flex justify-content-between align-items-center border-bottom">
    <span class="text-white small">Welcome, <?= htmlspecialchars($username ?? 'Admin') ?></span>
    <button class="toggle-btn d-none d-md-inline" id="desktopToggle" type="button">
        <i class="bi bi-list"></i>
    </button>
</div>

<ul class="nav flex-column px-2 mt-3">
<!-- DASHBOARD -->
<li class="nav-item">
<a class="nav-link <?= $page=='home'?'active':'' ?>" href="?page=home">
    <span class="icon"><i class="bi bi-speedometer2"></i></span>
    <span class="text">Dashboard</span>
</a>
</li>

<!-- COMPETITIONS -->
<li class="nav-item">
<a class="nav-link dropdown-toggle <?= in_array($page,['competitions','fixtures','stats','league'])?'active':'' ?>" data-bs-toggle="collapse" href="#competitionMenu">
    <span class="icon"><i class="bi bi-calendar-event-fill"></i></span>
    <span class="text">Competitions</span>
</a>
<div class="collapse <?= in_array($page,['competitions','fixtures','stats','league'])?'show':'' ?>" id="competitionMenu" data-bs-parent="#sidebar">
<ul class="nav flex-column sub-menu">
<li><a class="nav-link <?= $page=='competitions'?'active':'' ?>" href="?page=competitions">All Competitions</a></li>
<li><a class="nav-link <?= $page=='fixtures'?'active':'' ?>" href="?page=fixtures">League Fixtures</a></li>
<li><a class="nav-link <?= $page=='stats'?'active':'' ?>" href="?page=stats">League Stats</a></li>
<li><a class="nav-link <?= $page=='league'?'active':'' ?>" href="?page=league">League Table</a></li>
</ul>
</div>
</li>

<!-- TOURNAMENTS -->
<li class="nav-item">
<a class="nav-link dropdown-toggle <?= in_array($page,['tournaments','tournament_stats','tournament_images'])?'active':'' ?>" data-bs-toggle="collapse" href="#tournamentMenu">
    <span class="icon"><i class="bi bi-trophy"></i></span>
    <span class="text">Tournaments</span>
</a>
<div class="collapse <?= in_array($page,['tournaments','tournament_stats','tournament_images'])?'show':'' ?>" id="tournamentMenu" data-bs-parent="#sidebar">
<ul class="nav flex-column sub-menu">
<li><a class="nav-link <?= $page=='tournaments'?'active':'' ?>" href="?page=tournaments">Fixtures</a></li>
<li><a class="nav-link <?= $page=='tournament_stats'?'active':'' ?>" href="?page=tournament_stats">Stats</a></li>
<li><a class="nav-link <?= $page=='tournament_images'?'active':'' ?>" href="?page=tournament_images">Images</a></li>
</ul>
</div>
</li>

<!-- TEAMS -->
<li class="nav-item">
<a class="nav-link dropdown-toggle <?= in_array($page,['clubs','players'])?'active':'' ?>" data-bs-toggle="collapse" href="#teamMenu">
    <span class="icon"><i class="bi bi-people-fill"></i></span>
    <span class="text">Teams & Players</span>
</a>
<div class="collapse <?= in_array($page,['clubs','players'])?'show':'' ?>" id="teamMenu" data-bs-parent="#sidebar">
<ul class="nav flex-column sub-menu">
<li><a class="nav-link <?= $page=='clubs'?'active':'' ?>" href="?page=clubs">Clubs</a></li>
<li><a class="nav-link <?= $page=='players'?'active':'' ?>" href="?page=players">Players</a></li>
<li><a class="nav-link <?= $page=='management'?'active':'' ?>" href="?page=management">Management</a></li>
</ul>

</div>
</li>

<!-- MEDIA -->
<li class="nav-item">
<a class="nav-link dropdown-toggle <?= in_array($page,['news','slideshow','gallery'])?'active':'' ?>" data-bs-toggle="collapse" href="#mediaMenu">
    <span class="icon"><i class="bi bi-images"></i></span>
    <span class="text">Media</span>
</a>
<div class="collapse <?= in_array($page,['news','slideshow','gallery'])?'show':'' ?>" id="mediaMenu" data-bs-parent="#sidebar">
<ul class="nav flex-column sub-menu">
<li><a class="nav-link <?= $page=='news'?'active':'' ?>" href="?page=news">News</a></li>
<li><a class="nav-link <?= $page=='slideshow'?'active':'' ?>" href="?page=slideshow">Slideshow</a></li>
<li><a class="nav-link <?= $page=='gallery'?'active':'' ?>" href="?page=gallery">Gallery</a></li>
<li><a class="nav-link <?= $page=='contributions'?'active':'' ?>" href="?page=contributions">Contributions</a></li>
</ul>
</div>
</li>

<!-- SETTINGS -->
<li class="nav-item">
<a class="nav-link dropdown-toggle <?= in_array($page,['about_us','logos','social_media','nav_bar','smtp_settings'])?'active':'' ?>" data-bs-toggle="collapse" href="#settingsMenu">
    <span class="icon"><i class="bi bi-gear-fill"></i></span>
    <span class="text">Site Settings</span>
</a>
<div class="collapse <?= in_array($page,['about_us','logos','social_media','nav_bar','smtp_settings'])?'show':'' ?>" id="settingsMenu" data-bs-parent="#sidebar">
<ul class="nav flex-column sub-menu">
<li><a class="nav-link <?= $page=='about_us'?'active':'' ?>" href="?page=about_us">About Us</a></li>
<li><a class="nav-link <?= $page=='logos'?'active':'' ?>" href="?page=logos">Logos</a></li>
<li><a class="nav-link <?= $page=='social_media'?'active':'' ?>" href="?page=social_media">Social Media</a></li>
<li><a class="nav-link <?= $page=='nav_bar'?'active':'' ?>" href="?page=nav_bar">Frontend Navbar</a></li>
<li><a class="nav-link <?= $page=='smtp_settings'?'active':'' ?>" href="?page=smtp_settings">SMTP</a></li>
</ul>
</div>
</li>

<?php if($_SESSION['is_admin'] ?? false): ?>
<li class="nav-item">
<a class="nav-link <?= $page=='users'?'active':'' ?>" href="?page=users">
    <span class="icon"><i class="bi bi-person-gear"></i></span>
    <span class="text">Manage Users</span>
</a>
</li>
<?php endif; ?>

</ul>

<!-- LOGOUT -->
<div class="nav-bottom">
<ul class="nav flex-column">
<li class="nav-item">
<a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
    <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
    <span class="text">Logout</span>
</a>
</li>
</ul>
</div>

</nav>

<main class="main-content">

<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-right text-danger me-2"></i>
                    Confirm Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');

// Desktop toggle
document.getElementById('desktopToggle')?.addEventListener('click',()=>{
    sidebar.classList.toggle('collapsed');
});

// Mobile toggle
document.getElementById('mobileToggle')?.addEventListener('click',()=>{
    sidebar.classList.toggle('show');
});

// Auto-close sidebar on mobile only when actual tab/link clicked
document.querySelectorAll('.sidebar a:not(.dropdown-toggle)').forEach(link=>{
    link.addEventListener('click',()=>{
        if(window.innerWidth<=768){ sidebar.classList.remove('show'); }
    });
});

// Ensure only one dropdown open at a time
const collapses = sidebar.querySelectorAll('.collapse');
collapses.forEach(c=>{
    c.addEventListener('show.bs.collapse',()=>{
        collapses.forEach(other=>{
            if(other!==c) bootstrap.Collapse.getInstance(other)?.hide();
        });
    });
});
</script>
