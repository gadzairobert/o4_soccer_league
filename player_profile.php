<?php
ob_start();
require_once 'config.php';
include 'includes/header.php';
include 'includes/properties.php';

$player_id = (int)($_GET['player_id'] ?? 0);
$player = getPlayerById($player_id);

if (!$player) { ?>
    <div class="container py-5 text-center">
        <div style="background:#ffffff;border:1px solid rgba(201,168,76,0.2);border-radius:14px;padding:2.5rem;display:inline-block;box-shadow:0 2px 16px rgba(0,0,0,0.08);">
            <i class="bi bi-person-x" style="font-size:4rem;color:rgba(154,111,30,0.5);display:block;margin-bottom:1rem;"></i>
            <h3 style="font-family:'Playfair Display',serif;color:#1a1a2e;">Player Not Found</h3>
            <p style="color:#6b7280;margin-bottom:1.2rem;">The player with ID <code><?= $player_id ?></code> does not exist.</p>
            <a href="index.php" class="btn" style="background:rgba(201,168,76,0.12);border:1px solid rgba(201,168,76,0.4);color:#9a6f1e;border-radius:50px;padding:0.5rem 2rem;font-weight:600;">Back to Home</a>
        </div>
    </div>
<?php
    include 'includes/footer.php';
    ob_end_flush();
    exit;
}

$currentLeague  = $pdo->query("SELECT id FROM competition_seasons WHERE is_current = 1 AND type = 'league' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$leagueSeasonId = $currentLeague['id'] ?? null;
$leagueStats    = getPlayerLeagueStats($player_id, $leagueSeasonId);
$tournamentStats = getPlayerTournamentStatsByYear($player_id);

$age = 'N/A';
if (!empty($player['date_of_birth']) && $player['date_of_birth'] !== '0000-00-00' && $player['date_of_birth'] !== null) {
    try {
        $dob = new DateTime($player['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y . ' years';
    } catch (Exception $e) {}
}

$displayPhoto    = $player['photo']
    ? "uploads/players/" . htmlspecialchars($player['photo'])
    : "https://via.placeholder.com/500/1a1a2e/c9a84c?text=" . urlencode(substr($player['name'], 0, 2));
$displayClubLogo = $player['club_logo']
    ? "uploads/clubs/" . htmlspecialchars($player['club_logo'])
    : "https://via.placeholder.com/60/1a1a2e/c9a84c?text=" . urlencode(substr($player['club_name'] ?? 'FA', 0, 2));
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
    }

    /* ── PAGE BACKGROUND: LIGHT ── */
    html, body {
        background-color: #f0ede8 !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.07) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(180,160,120,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: var(--text-main);
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .player-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .player-page-wrapper { margin-top: 0; padding: 1rem 0 3rem; width: 100%; }
    }

    /* ── Main Layout ── */
    .main-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: flex-start;
        justify-content: center;
    }
    .profile-section {
        flex: 2 1 620px;
        min-width: 0;
        max-width: 100%;
    }
    .card-section {
        flex: 1 1 380px;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100%;
    }
    @media (max-width: 992px) {
        .main-layout { flex-direction: column; align-items: stretch; gap: 1.6rem; }
        .profile-section, .card-section { max-width: none; width: 100%; }
    }

    /* ── Profile Card — LIGHT body ── */
    .player-profile-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        width: 100%;
    }

    /* ── Section Header — DARK ── */
    .section-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.8rem;
    }
    .section-header h1, .section-header h3 {
        font-family: 'Playfair Display', serif;
        margin: 0;
        font-size: 1.7rem;
        font-weight: 900;
        color: var(--cream);
    }
    .section-header h3 { font-size: 1.1rem; }
    .section-header .position {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--gold);
        letter-spacing: 0.5px;
        margin-top: 3px;
    }
    .jersey-number {
        font-family: 'Playfair Display', serif;
        background: rgba(201,168,76,0.15);
        border: 1px solid rgba(201,168,76,0.4);
        color: var(--gold);
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 1.3rem;
        font-weight: 900;
    }

    /* ── Player Body — LIGHT ── */
    .player-body {
        padding: 1.8rem 2rem 2.2rem;
        text-align: center;
        background: #ffffff;
    }
    @media (max-width: 576px) { .player-body { padding: 1.4rem 1rem; } }

    /* ── Photo ── */
    .player-photo-wrapper {
        width: 240px; height: 240px;
        margin: 0 auto 1.6rem auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.15);
        border: 4px solid var(--border);
        background: #f0ede8;
        transition: border-color 0.3s;
    }
    .player-photo-wrapper:hover { border-color: var(--gold); }
    .player-photo { width: 100%; height: 100%; object-fit: cover; display: block; }
    @media (max-width: 576px) {
        .player-photo-wrapper { width: 190px; height: 190px; }
    }

    /* ── Info Grid — LIGHT ── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.9rem;
        margin-bottom: 1.4rem;
        text-align: left;
    }
    @media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

    .info-item {
        background: #f9f7f2;
        border: 1px solid #e5e7eb;
        border-left: 3px solid var(--gold);
        border-radius: 8px;
        padding: 0.9rem 1rem;
    }
    .info-item strong {
        display: block;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--gold-dark);
        margin-bottom: 5px;
    }
    .info-item span {
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* ── Club Link ── */
    .club-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 12px;
        background: rgba(201,168,76,0.1);
        border: 1px solid rgba(201,168,76,0.3);
        border-radius: 50px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--gold-dark);
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .club-link:hover {
        background: rgba(201,168,76,0.18);
        border-color: var(--gold);
        color: var(--gold-dark);
        transform: translateY(-2px);
    }
    .club-link img { width: 28px; height: 28px; object-fit: contain; border-radius: 50%; }

    /* ── Stats Section — LIGHT ── */
    .stats-section {
        background: #f9f7f2;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.2rem;
        margin-top: 1.2rem;
        text-align: left;
    }
    .stats-section h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.9rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid #e5e7eb;
    }

    /* ── Stats Table — LIGHT with DARK header ── */
    .stats-table {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        width: 100%;
        margin-bottom: 0;
    }
    .stats-table th {
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab)) !important;
        color: var(--gold) !important;
        border-bottom: 1px solid rgba(201,168,76,0.2) !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.6rem 0.7rem;
        text-align: center;
    }
    .stats-table td {
        background: #ffffff !important;
        color: var(--text-main) !important;
        border-bottom: 1px solid #f3f4f6 !important;
        padding: 0.6rem 0.7rem;
        text-align: center;
    }
    .stats-table tbody tr:hover { background: #fdf9f0 !important; }
    .stats-table tbody tr:last-child td { border-bottom: none !important; }

    /* Stat colours — adjusted for light bg */
    .text-success { color: #15803d !important; }
    .text-info    { color: #0e7490 !important; }
    .text-warning { color: #b45309 !important; }
    .text-danger  { color: #dc2626 !important; }
    .text-primary { color: var(--gold-dark) !important; }
    .text-muted   { color: var(--muted) !important; }

    /* ── Official Card Container ── */
    .card-container {
        width: 100%;
        max-width: 460px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }

    #player-card-preview {
        width: 100%;
        height: 600px;
        border: none;
        background: #f9f7f2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 576px) { #player-card-preview { height: 480px; } }

    /* ── Action Buttons ── */
    .action-buttons {
        margin-top: 1.6rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.6rem;
        justify-content: center;
    }
    .action-btn {
        flex: 1 1 110px;
        min-width: 100px;
        max-width: 130px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        transition: transform 0.3s ease, color 0.2s ease;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .action-btn:hover { transform: translateY(-5px); color: var(--text-main); }
    .action-btn i {
        font-size: 2.8rem;
        width: 82px; height: 82px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: background 0.25s, border-color 0.25s, box-shadow 0.25s;
    }
    .action-btn:hover i {
        background: #fdf9f0;
        border-color: rgba(201,168,76,0.4);
        box-shadow: 0 6px 20px rgba(201,168,76,0.15);
    }

    #share-fallback {
        margin-top: 1rem;
        text-align: center;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        display: none;
    }

    /* ── Admin Modal — stays DARK (intentional contrast) ── */
    #admin-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #admin-modal-overlay.active { display: flex; }

    #admin-modal {
        background: linear-gradient(145deg, #1e1c38, #2a2850);
        border: 1px solid rgba(201,168,76,0.35);
        border-radius: 18px;
        padding: 2.4rem 2.2rem 2rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 30px 70px rgba(0,0,0,0.6);
        text-align: center;
        position: relative;
        animation: modal-in 0.25s ease;
    }
    @keyframes modal-in {
        from { opacity: 0; transform: scale(0.92) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    #admin-modal .modal-lock-icon {
        font-size: 3rem;
        color: var(--gold);
        margin-bottom: 1rem;
        display: block;
    }
    #admin-modal h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--cream);
        margin-bottom: 0.4rem;
    }
    #admin-modal p.subtitle {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        color: rgba(255,255,255,0.45);
        margin-bottom: 1.6rem;
    }
    #admin-modal .field {
        margin-bottom: 1rem;
        text-align: left;
    }
    #admin-modal label {
        display: block;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--gold);
        margin-bottom: 6px;
    }
    #admin-modal input {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(201,168,76,0.3);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        color: #fff;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    #admin-modal input:focus { border-color: var(--gold); }

    #admin-modal-error {
        display: none;
        background: rgba(248,113,113,0.12);
        border: 1px solid rgba(248,113,113,0.4);
        border-radius: 8px;
        color: #f87171;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        padding: 0.65rem 1rem;
        margin-bottom: 1rem;
        text-align: left;
    }

    .modal-btn-row {
        display: flex;
        gap: 0.8rem;
        margin-top: 0.6rem;
    }
    .modal-btn {
        flex: 1;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s, transform 0.15s;
    }
    .modal-btn:active { transform: scale(0.97); }
    .modal-btn-primary {
        background: linear-gradient(135deg, #c9a84c, #f0d080);
        color: #1a1a2e;
    }
    .modal-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .modal-btn-cancel {
        background: rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.45);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .modal-btn-cancel:hover { background: rgba(255,255,255,0.12); color: #fff; }

    #admin-modal .close-btn {
        position: absolute;
        top: 14px; right: 16px;
        background: none;
        border: none;
        color: rgba(255,255,255,0.45);
        font-size: 1.4rem;
        cursor: pointer;
        line-height: 1;
        transition: color 0.2s;
    }
    #admin-modal .close-btn:hover { color: #fff; }

    /* ── Mobile Overrides ── */
    @media (max-width: 576px) {
        .player-profile-card, .card-container { border-radius: 0; border-left: none; border-right: none; }
        .section-header { padding: 1rem; flex-direction: column; text-align: center; }
        .section-header h1 { font-size: 1.5rem; }
        .action-btn i { width: 72px; height: 72px; font-size: 2.4rem; }
        #admin-modal { margin: 1rem; padding: 1.8rem 1.4rem 1.6rem; }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- ── Admin Auth Modal ─────────────────────────────────────────────────────── -->
<div id="admin-modal-overlay">
    <div id="admin-modal" role="dialog" aria-modal="true" aria-labelledby="admin-modal-title">
        <button class="close-btn" id="modal-close-btn" aria-label="Close">&times;</button>
        <i class="bi bi-shield-lock-fill modal-lock-icon"></i>
        <h4 id="admin-modal-title">Admin Verification Required</h4>
        <p class="subtitle">Enter your admin credentials to download this player card with the ID number visible.</p>

        <div id="admin-modal-error" role="alert"></div>

        <div class="field">
            <label for="admin-username">Username or Email</label>
            <input type="text" id="admin-username" autocomplete="username" placeholder="admin@example.com">
        </div>
        <div class="field">
            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" autocomplete="current-password" placeholder="••••••••">
        </div>

        <div class="modal-btn-row">
            <button class="modal-btn modal-btn-cancel" id="modal-cancel-btn">Cancel</button>
            <button class="modal-btn modal-btn-primary" id="modal-confirm-btn">Verify &amp; Download</button>
        </div>
    </div>
</div>
<!-- ─────────────────────────────────────────────────────────────────────────── -->

<div class="player-page-wrapper container">
    <div class="main-layout">

        <!-- ── Left: Player Profile ── -->
        <div class="profile-section">
            <div class="player-profile-card">
                <div class="section-header">
                    <div>
                        <h1><?= htmlspecialchars($player['name']) ?></h1>
                        <div class="position"><?= htmlspecialchars($player['position'] ?? 'Unknown') ?></div>
                    </div>
                    <div class="jersey-number">#<?= htmlspecialchars($player['jersey_number'] ?? '-') ?></div>
                </div>

                <div class="player-body">
                    <div class="player-photo-wrapper">
                        <img src="<?= $displayPhoto ?>"
                             alt="<?= htmlspecialchars($player['name']) ?>"
                             class="player-photo"
                             onerror="this.src='https://via.placeholder.com/500/1a1a2e/c9a84c?text=<?= urlencode(substr($player['name'], 0, 2)) ?>'">
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Full Name</strong>
                            <span><?= htmlspecialchars($player['name']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Age</strong>
                            <span><?= $age ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Current Club</strong>
                            <?php if ($player['club_name']): ?>
                                <a href="clubs.php?club_id=<?= $player['club_id'] ?>" class="club-link">
                                    <img src="<?= $displayClubLogo ?>" alt="<?= htmlspecialchars($player['club_name']) ?>">
                                    <?= htmlspecialchars($player['club_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Free Agent</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- League Stats -->
                    <div class="stats-section">
                        <h4>League Stats <?= date('Y') ?></h4>
                        <div class="table-responsive">
                            <table class="table stats-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Goals</th>
                                        <th>Assists</th>
                                        <th>YC</th>
                                        <th>RC</th>
                                        <?php if (in_array(strtoupper($player['position'] ?? ''), ['GK','GOALKEEPER'])): ?>
                                            <th>CS</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-success fw-bold"><?= $leagueStats['goals'] ?></td>
                                        <td class="text-info fw-bold"><?= $leagueStats['assists'] ?></td>
                                        <td class="text-warning fw-bold"><?= $leagueStats['yellow_cards'] ?></td>
                                        <td class="text-danger fw-bold"><?= $leagueStats['red_cards'] ?></td>
                                        <?php if (in_array(strtoupper($player['position'] ?? ''), ['GK','GOALKEEPER'])): ?>
                                            <td class="text-primary fw-bold"><?= $leagueStats['clean_sheets'] ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tournament Stats -->
                    <?php if (!empty($tournamentStats)): ?>
                        <div class="stats-section mt-3">
                            <h4>Tournament Stats</h4>
                            <div class="table-responsive">
                                <table class="table stats-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Year</th><th>Goals</th><th>Assists</th><th>YC</th><th>RC</th><th>CS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tournamentStats as $stat): ?>
                                            <tr>
                                                <td class="fw-bold" style="color:var(--gold-dark);"><?= $stat['year'] ?></td>
                                                <td class="text-success fw-bold"><?= (int)$stat['goals'] ?></td>
                                                <td class="text-info fw-bold"><?= (int)$stat['assists'] ?></td>
                                                <td class="text-warning fw-bold"><?= (int)$stat['yellow_cards'] ?></td>
                                                <td class="text-danger fw-bold"><?= (int)$stat['red_cards'] ?></td>
                                                <td class="text-primary fw-bold"><?= (int)$stat['clean_sheets'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Right: Official Player Card ── -->
        <div class="card-section">
            <div class="card-container">
                <div class="section-header">
                    <h3>Official Player Card</h3>
                    <span style="font-family:'DM Sans',sans-serif;font-size:0.78rem;color:rgba(255,255,255,0.45);display:flex;align-items:center;gap:5px;">
                        <i class="bi bi-eye-slash-fill" style="color:var(--gold);"></i> ID hidden in preview
                    </span>
                </div>
                <div id="player-card-preview">
                    <p style="font-family:'DM Sans',sans-serif;font-size:0.88rem;color:var(--muted);text-align:center;padding:3rem 1rem;">Loading card preview…</p>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" class="action-btn" data-action="download" data-format="pdf">
                    <i class="bi bi-file-earmark-pdf" style="color:#dc2626;"></i>
                    <span>PDF</span>
                </button>
                <button type="button" class="action-btn" data-action="download" data-format="png">
                    <i class="bi bi-image" style="color:#0e7490;"></i>
                    <span>PNG</span>
                </button>
                <button type="button" class="action-btn" data-action="share" id="share-general">
                    <i class="bi bi-share-fill" style="color:var(--gold-dark);"></i>
                    <span>Share</span>
                </button>
            </div>

            <div id="share-fallback">
                <p>Download PNG and share manually if needed.</p>
            </div>
        </div>

    </div><!-- /.main-layout -->
</div><!-- /.player-page-wrapper -->

<script>
(function () {
    const PLAYER_ID  = <?= (int)$player_id ?>;
    const playerName = <?= json_encode($player['name']) ?>;
    const clubName   = <?= json_encode($player['club_name'] ?? 'Free Agent') ?>;
    const caption    = `Official Player Card\n${playerName}\n${clubName}\nWWW.04SL.ONLINE`;

    // ── Blurred preview (public, no auth) ────────────────────────────────────
    const preview = document.getElementById('player-card-preview');
    const img = document.createElement('img');
    img.src = `generate_card.php?player_id=${PLAYER_ID}&format=png&blur=1`;
    img.alt = 'Official Player Card Preview';
    img.style.cssText = 'width:100%;height:100%;object-fit:contain;background:transparent;display:block;';
    img.onload  = () => { preview.innerHTML = ''; preview.appendChild(img); };
    img.onerror = () => {
        preview.innerHTML = '<p style="color:#dc2626;text-align:center;padding:3rem 1rem;font-family:\'DM Sans\',sans-serif;">Preview unavailable<br><small style="color:var(--muted);">Use the download buttons below</small></p>';
    };

    // ── Modal state ───────────────────────────────────────────────────────────
    const overlay    = document.getElementById('admin-modal-overlay');
    const errorBox   = document.getElementById('admin-modal-error');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const cancelBtn  = document.getElementById('modal-cancel-btn');
    const closeBtn   = document.getElementById('modal-close-btn');
    const usernameIn = document.getElementById('admin-username');
    const passwordIn = document.getElementById('admin-password');

    let pendingAction = null;
    let cachedToken   = null;

    function openModal(action) {
        pendingAction = action;
        errorBox.style.display = 'none';
        usernameIn.value = '';
        passwordIn.value = '';
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Verify & Download';
        overlay.classList.add('active');
        setTimeout(() => usernameIn.focus(), 50);
    }

    function closeModal() {
        overlay.classList.remove('active');
        pendingAction = null;
    }

    cancelBtn.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
    });

    passwordIn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') confirmBtn.click();
    });

    document.querySelectorAll('.action-btn[data-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = { type: btn.dataset.action, format: btn.dataset.format || 'png' };
            if (cachedToken) {
                executePendingAction(action, cachedToken);
            } else {
                openModal(action);
            }
        });
    });

    confirmBtn.addEventListener('click', async () => {
        const username = usernameIn.value.trim();
        const password = passwordIn.value;

        if (!username || !password) {
            showError('Please enter both username and password.');
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Verifying…';

        try {
            const fd = new FormData();
            fd.append('username', username);
            fd.append('password', password);

            const res  = await fetch('verify_admin.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success && data.token) {
                cachedToken = data.token;
                setTimeout(() => { cachedToken = null; }, 4.5 * 60 * 1000);

                closeModal();
                executePendingAction(pendingAction, cachedToken);
            } else {
                showError(data.message || 'Authentication failed. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Verify & Download';
                passwordIn.value = '';
                passwordIn.focus();
            }
        } catch (err) {
            showError('Network error. Please check your connection and try again.');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Verify & Download';
        }
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.style.display = 'block';
    }

    function executePendingAction(action, token) {
        if (!action) return;
        if (action.type === 'download') {
            triggerDownload(action.format, token);
        } else if (action.type === 'share') {
            triggerShare(token);
        }
    }

    function buildUrl(format, token) {
        return `generate_card.php?player_id=${PLAYER_ID}&format=${format}&token=${encodeURIComponent(token)}`;
    }

    function triggerDownload(format, token) {
        const url = buildUrl(format, token);
        const a = document.createElement('a');
        a.href = url;
        a.download = `04SL_Player_Card_${PLAYER_ID}.${format}`;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    async function triggerShare(token) {
        const url = buildUrl('png', token);
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Fetch failed');
            const blob = await res.blob();
            const file = new File([blob], `04SL_Player_Card_${playerName.replace(/[^a-zA-Z0-9]/g, '_')}.png`, { type: 'image/png' });
            const shareData = { files: [file], title: 'Player Card - ' + playerName, text: caption };

            if (navigator.canShare && navigator.canShare(shareData)) {
                await navigator.share(shareData);
                return;
            }
        } catch (err) {
            console.log('Share failed:', err);
        }
        triggerDownload('png', token);
        const fallback = document.getElementById('share-fallback');
        fallback.style.display = 'block';
        setTimeout(() => { fallback.style.display = 'none'; }, 5000);
    }
})();
</script>

<?php include 'includes/footer.php'; ob_end_flush(); ?>