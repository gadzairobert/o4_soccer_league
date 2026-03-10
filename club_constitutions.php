<?php
ob_start();
header('Content-Type: text/html; charset=UTF-8');
require 'config.php';

// ======================================================
// RESOLVE WHICH CONSTITUTION TO SHOW
// Strategy (most → least specific):
//  1. ?nav_id=X  → match nav item by id, then find constitution by title
//  2. ?name=X    → match nav item by name, then find constitution by title
//  3. No params  → just load the first active constitution from DB directly
//  4. Last resort → load ANY constitution from DB (newest first)
// This means the page always shows something if a PDF exists.
// ======================================================
$nav_id   = (int)($_GET['nav_id'] ?? 0);
$nav_name = trim($_GET['name']    ?? '');

$constitution = null;
$nav_item     = null;

// --- Fetch all child nav items under a parent named "Constitutions" ---
$constitutionsNavStmt = $pdo->prepare("
    SELECT ni.*
    FROM nav_items ni
    INNER JOIN nav_items parent ON ni.parent_id = parent.id
    WHERE parent.name = 'Constitutions'
      AND parent.is_active = 1
      AND ni.is_active = 1
    ORDER BY ni.sort_order ASC, ni.id ASC
");
$constitutionsNavStmt->execute();
$constitutionNavItems = $constitutionsNavStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Try to pin a nav_item from URL params ---
if ($nav_id > 0) {
    foreach ($constitutionNavItems as $ni) {
        if ((int)$ni['id'] === $nav_id) { $nav_item = $ni; break; }
    }
    // Also try direct DB lookup for nav item in case it's not under Constitutions parent
    if (!$nav_item) {
        $s = $pdo->prepare("SELECT * FROM nav_items WHERE id = ? AND is_active = 1 LIMIT 1");
        $s->execute([$nav_id]);
        $nav_item = $s->fetch(PDO::FETCH_ASSOC) ?: null;
    }
} elseif ($nav_name !== '') {
    foreach ($constitutionNavItems as $ni) {
        if (strtolower(trim($ni['name'])) === strtolower($nav_name)) { $nav_item = $ni; break; }
    }
} else {
    $nav_item = $constitutionNavItems[0] ?? null;
}

// --- Fetch constitution: try nav title match first, then fall back broadly ---
if ($nav_item) {
    // Try exact title match (active first)
    $s = $pdo->prepare("
        SELECT * FROM club_constitutions
        WHERE LOWER(TRIM(title)) = LOWER(TRIM(?))
        ORDER BY is_active DESC, effective_date DESC
        LIMIT 1
    ");
    $s->execute([$nav_item['name']]);
    $constitution = $s->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Fallback 1: no nav match or title match → first active constitution in DB
if (!$constitution) {
    $constitution = $pdo->query("
        SELECT * FROM club_constitutions
        WHERE is_active = 1
        ORDER BY effective_date DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Fallback 2: no active one at all → any constitution
if (!$constitution) {
    $constitution = $pdo->query("
        SELECT * FROM club_constitutions
        ORDER BY effective_date DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC) ?: null;
}

// If we got a constitution but no nav_item label, use constitution title as label
if ($constitution && !$nav_item) {
    $nav_item = ['name' => $constitution['title']];
}

// --- All constitutions for sidebar ---
$allConstitutions = $pdo->query("
    SELECT * FROM club_constitutions
    ORDER BY is_active DESC, effective_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$page_title = ($nav_item['name'] ?? 'Club Constitution') . ' — ' . ($league_name ?? 'League');

include 'includes/header.php';
?>

<style>
    /* =============================================
       CONSTITUTION PAGE — EDITORIAL / REFINED DARK
       ============================================= */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --ink:       #1a1a2e;
        --ink-light: #2c3e50;
        --gold:      #c9a84c;
        --gold-light:#f0d080;
        --cream:     #fdf8ef;
        --pale:      #f5f0e8;
        --muted:     #7a7a8a;
        --border:    #ddd6c8;
        --accent:    #7b2d8b;
        --page-bg:   #e8e0d5;
        --shadow-book: 0 30px 80px rgba(0,0,0,0.35), 0 8px 24px rgba(0,0,0,0.2);
    }

    html, body {
        background-color: #1a1a2e !important;
        color: #333;
        margin: 0; padding: 0;
    }

    body {
        background-image:
            radial-gradient(ellipse at 20% 20%, rgba(201,168,76,0.06) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 80%, rgba(123,45,139,0.05) 0%, transparent 50%);
        background-attachment: fixed;
    }

    .constitution-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem 4rem;
    }
    @media (min-width: 992px) {
        .constitution-wrapper {
            padding-top: 4.5rem;
        }
    }

    /* ── Page Header ── */
    .constitution-page-header {
        text-align: center;
        margin-bottom: 2.5rem;
        position: relative;
    }
    .constitution-page-header::before {
        content: '';
        display: block;
        width: 60px; height: 3px;
        background: var(--gold);
        margin: 0 auto 1.2rem;
        border-radius: 2px;
    }
    .constitution-page-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.6rem, 4vw, 2.8rem);
        font-weight: 900;
        color: var(--cream);
        letter-spacing: -0.5px;
        margin: 0 0 0.5rem;
        text-shadow: 0 2px 20px rgba(0,0,0,0.4);
    }
    .constitution-page-header p {
        font-family: 'DM Sans', sans-serif;
        color: rgba(255,255,255,0.5);
        font-size: 0.95rem;
        margin: 0;
    }
    .constitution-page-header::after {
        content: '';
        display: block;
        width: 40px; height: 2px;
        background: var(--gold);
        opacity: 0.5;
        margin: 1.2rem auto 0;
        border-radius: 2px;
    }

    /* ── Layout ── */
    .constitution-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 991px) {
        .constitution-layout { grid-template-columns: 1fr; }
    }

    /* ── Sidebar ── */
    .constitution-sidebar {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(201,168,76,0.2);
        border-radius: 14px;
        padding: 1.4rem;
        position: sticky;
        top: 130px;
    }
    .sidebar-title {
        font-family: 'Playfair Display', serif;
        color: var(--gold);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 1rem;
        padding-bottom: 0.7rem;
        border-bottom: 1px solid rgba(201,168,76,0.2);
    }
    .sidebar-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 0.75rem 0.8rem;
        border-radius: 8px;
        text-decoration: none;
        color: rgba(255,255,255,0.6);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        transition: all 0.25s;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .sidebar-item:hover {
        background: rgba(201,168,76,0.1);
        color: var(--gold-light);
        transform: translateX(3px);
    }
    .sidebar-item.active {
        background: rgba(201,168,76,0.15);
        color: var(--gold);
        font-weight: 600;
        border-left: 3px solid var(--gold);
    }
    .sidebar-item .si-icon {
        width: 28px; height: 28px;
        background: rgba(201,168,76,0.15);
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; flex-shrink: 0; color: var(--gold);
    }
    .sidebar-item .si-info { flex: 1; min-width: 0; }
    .sidebar-item .si-name { display: block; font-weight: 500; }
    .sidebar-item .si-meta { display: block; font-size: 0.72rem; color: rgba(255,255,255,0.35); margin-top: 2px; }
    .sidebar-item.active .si-meta { color: rgba(201,168,76,0.6); }

    /* ── Main Viewer Card ── */
    .constitution-viewer-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(201,168,76,0.15);
        border-radius: 16px;
        overflow: hidden;
    }

    /* ── Viewer Header ── */
    .viewer-header {
        background: linear-gradient(135deg, var(--ink-light), var(--ink));
        border-bottom: 2px solid var(--gold);
        padding: 1.2rem 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .viewer-title-group { flex: 1; min-width: 0; }
    .viewer-doc-title {
        font-family: 'Playfair Display', serif;
        color: var(--cream);
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 3px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .viewer-doc-meta {
        font-family: 'DM Sans', sans-serif;
        color: rgba(255,255,255,0.45);
        font-size: 0.8rem;
    }
    .viewer-doc-meta span { margin-right: 12px; }
    .viewer-doc-meta .badge-version {
        background: rgba(201,168,76,0.2);
        color: var(--gold);
        padding: 2px 8px; border-radius: 4px;
        font-weight: 600; font-size: 0.72rem; letter-spacing: 0.5px;
    }
    .viewer-actions {
        display: flex; align-items: center; gap: 0.6rem; flex-shrink: 0;
    }
    /* Mobile: stack title+meta above actions, each full-width */
    @media (max-width: 767px) {
        .viewer-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem 1.2rem;
            gap: 0.75rem;
        }
        .viewer-title-group { width: 100%; }
        .viewer-doc-title { font-size: 1rem; white-space: normal; }
        .viewer-doc-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 0;
            margin-top: 4px;
        }
        .viewer-doc-meta span { margin-right: 10px; line-height: 1.8; }
        .viewer-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
    .btn-viewer {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem; font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.25s;
        border: none; cursor: pointer;
    }
    .btn-viewer-gold {
        background: var(--gold);
        color: var(--ink);
    }
    .btn-viewer-gold:hover {
        background: var(--gold-light);
        color: var(--ink);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(201,168,76,0.4);
    }
    .btn-viewer-outline {
        background: rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.7);
        border: 1px solid rgba(255,255,255,0.15);
    }
    .btn-viewer-outline:hover {
        background: rgba(255,255,255,0.12);
        color: #fff;
    }

    /* ── Flipbook Stage ── */
    .flipbook-stage {
        background: #0f0f1a;
        padding: 2.5rem 1.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 600px;
        position: relative;
        overflow: hidden;
    }
    .flipbook-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 50% 0%, rgba(201,168,76,0.06) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ── Book Container ── */
    .book-container {
        position: relative;
        width: 100%;
        max-width: 900px;
        perspective: 2000px;
    }

    /* Desktop: two-page spread */
    .book-spread {
        display: flex;
        gap: 0;
        justify-content: center;
        align-items: stretch;
        filter: drop-shadow(var(--shadow-book));
        position: relative;
    }

    .page-wrapper {
        position: relative;
        flex: 0 0 50%;
        max-width: 50%;
    }
    .page-wrapper.left  { transform-origin: right center; }
    .page-wrapper.right { transform-origin: left center; }

    /* Page animation classes */
    .page-wrapper.flip-out-left {
        animation: flipOutLeft 0.45s ease-in forwards;
    }
    .page-wrapper.flip-in-left {
        animation: flipInLeft 0.45s ease-out forwards;
    }
    .page-wrapper.flip-out-right {
        animation: flipOutRight 0.45s ease-in forwards;
    }
    .page-wrapper.flip-in-right {
        animation: flipInRight 0.45s ease-out forwards;
    }

    @keyframes flipOutLeft  { from { transform: rotateY(0); opacity:1; } to { transform: rotateY(-90deg); opacity:0; } }
    @keyframes flipInLeft   { from { transform: rotateY(90deg); opacity:0; } to { transform: rotateY(0); opacity:1; } }
    @keyframes flipOutRight { from { transform: rotateY(0); opacity:1; } to { transform: rotateY(90deg); opacity:0; } }
    @keyframes flipInRight  { from { transform: rotateY(-90deg); opacity:0; } to { transform: rotateY(0); opacity:1; } }

    .book-page {
        width: 100%;
        aspect-ratio: 3/4;
        background: var(--cream);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }
    .page-wrapper.left  .book-page {
        border-right: 2px solid #b0a090;
        box-shadow: inset -8px 0 20px rgba(0,0,0,0.08);
        border-radius: 4px 0 0 4px;
    }
    .page-wrapper.right .book-page {
        border-left: 2px solid #c8baa8;
        border-radius: 0 4px 4px 0;
        box-shadow: inset 8px 0 20px rgba(0,0,0,0.06);
    }

    /* Page spine shadow line */
    .book-page::before {
        content: '';
        position: absolute;
        top: 0; bottom: 0;
        width: 20px;
        background: linear-gradient(to right, rgba(0,0,0,0.06), transparent);
        pointer-events: none;
        z-index: 2;
    }
    .page-wrapper.right .book-page::before {
        left: 0;
        background: linear-gradient(to right, rgba(0,0,0,0.08), transparent);
    }
    .page-wrapper.left .book-page::before {
        right: 0; left: auto;
        background: linear-gradient(to left, rgba(0,0,0,0.08), transparent);
    }

    /* Canvas inside each page */
    .book-page canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }

    /* Cover page styling */
    .cover-page {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 2rem;
        background: linear-gradient(160deg, #1a1a2e 0%, #2c2060 50%, #1a1a2e 100%);
        color: white;
        text-align: center;
    }
    .cover-page .cover-emblem {
        width: 80px; height: 80px;
        background: rgba(201,168,76,0.15);
        border: 2px solid var(--gold);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; color: var(--gold);
        margin-bottom: 1.5rem;
        box-shadow: 0 0 30px rgba(201,168,76,0.2);
    }
    .cover-page .cover-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1rem, 2.5vw, 1.5rem);
        font-weight: 900;
        color: var(--gold);
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    .cover-page .cover-subtitle {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        color: rgba(255,255,255,0.5);
        margin-bottom: 1.5rem;
    }
    .cover-page .cover-rule {
        width: 40px; height: 2px; background: var(--gold); opacity: 0.5;
        margin: 0 auto 1.5rem; border-radius: 1px;
    }
    .cover-page .cover-version {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        color: rgba(255,255,255,0.35);
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* Blank page */
    .blank-page {
        height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: var(--pale);
    }
    .blank-page::after {
        content: '';
        display: block;
        width: 60px; height: 60px;
        border: 1px solid var(--border);
        border-radius: 50%;
        opacity: 0.4;
    }

    /* Page number strip */
    .page-number-strip {
        position: absolute;
        bottom: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        color: var(--muted);
        letter-spacing: 1px;
    }
    .page-wrapper.left  .page-number-strip { left: 16px; }
    .page-wrapper.right .page-number-strip { right: 16px; }

    /* Loading overlay */
    .page-loading {
        position: absolute;
        inset: 0;
        background: var(--pale);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: 10;
    }
    .loading-spinner {
        width: 36px; height: 36px;
        border: 3px solid var(--border);
        border-top-color: var(--gold);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 10px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        color: var(--muted);
    }

    /* ── Controls ── */
    .flipbook-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 1.8rem;
        flex-wrap: wrap;
    }
    .ctrl-btn {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        font-size: 1.2rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.25s;
        flex-shrink: 0;
    }
    .ctrl-btn:hover:not(:disabled) {
        background: rgba(201,168,76,0.15);
        border-color: var(--gold);
        transform: scale(1.1);
        box-shadow: 0 4px 20px rgba(201,168,76,0.25);
    }
    .ctrl-btn:disabled {
        opacity: 0.25; cursor: not-allowed; transform: none;
    }
    .ctrl-page-info {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        color: rgba(255,255,255,0.5);
        text-align: center;
        min-width: 120px;
    }
    .ctrl-page-info strong {
        color: var(--gold);
        font-size: 1.1rem;
    }

    /* Page jump input */
    .ctrl-jump {
        display: flex; align-items: center; gap: 8px;
    }
    .ctrl-jump input {
        width: 60px;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        border-radius: 8px;
        padding: 0.4rem 0.6rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        text-align: center;
        outline: none;
        transition: border-color 0.2s;
    }
    .ctrl-jump input:focus { border-color: var(--gold); }
    .ctrl-jump button {
        background: rgba(201,168,76,0.15);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .ctrl-jump button:hover {
        background: rgba(201,168,76,0.25);
    }

    /* Mobile: single page mode */
    @media (max-width: 768px) {
        .page-wrapper {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .book-spread { gap: 0; }
        /* Hide right page on small screens — single page mode */
        .page-wrapper.right.desktop-only-page {
            display: none;
        }
        .flipbook-stage { padding: 1.5rem 0.75rem 1rem; }
        .book-container { max-width: 420px; }
        .ctrl-btn { width: 44px; height: 44px; font-size: 1rem; }
        .constitution-sidebar { position: static; }
    }

    /* ── Not Found State ── */
    .constitution-not-found {
        text-align: center;
        padding: 5rem 2rem;
    }
    .constitution-not-found .nf-icon {
        font-size: 4rem;
        color: rgba(201,168,76,0.3);
        margin-bottom: 1rem;
    }
    .constitution-not-found h3 {
        font-family: 'Playfair Display', serif;
        color: var(--cream);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .constitution-not-found p {
        color: rgba(255,255,255,0.4);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
    }

    /* ── Swipe hint ── */
    .swipe-hint {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        color: rgba(255,255,255,0.25);
        text-align: center;
        margin-top: 0.8rem;
        display: none;
    }
    @media (max-width: 768px) { .swipe-hint { display: block; } }
</style>

<?php
// ── Determine PDF path ──────────────────────────────────────────────
// pdf_path stored in DB is always relative from site root, e.g. uploads/constitutions/file.pdf
// Normalise slashes so it works on both Windows (localhost) and Linux (cPanel).
$pdfRelPath = $constitution ? ltrim(str_replace('\\', '/', $constitution['pdf_path']), '/') : null;

// Absolute disk path — __DIR__ is this file's directory (site root).
// str_replace handles Windows __DIR__ backslashes.
$siteRoot   = rtrim(str_replace('\\', '/', __DIR__), '/');
$pdfAbsPath = $pdfRelPath ? $siteRoot . '/' . $pdfRelPath : null;
$pdfExists  = $pdfAbsPath && file_exists($pdfAbsPath);

// Build a fully-qualified URL so PDF.js (running in browser) can fetch the file.
// Works on localhost, cPanel subdirectory, or any domain.
if ($pdfExists) {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'];
    // SCRIPT_NAME is e.g. /04sl/club_constitutions.php  → base = /04sl
    $base    = rtrim(dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])), '/');
    $pdfUrl  = $scheme . '://' . $host . $base . '/' . $pdfRelPath;
} else {
    $pdfUrl  = null;
}
$pdfUrlHtml = $pdfUrl ? htmlspecialchars($pdfUrl) : null;
?>

<div class="constitution-wrapper">
    <!-- Page Header -->
    <div class="constitution-page-header">
        <h1><?= htmlspecialchars($nav_item['name'] ?? 'Club Constitution') ?></h1>
        <p>Official Documents &amp; Governance</p>
    </div>

    <div class="constitution-layout">

        <!-- ══ SIDEBAR ══ -->
        <aside class="constitution-sidebar">
            <div class="sidebar-title">Documents</div>
            <?php if (empty($allConstitutions)): ?>
                <p style="color:rgba(255,255,255,0.3);font-size:0.82rem;text-align:center;padding:1rem 0;">
                    No documents uploaded yet.
                </p>
            <?php else: ?>
                <?php foreach ($allConstitutions as $c):
                    $isActive   = $constitution && $c['id'] === $constitution['id'];
                    $linkParams = [];
                    // Match back to nav item by title
                    foreach ($constitutionNavItems as $ni) {
                        if (strtolower($ni['name']) === strtolower($c['title'])) {
                            $linkParams = ['nav_id' => $ni['id']];
                            break;
                        }
                    }
                    $href = 'club_constitutions.php' . ($linkParams ? '?nav_id=' . $linkParams['nav_id'] : '?name=' . urlencode($c['title']));
                ?>
                <a href="<?= $href ?>" class="sidebar-item <?= $isActive ? 'active' : '' ?>">
                    <div class="si-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                    <div class="si-info">
                        <span class="si-name"><?= htmlspecialchars($c['title']) ?></span>
                        <span class="si-meta">
                            v<?= htmlspecialchars($c['version']) ?>
                            &bull;
                            <?= date('M Y', strtotime($c['effective_date'])) ?>
                            <?= $c['is_active'] ? ' &bull; <span style="color:var(--gold)">Active</span>' : '' ?>
                        </span>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <!-- ══ MAIN VIEWER ══ -->
        <main>
            <div class="constitution-viewer-card">

                <?php if ($constitution && $pdfExists): ?>

                <!-- Viewer Header -->
                <div class="viewer-header">
                    <div class="viewer-title-group">
                        <div class="viewer-doc-title">
                            <?= htmlspecialchars($constitution['title']) ?>
                        </div>
                        <div class="viewer-doc-meta">
                            <span class="badge-version">v<?= htmlspecialchars($constitution['version']) ?></span>
                            <span><i class="bi bi-calendar3"></i> Effective: <?= date('j M Y', strtotime($constitution['effective_date'])) ?></span>
                            <?php if ($constitution['uploaded_by']): ?>
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars($constitution['uploaded_by']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="viewer-actions">
                        <a href="<?= $pdfUrlHtml ?>" target="_blank" class="btn-viewer btn-viewer-outline">
                            <i class="bi bi-box-arrow-up-right"></i> Open
                        </a>
                        <a href="<?= $pdfUrlHtml ?>" download class="btn-viewer btn-viewer-gold">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                </div>

                <!-- Flipbook Stage -->
                <div class="flipbook-stage">
                    <div class="book-container" id="bookContainer">
                        <div class="book-spread" id="bookSpread">

                            <!-- LEFT PAGE -->
                            <div class="page-wrapper left" id="leftPageWrapper">
                                <div class="book-page" id="leftPage">
                                    <div class="page-loading" id="leftLoading">
                                        <div class="loading-spinner"></div>
                                        <div class="loading-text">Rendering…</div>
                                    </div>
                                    <canvas id="leftCanvas"></canvas>
                                    <div class="page-number-strip" id="leftPageNum"></div>
                                </div>
                            </div>

                            <!-- RIGHT PAGE (desktop only in two-page spread) -->
                            <div class="page-wrapper right desktop-only-page" id="rightPageWrapper">
                                <div class="book-page" id="rightPage">
                                    <div class="page-loading" id="rightLoading">
                                        <div class="loading-spinner"></div>
                                        <div class="loading-text">Rendering…</div>
                                    </div>
                                    <canvas id="rightCanvas"></canvas>
                                    <div class="page-number-strip" id="rightPageNum"></div>
                                </div>
                            </div>

                        </div><!-- /.book-spread -->
                    </div><!-- /.book-container -->

                    <!-- Controls -->
                    <div class="flipbook-controls">
                        <button class="ctrl-btn" id="btnFirst" title="First page">
                            <i class="bi bi-skip-backward-fill"></i>
                        </button>
                        <button class="ctrl-btn" id="btnPrev" title="Previous page">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="ctrl-page-info">
                            <strong id="currentPageDisplay">—</strong>
                            <div id="totalPageDisplay" style="font-size:0.75rem;margin-top:2px;"></div>
                        </div>
                        <button class="ctrl-btn" id="btnNext" title="Next page">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <button class="ctrl-btn" id="btnLast" title="Last page">
                            <i class="bi bi-skip-forward-fill"></i>
                        </button>
                        <div class="ctrl-jump">
                            <input type="number" id="jumpInput" min="1" placeholder="pg">
                            <button id="btnJump">Go</button>
                        </div>
                    </div>
                    <div class="swipe-hint">← Swipe or use arrows →</div>
                </div>

                <!-- Description -->
                <?php if ($constitution['description']): ?>
                <div style="padding:1.2rem 1.8rem;border-top:1px solid rgba(255,255,255,0.06);">
                    <p style="font-family:'DM Sans',sans-serif;font-size:0.88rem;color:rgba(255,255,255,0.4);margin:0;line-height:1.6;">
                        <i class="bi bi-info-circle me-1" style="color:var(--gold);"></i>
                        <?= htmlspecialchars($constitution['description']) ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php elseif ($constitution && !$pdfExists): ?>
                <!-- File missing -->
                <div class="constitution-not-found">
                    <div class="nf-icon"><i class="bi bi-file-earmark-x"></i></div>
                    <h3>PDF File Not Found</h3>
                    <p>The PDF for "<?= htmlspecialchars($constitution['title']) ?>" is no longer accessible.<br>
                       Please contact the administrator.</p>
                </div>

                <?php else: ?>
                <!-- No constitution matched -->
                <div class="constitution-not-found">
                    <div class="nf-icon"><i class="bi bi-journal-bookmark"></i></div>
                    <h3><?= htmlspecialchars($nav_item['name'] ?? 'Constitution Not Found') ?></h3>
                    <p>No document has been uploaded for this section yet.<br>
                       Please check back later or contact the league administrator.</p>
                </div>
                <?php endif; ?>

            </div><!-- /.constitution-viewer-card -->
        </main>

    </div><!-- /.constitution-layout -->
</div><!-- /.constitution-wrapper -->

<?php if ($pdfExists && $pdfUrl): ?>
<!-- PDF.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    'use strict';

    // ── Config ────────────────────────────────────────
    const PDF_URL       = <?= json_encode($pdfUrl) ?>;  // absolute URL, works on any host
    const WORKER_SRC    = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    pdfjsLib.GlobalWorkerOptions.workerSrc = WORKER_SRC;

    // ── DOM refs ──────────────────────────────────────
    const leftCanvas       = document.getElementById('leftCanvas');
    const rightCanvas      = document.getElementById('rightCanvas');
    const leftCtx          = leftCanvas.getContext('2d');
    const rightCtx         = rightCanvas.getContext('2d');
    const leftLoading      = document.getElementById('leftLoading');
    const rightLoading     = document.getElementById('rightLoading');
    const leftPageNum      = document.getElementById('leftPageNum');
    const rightPageNum     = document.getElementById('rightPageNum');
    const leftWrapper      = document.getElementById('leftPageWrapper');
    const rightWrapper     = document.getElementById('rightPageWrapper');
    const currentDisplay   = document.getElementById('currentPageDisplay');
    const totalDisplay     = document.getElementById('totalPageDisplay');
    const btnFirst         = document.getElementById('btnFirst');
    const btnPrev          = document.getElementById('btnPrev');
    const btnNext          = document.getElementById('btnNext');
    const btnLast          = document.getElementById('btnLast');
    const jumpInput        = document.getElementById('jumpInput');
    const btnJump          = document.getElementById('btnJump');
    const bookSpread       = document.getElementById('bookSpread');

    // ── State ─────────────────────────────────────────
    let pdfDoc       = null;
    let totalPages   = 0;
    let currentSpread = 0;   // 0-indexed spread (pair of pages)
    let isAnimating  = false;
    let isMobile     = window.innerWidth < 769;

    // Spread 0 = cover (page 1) + blank OR page 1 mobile
    // Spread N covers pages: leftPage = 2N+1, rightPage = 2N+2

    // ── Helpers ───────────────────────────────────────
    function spreadToPages(spreadIndex) {
        if (isMobile) {
            return { left: spreadIndex + 1, right: null };
        }
        if (spreadIndex === 0) {
            return { left: 1, right: 2 };
        }
        const left  = spreadIndex * 2 + 1;
        const right = left + 1;
        return { left, right };
    }

    function maxSpread() {
        if (isMobile) return totalPages - 1;
        return Math.ceil(totalPages / 2) - 1;
    }

    // ── Render a single page to a canvas ─────────────
    async function renderPageToCanvas(pageNum, canvas, ctx, loadingEl, numEl) {
        if (pageNum < 1 || pageNum > totalPages) {
            // blank page
            loadingEl.style.display = 'none';
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            canvas.style.display = 'none';
            numEl.textContent = '';
            return;
        }
        loadingEl.style.display = 'flex';
        canvas.style.display = 'none';

        try {
            const page     = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: window.devicePixelRatio >= 2 ? 2.5 : 1.8 });

            canvas.width  = viewport.width;
            canvas.height = viewport.height;

            await page.render({ canvasContext: ctx, viewport }).promise;

            loadingEl.style.display = 'none';
            canvas.style.display    = 'block';
            numEl.textContent        = pageNum;
        } catch (err) {
            loadingEl.innerHTML = '<div style="font-size:0.8rem;color:#c0392b;text-align:center;padding:1rem;">Failed to render page.</div>';
            console.error('PDF render error:', err);
        }
    }

    // ── Render current spread ─────────────────────────
    async function renderSpread(spreadIndex) {
        const { left, right } = spreadToPages(spreadIndex);

        // Update UI info
        if (isMobile) {
            currentDisplay.textContent = 'Page ' + left;
            totalDisplay.textContent   = 'of ' + totalPages;
            jumpInput.max              = totalPages;
        } else {
            const rLabel = right <= totalPages ? '–' + right : '';
            currentDisplay.textContent = left + (rLabel || '');
            totalDisplay.textContent   = 'of ' + totalPages + ' pages';
            jumpInput.max              = totalPages;
        }

        // Control states
        btnFirst.disabled = spreadIndex === 0;
        btnPrev.disabled  = spreadIndex === 0;
        btnNext.disabled  = spreadIndex >= maxSpread();
        btnLast.disabled  = spreadIndex >= maxSpread();

        // Render pages
        await Promise.all([
            renderPageToCanvas(left,  leftCanvas,  leftCtx,  leftLoading,  leftPageNum),
            right
                ? renderPageToCanvas(right, rightCanvas, rightCtx, rightLoading, rightPageNum)
                : (() => {
                    rightLoading.style.display = 'none';
                    rightCanvas.style.display  = 'none';
                    rightPageNum.textContent   = '';
                    return Promise.resolve();
                  })()
        ]);
    }

    // ── Flip animation ────────────────────────────────
    function flipTo(newSpread, direction) {
        if (isAnimating || newSpread === currentSpread) return;
        if (newSpread < 0 || newSpread > maxSpread()) return;
        isAnimating = true;

        const outClass = direction === 'next' ? 'flip-out-left' : 'flip-out-right';
        const inClass  = direction === 'next' ? 'flip-in-right' : 'flip-in-left';

        // Apply out animation
        leftWrapper.classList.add(outClass);
        if (!isMobile) rightWrapper.classList.add(outClass);

        setTimeout(async () => {
            // Remove out animation
            leftWrapper.classList.remove(outClass);
            if (!isMobile) rightWrapper.classList.remove(outClass);

            // Update spread and render
            currentSpread = newSpread;
            await renderSpread(currentSpread);

            // Apply in animation
            leftWrapper.classList.add(inClass);
            if (!isMobile) rightWrapper.classList.add(inClass);

            setTimeout(() => {
                leftWrapper.classList.remove(inClass);
                if (!isMobile) rightWrapper.classList.remove(inClass);
                isAnimating = false;
            }, 450);
        }, 450);
    }

    // ── Control bindings ──────────────────────────────
    btnFirst.addEventListener('click', () => flipTo(0, 'prev'));
    btnPrev.addEventListener('click',  () => flipTo(currentSpread - 1, 'prev'));
    btnNext.addEventListener('click',  () => flipTo(currentSpread + 1, 'next'));
    btnLast.addEventListener('click',  () => flipTo(maxSpread(), 'next'));

    btnJump.addEventListener('click', () => {
        const pg = parseInt(jumpInput.value, 10);
        if (!pg || pg < 1 || pg > totalPages) return;
        let targetSpread = isMobile ? pg - 1 : Math.floor((pg - 1) / 2);
        const dir = targetSpread > currentSpread ? 'next' : 'prev';
        flipTo(targetSpread, dir);
        jumpInput.value = '';
    });
    jumpInput.addEventListener('keydown', e => { if (e.key === 'Enter') btnJump.click(); });

    // Keyboard navigation
    document.addEventListener('keydown', e => {
        if (['ArrowLeft', 'ArrowUp'].includes(e.key))  flipTo(currentSpread - 1, 'prev');
        if (['ArrowRight', 'ArrowDown'].includes(e.key)) flipTo(currentSpread + 1, 'next');
    });

    // ── Touch/Swipe ───────────────────────────────────
    let touchStartX = 0;
    bookSpread.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    bookSpread.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? flipTo(currentSpread + 1, 'next') : flipTo(currentSpread - 1, 'prev');
        }
    });

    // ── Responsive handler ────────────────────────────
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const wasMobile = isMobile;
            isMobile = window.innerWidth < 769;
            rightWrapper.style.display = isMobile ? 'none' : '';
            if (wasMobile !== isMobile) {
                // Recalculate spread to not lose position too much
                currentSpread = 0;
                renderSpread(0);
            }
        }, 200);
    });

    // ── Init ──────────────────────────────────────────
    (async function init() {
        try {
            // Initial mobile check for right panel
            if (isMobile) rightWrapper.style.display = 'none';

            const loadingTask = pdfjsLib.getDocument(PDF_URL);
            pdfDoc       = await loadingTask.promise;
            totalPages   = pdfDoc.numPages;
            jumpInput.max = totalPages;

            await renderSpread(0);
        } catch (err) {
            leftLoading.innerHTML = `
                <div style="text-align:center;padding:2rem;font-family:'DM Sans',sans-serif;">
                    <i class="bi bi-exclamation-triangle" style="font-size:2rem;color:#c0392b;display:block;margin-bottom:0.5rem;"></i>
                    <div style="color:#c0392b;font-size:0.85rem;">Could not load PDF.<br>Try downloading directly.</div>
                </div>`;
            rightLoading.style.display = 'none';
            console.error('PDF load error:', err);
        }
    })();
})();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ob_end_flush(); ?>