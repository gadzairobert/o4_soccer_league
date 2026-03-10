<?php
// admin/actions/contributions.php
if (!defined('IN_DASHBOARD')) {
    die('Direct access not allowed');
}

$action = $_POST['action'] ?? '';

// =============================================================================
// PLAYER / MANAGEMENT CASH CONTRIBUTIONS
// =============================================================================
if ($action === 'add_player_mgmt_contrib' || $action === 'edit_player_mgmt_contrib') {

    $id               = $action === 'edit_player_mgmt_contrib' ? (int)($_POST['id'] ?? 0) : 0;
    $contributor_type = $_POST['contributor_type'] ?? ''; // 'player' or 'management'
    $club_id          = (int)($_POST['club_id'] ?? 0);
    $contributor_id   = (int)($_POST['contributor_id'] ?? 0);
    $amount           = floatval($_POST['amount'] ?? 0);
    $custom_date      = $_POST['custom_date'] ?? date('Y-m-d');
    $purpose          = trim($_POST['purpose'] ?? '');
    $description      = trim($_POST['description'] ?? '');

    if (!in_array($contributor_type, ['player', 'management']) || !$club_id || !$contributor_id || $amount <= 0) {
        $_SESSION['error'] = 'Please select contributor type, club, person and enter a valid amount.';
        header('Location: ?page=contributions&tab=player_contributions');
        exit;
    }

    if (empty($custom_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $custom_date)) {
        $custom_date = date('Y-m-d');
    }
    $recorded_at = $custom_date . ' ' . date('H:i:s');

    try {
        // ----------------------------------------------------------------------
        // 1. Find or create dummy member (with valid unique email)
        // ----------------------------------------------------------------------
        $dummy_username = ($contributor_type === 'player' ? 'player_' : 'mgmt_') . $contributor_id . '_' . time();
        $dummy_email    = 'dummy.' . $dummy_username . '@noemail.local'; // unique & valid format

        // Get display name
        if ($contributor_type === 'player') {
            $stmt = $pdo->prepare("
                SELECT p.name AS person_name, c.name AS club_name
                FROM players p
                JOIN clubs c ON p.club_id = c.id
                WHERE p.id = ?
            ");
            $stmt->execute([$contributor_id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            $display_name = "Player: " . ($info['person_name'] ?? 'Unknown') . " - " . ($info['club_name'] ?? 'Unknown Club');
        } else {
            $stmt = $pdo->prepare("
                SELECT m.full_name AS person_name, m.role, c.name AS club_name
                FROM management m
                JOIN clubs c ON m.club_id = c.id
                WHERE m.id = ?
            ");
            $stmt->execute([$contributor_id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            $display_name = "Management: " . ($info['person_name'] ?? 'Unknown') . 
                           ($info['role'] ? " ({$info['role']})" : '') . 
                           " - " . ($info['club_name'] ?? 'Unknown Club');
        }

        // Check if dummy already exists
        $stmt = $pdo->prepare("SELECT id FROM members WHERE username = ? LIMIT 1");
        $stmt->execute([$dummy_username]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $member_id = $existing['id'];
        } else {
            // Create dummy member with all required fields
            $stmt = $pdo->prepare("
                INSERT INTO members 
                (full_name, username, email, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$display_name, $dummy_username, $dummy_email]);
            $member_id = $pdo->lastInsertId();
        }

        // ----------------------------------------------------------------------
        // 2. Use a fixed/default contribution type for player/management
        //    CHANGE 5 → your actual ID from contribution_types table
        // ----------------------------------------------------------------------
        $default_type_id = 5; // ←←← IMPORTANT: REPLACE WITH REAL ID OF "Cash – Player/Management" or any monetary type

        // ----------------------------------------------------------------------
        // 3. Insert / Update contribution
        // ----------------------------------------------------------------------
        if ($action === 'add_player_mgmt_contrib') {
            $stmt = $pdo->prepare("
                INSERT INTO contributions 
                (member_id, type_id, contributor_type, contributor_id, 
                 amount, description, purpose, recorded_by, recorded_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $member_id,
                $default_type_id,
                $contributor_type,
                $contributor_id,
                $amount,
                $description,
                $purpose,
                $_SESSION['admin_id'],
                $recorded_at
            ]);
            $_SESSION['success'] = 'Player/Management contribution added successfully.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE contributions 
                SET member_id = ?,
                    type_id = ?,
                    contributor_type = ?, 
                    contributor_id = ?, 
                    amount = ?, 
                    description = ?, 
                    purpose = ?, 
                    recorded_at = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $member_id,
                $default_type_id,
                $contributor_type,
                $contributor_id,
                $amount,
                $description,
                $purpose,
                $recorded_at,
                $id
            ]);
            $_SESSION['success'] = 'Player/Management contribution updated successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: ?page=contributions&tab=player_contributions');
    exit;
}

// =============================================================================
// MEMBER CONTRIBUTIONS (original – unchanged)
// =============================================================================
if ($action === 'add_contribution' || $action === 'edit_contribution') {

    $id          = $action === 'edit_contribution' ? (int)($_POST['id'] ?? 0) : 0;
    $member_id   = (int)($_POST['member_id'] ?? 0);
    $type_id     = (int)($_POST['type_id'] ?? 0);
    $custom_date = $_POST['custom_date'] ?? date('Y-m-d');
    if (empty($custom_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $custom_date)) {
        $custom_date = date('Y-m-d');
    }
    $recorded_at = $custom_date . ' ' . date('H:i:s');

    $amount      = null;
    $quantity    = null;

    $stmt = $pdo->prepare("SELECT is_monetary FROM contribution_types WHERE id = ?");
    $stmt->execute([$type_id]);
    $type = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($type && $type['is_monetary']) {
        $amount = floatval($_POST['amount'] ?? 0);
    } else {
        $quantity = (int)($_POST['quantity'] ?? 1);
    }

    $description = trim($_POST['description'] ?? '');
    $purpose     = trim($_POST['purpose'] ?? '');

    if (!$member_id || !$type_id) {
        $_SESSION['error'] = 'Member and contribution type are required.';
        header('Location: ?page=contributions&tab=contributions');
        exit;
    }

    try {
        if ($action === 'add_contribution') {
            $stmt = $pdo->prepare("
                INSERT INTO contributions 
                (member_id, type_id, amount, quantity, description, purpose, recorded_by, recorded_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$member_id, $type_id, $amount, $quantity, $description, $purpose, $_SESSION['admin_id'], $recorded_at]);
            $_SESSION['success'] = 'Contribution added successfully.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE contributions 
                SET member_id = ?, type_id = ?, amount = ?, quantity = ?, description = ?, purpose = ?, recorded_at = ?
                WHERE id = ?
            ");
            $stmt->execute([$member_id, $type_id, $amount, $quantity, $description, $purpose, $recorded_at, $id]);
            $_SESSION['success'] = 'Contribution updated successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: ?page=contributions&tab=contributions');
    exit;
}

elseif ($action === 'delete_contribution') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM contributions WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Contribution deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete contribution.';
        }
    }
    header('Location: ?page=contributions&tab=' . ($_POST['tab'] ?? 'contributions'));
    exit;
}

// =============================================================================
// EXPENSES (unchanged)
// =============================================================================
elseif (in_array($action, ['add_expense', 'edit_expense', 'delete_expense'])) {

    $id = in_array($action, ['edit_expense', 'delete_expense']) ? (int)($_POST['id'] ?? 0) : 0;

    $custom_date = $_POST['custom_date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $custom_date)) {
        $custom_date = date('Y-m-d');
    }
    $recorded_at = $custom_date . ' ' . date('H:i:s');

    $amount = floatval($_POST['amount'] ?? 0);
    $type   = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');

    if ($type === 'Other') {
        $custom_type = trim($_POST['custom_type'] ?? '');
        if (empty($custom_type)) {
            $_SESSION['error'] = 'Please describe the "Other" expense type.';
            header('Location: ?page=contributions&tab=expenses');
            exit;
        }
        $type = $custom_type;
    }

    if ($amount <= 0 || empty($type)) {
        $_SESSION['error'] = 'Valid amount and expense type are required.';
        header('Location: ?page=contributions&tab=expenses');
        exit;
    }

    try {
        if ($action === 'add_expense') {
            $stmt = $pdo->prepare("
                INSERT INTO contribution_expenses 
                (amount, type, description, purpose, recorded_by, recorded_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$amount, $type, $description, $purpose, $_SESSION['admin_id'], $recorded_at]);
            $_SESSION['success'] = 'Expense added successfully.';
        }
        elseif ($action === 'edit_expense') {
            $stmt = $pdo->prepare("
                UPDATE contribution_expenses 
                SET amount = ?, type = ?, description = ?, purpose = ?, recorded_at = ? 
                WHERE id = ?
            ");
            $stmt->execute([$amount, $type, $description, $purpose, $recorded_at, $id]);
            $_SESSION['success'] = 'Expense updated successfully.';
        }
        elseif ($action === 'delete_expense') {
            $stmt = $pdo->prepare("DELETE FROM contribution_expenses WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Expense deleted successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: ?page=contributions&tab=expenses');
    exit;
}

// Fallback
header('Location: ?page=contributions');
exit;