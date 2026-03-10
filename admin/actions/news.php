<?php
// admin/actions/news.php

if (!defined('IN_DASHBOARD')) {
    exit('Direct access not allowed');
}

require_once __DIR__ . '/../includes/functions.php';
global $newsDir;

$action = $_POST['action'] ?? '';

if ($action === 'add_news' || $action === 'edit_news') {
    $id = $action === 'edit_news' ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $image = $_POST['existing_image'] ?? null;
    $is_published = isset($_POST['is_published']) && $_POST['is_published'] == '1' ? 1 : 0;
    $publish_date = $_POST['publish_date'] ?? date('Y-m-d H:i:s');

    // Reset error before processing
    unset($_SESSION['error']);

    // === IMAGE UPLOAD ===
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadFile($_FILES['image'], $newsDir);
        if ($uploaded) {
            $image = $uploaded;
            // If new image uploaded and editing, delete old one
            if ($action === 'edit_news' && !empty($_POST['existing_image']) && $image !== $_POST['existing_image']) {
                deleteFile($newsDir . $_POST['existing_image']);
            }
        } else {
            $_SESSION['error'] = 'Invalid image upload (JPG/PNG only, max 5MB).';
        }
    }

    // Only validate title and content — image error should not block if title/content are filled
    if (!$title || !$content) {
        $_SESSION['error'] = 'Title and content are required.';
    }

    // Proceed only if no critical errors
    if ($title && $content && !isset($_SESSION['error'])) {
        try {
            if ($action === 'add_news') {
                $stmt = $pdo->prepare("
                    INSERT INTO news 
                    (title, content, image, publish_date, is_published, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$title, $content, $image, $publish_date, $is_published]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE news 
                    SET title = ?, content = ?, image = ?, publish_date = ?, is_published = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $content, $image, $publish_date, $is_published, $id]);
            }
            $_SESSION['success'] = 'News saved successfully.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }

} elseif ($action === 'delete_news') {
    // ... (delete logic unchanged, good)
    $id = (int)$_POST['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['image']) {
            deleteFile($newsDir . $row['image']);
        }

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'News deleted successfully.';
    } else {
        $_SESSION['error'] = 'Invalid news ID.';
    }

} elseif ($action === 'toggle_publish') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("UPDATE news SET is_published = NOT is_published WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Publish status updated.';
}