<?php
// admin/pages/nav_bar_preview.php - Shows how navbar will look on frontend
define('IN_DASHBOARD', true);

// Same navbar fetching logic as frontend
$navStmt = $pdo->prepare("
    SELECT * FROM nav_items 
    WHERE parent_id = 0 AND is_active = 1 
    ORDER BY sort_order ASC, id ASC
");
$navStmt->execute();
$main_nav_items = $navStmt->fetchAll(PDO::FETCH_ASSOC);

$dropdownStmt = $pdo->prepare("
    SELECT * FROM nav_items 
    WHERE parent_id IN (SELECT id FROM nav_items WHERE parent_id = 0 AND is_active = 1) 
    AND is_active = 1 
    ORDER BY parent_id ASC, sort_order ASC, id ASC
");
$dropdownStmt->execute();
$dropdown_items = $dropdownStmt->fetchAll(PDO::FETCH_ASSOC);

$nav_dropdowns = [];
foreach ($dropdown_items as $item) {
    $nav_dropdowns[$item['parent_id']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Preview - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .navbar {
            background: linear-gradient(135deg, #1a1a1a, #2c2c2c) !important;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            padding: 0.5rem 1rem;
            min-height: 60px;
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-link, .dropdown-item {
            color: #ddd !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .dropdown-item:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.15) !important;
            transform: translateX(4px);
        }
        .preview-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <a href="?page=nav_bar" class="btn btn-light back-btn shadow">
        <i class="bi bi-arrow-left me-2"></i>Back to Admin
    </a>

    <div class="preview-container">
        <div class="preview-header">
            <h1 class="display-4 fw-bold mb-3">Navbar Preview</h1>
            <p class="lead mb-0 opacity-90">This is how your navigation menu will appear on the frontend</p>
        </div>

        <div class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="bi bi-shield-check me-2"></i>Ward 24 Community League
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#previewNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="previewNavbar">
                    <ul class="navbar-nav ms-auto">
                        <?php foreach ($main_nav_items as $item): ?>
                            <?php 
                            $has_dropdown = isset($nav_dropdowns[$item['id']]) && !empty($nav_dropdowns[$item['id']]);
                            $is_dropdown_parent = $has_dropdown || ($item['link'] === null);
                            ?>
                            
                            <li class="nav-item <?= $is_dropdown_parent ? 'dropdown' : '' ?>">
                                <a class="nav-link <?= $is_dropdown_parent ? 'dropdown-toggle' : '' ?>" 
                                   href="<?= $is_dropdown_parent ? '#' : '#' ?>" 
                                   <?= $is_dropdown_parent ? 'role="button" data-bs-toggle="dropdown"' : '' ?>>
                                    <?= htmlspecialchars($item['name']) ?>
                                    <?php if ($item['icon_class']): ?>
                                        <i class="<?= htmlspecialchars($item['icon_class']) ?> ms-1"></i>
                                    <?php endif; ?>
                                </a>
                                
                                <?php if ($has_dropdown): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($nav_dropdowns[$item['id']] as $sub_item): ?>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <?= htmlspecialchars($sub_item['name']) ?>
                                                    <?php if ($sub_item['icon_class']): ?>
                                                        <i class="<?= htmlspecialchars($sub_item['icon_class']) ?> ms-2 opacity-75"></i>
                                                    <?php endif; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <div class="card border-0 bg-white shadow-lg p-4">
                <h5 class="text-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>Ready to Go Live!
                </h5>
                <p class="text-muted mb-0">Your navigation menu is now fully configured and will appear exactly like this on the frontend website.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>