<?php
session_start();
require_once '../config.php';

// Block already logged-in members
if (isset($_SESSION['member_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = "Please enter your username/email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password_hash, full_name FROM members WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['member_id'] = $user['id'];
            $_SESSION['member_name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid username, email, or password.";
        }
    }
}

// Dynamic logo - same as admin
$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'login_logo' AND is_active = 1 LIMIT 1");
$stmt->execute();
$loginLogo = $stmt->fetchColumn();
$logoSrc = $loginLogo ? '../uploads/admin/logos/' . $loginLogo : '../uploads/logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Login – 04 Soccer League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .login-card { 
            max-width: 420px; 
            width: 100%; 
            border-radius: 1.6rem; 
            overflow: hidden; 
            box-shadow: 0 25px 60px rgba(0,0,0,0.5); 
            background: white; 
        }
        .login-header { 
            background: #ffffff; 
            padding: 2.2rem 2rem 1.8rem; 
            text-align: center; 
            border-bottom: 6px solid #0d6efd; 
        }
        .login-header img { 
            height: 140px; 
            width: auto; 
            object-fit: contain; 
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.3)); 
            margin-bottom: 0.8rem; 
            transition: transform 0.4s ease; 
        }
        .login-header img:hover { 
            transform: scale(1.08); 
        }
        .login-header h3 { 
            color: #1e293b; 
            font-weight: 800; 
            margin: 0; 
            font-size: 1.75rem; 
        }
        .login-header small { 
            color: #64748b; 
            font-size: 0.98rem; 
            font-weight: 500; 
        }
        .login-body { 
            padding: 2.2rem 2.5rem 2.5rem; 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); 
        }
        .form-control { 
            height: 54px; 
            border-radius: 1rem; 
            padding: 0 1.3rem; 
            font-size: 1.02rem; 
            border: 2px solid #e2e8f0; 
        }
        .form-control:focus { 
            border-color: #0d6efd; 
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.18); 
        }
        .btn-login { 
            height: 54px; 
            border-radius: 1rem; 
            font-weight: 700; 
            font-size: 1.12rem; 
            background: linear-gradient(135deg, #0d6efd, #2563eb); 
            border: none; 
        }
        .btn-login:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.4); 
        }
        .alert { 
            border-radius: 1rem; 
            padding: 0.9rem 1.2rem; 
            font-size: 0.95rem; 
        }
        .footer-text { 
            color: #475569; 
            font-size: 0.88rem; 
            font-weight: 500; 
            margin-top: 1.5rem; 
        }
        .register-link {
            color: #0d6efd;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="<?= htmlspecialchars($logoSrc) ?>" alt="04 Soccer League Logo">
        <h3>Member Portal</h3>
        <small>League Membership Login</small>
    </div>

    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Username or Email</label>
                <input type="text" name="login" class="form-control" required autofocus value="<?= htmlspecialchars($login ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-dark">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-login text-white">
                Login to My Dashboard
            </button>
        </form>

        <div class="text-center footer-text">
            Don't have an account yet?<br>
            <a href="register.php" class="register-link">Register as a Member</a>
            <hr class="my-4">
            © <?= date('Y') ?> <strong>04 Soccer League</strong> • All Rights Reserved
        </div>
    </div>
</div>

<script>
if (history.pushState) {
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>