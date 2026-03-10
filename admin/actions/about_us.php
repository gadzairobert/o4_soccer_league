<?php
// admin/actions/about_us.php
if (!defined('IN_DASHBOARD')) {
    exit('Direct access not allowed');
}
require_once __DIR__ . '/../includes/functions.php';
global $aboutUsDir;
$aboutUsDir = '../uploads/admin/about_us/';

$action = $_POST['action'] ?? '';

if ($action === 'add_member' || $action === 'edit_member') {
    $id          = $action === 'edit_member' ? (int)$_POST['id'] : 0;
    $category    = $_POST['category'] ?? 'about_us';        // ← ONLY ADDED THIS
    $name        = trim($_POST['name'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image       = $_POST['existing_image'] ?? null;
    $sort_order  = (int)($_POST['sort_order'] ?? 0);

    // Image upload - EXACTLY YOUR ORIGINAL CODE
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploaded = uploadFile($_FILES['image'], $aboutUsDir);
        if ($uploaded) {
            if ($image && file_exists($aboutUsDir . $image)) {
                @unlink($aboutUsDir . $image);
            }
            $image = $uploaded;
        } else {
            $_SESSION['error'] = 'Invalid image. Use JPG/PNG, max 5MB.';
        }
    }

    if ($name && $title && $description && !isset($_SESSION['error'])) {
        try {
            if ($action === 'add_member') {
                $stmt = $pdo->prepare("
                    INSERT INTO about_us 
                    (category, name, title, description, image, sort_order, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
                ");
                $stmt->execute([$category, $name, $title, $description, $image, $sort_order]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE about_us 
                    SET category = ?, name = ?, title = ?, description = ?, image = ?, sort_order = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$category, $name, $title, $description, $image, $sort_order, $id]);
            }
            $_SESSION['success'] = 'Team member saved successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Name, title, and description are required.';
    }

} elseif ($action === 'delete_member') {
    $id = (int)$_POST['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT image FROM about_us WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['image'] && file_exists($aboutUsDir . $row['image'])) {
            @unlink($aboutUsDir . $row['image']);
        }
        $stmt = $pdo->prepare("DELETE FROM about_us WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Team member deleted.';
    }
}