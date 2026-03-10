<?php
// admin/actions/players.php
if (!defined('IN_DASHBOARD')) {
    die('Direct access not allowed');
}

$action = $_POST['action'] ?? '';

if ($action === 'add_player' || $action === 'edit_player') {

    $id            = $action === 'edit_player' ? (int)($_POST['id'] ?? 0) : 0;
    $name          = trim($_POST['name'] ?? '');
    $club_id       = (int)($_POST['club_id'] ?? 0);
    $id_number     = trim($_POST['id_number'] ?? '') ?: null;
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    
    $raw_position  = trim($_POST['position'] ?? '');
    $position      = $raw_position === '' ? '' : strtoupper($raw_position);
    
    $jersey_number = !empty($_POST['jersey_number']) ? (int)$_POST['jersey_number'] : null;
    $photo         = $_POST['existing_photo'] ?? null;

    // New: Status
    $status = in_array($_POST['status'] ?? '', ['Active', 'Inactive', 'Suspended', 'Transferred', 'Banned'])
        ? $_POST['status']
        : 'Active';

    // Photo upload handling
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed) && $file['size'] < 5_000_000) {
            $filename = 'player_' . uniqid() . '.' . $ext;
            $path = '../uploads/players/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $photo = $filename;

                if ($action === 'edit_player' && !empty($_POST['existing_photo']) && $_POST['existing_photo'] !== $filename) {
                    @unlink('../uploads/players/' . $_POST['existing_photo']);
                }
            }
        }
    }

    if (!$name || !$club_id) {
        $_SESSION['error'] = 'Name and club are required.';
        header('Location: ?page=players');
        exit;
    }

    try {
        // === DUPLICATE CHECK: NAME ONLY (case-insensitive) ===
        $dupSql = "SELECT id FROM players WHERE LOWER(name) = LOWER(?)";
        $dupParams = [$name];

        if ($action === 'edit_player' && $id > 0) {
            $dupSql .= " AND id != ?";
            $dupParams[] = $id;
        }

        $stmt = $pdo->prepare($dupSql);
        $stmt->execute($dupParams);

        if ($stmt->fetch()) {
            $_SESSION['error'] = 'A player with the name "' . htmlspecialchars($name) . '" already exists in the system.';
            header('Location: ?page=players');
            exit;
        }

        // === INSERT OR UPDATE ===
        if ($action === 'add_player') {
            $stmt = $pdo->prepare("INSERT INTO players 
                (name, club_id, id_number, date_of_birth, position, jersey_number, photo, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $club_id, $id_number, $date_of_birth, $position, $jersey_number, $photo, $status]);
            $_SESSION['success'] = 'Player added successfully.';
        } else {
            $stmt = $pdo->prepare("UPDATE players 
                SET name = ?, club_id = ?, id_number = ?, date_of_birth = ?, 
                    position = ?, jersey_number = ?, photo = ?, status = ? 
                WHERE id = ?");
            $stmt->execute([$name, $club_id, $id_number, $date_of_birth, $position, $jersey_number, $photo, $status, $id]);
            $_SESSION['success'] = 'Player updated successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

} elseif ($action === 'delete_player') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT photo FROM players WHERE id = ?");
            $stmt->execute([$id]);
            $player = $stmt->fetch();

            $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
            $stmt->execute([$id]);

            if ($player && $player['photo']) {
                @unlink('../uploads/players/' . $player['photo']);
            }

            $_SESSION['success'] = 'Player deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete player.';
        }
    } else {
        $_SESSION['error'] = 'Invalid player ID.';
    }
}

header('Location: ?page=players');
exit;