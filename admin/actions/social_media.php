<?php
// admin/actions/social_media.php
if (!defined('IN_DASHBOARD')) die('Access denied');

$action = $_POST['action'] ?? '';

if ($action === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $platform = trim($_POST['platform_name']);
    $icon = trim($_POST['icon_class']);
    $url = trim($_POST['url']);
    $header = isset($_POST['display_in_header']) ? 1 : 0;
    $footer = isset($_POST['display_in_footer']) ? 1 : 0;
    $contact = isset($_POST['display_in_contact']) ? 1 : 0;
    $active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($platform) || empty($icon) || empty($url)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = "Please enter a valid URL.";
    } else {
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE social_links SET platform_name=?, icon_class=?, url=?, display_in_header=?, display_in_footer=?, display_in_contact=?, is_active=? WHERE id=?");
                $stmt->execute([$platform, $icon, $url, $header, $footer, $contact, $active, $id]);
                $_SESSION['success'] = "Link updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO social_links (platform_name, icon_class, url, display_in_header, display_in_footer, display_in_contact, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT IFNULL(MAX(sort_order),0)+1 FROM social_links t))");
                $stmt->execute([$platform, $icon, $url, $header, $footer, $contact, $active]);
                $_SESSION['success'] = "Social link added!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Database error.";
        }
    }

} elseif ($action === 'delete') {
    $id = (int)$_POST['id'];
    $pdo->prepare("DELETE FROM social_links WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = "Link deleted.";
}

header("Location: ?page=social_media");
exit;