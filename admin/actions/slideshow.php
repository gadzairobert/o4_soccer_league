<?php
// admin/actions/slideshow.php

if (!defined('IN_DASHBOARD')) {
    die('Direct access not allowed');
}

$action = $_POST['action'] ?? '';

if ($action === 'add_image' || $action === 'edit_image') {

    $id          = $action === 'edit_image' ? (int)($_POST['id'] ?? 0) : 0;
    $caption     = trim($_POST['caption'] ?? '');
    $alt_text    = trim($_POST['alt_text'] ?? '');
    $sort_order  = (int)($_POST['sort_order'] ?? 0);
    $is_active   = !empty($_POST['is_active']) ? 1 : 0;
    $filename    = $_POST['existing_filename'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed) && $file['size'] < 8_000_000) {
            $filename_new = 'img_' . uniqid() . '.' . $ext;
            $path = '../uploads/admin/slideshow/' . $filename_new;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $filename = $filename_new;
                if ($action === 'edit_image' && !empty($_POST['existing_filename']) && $_POST['existing_filename'] !== $filename_new) {
                    @unlink('../uploads/admin/slideshow/' . $_POST['existing_filename']);
                }
            } else {
                $_SESSION['error'] = 'Failed to save file.';
            }
        } else {
            $_SESSION['error'] = 'Invalid file type or size.';
        }
    }

    if (!$filename && $action === 'add_image') {
        $_SESSION['error'] = 'Please select an image.';
    }

    if (!isset($_SESSION['error'])) {
        try {
            if ($action === 'add_image') {
                $pdo->prepare("INSERT INTO slideshow_images (filename,caption,alt_text,sort_order,is_active,created_at) VALUES (?,?,?,?,?,NOW())")
                    ->execute([$filename, $caption, $alt_text, $sort_order, $is_active]);
                $_SESSION['success'] = 'Image added!';
            } else {
                $pdo->prepare("UPDATE slideshow_images SET filename=?,caption=?,alt_text=?,sort_order=?,is_active=? WHERE id=?")
                    ->execute([$filename, $caption, $alt_text, $sort_order, $is_active, $id]);
                $_SESSION['success'] = 'Image updated!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'DB Error: ' . $e->getMessage();
        }
    }

} elseif ($action === 'delete_image') {
    $id = (int)$_POST['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT filename FROM slideshow_images WHERE id=?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        $pdo->prepare("DELETE FROM slideshow_images WHERE id=?")->execute([$id]);
        if ($img && $img['filename']) {
            @unlink('../uploads/admin/slideshow/' . $img['filename']);
        }
        $_SESSION['success'] = 'Image deleted.';
    }

} elseif ($action === 'toggle_active') {
    $id = (int)$_POST['id'];
    $pdo->prepare("UPDATE slideshow_images SET is_active = NOT is_active WHERE id=?")->execute([$id]);

} elseif ($action === 'save_order') {
    foreach ($_POST['order'] ?? [] as $order => $id) {
        $pdo->prepare("UPDATE slideshow_images SET sort_order=? WHERE id=?")->execute([(int)$order, (int)$id]);
    }
    $_SESSION['success'] = 'Order saved.';
}