<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}
$member_id = $_SESSION['member_id'];
// Fetch member info
$stmt = $pdo->prepare("SELECT full_name, username, email FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$member) {
    session_destroy();
    header('Location: login.php');
    exit;
}
// Handle password change
$password_error = $password_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_error = "All password fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($current_password, $row['password_hash'])) {
            $password_error = "Current password is incorrect.";
        } elseif (strlen($new_password) < 8) {
            $password_error = "New password must be at least 8 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $password_error = "New passwords do not match.";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE members SET password_hash = ? WHERE id = ?");
            if ($update_stmt->execute([$new_hash, $member_id])) {
                $password_success = "Password changed successfully!";
            } else {
                $password_error = "Failed to update password. Please try again.";
            }
        }
    }
}
// Helper function to clean member name
function clean_member_name($full_name, $contributor_type) {
    $name = trim($full_name ?? '');
    if (strtolower($contributor_type ?? '') === 'player') {
        $name = preg_replace('/^Player:\s*/i', '', $name);
        $name = preg_replace('/\s*-\s*04 FC$/i', '', $name);
    }
    elseif (strtolower($contributor_type ?? '') === 'management') {
        $name = preg_replace('/^.*:\s*/', '', $name);
        $name = preg_replace('/\s*\([^)]*\)/', '', $name);
        $name = preg_replace('/\s*-\s*04 FC$/i', '', $name);
    }
    return trim($name);
}
// Calculate Current League Balance (monetary only)
$total_contributions = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM contributions
    WHERE amount IS NOT NULL AND amount > 0
")->fetchColumn();
$total_expenses = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM contribution_expenses
")->fetchColumn();
$current_balance = $total_contributions - $total_expenses;
// Get latest update time
$latest_contrib = $pdo->query("SELECT COALESCE(MAX(recorded_at), '1970-01-01') FROM contributions")->fetchColumn();
$latest_expense = $pdo->query("SELECT COALESCE(MAX(recorded_at), '1970-01-01') FROM contribution_expenses")->fetchColumn();
$latest_update = max($latest_contrib, $latest_expense);
$last_updated_str = $latest_update ? date('d F Y \a\t H:i', strtotime($latest_update)) : 'Never';
// Determine active tab and sorting
$tab = $_GET['tab'] ?? 'all';
$sort = $_GET['sort'] ?? 'date';
$order = strtoupper($_GET['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
// Valid sort columns
$valid_sorts = [
    'contributions' => ['date', 'member', 'type', 'purpose', 'value'],
    'expenses' => ['date', 'type', 'purpose', 'details', 'amount'],
    'all' => ['date']
];
if (!in_array($sort, $valid_sorts[$tab] ?? [])) {
    $sort = 'date';
}
// Build base_url preserving current filters but excluding sort/order
$query_params = $_GET;
unset($query_params['sort'], $query_params['order']);
$query_params['tab'] = $tab;
$base_url = '?' . http_build_query($query_params);
// Fetch login logo
$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'login_logo' AND is_active = 1 LIMIT 1");
$stmt->execute();
$loginLogo = $stmt->fetchColumn();
$logoSrc = $loginLogo ? '../uploads/admin/logos/' . $loginLogo : '../uploads/logo.png';
$faviconSrc = $logoSrc;
// Sortable header helper
function sort_link($column, $label, $current_sort, $current_order, $base_url) {
    $new_order = ($current_sort === $column && $current_order === 'DESC') ? 'ASC' : 'DESC';
    $icon = ($current_sort === $column) ? ($current_order === 'DESC' ? '↓' : '↑') : '';
    return '<a href="' . $base_url . '&sort=' . $column . '&order=' . $new_order . '" class="text-white text-decoration-none">'
           . htmlspecialchars($label) . ' ' . $icon . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - 04 Soccer League</title>
    <link rel="icon" href="<?= htmlspecialchars($faviconSrc) ?>" type="image/png">
    <link rel="shortcut icon" href="<?= htmlspecialchars($faviconSrc) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($faviconSrc) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 280px;
            background: linear-gradient(135deg, #1a1a1a, #2c2c2c); color: #fff;
            padding: 20px 0; box-shadow: 4px 0 15px rgba(0,0,0,0.3);
            z-index: 1000; transition: transform 0.3s ease;
        }
        .sidebar-navbar { background: #111; padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo-circle { width: 100px; height: 100px; background: white; border-radius: 50%; padding: 8px; display: inline-block; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
        .sidebar-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
        .sidebar-header { text-align: center; padding: 20px; }
        .sidebar-header h4 { margin: 15px 0 5px; font-weight: 700; }
        .sidebar-header small { opacity: 0.8; }
        .nav-link { color: #ddd !important; padding: 12px 30px; font-size: 1.02rem; border-left: 3px solid transparent; transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff !important; border-left-color: #007bff; }
        .nav-link i { width: 24px; text-align: center; }
        .logout-btn { position: absolute; bottom: 20px; left: 30px; right: 30px; }
        .main-content { margin-left: 280px; padding: 20px; transition: margin-left 0.3s ease; }
        .mobile-toggle { position: fixed; top: 15px; left: 15px; z-index: 1001; background: #1a1a1a; color: white; border: none; width: 50px; height: 50px; border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.3); display: none; }
        .card { border: none; border-radius: 0 !important; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 0 !important; }
        .no-records { text-align: center; padding: 60px 20px; color: #666; }
        .no-records i { font-size: 4rem; color: #ccc; margin-bottom: 20px; }
        .finance-navbar {
            background: #343a40;
            border-bottom: 3px solid #007bff;
            padding: 8px 20px;
            margin-bottom: 0;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .finance-navbar .nav-tabs { padding-left: 10px; }
        .finance-navbar .nav-link { color: #ddd !important; padding: 10px 20px; font-weight: 600; }
        .finance-navbar .nav-link.active { background: #007bff !important; color: white !important; }
        .balance-row { display: flex; align-items: center; justify-content: flex-end; white-space: nowrap; padding-right: 15px; }
        .balance-label { color: white; font-size: 0.95rem; text-transform: uppercase; font-weight: 600; opacity: 0.9; margin: 0 8px 0 0; }
        .balance-amount { color: white; font-size: 1.3rem; font-weight: bold; }
        .sortable-header a { color: white !important; text-decoration: none; }
        .sortable-header a:hover { text-decoration: underline; }
        .table th, .table td { vertical-align: middle; padding: 0.5rem 0.6rem; }
        .all-transactions-table { width: 100%; }
        .expenses-table { width: 100%; }
        @media (min-width: 992px) {
            .all-transactions-table { table-layout: fixed; }
            .expenses-table { table-layout: fixed; }
            .all-transactions-table col.date { width: 90px; }
            .all-transactions-table col.member { width: 160px; }
            .all-transactions-table col.purpose { width: 200px; }
            .all-transactions-table col.amount { width: 110px; }
            .all-transactions-table col.expenses { width: 130px; }
            .expenses-table col.date { width: 110px; }
            .expenses-table col.type { width: 160px; }
            .expenses-table col.purpose { width: 180px; }
            .expenses-table col.details { width: 1fr; }
            .expenses-table col.amount { width: 140px; }
        }
        .all-transactions-table .purpose-col {
            white-space: normal;
            word-wrap: break-word;
            font-size: 0.9rem;
        }
        .all-transactions-table .amount-col {
            text-align: right;
            padding: 0.5rem 0.8rem;
            font-weight: normal;
        }
        .all-transactions-table .totals-row td {
            font-weight: bold;
            background-color: #e9ecef !important;
            border-top: 2px solid #adb5bd;
        }
        .expenses-table .amount-col { text-align: right; padding-right: 1.2rem !important; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .download-btn { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 8px; }
        .download-pdf-btn { background: #007bff; color: white; cursor: pointer; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-280px); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0 !important; padding: 15px; }
            .mobile-toggle { display: block; }
            .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
            .finance-navbar .nav-link { padding: 10px 15px; font-size: 0.95rem; }
            .balance-row { padding-right: 10px; }
        }
        .table thead { background: #495057; color: white; }
        .container-fluid { padding-top: 0 !important; margin-top: 0 !important; }
        .filter-controls .row > .col-auto { padding: 0 4px; }
        .filter-controls label { margin-bottom: 0; font-size: 0.9rem; }
    </style>
</head>
<body>
<button class="mobile-toggle" id="sidebarToggle">
    <i class="bi bi-list fs-3"></i>
</button>
<div class="sidebar" id="sidebar">
    <div class="sidebar-navbar">
        <div class="sidebar-logo-circle">
            <img src="<?= htmlspecialchars($logoSrc) ?>" alt="League Logo">
        </div>
    </div>
    <div class="sidebar-header">
        <h4><?= htmlspecialchars($member['full_name']) ?></h4>
        <small>@<?= htmlspecialchars($member['username']) ?></small>
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="?tab=all" class="nav-link <?= $tab === 'all' ? 'active' : '' ?>">
                <i class="bi bi-list-check"></i> All Transactions
            </a>
        </li>
        <li class="nav-item">
            <a href="?tab=contributions" class="nav-link <?= $tab === 'contributions' ? 'active' : '' ?>">
                <i class="bi bi-table"></i> Contributions
            </a>
        </li>
        <li class="nav-item">
            <a href="?tab=expenses" class="nav-link <?= $tab === 'expenses' ? 'active' : '' ?>">
                <i class="bi bi-wallet2"></i> League Expenses
            </a>
        </li>
        <li class="nav-item">
            <a href="#changePasswordModal" class="nav-link" data-bs-toggle="modal">
                <i class="bi bi-key"></i> Change Password
            </a>
        </li>
    </ul>
    <div class="logout-btn">
        <a href="logout.php" class="btn btn-outline-light w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>
<div class="main-content">
    <div class="container-fluid">
        <!-- Hidden element for PDF to read the current balance amount -->
        <div id="pdfCurrentBalance" style="display:none;"><?= number_format($current_balance, 2) ?></div>
        <div class="finance-navbar d-flex justify-content-between align-items-center flex-wrap">
            <ul class="nav nav-tabs mb-0">
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'all' ? 'active' : '' ?>" href="?tab=all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'contributions' ? 'active' : '' ?>" href="?tab=contributions">Contributions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'expenses' ? 'active' : '' ?>" href="?tab=expenses">League Expenses</a>
                </li>
            </ul>
            <div class="balance-row">
                <div class="balance-label">Current League Balance:</div>
                <div class="balance-amount">$<?= number_format($current_balance, 2) ?></div>
            </div>
        </div>
        <?php
        $contributions = [];
        $expenses = [];
        $all_transactions = [];
        $members = [];
        $filter_month = date('Y-m');
        $filter_member = 'all';
        $selected_member = null;
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        $total_registration = 0;
        $total_other_contrib = 0;
        $total_expense_all = 0;
        if ($tab === 'contributions') {
            $members = $pdo->query("SELECT id, full_name, username FROM members ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
            $filter_month = $_GET['month'] ?? date('Y-m');
            $filter_member = $_GET['member'] ?? 'all';
            if ($filter_member !== 'all') {
                $stmt = $pdo->prepare("SELECT full_name, username FROM members WHERE id = ?");
                $stmt->execute([$filter_member]);
                $selected_member = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$selected_member) $filter_member = 'all';
            }
            $sql = "
                SELECT
                    c.*,
                    m.full_name,
                    m.username,
                    t.name AS type_name,
                    t.is_monetary,
                    COALESCE(c.contributor_type, 'member') AS contributor_type
                FROM contributions c
                JOIN members m ON c.member_id = m.id
                JOIN contribution_types t ON c.type_id = t.id
            ";
            $where = [];
            $params = [];
            if ($filter_member !== 'all') {
                $where[] = "c.member_id = ?";
                $params[] = $filter_member;
            } else {
                $where[] = "DATE_FORMAT(c.recorded_at, '%Y-%m') = ?";
                $params[] = $filter_month;
            }
            if ($where) $sql .= " WHERE " . implode(" AND ", $where);
            $contrib_order = match($sort) {
                'date' => "c.recorded_at $order",
                'member' => "m.full_name $order, m.username $order",
                'type' => "t.name $order",
                'purpose' => "COALESCE(c.purpose, '') $order",
                default => "COALESCE(c.amount, 0) $order, COALESCE(c.quantity, 0) $order"
            };
            $sql .= " ORDER BY $contrib_order";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($tab === 'expenses') {
            $date_from = $_GET['date_from'] ?? '';
            $date_to = $_GET['date_to'] ?? '';
            $expense_order = match($sort) {
                'date' => "recorded_at $order",
                'type' => "type $order",
                'purpose' => "COALESCE(purpose, '') $order",
                'details' => "description $order",
                default => "amount $order"
            };
            $where = [];
            $params = [];
            if ($date_from !== '') {
                $where[] = "recorded_at >= ?";
                $params[] = $date_from . ' 00:00:00';
            }
            if ($date_to !== '') {
                $where[] = "recorded_at <= ?";
                $params[] = $date_to . ' 23:59:59';
            }
            $sql = "SELECT * FROM contribution_expenses";
            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            $sql .= " ORDER BY $expense_order";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($tab === 'all') {
            $contrib_where = [];
            $expense_where = [];
            $contrib_params = [];
            $expense_params = [];
            if (!empty($date_from)) {
                $contrib_where[] = "c.recorded_at >= ?";
                $expense_where[] = "e.recorded_at >= ?";
                $contrib_params[] = $date_from . ' 00:00:00';
                $expense_params[] = $date_from . ' 00:00:00';
            }
            if (!empty($date_to)) {
                $contrib_where[] = "c.recorded_at <= ?";
                $expense_where[] = "e.recorded_at <= ?";
                $contrib_params[] = $date_to . ' 23:59:59';
                $expense_params[] = $date_to . ' 23:59:59';
            }
            $contrib_clause = $contrib_where ? "WHERE " . implode(" AND ", $contrib_where) : "";
            $expense_clause = $expense_where ? "WHERE " . implode(" AND ", $expense_where) : "";
            $params = array_merge($contrib_params, $expense_params);
            $sql = "
                SELECT
                    'contribution' AS record_type,
                    c.recorded_at AS date,
                    m.full_name,
                    COALESCE(c.contributor_type, 'member') AS contributor_type,
                    m.username,
                    COALESCE(NULLIF(TRIM(c.purpose), ''), 'Contribution') AS purpose,
                    CASE
                        WHEN c.amount > 0 AND LOWER(TRIM(COALESCE(c.purpose, ''))) LIKE '%registration%'
                        THEN c.amount
                        ELSE NULL
                    END AS registration_amount,
                    CASE
                        WHEN c.amount > 0 AND (c.purpose IS NULL OR TRIM(c.purpose) = '' OR LOWER(TRIM(c.purpose)) NOT LIKE '%registration%')
                        THEN c.amount
                        ELSE NULL
                    END AS other_contribution_amount,
                    NULL AS expense_amount,
                    NULL AS expense_type,
                    NULL AS expense_description
                FROM contributions c
                JOIN members m ON c.member_id = m.id
                $contrib_clause

                UNION ALL

                SELECT
                    'expense' AS record_type,
                    e.recorded_at AS date,
                    NULL AS full_name,
                    NULL AS contributor_type,
                    NULL AS username,
                    CONCAT(
                        COALESCE(NULLIF(TRIM(e.type), ''), 'Expense'),
                        CASE WHEN TRIM(e.description) != '' AND e.description IS NOT NULL
                             THEN CONCAT(' (', TRIM(e.description), ')')
                             ELSE ''
                        END
                    ) AS purpose,
                    NULL AS registration_amount,
                    NULL AS other_contribution_amount,
                    e.amount AS expense_amount,
                    e.type AS expense_type,
                    e.description AS expense_description
                FROM contribution_expenses e
                $expense_clause

                ORDER BY date DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $all_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($all_transactions as $row) {
                $total_registration += floatval($row['registration_amount'] ?? 0);
                $total_other_contrib += floatval($row['other_contribution_amount'] ?? 0);
                $total_expense_all += floatval($row['expense_amount'] ?? 0);
            }
        }
        ?>
        <?php if ($tab === 'all'): ?>
            <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 id="pageTitle" class="mb-0">All Transactions</h4>
                <div class="filter-controls d-flex gap-2 align-items-center flex-wrap">
                    <form method="GET" class="filter-form row g-2 align-items-center">
                        <input type="hidden" name="tab" value="all">
                        <div class="col-auto">
                            <label class="col-form-label fw-semibold">From</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>" onchange="this.form.submit()">
                        </div>
                        <div class="col-auto">
                            <label class="col-form-label fw-semibold">To</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>" onchange="this.form.submit()">
                        </div>
                        <?php if (!empty($date_from) || !empty($date_to)): ?>
                            <div class="col-auto">
                                <a href="?tab=all" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="download-btn download-pdf-btn" title="Download PDF">
                        <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                    </button>
                </div>
            </div>
            <?php if (empty($all_transactions)): ?>
                <div class="card mt-0">
                    <div class="card-body no-records">
                        <i class="bi bi-receipt-cutoff"></i>
                        <h4>No transactions found</h4>
                        <p>No income or expenses recorded in the selected period.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mt-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 all-transactions-table">
                                <colgroup>
                                    <col class="date">
                                    <col class="member">
                                    <col class="purpose">
                                    <col class="amount">
                                    <col class="amount">
                                    <col class="expenses">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="sortable-header"><?= sort_link('date', 'Date', $sort, $order, $base_url) ?></th>
                                        <th>Member Name</th>
                                        <th>Purpose</th>
                                        <th class="text-end">Contributions</th>
                                        <th class="text-end">Registrations</th>
                                        <th class="text-end">Expenses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="totals-row">
                                        <td colspan="3"></td>
                                        <td class="amount-col text-end">$<?= number_format($total_other_contrib, 2) ?></td>
                                        <td class="amount-col text-end">$<?= number_format($total_registration, 2) ?></td>
                                        <td class="amount-col text-end text-danger">$<?= number_format($total_expense_all, 2) ?></td>
                                    </tr>
                                    <?php foreach ($all_transactions as $row):
                                        $display_name = ($row['record_type'] === 'contribution')
                                            ? clean_member_name($row['full_name'], $row['contributor_type'])
                                            : '—';
                                    ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                                        <td><?= htmlspecialchars($display_name) ?></td>
                                        <td class="purpose-col"><?= htmlspecialchars($row['purpose']) ?></td>
                                        <td class="amount-col text-end">
                                            <?php if ($row['other_contribution_amount'] !== null): ?>
                                                $<?= number_format($row['other_contribution_amount'], 2) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td class="amount-col text-end">
                                            <?php if ($row['registration_amount'] !== null): ?>
                                                $<?= number_format($row['registration_amount'], 2) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td class="amount-col text-end text-danger">
                                            <?php if ($row['expense_amount'] !== null): ?>
                                                $<?= number_format($row['expense_amount'], 2) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif ($tab === 'contributions'): ?>
            <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 id="pageTitle" class="mb-0">
                    Contributions
                    <small class="text-muted">
                        <?php if ($filter_member !== 'all' && $selected_member): ?>
                            — All time for <?= htmlspecialchars($selected_member['full_name']) ?>
                        <?php else: ?>
                            — <?= date('F Y', strtotime($filter_month . '-01')) ?>
                        <?php endif; ?>
                    </small>
                </h4>
                <div class="filter-controls d-flex gap-2 align-items-center flex-wrap">
                    <form method="GET" class="filter-form row g-2 align-items-center">
                        <input type="hidden" name="tab" value="contributions">
                        <div class="col-auto"><label class="col-form-label fw-semibold">Month</label></div>
                        <div class="col-auto">
                            <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($filter_month) ?>"
                                   onchange="this.form.submit()" <?= $filter_member !== 'all' ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-auto"><label class="col-form-label fw-semibold">Member</label></div>
                        <div class="col-auto">
                            <select name="member" class="form-select" onchange="this.form.submit()">
                                <option value="all">All Members</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= $filter_member == $m['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if ($filter_member !== 'all' || $filter_month !== date('Y-m')): ?>
                            <div class="col-auto">
                                <a href="?tab=contributions" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="download-btn download-pdf-btn" title="Download PDF">
                        <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                    </button>
                </div>
            </div>
            <?php if (empty($contributions)): ?>
                <div class="card mt-0">
                    <div class="card-body no-records">
                        <i class="bi bi-receipt"></i>
                        <h4>No contributions found</h4>
                        <p>
                            <?php if ($filter_member !== 'all'): ?>
                                No contributions recorded for <?= htmlspecialchars($selected_member['full_name'] ?? 'this member') ?> yet.
                            <?php else: ?>
                                No contributions recorded in <?= date('F Y', strtotime($filter_month . '-01')) ?>.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mt-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="sortable-header"><?= sort_link('date', 'Date', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('member', 'Member', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('type', 'Type', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('purpose', 'Purpose', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('value', 'Amount / Quantity', $sort, $order, $base_url) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contributions as $c):
                                        $display_name = clean_member_name($c['full_name'], $c['contributor_type']);
                                        $is_cash = ($c['amount'] !== null && is_numeric($c['amount']) && floatval($c['amount']) > 0);
                                    ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($c['recorded_at'])) ?></td>
                                        <td><?= htmlspecialchars($display_name) ?></td>
                                        <td>
                                            <?php if ($is_cash): ?>
                                                Cash
                                            <?php else: ?>
                                                <?= htmlspecialchars($c['type_name'] ?? 'Other') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($c['purpose'] ?? '—') ?></td>
                                        <td>
                                            <?php if ($is_cash): ?>
                                                $<?= number_format($c['amount'], 2) ?>
                                            <?php elseif ($c['quantity'] !== null && $c['quantity'] > 0): ?>
                                                <?= number_format($c['quantity']) ?> item(s)
                                                <?php if (!empty($c['description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($c['description']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 id="pageTitle" class="mb-0">League Expenses</h4>
                <div class="filter-controls d-flex gap-2 align-items-center flex-wrap">
                    <form method="GET" class="filter-form row g-2 align-items-center">
                        <input type="hidden" name="tab" value="expenses">
                        <div class="col-auto">
                            <label class="col-form-label fw-semibold">From</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>" onchange="this.form.submit()">
                        </div>
                        <div class="col-auto">
                            <label class="col-form-label fw-semibold">To</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>" onchange="this.form.submit()">
                        </div>
                        <?php if (!empty($date_from) || !empty($date_to)): ?>
                            <div class="col-auto">
                                <a href="?tab=expenses" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="download-btn download-pdf-btn" title="Download PDF">
                        <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                    </button>
                </div>
            </div>
            <?php if (empty($expenses)): ?>
                <div class="card mt-0">
                    <div class="card-body no-records">
                        <i class="bi bi-receipt-cutoff"></i>
                        <h4>No expenses found</h4>
                        <p>
                            <?php if (!empty($date_from) || !empty($date_to)): ?>
                                No expenses recorded in the selected date range.
                            <?php else: ?>
                                No expenses recorded yet.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mt-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 expenses-table">
                                <colgroup>
                                    <col class="date">
                                    <col class="type">
                                    <col class="purpose">
                                    <col class="details">
                                    <col class="amount">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="sortable-header"><?= sort_link('date', 'Date', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('type', 'Type', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('purpose', 'Purpose', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header"><?= sort_link('details', 'Details', $sort, $order, $base_url) ?></th>
                                        <th class="sortable-header text-end"><?= sort_link('amount', 'Amount', $sort, $order, $base_url) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $e): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($e['recorded_at'])) ?></td>
                                        <td><?= htmlspecialchars($e['type'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($e['purpose'] ?? '—') ?></td>
                                        <td><?= nl2br(htmlspecialchars($e['description'] ?? '')) ?></td>
                                        <td class="amount-col text-end text-danger">
                                            $<?= number_format($e['amount'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="mt-4 text-muted small px-3">
            <p>Last updated: <?= htmlspecialchars($last_updated_str) ?></p>
        </div>
    </div>
</div>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($password_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($password_error) ?></div>
                    <?php endif; ?>
                    <?php if ($password_success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($password_success) ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}
document.getElementById('sidebarToggle')?.addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('sidebar').classList.toggle('active');
});
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (window.innerWidth <= 991 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('active');
    }
});
document.querySelectorAll('.download-pdf-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const titleEl = document.getElementById('pageTitle');
        if (!titleEl) return;
        const titleText = titleEl.innerText.trim();
        const tableContainer = document.querySelector('.table-responsive');
        const noRecords = document.querySelector('.no-records');
        if (!tableContainer && !noRecords) {
            alert('Nothing to download.');
            return;
        }
        const isAllTab = document.querySelector('.nav-link[href="?tab=all"]').classList.contains('active');
        const filename = isAllTab ? '04-soccer-league-all-transactions.pdf' :
                         (document.querySelector('.nav-link[href="?tab=contributions"]').classList.contains('active')
                             ? '04-soccer-league-contributions.pdf'
                             : '04-soccer-league-expenses.pdf');
        const clone = document.createElement('div');
        clone.style.padding = '30px 15px';
        clone.style.background = '#fff';
        clone.style.fontFamily = 'Arial, Helvetica, sans-serif';
        clone.style.width = '700px';
        clone.style.margin = '0 auto';
        clone.style.fontSize = '9.5px';
        // Header with title on left and balance on right (only for All Transactions)
        const headerBar = document.createElement('div');
        headerBar.style.display = 'flex';
        headerBar.style.justifyContent = isAllTab ? 'space-between' : 'center';
        headerBar.style.alignItems = 'center';
        headerBar.style.marginBottom = '20px';
        const titleH2 = document.createElement('h2');
        titleH2.innerText = '04 Soccer League — ' + titleText;
        titleH2.style.margin = '0';
        titleH2.style.color = '#343a40';
        titleH2.style.fontSize = '16px';
        headerBar.appendChild(titleH2);
        if (isAllTab) {
            const balanceDiv = document.createElement('div');
            balanceDiv.style.fontSize = '14px';
            balanceDiv.style.fontWeight = 'bold';
            balanceDiv.innerText = 'Current League Balance: $' + document.getElementById('pdfCurrentBalance').innerText;
            headerBar.appendChild(balanceDiv);
        }
        clone.appendChild(headerBar);
        if (tableContainer) {
            const tableClone = tableContainer.cloneNode(true);
            const table = tableClone.querySelector('table');
            table.style.width = '100%';
            table.style.tableLayout = 'fixed';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '9.5px';
            table.querySelectorAll('th, td').forEach(cell => {
                cell.style.border = '0.5px solid #ccc';
                cell.style.padding = '5px 8px';
                cell.style.verticalAlign = 'middle';
                cell.style.lineHeight = '1.3';
                cell.style.fontSize = '9.5px';
            });
            /* Left-align all three amount columns */
            table.querySelectorAll('th:nth-child(4), td:nth-child(4),' +
                                  'th:nth-child(5), td:nth-child(5),' +
                                  'th:nth-child(6), td:nth-child(6)').forEach(cell => {
                cell.style.textAlign = 'left';
                cell.style.paddingLeft = '12px';
            });
            table.querySelectorAll('.text-danger').forEach(cell => {
                cell.style.color = '#dc3545';
            });
            const thead = table.querySelector('thead');
            if (thead) {
                thead.style.backgroundColor = '#495057';
                thead.querySelectorAll('th').forEach(th => {
                    th.style.color = '#fff';
                    th.style.fontWeight = '600';
                });
            }
            table.querySelectorAll('.totals-row td').forEach(cell => {
                cell.style.fontWeight = 'bold';
                cell.style.backgroundColor = '#e9ecef';
            });
            table.querySelectorAll('.purpose-col').forEach(cell => {
                cell.style.whiteSpace = 'normal';
                cell.style.wordWrap = 'break-word';
                cell.style.wordBreak = 'break-word';
            });
            const colgroup = tableClone.querySelector('colgroup');
            if (colgroup && isAllTab) {
                const cols = colgroup.querySelectorAll('col');
                if (cols.length === 6) {
                    cols[0].style.width = '70px'; // Date
                    cols[1].style.width = '140px'; // Member Name
                    cols[2].style.width = '140px'; // Purpose
                    cols[3].style.width = '82px'; // Contributions
                    cols[4].style.width = '82px'; // Registrations
                    cols[5].style.width = '82px'; // Expenses
                }
            }
            clone.appendChild(tableClone);
        } else if (noRecords) {
            const msg = document.createElement('p');
            msg.innerText = noRecords.querySelector('h4').innerText;
            msg.style.textAlign = 'center';
            msg.style.fontSize = '18px';
            msg.style.color = '#666';
            clone.appendChild(msg);
        }
        html2pdf()
            .set({
                margin: [0.5, 0.5, 0.5, 0.5],
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2.2, useCORS: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            })
            .from(clone)
            .save();
    });
});
</script>
</body>
</html>