<?php
require '../config.php';
require_once 'stats.php'; // Include stats functions
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Check if current user is admin (for role-based access)
$userStmt = $pdo->prepare("SELECT role, username FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['admin_id']]);
$currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    die("Access denied. Admins only.");
}
$page = $_GET['page'] ?? 'home';
$username = $currentUser['username'];
// Fetch clubs for dropdowns (used in modals)
$clubs = $pdo->query("SELECT id, name, stadium FROM clubs ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// New: Fetch all players for JS filtering in modals
$all_players = $pdo->query("SELECT id, club_id, name, jersey_number FROM players ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// Handle POST actions
$success = '';
$error = '';
$edit_id = (int)($_GET['edit_id'] ?? 0); // For pre-filling edit modals
$edit_data = []; // To hold data for edit modals
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'add_club' || $action == 'edit_club') {
        $id = $action == 'edit_club' ? (int)$_POST['id'] : 0;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $stadium = trim($_POST['stadium']);
        $logo = $_POST['existing_logo'] ?? NULL;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $logo = uploadFile($_FILES['logo'], '../uploads/clubs/');
            if (!$logo) $error = 'Logo upload failed.';
        }
        if ($name && !$error) {
            if ($action == 'add_club') {
                $stmt = $pdo->prepare("INSERT INTO clubs (name, logo, description, stadium) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $logo, $description, $stadium]);
            } else {
                $stmt = $pdo->prepare("UPDATE clubs SET name = ?, logo = ?, description = ?, stadium = ? WHERE id = ?");
                $stmt->execute([$name, $logo, $description, $stadium, $id]);
            }
            $success = ($action == 'add_club' ? 'Club' : 'Update') . ' successful!';
        } else {
            $error = 'Club name is required.';
        }
    } elseif ($action == 'add_player' || $action == 'edit_player') {
        $id = $action == 'edit_player' ? (int)$_POST['id'] : 0;
        $club_id = (int)$_POST['club_id'];
        $name = trim($_POST['name']);
        $position = $_POST['position'];
        $jersey_number = (int)$_POST['jersey_number'];
        $dob = $_POST['dob'] ?: NULL;
        $nationality = trim($_POST['nationality']);
        $photo = $_POST['existing_photo'] ?? NULL;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photo = uploadFile($_FILES['photo'], '../uploads/players/');
            if (!$photo) $error = 'Photo upload failed.';
        }
        if ($club_id && $name && in_array($position, ['GK', 'DF', 'MF', 'FW']) && $jersey_number && !$error) {
            if ($action == 'add_player') {
                $stmt = $pdo->prepare("INSERT INTO players (club_id, name, photo, position, jersey_number, date_of_birth, nationality) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$club_id, $name, $photo, $position, $jersey_number, $dob, $nationality]);
            } else {
                $stmt = $pdo->prepare("UPDATE players SET club_id = ?, name = ?, photo = ?, position = ?, jersey_number = ?, date_of_birth = ?, nationality = ? WHERE id = ?");
                $stmt->execute([$club_id, $name, $photo, $position, $jersey_number, $dob, $nationality, $id]);
            }
            $success = ($action == 'add_player' ? 'Player' : 'Update') . ' successful!';
        } else {
            $error = 'Required fields missing or invalid.';
        }
    } elseif ($action == 'add_fixture' || $action == 'edit_fixture') {
        $id = $action == 'edit_fixture' ? (int)$_POST['id'] : 0;
        $home_club_id = (int)$_POST['home_club_id'];
        $away_club_id = (int)$_POST['away_club_id'];
        $fixture_date = $_POST['fixture_date'];
        $venue = trim($_POST['venue']);
        if ($home_club_id && $away_club_id && $home_club_id != $away_club_id && $fixture_date && !$error) {
            try {
                if ($action == 'add_fixture') {
                    $stmt = $pdo->prepare("INSERT INTO fixtures (home_club_id, away_club_id, fixture_date, venue, status) VALUES (?, ?, ?, ?, 'Scheduled')");
                    $stmt->execute([$home_club_id, $away_club_id, $fixture_date, $venue]);
                } else {
                    $stmt = $pdo->prepare("UPDATE fixtures SET home_club_id = ?, away_club_id = ?, fixture_date = ?, venue = ? WHERE id = ?");
                    $stmt->execute([$home_club_id, $away_club_id, $fixture_date, $venue, $id]);
                }
                $success = ($action == 'add_fixture' ? 'Fixture' : 'Update') . ' successful!';
            } catch (PDOException $e) {
                $error = 'Fixture already exists or invalid data.';
            }
        } else {
            $error = 'Required fields missing or clubs are the same.';
        }
    } elseif ($action == 'add_news' || $action == 'edit_news') {
        $id = $action == 'edit_news' ? (int)$_POST['id'] : 0;
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $publish_date = $_POST['publish_date'];
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $image = $_POST['existing_image'] ?? NULL;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = uploadFile($_FILES['image'], '../uploads/news/');
            if (!$image) $error = 'Image upload failed.';
        }
        if ($title && $content && $publish_date && !$error) {
            if ($action == 'add_news') {
                $stmt = $pdo->prepare("INSERT INTO news (title, content, image, publish_date, is_published) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $content, $image, $publish_date, $is_published]);
            } else {
                $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image = ?, publish_date = ?, is_published = ? WHERE id = ?");
                $stmt->execute([$title, $content, $image, $publish_date, $is_published, $id]);
            }
            $success = ($action == 'add_news' ? 'News' : 'Update') . ' successful!';
        } else {
            $error = 'Required fields missing.';
        }
    } elseif ($action == 'add_user' || $action == 'edit_user') {
        $id = $action == 'edit_user' ? (int)$_POST['id'] : 0;
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = '';
        if ($action == 'add_user') {
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        } else if (!empty($_POST['password'])) {
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        }
        $role = $_POST['role'];
        if ($username && $email && in_array($role, ['admin', 'moderator']) && !$error) {
            try {
                if ($action == 'add_user') {
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $password, $email, $role]);
                } else {
                    $sql = "UPDATE users SET username = ?, email = ?, role = ?";
                    $params = [$username, $email, $role, $id];
                    if ($password) {
                        $sql .= ", password = ?";
                        array_splice($params, 3, 0, $password);
                    }
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                }
                $success = ($action == 'add_user' ? 'User' : 'Update') . ' successful!';
            } catch (PDOException $e) {
                $error = 'Username or email already exists.';
            }
        } else {
            $error = 'Required fields missing or invalid.';
        }
    } elseif ($action == 'add_tournament_fixture' || $action == 'edit_tournament_fixture') {
        $id = $action == 'edit_tournament_fixture' ? (int)$_POST['id'] : 0;
        $home_club_id = (int)$_POST['home_club_id'];
        $away_club_id = (int)$_POST['away_club_id'];
        $tournament_date = $_POST['tournament_date'];
        $venue = trim($_POST['venue']);
        if ($home_club_id && $away_club_id && $home_club_id != $away_club_id && $tournament_date && !$error) {
            try {
                if ($action == 'add_tournament_fixture') {
                    $stmt = $pdo->prepare("INSERT INTO tournament_fixtures (home_club_id, away_club_id, tournament_date, venue, status) VALUES (?, ?, ?, ?, 'Scheduled')");
                    $stmt->execute([$home_club_id, $away_club_id, $tournament_date, $venue]);
                } else {
                    $stmt = $pdo->prepare("UPDATE tournament_fixtures SET home_club_id = ?, away_club_id = ?, tournament_date = ?, venue = ? WHERE id = ?");
                    $stmt->execute([$home_club_id, $away_club_id, $tournament_date, $venue, $id]);
                }
                $success = ($action == 'add_tournament_fixture' ? 'Tournament Fixture' : 'Update') . ' successful!';
            } catch (PDOException $e) {
                $error = 'Tournament fixture already exists or invalid data.';
            }
        } else {
            $error = 'Required fields missing or clubs are the same.';
        }
    } elseif ($action == 'record_match_result' || $action == 'edit_match_result') {
        $fixture_id = (int)$_POST['fixture_id'];
        $home_score = (int)$_POST['home_score'];
        $away_score = (int)$_POST['away_score'];
        $match_date = date('Y-m-d H:i:s'); // Auto-set to now
        $type = $_POST['type'] ?? 'fixture'; // New: league or tournament
        $table = $type == 'tournament' ? 'tournament_matches' : 'matches';
        $fixture_table = $type == 'tournament' ? 'tournament_fixtures' : 'fixtures';
        if ($home_score >= 0 && $away_score >= 0 && $fixture_id) {
            try {
                if ($action == 'record_match_result') {
                    // Check if match already exists
                    $check = $pdo->prepare("SELECT id FROM $table WHERE fixture_id = ?");
                    $check->execute([$fixture_id]);
                    if ($check->fetch()) {
                        $error = 'Match result already recorded.';
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO $table (fixture_id, home_score, away_score, match_date) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$fixture_id, $home_score, $away_score, $match_date]);
                        // Update fixture status to Completed
                        $update_status = $pdo->prepare("UPDATE $fixture_table SET status = 'Completed' WHERE id = ?");
                        $update_status->execute([$fixture_id]);
                        $success = 'Match result recorded successfully!';
                    }
                } else {
                    $match_id = (int)$_POST['match_id'];
                    $stmt = $pdo->prepare("UPDATE $table SET home_score = ?, away_score = ? WHERE id = ?");
                    $stmt->execute([$home_score, $away_score, $match_id]);
                    $success = 'Match result updated successfully!';
                }
            } catch (PDOException $e) {
                $error = 'Error recording result: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid scores.';
        }
    } elseif ($action == 'add_goal') {
        $match_id = (int)$_POST['match_id'];
        $player_id = (int)$_POST['player_id'];
        $minute = (int)$_POST['minute'];
        $is_penalty = isset($_POST['is_penalty']) ? 1 : 0;
        if ($match_id && $player_id && $minute >= 0 && $minute <= 120) {
            try {
                $stmt = $pdo->prepare("INSERT INTO goals (match_id, player_id, minute, is_penalty) VALUES (?, ?, ?, ?)");
                $stmt->execute([$match_id, $player_id, $minute, $is_penalty]);
                // Handle assist if provided
                if (!empty($_POST['assister_id'])) {
                    $goal_id = $pdo->lastInsertId();
                    $assist_stmt = $pdo->prepare("INSERT INTO assists (goal_id, player_id) VALUES (?, ?)");
                    $assist_stmt->execute([$goal_id, (int)$_POST['assister_id']]);
                }
                $success = 'Goal added successfully!';
            } catch (PDOException $e) {
                $error = 'Error adding goal: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid goal data.';
        }
    } elseif ($action == 'add_tournament_goal') {
        $match_id = (int)$_POST['match_id'];
        $player_id = (int)$_POST['player_id'];
        $minute = (int)$_POST['minute'];
        $is_penalty = isset($_POST['is_penalty']) ? 1 : 0;
        if ($match_id && $player_id && $minute >= 0 && $minute <= 120) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tournament_goals (match_id, player_id, minute, is_penalty) VALUES (?, ?, ?, ?)");
                $stmt->execute([$match_id, $player_id, $minute, $is_penalty]);
                $success = 'Goal added successfully!';
            } catch (PDOException $e) {
                $error = 'Error adding goal: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid goal data.';
        }
    } elseif ($action == 'add_card') {
        $match_id = (int)$_POST['match_id'];
        $player_id = (int)$_POST['player_id'];
        $card_type = $_POST['card_type'];
        $minute = (int)$_POST['minute'];
        if ($match_id && $player_id && in_array($card_type, ['yellow', 'red']) && $minute >= 0 && $minute <= 120) {
            try {
                $stmt = $pdo->prepare("INSERT INTO cards (match_id, player_id, card_type, minute) VALUES (?, ?, ?, ?)");
                $stmt->execute([$match_id, $player_id, $card_type, $minute]);
                $success = 'Card added successfully!';
            } catch (PDOException $e) {
                $error = 'Error adding card: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid card data.';
        }
    } elseif ($action == 'add_tournament_card') {
        $match_id = (int)$_POST['match_id'];
        $player_id = (int)$_POST['player_id'];
        $card_type = $_POST['card_type'];
        $minute = (int)$_POST['minute'];
        if ($match_id && $player_id && in_array($card_type, ['yellow', 'red']) && $minute >= 0 && $minute <= 120) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tournament_cards (match_id, player_id, card_type, minute) VALUES (?, ?, ?, ?)");
                $stmt->execute([$match_id, $player_id, $card_type, $minute]);
                $success = 'Card added successfully!';
            } catch (PDOException $e) {
                $error = 'Error adding card: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid card data.';
        }
    }
}
// Fetch edit data if edit_id set
if ($edit_id > 0) {
    switch ($page) {
        case 'clubs':
            $edit_stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
        case 'players':
            $edit_stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
        case 'fixtures':
            $edit_stmt = $pdo->prepare("SELECT * FROM fixtures WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
        case 'news':
            $edit_stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
        case 'users':
            $edit_stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
        case 'tournaments':
            $edit_stmt = $pdo->prepare("SELECT * FROM tournament_fixtures WHERE id = ?");
            $edit_stmt->execute([$edit_id]);
            $edit_data = $edit_stmt->fetch(PDO::FETCH_ASSOC);
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - 04 Soccer League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            transition: width 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 15px;
            border-radius: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link .icon {
            display: inline-block;
            min-width: 20px;
            margin-right: 8px;
        }
        .sidebar.collapsed .nav-link .text {
            display: none;
        }
        .sidebar.collapsed .nav-link {
            padding: 10px 10px;
            justify-content: center;
        }
        .user-section {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #495057;
            text-align: center;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #6c757d;
            display: inline-block;
            margin-bottom: 10px;
        }
        .sidebar-text {
            color: #fff;
            font-weight: bold;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
        }
        .sidebar.collapsed ~ .main-content {
            margin-left: 60px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-lg .modal-dialog { max-width: 800px; }
        .toggle-btn {
            background: #495057;
            border: none;
            color: white;
            padding: 5px 10px;
            margin: 10px;
        }
        .stats-section {
            max-height: 300px;
            overflow-y: auto;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                opacity: 0;
            }
            .sidebar.collapsed {
                width: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            .table-responsive {
                font-size: 0.875rem;
            }
        }
        @media (min-width: 768px) and (max-width: 992px) {
            .main-content {
                padding: 15px;
            }
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="user-section">
            <div class="text-center text-white">
                <div class="user-avatar"></div>
                <div class="sidebar-text"><?= htmlspecialchars($username) ?></div>
                <small>Admin</small>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between p-3">
            <h4 class="text-white mb-0 sidebar-title">Menu</h4>
            <button class="toggle-btn" id="toggleSidebar" title="Toggle Sidebar">
                <i class="bi bi-list"></i>
            </button>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $page == 'home' ? 'active' : '' ?>" href="?page=home">
                    <span class="icon">🏠</span>
                    <span class="text">Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'league' ? 'active' : '' ?>" href="?page=league">
                    <span class="icon">🏆</span>
                    <span class="text">League Table</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'clubs' ? 'active' : '' ?>" href="?page=clubs">
                    <span class="icon">⚽</span>
                    <span class="text">Clubs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'players' ? 'active' : '' ?>" href="?page=players">
                    <span class="icon">👥</span>
                    <span class="text">Players</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'stats' ? 'active' : '' ?>" href="?page=stats">
                    <span class="icon">📊</span>
                    <span class="text">Match Stats</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'tournaments' ? 'active' : '' ?>" href="?page=tournaments">
                    <span class="icon">🏟️</span>
                    <span class="text">Tournaments</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'fixtures' ? 'active' : '' ?>" href="?page=fixtures">
                    <span class="icon">📅</span>
                    <span class="text">Fixtures & Results</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'news' ? 'active' : '' ?>" href="?page=news">
                    <span class="icon">📰</span>
                    <span class="text">News</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'users' ? 'active' : '' ?>" href="?page=users">
                    <span class="icon">👤</span>
                    <span class="text">Users</span>
                </a>
            </li>
        </ul>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <span class="icon">🚪</span>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    <!-- Main Content -->
    <main class="main-content">
        <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($page == 'home'): ?>
            <div class="row">
                <div class="col-md-12">
                    <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
                    <p class="lead">Overview of the 04 Soccer League Admin Panel.</p>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total Clubs</h5>
                            <?php
                            $total_clubs = $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
                            echo "<h3>$total_clubs</h3>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total Players</h5>
                            <?php
                            $total_players = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
                            echo "<h3>$total_players</h3>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Played Matches</h5>
                            <?php
                            $total_matches = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();
                            echo "<h3>$total_matches</h3>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total News</h5>
                            <?php
                            $total_news = $pdo->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn();
                            echo "<h3>$total_news</h3>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h4>Recent News</h4>
                    <?php
                    $recent_news = $pdo->query("SELECT title, publish_date FROM news WHERE is_published = 1 ORDER BY publish_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($recent_news)): ?>
                        <p class="text-muted">No news yet.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($recent_news as $n): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><?= htmlspecialchars($n['title']) ?></span>
                                    <small><?= $n['publish_date'] ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h4>Recent Matches</h4>
                    <?php
                    $recent_matches = $pdo->query("SELECT m.home_score, m.away_score, f.fixture_date, ch.name as home, ca.name as away FROM matches m JOIN fixtures f ON m.fixture_id = f.id JOIN clubs ch ON f.home_club_id = ch.id JOIN clubs ca ON f.away_club_id = ca.id ORDER BY m.match_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($recent_matches)): ?>
                        <p class="text-muted">No matches yet.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($recent_matches as $m): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><?= htmlspecialchars($m['home']) ?> <?= $m['home_score'] ?> - <?= $m['away_score'] ?> <?= htmlspecialchars($m['away']) ?></span>
                                    <small><?= $m['fixture_date'] ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($page == 'league'): ?>
            <h2>League Table</h2>
            <?php
            $standings_sql = "
    SELECT
        c.id, c.name, c.logo,
        COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS wins,
        COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END) AS draws,
        COUNT(CASE WHEN ((f.home_club_id =             c.id AND m.home_score < m.away_score) OR (f.away_club_id = c.id AND m.away_score < m.home_score)) THEN 1 END) AS losses,
        SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END) AS gf,
        SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END) AS ga,
        (COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) * 3 +
         COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END)) AS points,
        (SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END) -
         SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END)) AS gd
    FROM clubs c
    LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id)
    LEFT JOIN matches m ON m.fixture_id = f.id
    GROUP BY c.id
    ORDER BY points DESC, gd DESC
";
$standings = $pdo->query($standings_sql)->fetchAll(PDO::FETCH_ASSOC);
?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr><th>Pos</th><th>Club</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GF</th><th>GA</th><th>GD</th><th>Pts</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($standings as $index => $s):
                            $played = $s['wins'] + $s['draws'] + $s['losses'];
                        ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if ($s['logo']): ?><img src="../uploads/clubs/<?= htmlspecialchars($s['logo']) ?>" width="20" class="me-2 club-logo" alt="<?= htmlspecialchars($s['name']) ?>"> <?php endif; ?>
                                    <?= htmlspecialchars($s['name']) ?>
                                </td>
                                <td><?= $played ?></td>
                                <td><?= $s['wins'] ?></td>
                                <td><?= $s['draws'] ?></td>
                                <td><?= $s['losses'] ?></td>
                                <td><?= $s['gf'] ?? 0 ?></td>
                                <td><?= $s['ga'] ?? 0 ?></td>
                                <td><?= $s['gd'] ?? 0 ?></td>
                                <td><strong><?= $s['points'] ?? 0 ?></strong></td>
                                <td>
                                    <a href="?page=clubs&edit_id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $s['id'] ?>" data-delete-url="delete_club.php">Del</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($page == 'clubs'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Clubs</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClubModal">Add New Club</button>
            </div>
            <?php
            $clubs_list = $pdo->query("SELECT * FROM clubs ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($clubs_list)): ?>
                <div class="alert alert-info">No clubs added yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>Name</th><th>Logo</th><th>Description</th><th>Stadium</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clubs_list as $club): ?>
                                <tr>
                                    <td><?= htmlspecialchars($club['name']) ?></td>
                                    <td><?php if ($club['logo']): ?><img src="../uploads/clubs/<?= htmlspecialchars($club['logo']) ?>" width="50" alt="<?= htmlspecialchars($club['name']) ?>"> <?php endif; ?></td>
                                    <td><?= htmlspecialchars(substr($club['description'] ?? '', 0, 50)) ?>...</td>
                                    <td><?= htmlspecialchars($club['stadium'] ?? 'TBD') ?></td>
                                    <td>
                                        <a href="?page=clubs&edit_id=<?= $club['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $club['id'] ?>" data-delete-url="delete_club.php">Del</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php elseif ($page == 'players'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Players</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlayerModal">Add New Player</button>
            </div>
            <?php
            $players = getPlayersWithStats($pdo);
            if (empty($players)): ?>
                <div class="alert alert-info">No players added yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>Club</th><th>Player</th><th>#</th><th>Pos</th><th>Goals</th><th>Assists</th><th>YC</th><th>RC</th><th>CS</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player): ?>
                                <tr>
                                    <td><?= htmlspecialchars($player['club_name']) ?></td>
                                    <td><?= htmlspecialchars($player['name']) ?></td>
                                    <td><?= $player['jersey_number'] ?></td>
                                    <td><?= strtoupper($player['position']) ?></td>
                                    <td><?= $player['goals'] ?></td>
                                    <td><?= $player['assists'] ?></td>
                                    <td><?= $player['yellow_cards'] ?></td>
                                    <td><?= $player['red_cards'] ?></td>
                                    <td><?= $player['position'] == 'GK' ? $player['clean_sheets'] : '-' ?></td>
                                    <td>
                                        <?php if ($player['photo']): ?><img src="../uploads/players/<?= htmlspecialchars($player['photo']) ?>" width="25" class="me-1" alt="<?= htmlspecialchars($player['name']) ?>"> <?php endif; ?>
                                        <a href="?page=players&edit_id=<?= $player['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $player['id'] ?>" data-delete-url="delete_player.php">Del</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php elseif ($page == 'stats'): ?>
            <h2>Match Stats</h2>
            <p>Recent matches with detailed stats (goal scorers, cards, clean sheets). Use "Edit Stats" to add details.</p>
            <?php
            $stats_matches = getRecentMatchesWithStats($pdo, 20);
            if (empty($stats_matches)): ?>
                <p class="text-muted">No match stats yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>Date</th><th>Match</th><th>Score</th><th>Goal Scorers</th><th>YC</th><th>RC</th><th>Clean Sheets</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats_matches as $m): ?>
                                <tr>
                                    <td><?= $m['fixture_date'] ?></td>
                                    <td><?= htmlspecialchars($m['home_name']) ?> vs <?= htmlspecialchars($m['away_name']) ?></td>
                                    <td><?= $m['home_score'] ?> - <?= $m['away_score'] ?></td>
                                    <td><?= htmlspecialchars($m['goal_scorers']) ?></td>
                                    <td><?= $m['yellow_cards'] ?></td>
                                    <td><?= $m['red_cards'] ?></td>
                                    <td>Home: <?= $m['home_cs'] ?>, Away: <?= $m['away_cs'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-result-btn" data-match-id="<?= $m['id'] ?>" data-type="fixture">Edit Result</button>
                                        <button class="btn btn-sm btn-primary edit-stats-btn" data-match-id="<?= $m['id'] ?>" data-home-club="<?= $m['home_club_id'] ?>" data-away-club="<?= $m['away_club_id'] ?>" data-type="fixture">Edit Stats</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php elseif ($page == 'tournaments'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Tournaments</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTournamentModal">Add New Tournament Fixture</button>
            </div>
            <ul class="nav nav-tabs" id="tournamentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tournament-upcoming-tab" data-bs-toggle="tab" data-bs-target="#tournament-upcoming" type="button">Upcoming</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tournament-results-tab" data-bs-toggle="tab" data-bs-target="#tournament-results" type="button">Results</button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="tournament-upcoming" role="tabpanel">
                    <?php
                    $t_upcoming = $pdo->query("SELECT tf.*, ch.name as home_name, ca.name as away_name FROM tournament_fixtures tf JOIN clubs ch ON tf.home_club_id = ch.id JOIN clubs ca ON tf.away_club_id = ca.id WHERE tf.status = 'Scheduled' ORDER BY tf.tournament_date ASC")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($t_upcoming)): ?>
                        <p class="text-muted">No upcoming tournament fixtures.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>Date</th><th>Fixture</th><th>Venue</th><th>Actions</th></tr></thead>
                                <tbody>
                                    <?php foreach ($t_upcoming as $tf): ?>
                                        <tr>
                                            <td><?= $tf['tournament_date'] ?></td>
                                            <td><?= htmlspecialchars($tf['home_name']) ?> vs <?= htmlspecialchars($tf['away_name']) ?></td>
                                            <td><?= htmlspecialchars($tf['venue'] ?? 'TBD') ?></td>
                                            <td>
                                                <a href="?page=tournaments&edit_id=<?= $tf['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $tf['id'] ?>" data-delete-url="delete_tournament_fixture.php">Del</button>
                                                <button class="btn btn-sm btn-success record-result-btn" data-fixture-id="<?= $tf['id'] ?>" data-type="tournament" data-home-club="<?= $tf['home_club_id'] ?>" data-away-club="<?= $tf['away_club_id'] ?>">Record Result</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="tournament-results" role="tabpanel">
                    <?php
                    $t_results = $pdo->query("SELECT tm.*, tf.tournament_date, ch.name as home_name, ca.name as away_name, tf.home_club_id, tf.away_club_id FROM tournament_matches tm JOIN tournament_fixtures tf ON tm.fixture_id = tf.id JOIN clubs ch ON tf.home_club_id = ch.id JOIN clubs ca ON tf.away_club_id = ca.id ORDER BY tm.match_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($t_results)): ?>
                        <p class="text-muted">No tournament results yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr><th>Date</th><th>Result</th><th>Score</th><th>Goal Scorers</th><th>YC</th><th>RC</th><th>Clean Sheets</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($t_results as $tm):
                                        $goal_scorers = getTournamentGoalScorers($pdo, $tm['id']);
                                        $yellows = getTournamentYellowCards($pdo, $tm['id']);
                                        $reds = getTournamentRedCards($pdo, $tm['id']);
                                        $home_cs = $tm['away_score'] == 0 ? 'Yes' : 'No';
                                        $away_cs = $tm['home_score'] == 0 ? 'Yes' : 'No';
                                    ?>
                                        <tr>
                                            <td><?= $tm['tournament_date'] ?></td>
                                            <td><?= htmlspecialchars($tm['home_name']) ?> vs <?= htmlspecialchars($tm['away_name']) ?></td>
                                            <td><?= $tm['home_score'] ?> - <?= $tm['away_score'] ?></td>
                                            <td><?= htmlspecialchars($goal_scorers) ?></td>
                                            <td><?= $yellows ?></td>
                                            <td><?= $reds ?></td>
                                            <td>Home: <?= $home_cs ?>, Away: <?= $away_cs ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-result-btn" data-match-id="<?= $tm['id'] ?>" data-type="tournament">Edit Result</button>
                                                <button class="btn btn-sm btn-primary edit-stats-btn" data-match-id="<?= $tm['id'] ?>" data-home-club="<?= $tm['home_club_id'] ?>" data-away-club="<?= $tm['away_club_id'] ?>" data-type="tournament">Edit Stats</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($page == 'fixtures'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Fixtures & Results</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFixtureModal">Add New Fixture</button>
            </div>
            <ul class="nav nav-tabs" id="fixtureTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">Upcoming Fixtures</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button">Recent Results</button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                    <?php
                    $upcoming = $pdo->query("SELECT f.*, ch.name as home_name, ca.name as away_name, ch.id as home_club_id, ca.id as away_club_id FROM fixtures f JOIN clubs ch ON f.home_club_id = ch.id JOIN clubs ca ON f.away_club_id = ca.id WHERE f.status = 'Scheduled' ORDER BY f.fixture_date ASC")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($upcoming)): ?>
                        <p class="text-muted">No upcoming fixtures.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>Date</th><th>Fixture</th><th>Venue</th><th>Actions</th></tr></thead>
                                <tbody>
                                    <?php foreach ($upcoming as $f): ?>
                                        <tr>
                                            <td><?= $f['fixture_date'] ?></td>
                                            <td><?= htmlspecialchars($f['home_name']) ?> vs <?= htmlspecialchars($f['away_name']) ?></td>
                                            <td><?= htmlspecialchars($f['venue'] ?? 'TBD') ?></td>
                                            <td>
                                                <a href="?page=fixtures&edit_id=<?= $f['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $f['id'] ?>" data-delete-url="delete_fixture.php">Del</button>
                                                <button class="btn btn-sm btn-success record-result-btn" data-fixture-id="<?= $f['id'] ?>" data-type="fixture" data-home-club="<?= $f['home_club_id'] ?>" data-away-club="<?= $f['away_club_id'] ?>">Record Result</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="results" role="tabpanel">
                    <?php
                    $results = $pdo->query("SELECT m.*, f.fixture_date, ch.name as home_name, ca.name as away_name, f.home_club_id, f.away_club_id FROM matches m JOIN fixtures f ON m.fixture_id = f.id JOIN clubs ch ON f.home_club_id = ch.id JOIN clubs ca ON f.away_club_id = ca.id ORDER BY m.match_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($results)): ?>
                        <p class="text-muted">No results yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr><th>Date</th><th>Match</th><th>Score</th><th>Goal Scorers</th><th>YC</th><th>RC</th><th>Clean Sheets</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $m):
                                        $goal_scorers = getMatchGoalScorers($pdo, $m['id']);
                                        $yellows = getMatchYellowCards($pdo, $m['id']);
                                        $reds = getMatchRedCards($pdo, $m['id']);
                                        $home_cs = $m['away_score'] == 0 ? 'Yes' : 'No';
                                        $away_cs = $m['home_score'] == 0 ? 'Yes' : 'No';
                                    ?>
                                        <tr>
                                            <td><?= $m['fixture_date'] ?></td>
                                            <td><?= htmlspecialchars($m['home_name']) ?> vs <?= htmlspecialchars($m['away_name']) ?></td>
                                            <td><?= $m['home_score'] ?> - <?= $m['away_score'] ?></td>
                                            <td><?= htmlspecialchars($goal_scorers) ?></td>
                                            <td><?= $yellows ?></td>
                                            <td><?= $reds ?></td>
                                            <td>Home: <?= $home_cs ?>, Away: <?= $away_cs ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-result-btn" data-match-id="<?= $m['id'] ?>" data-type="fixture">Edit Result</button>
                                                <button class="btn btn-sm btn-primary edit-stats-btn" data-match-id="<?= $m['id'] ?>" data-home-club="<?= $m['home_club_id'] ?>" data-away-club="<?= $m['away_club_id'] ?>" data-type="fixture">Edit Stats</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($page == 'news'): ?>
            <div class="d-flex justify-content-between  align-items-center mb-3">
                <h2>News</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNewsModal">Add New News</button>
            </div>
            <?php
            $news = $pdo->query("SELECT * FROM news ORDER BY publish_date DESC")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($news)): ?>
                <div class="alert alert-info">No news added yet.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($news as $n): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <?php if ($n['image']): ?><img src="../uploads/news/<?= htmlspecialchars($n['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($n['title']) ?>"> <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($n['title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars(substr($n['content'], 0, 150)) ?>...</p>
                                    <small>Published: <?= $n['publish_date'] ?> | <?= $n['is_published'] ? 'Published' : 'Draft' ?></small>
                                </div>
                                <div class="card-footer">
                                    <a href="?page=news&edit_id=<?= $n['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $n['id'] ?>" data-delete-url="delete_news.php">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php elseif ($page == 'users'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Users Management</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
            </div>
            <?php
            $users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($users)): ?>
                <div class="alert alert-info">No users yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'secondary' ?>"><?= ucfirst($u['role']) ?></span></td>
                                    <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                                    <td>
                                        <a href="?page=users&edit_id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $u['id'] ?>" data-delete-url="delete_user.php">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">Page not found. <a href="?page=home">Go to Home</a></div>
        <?php endif; ?>
    </main>
    <!-- Modals -->
    <!-- Add Club Modal -->
    <div class="modal fade" id="addClubModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_club">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Club</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Club Name</label>
                            <input type="text" name="name" class="form-control" required value="<?= $edit_data['name'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_logo" value="<?= $edit_data['logo'] ?? '' ?>">
                            <?php if (!empty($edit_data['logo'])): ?>
                                <small class="text-muted">Current: <?= htmlspecialchars($edit_data['logo']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= $edit_data['description'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stadium</label>
                            <input type="text" name="stadium" class="form-control" value="<?= $edit_data['stadium'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Club</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Club Modal (reuses add form with edit_id) -->
    <?php if ($page == 'clubs' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addClubModal'));
            modal.show();
            document.querySelector('#addClubModal form').querySelector('input[name="action"]').value = 'edit_club';
            document.querySelector('#addClubModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Add Player Modal -->
    <div class="modal fade" id="addPlayerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_player">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Player</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Club</label>
                            <select name="club_id" class="form-select" required>
                                <option value="">Select Club</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($edit_data['club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required value="<?= $edit_data['name'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <select name="position" class="form-select" required>
                                <option value="GK" <?= ($edit_data['position'] ?? '') == 'GK' ? 'selected' : '' ?>>Goalkeeper</option>
                                <option value="DF" <?= ($edit_data['position'] ?? '') == 'DF' ? 'selected' : '' ?>>Defender</option>
                                <option value="MF" <?= ($edit_data['position'] ?? '') == 'MF' ? 'selected' : '' ?>>Midfielder</option>
                                <option value="FW" <?= ($edit_data['position'] ?? '') == 'FW' ? 'selected' : '' ?>>Forward</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jersey Number</label>
                            <input type="number" name="jersey_number" class="form-control" min="1" max="99" required value="<?= $edit_data['jersey_number'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?= $edit_data['date_of_birth'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="<?= $edit_data['nationality'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_photo" value="<?= $edit_data['photo'] ?? '' ?>">
                            <?php if (!empty($edit_data['photo'])): ?>
                                <small class="text-muted">Current: <?= htmlspecialchars($edit_data['photo']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Player</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Player Modal -->
    <?php if ($page == 'players' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addPlayerModal'));
            modal.show();
            document.querySelector('#addPlayerModal form').querySelector('input[name="action"]').value = 'edit_player';
            document.querySelector('#addPlayerModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Add Fixture Modal -->
    <div class="modal fade" id="addFixtureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add_fixture">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Fixture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Home Team</label>
                            <select name="home_club_id" id="addFixtureHome" class="form-select" required>
                                <option value="">Select Home</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" data-stadium="<?= htmlspecialchars($c['stadium']) ?>" <?= ($edit_data['home_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Away Team</label>
                            <select name="away_club_id" id="addFixtureAway" class="form-select" required>
                                <option value="">Select Away</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($edit_data['away_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" name="fixture_date" class="form-control" required value="<?= $edit_data['fixture_date'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" id="addFixtureVenue" class="form-control" value="<?= $edit_data['venue'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Fixture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Fixture Modal -->
    <?php if ($page == 'fixtures' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addFixtureModal'));
            modal.show();
            document.querySelector('#addFixtureModal form').querySelector('input[name="action"]').value = 'edit_fixture';
            document.querySelector('#addFixtureModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Add Tournament Fixture Modal -->
    <div class="modal fade" id="addTournamentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add_tournament_fixture">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Tournament Fixture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Home Team</label>
                            <select name="home_club_id" id="addTournamentHome" class="form-select" required>
                                <option value="">Select Home</option>
                                <?php foreach ($clubs  as $c): ?>
                                    <option value="<?= $c['id'] ?>" data-stadium="<?= htmlspecialchars($c['stadium']) ?>" <?= ($edit_data['home_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Away Team</label>
                            <select name="away_club_id" id="addTournamentAway" class="form-select" required>
                                <option value="">Select Away</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($edit_data['away_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" name="tournament_date" class="form-control" required value="<?= $edit_data['tournament_date'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" id="addTournamentVenue" class="form-control" value="<?= $edit_data['venue'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Fixture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Tournament Modal -->
    <?php if ($page == 'tournaments' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addTournamentModal'));
            modal.show();
            document.querySelector('#addTournamentModal form').querySelector('input[name="action"]').value = 'edit_tournament_fixture';
            document.querySelector('#addTournamentModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_news">
                    <div class="modal-header">
                        <h5 class="modal-title">Add News</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required value="<?= $edit_data['title'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="6" required><?= $edit_data['content'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_image" value="<?= $edit_data['image'] ?? '' ?>">
                            <?php if (!empty($edit_data['image'])): ?>
                                <small class="text-muted">Current: <?= htmlspecialchars($edit_data['image']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Publish Date</label>
                            <input type="date" name="publish_date" class="form-control" required value="<?= $edit_data['publish_date'] ?? '' ?>">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_published" class="form-check-input" value="1" <?= ($edit_data['is_published'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label">Published</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add News</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit News Modal -->
    <?php if ($page == 'news' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addNewsModal'));
            modal.show();
            document.querySelector('#addNewsModal form').querySelector('input[name="action"]').value = 'edit_news';
            document.querySelector('#addNewsModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add_user">
                    <div class="modal-header">
                        <h5 class="modal-title">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required value="<?= $edit_data['username'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= $edit_data['email'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" <?= empty($edit_data) ? 'required' : '' ?>>
                            <small class="text-muted">Leave blank to keep current password.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= ($edit_data['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="moderator" <?= ($edit_data['role'] ?? '') == 'moderator' ? 'selected' : '' ?>>Moderator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit User Modal -->
    <?php if ($page == 'users' && $edit_id > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.show();
            document.querySelector('#addUserModal form').querySelector('input[name="action"]').value = 'edit_user';
            document.querySelector('#addUserModal form').insertAdjacentHTML('afterbegin', '<input type="hidden" name="id" value="<?= $edit_id ?>">');
        });
    </script>
    <?php endif; ?>
    <!-- Record/Edit Result Modal -->
    <div class="modal fade" id="recordResultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" id="resultAction" value="record_match_result">
                    <input type="hidden" name="fixture_id" id="resultFixtureId">
                    <input type="hidden" name="match_id" id="resultMatchId">
                    <input type="hidden" name="type" id="resultType">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordTitle">Record Result</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <label>Home Score</label>
                                <input type="number" name="home_score" class="form-control" min="0" required>
                            </div>
                            <div class="col-6">
                                <label>Away Score</label>
                                <input type="number" name="away_score" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Manage Match Stats Modal -->
    <div class="modal fade" id="manageMatchStatsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Match Stats</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="statsTabs">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#goalsTab">Goals</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cardsTab">Cards</button></li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="goalsTab">
                            <form method="POST" id="addGoalForm">
                                <input type="hidden" name="action" id="goalAction" value="add_goal">
                                <input type="hidden" name="match_id" id="statsMatchId">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label>Player</label>
                                        <select name="player_id" class="form-select" required></select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Min</label>
                                        <input type="number" name="minute" class="form-control" min="0" max="120" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Pen?</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="is_penalty" class="form-check-input" value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Assist</label>
                                        <select name="assister_id" class="form-select"><option value="">None</option></select>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">+</button>
                                    </div>
                                </div>
                            </form>
                            <div id="goalsList" class="mt-3"></div>
                        </div>
                        <div class="tab-pane fade" id="cardsTab">
                            <form method="POST" id="addCardForm">
                                <input type="hidden" name="action" id="cardAction" value="add_card">
                                <input type="hidden" name="match_id" id="statsMatchIdCopy">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label>Player</label>
                                        <select name="player_id" class="form-select" required></select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Min</label>
                                        <input type="number" name="minute" class="form-control" min="0" max="120" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Type</label>
                                        <select name="card_type" class="form-select" required>
                                            <option value="yellow">Yellow</option>
                                            <option value="red">Red</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Add Card</button>
                                    </div>
                                </div>
                            </form>
                            <div id="cardsList" class="mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="GET" action="">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                toggleBtn.querySelector('i').classList.toggle('bi-list');
                toggleBtn.querySelector('i').classList.toggle('bi-x');
            });
            // Delete handlers
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const url = btn.dataset.deleteUrl;
                    const id = btn.dataset.id;
                    document.getElementById('deleteForm').action = url;
                    document.getElementById('deleteId').value = id;
                    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
                });
            });
            // Record Result
            document.querySelectorAll('.record-result-btn, .edit-result-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const isEdit = this.classList.contains('edit-result-btn');
                    document.getElementById('resultAction').value = isEdit ? 'edit_match_result' : 'record_match_result';
                    document.getElementById('resultFixtureId').value = this.dataset.fixtureId || '';
                    document.getElementById('resultMatchId').value = this.dataset.matchId || '';
                    document.getElementById('resultType').value = this.dataset.type;
                    document.getElementById('recordTitle').textContent = isEdit ? 'Edit Result' : 'Record Result';
                    if (isEdit) {
                        // Populate scores if available
                        const row = this.closest('tr');
                        const scores = row.querySelector('td:nth-child(3)').textContent.trim().split(' - ');
                        document.querySelector('#recordResultModal input[name="home_score"]').value = scores[0];
                        document.querySelector('#recordResultModal input[name="away_score"]').value = scores[1];
                    }
                    new bootstrap.Modal(document.getElementById('recordResultModal')).show();
                });
            });
            // Edit Stats
            document.querySelectorAll('.edit-stats-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const matchId = this.dataset.matchId;
                    const homeClub = this.dataset.homeClub;
                    const awayClub = this.dataset.awayClub;
                    const type = this.dataset.type;
                    document.getElementById('statsMatchId').value = matchId;
                    document.getElementById('statsMatchIdCopy').value = matchId;
                    document.getElementById('goalAction').value = type === 'tournament' ? 'add_tournament_goal' : 'add_goal';
                    document.getElementById('cardAction').value = type === 'tournament' ? 'add_tournament_card' : 'add_card';
                    // Populate players
                    const players = <?= json_encode($all_players) ?>;
                    const homePlayers = players.filter(p => p.club_id == homeClub);
                    const awayPlayers = players.filter(p => p.club_id == awayClub);
                    const allForAssist = [...homePlayers, ...awayPlayers];
                    const goalPlayerSelect = document.querySelector('#goalsTab select[name="player_id"]');
                    const assistSelect = document.querySelector('#goalsTab select[name="assister_id"]');
                    const cardPlayerSelect = document.querySelector('#cardsTab select[name="player_id"]');
                    [goalPlayerSelect, cardPlayerSelect].forEach(sel => {
                        sel.innerHTML = '<option value="">Select Player</option>';
                        [...homePlayers, ...awayPlayers].forEach(p => {
                            sel.innerHTML += `<option value="${p.id}">${p.name} (#${p.jersey_number})</option>`;
                        });
                    });
                    assistSelect.innerHTML = '<option value="">None</option>';
                    allForAssist.forEach(p => {
                        assistSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                    });
                    // Load existing stats
                    fetch(`get_match_stats.php?match_id=${matchId}&type=${type}`)
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('goalsList').innerHTML = data.goals.map(g => `
                                <div class="alert alert-info small p-2 mb-1">
                                    ${g.minute}' ${g.player_name} ${g.is_penalty ? '(P)' : ''} ${g.assister_name ? ' (A: ' + g.assister_name + ')' : ''}
                                    <button type="button" class="btn-close btn-close-sm float-end" onclick="deleteGoal(${g.id})"></button>
                                </div>
                            `).join('');
                            document.getElementById('cardsList').innerHTML = data.cards.map(c => `
                                <div class="alert ${c.card_type === 'yellow' ? 'alert-warning' : 'alert-danger'} small p-2 mb-1">
                                    ${c.minute}' ${c.player_name} <strong>${c.card_type.toUpperCase()}</strong>
                                    <button type="button" class="btn-close btn-close-sm float-end" onclick="deleteCard(${c.id})"></button>
                                </div>
                            `).join('');
                        });
                    new bootstrap.Modal(document.getElementById('manageMatchStatsModal')).show();
                });
            });
            // Fixture venue auto-fill
            function setupVenueFill(homeId, awayId, venueId) {
                const home = document.getElementById(homeId);
                const venue = document.getElementById(venueId);
                if (home && venue) {
                    home.addEventListener('change', () => {
                        const opt = home.options[home.selectedIndex];
                        venue.value = opt.dataset.stadium || '';
                    });
                    if (home.value) home.dispatchEvent(new Event('change'));
                }
            }
            setupVenueFill('addFixtureHome', 'addFixtureAway', 'addFixtureVenue');
            setupVenueFill('addTournamentHome', 'addTournamentAway', 'addTournamentVenue');
            // Prevent same club selection
            function preventSameClub(homeId, awayId) {
                const home = document.getElementById(homeId);
                const away = document.getElementById(awayId);
                if (home && away) {
                    const update = () => {
                        const homeVal = home.value;
                        Array.from(away.options).forEach(opt => {
                            opt.disabled = opt.value === homeVal;
                        });
                        if (away.value === homeVal) away.value = '';
                    };
                    home.addEventListener('change', update);
                    away.addEventListener('change', () => {
                        if (away.value === home.value) away.value = '';
                    });
                    update();
                }
            }
            preventSameClub('addFixtureHome', 'addFixtureAway');
            preventSameClub('addTournamentHome', 'addTournamentAway');
            // Auto-open edit modals
            <?php if ($edit_id > 0): ?>
                // Handled in PHP above
            <?php endif; ?>
        });
    </script>
</body>
</html>