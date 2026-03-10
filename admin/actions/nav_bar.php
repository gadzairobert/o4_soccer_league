<?php
// admin/actions/nav_bar.php
if (!defined('IN_DASHBOARD')) die('Access denied');

$action = $_POST['action'] ?? '';

try {
    if ($action === 'save_league_name') {
        $name = trim($_POST['league_name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = "League name cannot be empty.";
        } else {
            $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('league_name', ?) ON DUPLICATE KEY UPDATE value = ?")
                ->execute([$name, $name]);
            $_SESSION['success'] = "League name updated!";
        }

    } elseif ($action === 'save') {
        // === SAVE / UPDATE MENU ITEM ===
        $id          = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name        = trim($_POST['name'] ?? '');
        $link        = trim($_POST['link'] ?? '');
        $parent_id   = (int)($_POST['parent_id'] ?? 0);
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $icon_class  = trim($_POST['icon_class'] ?? '');
        $target_blank = !empty($_POST['target_blank']) ? 1 : 0;
        $is_active   = !empty($_POST['is_active']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['error'] = "Menu name is required.";
        } elseif ($parent_id === 0 && empty($link) && $id === null) {
            // Allow empty link only for new dropdown parents
            $link = '';
        } elseif ($parent_id === 0 && empty($link)) {
            $_SESSION['error'] = "Main menu items must have a URL unless it's a dropdown parent.";
        } else {
            if ($id) {
                // Update existing
                $sql = "UPDATE nav_items SET 
                            name = ?, link = ?, parent_id = ?, sort_order = ?, 
                            icon_class = ?, target_blank = ?, is_active = ?
                        WHERE id = ?";
                $pdo->prepare($sql)->execute([
                    $name, $link, $parent_id, $sort_order,
                    $icon_class, $target_blank, $is_active, $id
                ]);
                $_SESSION['success'] = "Menu item updated successfully!";
            } else {
                // Insert new
                $sql = "INSERT INTO nav_items 
                            (name, link, parent_id, sort_order, icon_class, target_blank, is_active)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $pdo->prepare($sql)->execute([
                    $name, $link, $parent_id, $sort_order,
                    $icon_class, $target_blank, $is_active
                ]);
                $_SESSION['success'] = "Menu item added successfully!";
            }
        }

    } elseif ($action === 'toggle') {
        $id = (int)$_POST['id'];
        $pdo->prepare("UPDATE nav_items SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
        $_SESSION['success'] = "Status toggled.";

    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM nav_items WHERE id = ? OR parent_id = ?")->execute([$id, $id]);
        $_SESSION['success'] = "Menu item and its sub-items deleted.";
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: ?page=nav_bar");
exit;