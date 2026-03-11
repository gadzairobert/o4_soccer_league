<?php
ob_start();
header('Content-Type: text/html; charset=UTF-8');
require 'config.php';

$nav_id   = (int)($_GET['nav_id'] ?? 0);
$nav_name = trim($_GET['name']    ?? '');

$constitution = null;
$nav_item     = null;

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

if ($nav_id > 0) {
    foreach ($constitutionNavItems as $ni) {
        if ((int)$ni['id'] === $nav_id) { $nav_item = $ni; break; }
    }
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

if ($nav_item) {
    $s = $pdo->prepare("
        SELECT * FROM club_constitutions
        WHERE LOWER(TRIM(title)) = LOWER(TRIM(?))
        ORDER BY is_active DESC, effective_date DESC
        LIMIT 1
    ");
    $s->execute([$nav_item['name']]);
    $constitution = $s->fetch(PDO::FETCH_ASSOC) ?: null;
}

if (!$constitution) {
    $constitution = $pdo->query("
        SELECT * FROM club_constitutions
        WHERE is_active = 1
        ORDER BY effective_date DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC) ?: null;
}

if (!$constitution) {
    $constitution = $pdo->query("
        SELECT * FROM club_constitutions
        ORDER BY effective_date DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($constitution && !$nav_item) {
    $nav_item = ['name' => $constitution['title']];
}

$allConstitutions = $pdo->query("
    SELECT * FROM club_constitutions
    ORDER BY is_active DESC, effective_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$page_title = ($nav_item['name'] ?? 'Club Constitution') . ' — ' . ($league_name ?? 'League');

include 'includes/header.php';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --gold:        #c9a84c;
        --gold-light:  #f0d080;
        --gold-dark:   #9a6f1e;
        --cream:       #fdf8ef;
        --dark-panel:  #1a1a2e;
        --dark-tab:    #16152b;
        --dark-deeper: #0f0e22;
        --border:      rgba(201,168,76,0.22);
        --muted:       #6b7280;
        --text-main:   #1a1a2e;
        --text-soft:   #4b5563;
        /* book page colours stay as-is — they are the document itself */
        --pale:        #f5f0e8;
        --page-border: #ddd6c8;
        --shadow-book: 0 30px 80px rgba(0,0,0,0.25), 0 8px 24px rgba(0,0,0,0.15);
    }

    /* ── PAGE BACKGROUND: LIGHT ── */
    html, body {
        background-color: #f0ede8 !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.07) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(180,160,120,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: var(--text-main);
        margin: 0; padding: 0;
    }

    .constitution-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem 4rem;
    }
    @media (min-width: 992px) {
        .constitution-wrapper { padding-top: 4.5rem; }
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
        color: var(--text-main);
        letter-spacing: -0.5px;
        margin: 0 0 0.5rem;
    }
    .constitution-page-header p {
        font-family: 'DM Sans', sans-serif;
        color: var(--muted);
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

    /* ── Sidebar — LIGHT ── */
    .constitution-sidebar {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 1.4rem;
        position: sticky;
        top: 130px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .sidebar-title {
        font-family: 'Playfair Display', serif;
        color: var(--gold-dark);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 1rem;
        padding-bottom: 0.7rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .sidebar-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 0.75rem 0.8rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-soft);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        transition: all 0.25s;
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .sidebar-item:hover {
        background: #fdf9f0;
        color: var(--gold-dark);
        transform: translateX(3px);
    }
    .sidebar-item.active {
        background: rgba(201,168,76,0.1);
        color: var(--gold-dark);
        font-weight: 600;
        border-left: 3px solid var(--gold);
    }
    .sidebar-item .si-icon {
        width: 28px; height: 28px;
        background: rgba(201,168,76,0.1);
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; flex-shrink: 0; color: var(--gold-dark);
    }
    .sidebar-item .si-info { flex: 1; min-width: 0; }
    .sidebar-item .si-name { display: block; font-weight: 500; color: var(--text-main); }
    .sidebar-item .si-meta { display: block; font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
    .sidebar-item.active .si-meta { color: var(--gold-dark); }

    /* ── Main Viewer Card — LIGHT ── */
    .constitution-viewer-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }

    /* ── Viewer Header — DARK ── */
    .viewer-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
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
        .viewer-actions { width: 100%; justify-content: flex-start; }
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
        color: var(--dark-panel);
    }
    .btn-viewer-gold:hover {
        background: var(--gold-light);
        color: var(--dark-panel);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(201,168,76,0.35);
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

    /* ── Flipbook Stage — slightly darker than page bg to frame the book ── */
    .flipbook-stage {
        background: #e4dfd8;
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
        background: radial-gradient(ellipse at 50% 0%, rgba(201,168,76,0.06) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ── Book Container ── */
    .book-container {
        position: relative;
        width: 100%;
        max-width: 900px;
        perspective: 2000px;
    }
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

    .page-wrapper.flip-out-left  { animation: flipOutLeft  0.45s ease-in  forwards; }
    .page-wrapper.flip-in-left   { animation: flipInLeft   0.45s ease-out forwards; }
    .page-wrapper.flip-out-right { animation: flipOutRight 0.45s ease-in  forwards; }
    .page-wrapper.flip-in-right  { animation: flipInRight  0.45s ease-out forwards; }

    @keyframes flipOutLeft  { from { transform: rotateY(0);     opacity:1; } to { transform: rotateY(-90deg); opacity:0; } }
    @keyframes flipInLeft   { from { transform: rotateY(90deg); opacity:0; } to { transform: rotateY(0);      opacity:1; } }
    @keyframes flipOutRight { from { transform: rotateY(0);     opacity:1; } to { transform: rotateY(90deg);  opacity:0; } }
    @keyframes flipInRight  { from { transform: rotateY(-90deg);opacity:0; } to { transform: rotateY(0);      opacity:1; } }

    /* Book page — cream paper colour unchanged (it IS the document) */
    .book-page {
        width: 100%;
        aspect-ratio: 3/4;
        background: var(--pale);
        border: 1px solid var(--page-border);
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
    .book-page canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }

    /* Cover page — intentionally kept dark (it's a decorative element) */
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
        border: 1px solid var(--page-border);
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

    /* Loading overlay — light */
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
        border: 3px solid #e5e7eb;
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

    /* ── Controls — keep on dark stage ── */
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
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold-dark);
        font-size: 1.2rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.25s;
        flex-shrink: 0;
    }
    .ctrl-btn:hover:not(:disabled) {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
        transform: scale(1.1);
        box-shadow: 0 4px 20px rgba(201,168,76,0.2);
    }
    .ctrl-btn:disabled { opacity: 0.25; cursor: not-allowed; transform: none; }
    .ctrl-page-info {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        color: var(--text-soft);
        text-align: center;
        min-width: 120px;
    }
    .ctrl-page-info strong { color: var(--gold-dark); font-size: 1.1rem; }

    /* Page jump input */
    .ctrl-jump { display: flex; align-items: center; gap: 8px; }
    .ctrl-jump input {
        width: 60px;
        background: rgba(255,255,255,0.6);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--text-main);
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
        color: var(--gold-dark);
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .ctrl-jump button:hover { background: rgba(201,168,76,0.25); }

    /* Mobile */
    @media (max-width: 768px) {
        .page-wrapper { flex: 0 0 100%; max-width: 100%; }
        .book-spread { gap: 0; }
        .page-wrapper.right.desktop-only-page { display: none; }
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
        color: rgba(154,111,30,0.35);
        margin-bottom: 1rem;
    }
    .constitution-not-found h3 {
        font-family: 'Playfair Display', serif;
        color: var(--text-main);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .constitution-not-found p {
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
    }

    /* ── Description footer — LIGHT ── */
    .viewer-description-footer {
        padding: 1.2rem 1.8rem;
        border-top: 1px solid #e5e7eb;
        background: #f9f7f2;
    }
    .viewer-description-footer p {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        color: var(--muted);
        margin: 0;
        line-height: 1.6;
    }

    /* ── Swipe hint ── */
    .swipe-hint {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        color: var(--muted);
        text-align: center;
        margin-top: 0.8rem;
        display: none;
    }
    @media (max-width: 768px) { .swipe-hint { display: block; } }
</style>

<?php
$pdfRelPath = $constitution ? ltrim(str_replace('\\', '/', $constitution['pdf_path']), '/') : null;
$siteRoot   = rtrim(str_replace('\\', '/', __DIR__), '/');
$pdfAbsPath = $pdfRelPath ? $siteRoot . '/' . $pdfRelPath : null;
$pdfExists  = $pdfAbsPath && file_exists($pdfAbsPath);

if ($pdfExists) {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'];
    $base    = rtrim(dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])), '/');
    $pdfUrl  = $scheme . '://' . $host . $base . '/' . $pdfRelPath;
} else {
    $pdfUrl  = null;
}
$pdfUrlHtml = $pdfUrl ? htmlspecialchars($pdfUrl) : null;
?>

<div class="constitution-wrapper">
    <div class="constitution-page-header">
        <h1><?= htmlspecialchars($nav_item['name'] ?? 'Club Constitution') ?></h1>
        <p>Official Documents &amp; Governance</p>
    </div>

    <div class="constitution-layout">

        <!-- ══ SIDEBAR ══ -->
        <aside class="constitution-sidebar">
            <div class="sidebar-title">Documents</div>
            <?php if (empty($allConstitutions)): ?>
                <p style="color:var(--muted);font-size:0.82rem;text-align:center;padding:1rem 0;">
                    No documents uploaded yet.
                </p>
            <?php else: ?>
                <?php foreach ($allConstitutions as $c):
                    $isActive   = $constitution && $c['id'] === $constitution['id'];
                    $linkParams = [];
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
                            <?= $c['is_active'] ? ' &bull; <span style="color:var(--gold-dark)">Active</span>' : '' ?>
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

                <div class="flipbook-stage">
                    <div class="book-container" id="bookContainer">
                        <div class="book-spread" id="bookSpread">

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

                        </div>
                    </div>

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

                <?php if ($constitution['description']): ?>
                <div class="viewer-description-footer">
                    <p>
                        <i class="bi bi-info-circle me-1" style="color:var(--gold);"></i>
                        <?= htmlspecialchars($constitution['description']) ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php elseif ($constitution && !$pdfExists): ?>
                <div class="constitution-not-found">
                    <div class="nf-icon"><i class="bi bi-file-earmark-x"></i></div>
                    <h3>PDF File Not Found</h3>
                    <p>The PDF for "<?= htmlspecialchars($constitution['title']) ?>" is no longer accessible.<br>
                       Please contact the administrator.</p>
                </div>

                <?php else: ?>
                <div class="constitution-not-found">
                    <div class="nf-icon"><i class="bi bi-journal-bookmark"></i></div>
                    <h3><?= htmlspecialchars($nav_item['name'] ?? 'Constitution Not Found') ?></h3>
                    <p>No document has been uploaded for this section yet.<br>
                       Please check back later or contact the league administrator.</p>
                </div>
                <?php endif; ?>

            </div>
        </main>

    </div>
</div>

<?php if ($pdfExists && $pdfUrl): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    'use strict';

    const PDF_URL    = <?= json_encode($pdfUrl) ?>;
    const WORKER_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    pdfjsLib.GlobalWorkerOptions.workerSrc = WORKER_SRC;

    const leftCanvas     = document.getElementById('leftCanvas');
    const rightCanvas    = document.getElementById('rightCanvas');
    const leftCtx        = leftCanvas.getContext('2d');
    const rightCtx       = rightCanvas.getContext('2d');
    const leftLoading    = document.getElementById('leftLoading');
    const rightLoading   = document.getElementById('rightLoading');
    const leftPageNum    = document.getElementById('leftPageNum');
    const rightPageNum   = document.getElementById('rightPageNum');
    const leftWrapper    = document.getElementById('leftPageWrapper');
    const rightWrapper   = document.getElementById('rightPageWrapper');
    const currentDisplay = document.getElementById('currentPageDisplay');
    const totalDisplay   = document.getElementById('totalPageDisplay');
    const btnFirst       = document.getElementById('btnFirst');
    const btnPrev        = document.getElementById('btnPrev');
    const btnNext        = document.getElementById('btnNext');
    const btnLast        = document.getElementById('btnLast');
    const jumpInput      = document.getElementById('jumpInput');
    const btnJump        = document.getElementById('btnJump');
    const bookSpread     = document.getElementById('bookSpread');

    let pdfDoc       = null;
    let totalPages   = 0;
    let currentSpread = 0;
    let isAnimating  = false;
    let isMobile     = window.innerWidth < 769;

    function spreadToPages(spreadIndex) {
        if (isMobile) return { left: spreadIndex + 1, right: null };
        if (spreadIndex === 0) return { left: 1, right: 2 };
        const left = spreadIndex * 2 + 1;
        return { left, right: left + 1 };
    }

    function maxSpread() {
        return isMobile ? totalPages - 1 : Math.ceil(totalPages / 2) - 1;
    }

    async function renderPageToCanvas(pageNum, canvas, ctx, loadingEl, numEl) {
        if (pageNum < 1 || pageNum > totalPages) {
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
            loadingEl.innerHTML = '<div style="font-size:0.8rem;color:#dc2626;text-align:center;padding:1rem;">Failed to render page.</div>';
            console.error('PDF render error:', err);
        }
    }

    async function renderSpread(spreadIndex) {
        const { left, right } = spreadToPages(spreadIndex);
        if (isMobile) {
            currentDisplay.textContent = 'Page ' + left;
            totalDisplay.textContent   = 'of ' + totalPages;
        } else {
            const rLabel = right <= totalPages ? '–' + right : '';
            currentDisplay.textContent = left + (rLabel || '');
            totalDisplay.textContent   = 'of ' + totalPages + ' pages';
        }
        jumpInput.max = totalPages;
        btnFirst.disabled = spreadIndex === 0;
        btnPrev.disabled  = spreadIndex === 0;
        btnNext.disabled  = spreadIndex >= maxSpread();
        btnLast.disabled  = spreadIndex >= maxSpread();
        await Promise.all([
            renderPageToCanvas(left, leftCanvas, leftCtx, leftLoading, leftPageNum),
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

    function flipTo(newSpread, direction) {
        if (isAnimating || newSpread === currentSpread) return;
        if (newSpread < 0 || newSpread > maxSpread()) return;
        isAnimating = true;
        const outClass = direction === 'next' ? 'flip-out-left'  : 'flip-out-right';
        const inClass  = direction === 'next' ? 'flip-in-right'  : 'flip-in-left';
        leftWrapper.classList.add(outClass);
        if (!isMobile) rightWrapper.classList.add(outClass);
        setTimeout(async () => {
            leftWrapper.classList.remove(outClass);
            if (!isMobile) rightWrapper.classList.remove(outClass);
            currentSpread = newSpread;
            await renderSpread(currentSpread);
            leftWrapper.classList.add(inClass);
            if (!isMobile) rightWrapper.classList.add(inClass);
            setTimeout(() => {
                leftWrapper.classList.remove(inClass);
                if (!isMobile) rightWrapper.classList.remove(inClass);
                isAnimating = false;
            }, 450);
        }, 450);
    }

    btnFirst.addEventListener('click', () => flipTo(0, 'prev'));
    btnPrev.addEventListener('click',  () => flipTo(currentSpread - 1, 'prev'));
    btnNext.addEventListener('click',  () => flipTo(currentSpread + 1, 'next'));
    btnLast.addEventListener('click',  () => flipTo(maxSpread(), 'next'));

    btnJump.addEventListener('click', () => {
        const pg = parseInt(jumpInput.value, 10);
        if (!pg || pg < 1 || pg > totalPages) return;
        let targetSpread = isMobile ? pg - 1 : Math.floor((pg - 1) / 2);
        flipTo(targetSpread, targetSpread > currentSpread ? 'next' : 'prev');
        jumpInput.value = '';
    });
    jumpInput.addEventListener('keydown', e => { if (e.key === 'Enter') btnJump.click(); });

    document.addEventListener('keydown', e => {
        if (['ArrowLeft', 'ArrowUp'].includes(e.key))    flipTo(currentSpread - 1, 'prev');
        if (['ArrowRight', 'ArrowDown'].includes(e.key)) flipTo(currentSpread + 1, 'next');
    });

    let touchStartX = 0;
    bookSpread.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    bookSpread.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? flipTo(currentSpread + 1, 'next') : flipTo(currentSpread - 1, 'prev');
        }
    });

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const wasMobile = isMobile;
            isMobile = window.innerWidth < 769;
            rightWrapper.style.display = isMobile ? 'none' : '';
            if (wasMobile !== isMobile) { currentSpread = 0; renderSpread(0); }
        }, 200);
    });

    (async function init() {
        try {
            if (isMobile) rightWrapper.style.display = 'none';
            const loadingTask = pdfjsLib.getDocument(PDF_URL);
            pdfDoc      = await loadingTask.promise;
            totalPages  = pdfDoc.numPages;
            jumpInput.max = totalPages;
            await renderSpread(0);
        } catch (err) {
            leftLoading.innerHTML = `
                <div style="text-align:center;padding:2rem;font-family:'DM Sans',sans-serif;">
                    <i class="bi bi-exclamation-triangle" style="font-size:2rem;color:#dc2626;display:block;margin-bottom:0.5rem;"></i>
                    <div style="color:#dc2626;font-size:0.85rem;">Could not load PDF.<br>Try downloading directly.</div>
                </div>`;
            rightLoading.style.display = 'none';
            console.error('PDF load error:', err);
        }
    })();
})();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ob_end_flush(); ?>