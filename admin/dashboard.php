<?php
require '../config.php';
require 'includes/auth.php';
require_once 'stats.php';

$stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$username = $user['username'];
$_SESSION['is_admin'] = ($user['role'] === 'admin');
$page = $_GET['page'] ?? 'home';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    define('IN_DASHBOARD', true);
    $actionFile = "actions/{$page}.php";
    if (file_exists($actionFile)) require $actionFile;
    header("Location: ?page=" . urlencode($page));
    exit;
}

require 'includes/header.php';
?>

<!-- AGGRESSIVE BACK BUTTON & IDLE PROTECTION -->
<script>
// BLOCK BACK BUTTON COMPLETELY
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};

// Keep session alive
setInterval(() => {
    fetch('keep_alive.php?' + Date.now(), { cache: 'no-store' });
}, 15000);

// 30-minute idle logout
let idleTimeout = setTimeout(() => {
    alert('Session expired due to inactivity.');
    location.href = 'logout.php?timeout=1';
}, 30 * 60 * 1000);

document.onmousemove = document.onkeydown = document.onscroll = document.onclick = () => {
    clearTimeout(idleTimeout);
    idleTimeout = setTimeout(() => {
        alert('Session expired.');
        location.href = 'logout.php?timeout=1';
    }, 30 * 60 * 1000);
};
</script>

<?php if ($page === 'home'): ?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome back, <?= htmlspecialchars($username) ?>!</h2>
    <p class="lead text-muted">Here's your league overview</p>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Total Clubs</h5>
                    <h2 class="display-5 fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn() ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Total Players</h5>
                    <h2 class="display-5 fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn() ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">Matches Played</h5>
                    <h2 class="display-5 fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn() ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">Published News</h5>
                    <h2 class="display-5 fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn() ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <h4>Recent News</h4>
            <?php
            $recent_news = $pdo->query("SELECT title, publish_date FROM news WHERE is_published = 1 ORDER BY publish_date DESC LIMIT 5")->fetchAll();
            if (empty($recent_news)): ?>
                <p class="text-muted">No published news yet.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($recent_news as $n): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($n['title']) ?>
                            <small class="text-muted"><?= date('M j', strtotime($n['publish_date'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="col-lg-6 mb-4">
            <h4>Recent Matches</h4>
            <?php
            $recent_matches = $pdo->query("
                SELECT m.home_score, m.away_score, f.fixture_date, ch.name AS home, ca.name AS away
                FROM matches m
                JOIN fixtures f ON m.fixture_id = f.id
                JOIN clubs ch ON f.home_club_id = ch.id
                JOIN clubs ca ON f.away_club_id = ca.id
                ORDER BY m.match_date DESC LIMIT 5
            ")->fetchAll();
            if (empty($recent_matches)): ?>
                <p class="text-muted">No matches played yet.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($recent_matches as $m): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <?= htmlspecialchars($m['home']) ?> 
                                <strong><?= $m['home_score'] ?? '?' ?> - <?= $m['away_score'] ?? '?' ?></strong> 
                                <?= htmlspecialchars($m['away']) ?>
                            </span>
                            <small class="text-muted"><?= date('M j', strtotime($m['fixture_date'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php elseif ($page === 'users'): ?>
    <?php require 'pages/users.php'; ?>
<?php else: ?>
    <?php
    $file = "pages/{$page}.php";
    if (file_exists($file)) {
        require $file;
    } else {
        echo '<div class="container-fluid py-5 text-center"><div class="bg-light rounded-4 p-5 shadow">
                <h2>Page Not Found</h2>
                <p>The page "<strong>' . htmlspecialchars($page) . '</strong>" does not exist.</p>
                <a href="?page=home" class="btn btn-primary">Back to Dashboard</a>
              </div></div>';
    }
    ?>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>