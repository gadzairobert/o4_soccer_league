<?php
ob_start();
require_once 'config.php';
require_once 'includes/header.php';
include 'includes/properties.php';
$years = getAvailableSeasons();
$currentYear = date('Y');
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
        height: 100%;
        margin: 0;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .about-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    /* ── About Card — LIGHT ── */
    .about-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        display: flex;
        flex-direction: column;
    }

    /* ── About Header — DARK ── */
    .about-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 1rem 1.8rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 700;
        text-align: center;
    }

    /* ── Panel Tabs — DARK ── */
    .panel-tabs {
        display: flex;
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        flex-direction: column;
        border-bottom: none;
    }
    .panel-tabs a {
        padding: 16px 20px;
        color: rgba(255,255,255,0.45);
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        transition: background 0.3s;
    }
    .panel-tabs a::after {
        content: ''; position: absolute;
        left: 20px; right: 20px; bottom: 0;
        height: 1px; background: rgba(255,255,255,0.08);
    }
    .panel-tabs a:last-child::after { display: none; }
    .panel-tabs a:hover {
        background: rgba(255,255,255,0.06);
        color: var(--cream);
    }
    .panel-tabs a.active {
        background: rgba(201,168,76,0.1);
        color: var(--gold);
        border-bottom: 2px solid var(--gold);
    }
    .tab-controls { display: flex; gap: 10px; flex-wrap: wrap; }

    /* Selects inside tabs — dark background to match tab bar */
    .form-select {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        padding: 0.4rem 2rem 0.4rem 0.7rem;
        border-radius: 6px;
        flex: 1;
        min-width: 140px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23c9a84c'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 11px;
        cursor: pointer;
    }
    .form-select:focus { outline: none; border-color: var(--gold); }
    .form-select option { background: #1a1a2e; color: #eee; }

    @media (min-width: 768px) {
        .panel-tabs { flex-direction: row; }
        .panel-tabs a {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        .panel-tabs a::after {
            left: auto; right: 0; top: 20%; bottom: 20%;
            width: 1px; height: auto;
            background: rgba(255,255,255,0.1);
        }
    }

    /* ── Three-Panel Layout ── */
    @media (min-width: 992px) {
        .stats-layout {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 0;
            height: calc(100vh - 220px);
        }
        .stats-table-container {
            overflow-y: auto;
            border-right: 1px solid #e5e7eb;
            background: #ffffff;
        }
        #playerDetailPanel, #comparisonPanel {
            background: #f9f7f2;
            border-left: 1px solid #e5e7eb;
            box-shadow: -4px 0 12px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        #mobileComparisonPanel { display: none; }
    }

    /* ── Mobile Layout ── */
    @media (max-width: 991.98px) {
        .stats-layout { display: flex; flex-direction: column; }
        #comparisonPanel { display: none; }
        #mobileComparisonPanel {
            background: #f9f7f2;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 600px;
        }
        #playerDetailPanel {
            position: fixed; top: 0; right: 0;
            width: 100%; height: 100vh;
            z-index: 9999;
            background: #f0ede8;
            transform: translateY(100%);
            transition: transform 0.4s ease;
            box-shadow: 0 -8px 30px rgba(0,0,0,0.15);
        }
        #playerDetailPanel.open { transform: translateY(0); }
    }

    .tab-pane { display: none; }
    .tab-pane.active { display: block; }

    /* ── Tournament Header — DARK ── */
    .tournament-result-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 0.9rem 1.4rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
        border-radius: 8px 8px 0 0;
        margin: 0 1rem;
    }
    #tournamentStatsContainer { padding: 0 1rem 1rem !important; margin-top: 0 !important; }

    /* ── Thumbs ── */
    .player-thumb {
        width: 36px; height: 36px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(201,168,76,0.35);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .club-thumb {
        width: 32px; height: 32px;
        object-fit: contain;
        background: #ffffff;
        padding: 3px;
        border-radius: 50%;
        border: 1px solid rgba(201,168,76,0.25);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .pos-badge {
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold-dark);
        padding: 3px 8px;
        border-radius: 6px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        min-width: 34px;
        text-align: center;
    }

    /* ── Panel Header — DARK ── */
    .panel-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    .panel-header small { color: rgba(255,255,255,0.45); font-size: 0.8rem; display: block; }
    .close-panel {
        font-size: 1.9rem; cursor: pointer;
        color: rgba(255,255,255,0.45); line-height: 1;
        transition: color 0.2s;
    }
    .close-panel:hover { color: var(--gold); }

    /* ── Comparison Content — LIGHT ── */
    .comparison-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: calc(100vh - 300px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 1.2rem;
        background: #f9f7f2;
    }
    #mobileComparisonChart,
    #desktopComparisonChart {
        width: 100% !important;
        height: 350px !important;
        min-height: 350px;
        max-height: 350px;
        flex-shrink: 0;
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    /* Player search — LIGHT ── */
    .player-search-container { position: relative; margin-bottom: 12px; flex-shrink: 0; }
    .player-search-container input {
        width: 100%; padding: 10px;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        color: var(--text-main);
        box-sizing: border-box;
    }
    .player-search-container input::placeholder { color: #9ca3af; }
    .player-search-container input:focus {
        outline: none; border-color: var(--gold);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
    }
    .player-search-dropdown {
        position: absolute; top: 100%; left: 0; right: 0;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-top: none;
        max-height: 200px; overflow-y: auto;
        z-index: 10; display: none;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border-radius: 0 0 6px 6px;
    }
    .player-search-dropdown div {
        padding: 10px 12px; cursor: pointer;
        color: var(--text-soft);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        transition: background 0.15s;
    }
    .player-search-dropdown div:hover {
        background: #fdf9f0;
        color: var(--gold-dark);
    }
    .player-search-dropdown.active { display: block; }

    /* Compare button */
    .btn-primary {
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.4);
        color: var(--gold-dark);
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        font-size: 0.88rem;
        border-radius: 8px;
        padding: 0.55rem 1rem;
        transition: all 0.25s;
    }
    .btn-primary:hover {
        background: rgba(201,168,76,0.22);
        border-color: var(--gold);
        color: var(--gold-dark);
    }

    /* ── Stats table (AJAX-injected) — LIGHT ── */
    #leagueStatsContainer table,
    #tournamentStatsContainer table {
        background: #ffffff !important;
        color: var(--text-main) !important;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        width: 100%;
    }
    #leagueStatsContainer thead th,
    #tournamentStatsContainer thead th {
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab)) !important;
        color: var(--gold) !important;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none !important;
        padding: 0.65rem 0.6rem;
        white-space: nowrap;
    }
    #leagueStatsContainer tbody td,
    #tournamentStatsContainer tbody td {
        border-top: 1px solid #f3f4f6 !important;
        border-bottom: none !important;
        vertical-align: middle;
        padding: 0.55rem 0.6rem;
        color: var(--text-main) !important;
    }
    #leagueStatsContainer .table-hover > tbody > tr:hover > *,
    #tournamentStatsContainer .table-hover > tbody > tr:hover > * {
        background-color: #fdf9f0 !important;
        color: var(--text-main) !important;
        --bs-table-accent-bg: transparent;
    }
    #leagueStatsContainer a,
    #tournamentStatsContainer a {
        color: var(--text-soft) !important;
        text-decoration: none;
        transition: color 0.2s;
    }
    #leagueStatsContainer a:hover,
    #tournamentStatsContainer a:hover { color: var(--gold-dark) !important; }
    #leagueStatsContainer .fw-semibold,
    #tournamentStatsContainer .fw-semibold { color: var(--text-main) !important; }

    /* Player detail panel — LIGHT ── */
    #panelContent {
        padding: 0 1.6rem 1.6rem;
        overflow-y: auto;
        flex: 1;
        color: var(--text-soft);
        font-family: 'DM Sans', sans-serif;
        background: #f9f7f2;
    }
    #panelContent table { color: var(--text-main) !important; background: #ffffff !important; }
    #panelContent thead th {
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab)) !important;
        color: var(--gold) !important; border: none !important;
    }
    #panelContent tbody td {
        border-color: #f3f4f6 !important;
        color: var(--text-main) !important;
    }
    #panelContent .table-hover > tbody > tr:hover > * {
        background-color: #fdf9f0 !important;
        color: var(--text-main) !important;
    }

    /* Helpers */
    .text-danger  { color: #dc2626 !important; }
    .text-muted   { color: var(--muted) !important; }
    .text-primary { color: var(--gold-dark) !important; }
    .spinner-border { color: var(--gold) !important; }
    .chartjs-tooltip { background: rgba(0,0,0,0.75) !important; color: white !important; }
</style>

<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <div class="about-header">Player Statistics</div>

            <!-- Mobile: Player Comparison -->
            <div id="mobileComparisonPanel">
                <div class="panel-header">
                    <div>Player Comparison</div>
                </div>
                <div class="comparison-content">
                    <div class="player-search-container">
                        <input type="text" id="compPlayer1Input" placeholder="Search Player 1" oninput="filterCompPlayers(1)" onfocus="showCompDropdown(1)">
                        <div id="compPlayer1Dropdown" class="player-search-dropdown"></div>
                        <input type="hidden" id="compPlayer1Id">
                    </div>
                    <div class="player-search-container">
                        <input type="text" id="compPlayer2Input" placeholder="Search Player 2" oninput="filterCompPlayers(2)" onfocus="showCompDropdown(2)">
                        <div id="compPlayer2Dropdown" class="player-search-dropdown"></div>
                        <input type="hidden" id="compPlayer2Id">
                    </div>
                    <div class="player-search-container mb-4">
                        <input type="text" id="compPlayer3Input" placeholder="Search Player 3 (Optional)" oninput="filterCompPlayers(3)" onfocus="showCompDropdown(3)">
                        <div id="compPlayer3Dropdown" class="player-search-dropdown"></div>
                        <input type="hidden" id="compPlayer3Id">
                    </div>
                    <button onclick="compareSelectedPlayers()" class="btn btn-primary w-100 mb-3">Compare Players</button>
                    <canvas id="mobileComparisonChart"></canvas>
                    <p id="compErrorMessage" class="text-danger text-center mt-3 mb-0"></p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="panel-tabs">
                <a href="#" class="active" data-tab="league">
                    <span class="tab-label">League</span>
                    <div class="tab-controls" id="leagueControls">
                        <select class="form-select" id="leagueYear">
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?>/<?= $y + 1 ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </a>
                <a href="#" data-tab="tournament">
                    <span class="tab-label">Tournaments</span>
                    <div class="tab-controls" id="tournamentControls">
                        <select class="form-select" id="tournamentYear">
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select" id="tournamentSelect">
                            <option value="">All Tournaments</option>
                            <?php
                            $stmt = $pdo->query("SELECT id, name, short_name, competition_name, season FROM competition_seasons WHERE type = 'cup' ORDER BY season DESC");
                            while ($t = $stmt->fetch()) {
                                $display = $t['name'] ?: ($t['competition_name'] . ' ' . $t['season']);
                                echo '<option value="'.$t['id'].'" data-name="'.htmlspecialchars($display).'">'.htmlspecialchars($display).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </a>
            </div>

            <!-- Main Layout -->
            <div class="stats-layout">

                <!-- Left: Rankings -->
                <div class="stats-table-container">
                    <div id="league" class="tab-pane active">
                        <div id="leagueStatsContainer" style="padding:1rem;">
                            <div class="text-center py-5">
                                <div class="spinner-border"></div>
                                <p style="color:var(--muted);font-family:'DM Sans',sans-serif;margin-top:0.5rem;">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div id="tournament" class="tab-pane">
                        <div class="tournament-result-header" id="tournamentHeaderText">All Tournaments (<?= $currentYear ?>)</div>
                        <div id="tournamentStatsContainer" style="padding:0 1rem 1rem;">
                            <div class="text-center py-5" style="color:var(--muted);font-family:'DM Sans',sans-serif;">
                                <p class="lead">Select year &amp; tournament</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle: Player Detail Panel -->
                <div id="playerDetailPanel">
                    <div class="panel-header">
                        <div>
                            <div id="panelPlayerName">Select a player</div>
                            <small id="panelPlayerClub">—</small>
                        </div>
                        <span class="close-panel" onclick="closePanel()">×</span>
                    </div>
                    <div id="panelContent">
                        <p class="text-center mt-5" style="color:var(--muted);font-size:1rem;">Click any player to view their match actions</p>
                    </div>
                </div>

                <!-- Desktop: Comparison Panel -->
                <div id="comparisonPanel">
                    <div class="panel-header">
                        <div>Player Comparison</div>
                    </div>
                    <div class="comparison-content">
                        <div class="player-search-container">
                            <input type="text" id="compPlayer1InputDesktop" placeholder="Search Player 1" oninput="filterCompPlayersDesktop(1)" onfocus="showCompDropdownDesktop(1)">
                            <div id="compPlayer1DropdownDesktop" class="player-search-dropdown"></div>
                            <input type="hidden" id="compPlayer1IdDesktop">
                        </div>
                        <div class="player-search-container">
                            <input type="text" id="compPlayer2InputDesktop" placeholder="Search Player 2" oninput="filterCompPlayersDesktop(2)" onfocus="showCompDropdownDesktop(2)">
                            <div id="compPlayer2DropdownDesktop" class="player-search-dropdown"></div>
                            <input type="hidden" id="compPlayer2IdDesktop">
                        </div>
                        <div class="player-search-container mb-4">
                            <input type="text" id="compPlayer3InputDesktop" placeholder="Search Player 3 (Optional)" oninput="filterCompPlayersDesktop(3)" onfocus="showCompDropdownDesktop(3)">
                            <div id="compPlayer3DropdownDesktop" class="player-search-dropdown"></div>
                            <input type="hidden" id="compPlayer3IdDesktop">
                        </div>
                        <button onclick="compareSelectedPlayersDesktop()" class="btn btn-primary w-100 mb-3">Compare Players</button>
                        <canvas id="desktopComparisonChart"></canvas>
                        <p id="compErrorMessageDesktop" class="text-danger text-center mt-3 mb-0"></p>
                    </div>
                </div>

            </div><!-- /.stats-layout -->
        </div><!-- /.about-card -->
    </div><!-- /.about-page-wrapper -->
</div><!-- /.main-content -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
let mobileChart = null;
let desktopChart = null;
let allPlayers = [];

function loadPlayersForComparison() {
    const rows = document.querySelectorAll('#leagueStatsContainer tr, #tournamentStatsContainer tr');
    allPlayers = Array.from(rows).map(tr => {
        const onclick = tr.getAttribute('onclick');
        if (!onclick) return null;
        const match = onclick.match(/openPlayerPanel\((\d+),\s*'([^']+)',\s*'([^']*)'/);
        if (!match) return null;
        const cells = tr.querySelectorAll('td');
        if (cells.length < 10) return null;
        return {
            id: match[1], name: match[2],
            goals:        parseInt(cells[4].textContent.trim()) || 0,
            assists:      parseInt(cells[5].textContent.trim()) || 0,
            yellow_cards: parseInt(cells[7].textContent.trim()) || 0,
            red_cards:    parseInt(cells[8].textContent.trim()) || 0,
            clean_sheets: parseInt(cells[9].textContent.trim()) || 0
        };
    }).filter(Boolean);
}

function filterCompPlayers(n) {
    const input = document.getElementById(`compPlayer${n}Input`);
    const dd    = document.getElementById(`compPlayer${n}Dropdown`);
    dd.innerHTML = '';
    allPlayers.filter(p => p.name.toLowerCase().includes(input.value.toLowerCase())).forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => { input.value = p.name; document.getElementById(`compPlayer${n}Id`).value = p.id; dd.classList.remove('active'); };
        dd.appendChild(div);
    });
    dd.classList.toggle('active', dd.children.length > 0);
}
function showCompDropdown(n) {
    const dd = document.getElementById(`compPlayer${n}Dropdown`);
    dd.innerHTML = '';
    allPlayers.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => { document.getElementById(`compPlayer${n}Input`).value = p.name; document.getElementById(`compPlayer${n}Id`).value = p.id; dd.classList.remove('active'); };
        dd.appendChild(div);
    });
    dd.classList.add('active');
}
function compareSelectedPlayers() {
    const names = [1,2,3].map(n => document.getElementById(`compPlayer${n}Input`).value.trim()).filter(Boolean);
    if (names.length < 2) { document.getElementById('compErrorMessage').textContent = 'Please select at least 2 players'; return; }
    const sel = allPlayers.filter(p => names.includes(p.name));
    if (sel.length < 2) { document.getElementById('compErrorMessage').textContent = 'Player data not found'; return; }
    document.getElementById('compErrorMessage').textContent = '';
    renderChart(sel, 'mobileComparisonChart', mobileChart);
    mobileChart = chartInstance;
}
function filterCompPlayersDesktop(n) {
    const input = document.getElementById(`compPlayer${n}InputDesktop`);
    const dd    = document.getElementById(`compPlayer${n}DropdownDesktop`);
    dd.innerHTML = '';
    allPlayers.filter(p => p.name.toLowerCase().includes(input.value.toLowerCase())).forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => { input.value = p.name; document.getElementById(`compPlayer${n}IdDesktop`).value = p.id; dd.classList.remove('active'); };
        dd.appendChild(div);
    });
    dd.classList.toggle('active', dd.children.length > 0);
}
function showCompDropdownDesktop(n) {
    const dd = document.getElementById(`compPlayer${n}DropdownDesktop`);
    dd.innerHTML = '';
    allPlayers.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => { document.getElementById(`compPlayer${n}InputDesktop`).value = p.name; document.getElementById(`compPlayer${n}IdDesktop`).value = p.id; dd.classList.remove('active'); };
        dd.appendChild(div);
    });
    dd.classList.add('active');
}
function compareSelectedPlayersDesktop() {
    const names = [1,2,3].map(n => document.getElementById(`compPlayer${n}InputDesktop`).value.trim()).filter(Boolean);
    if (names.length < 2) { document.getElementById('compErrorMessageDesktop').textContent = 'Please select at least 2 players'; return; }
    const sel = allPlayers.filter(p => names.includes(p.name));
    if (sel.length < 2) { document.getElementById('compErrorMessageDesktop').textContent = 'Player data not found'; return; }
    document.getElementById('compErrorMessageDesktop').textContent = '';
    renderChart(sel, 'desktopComparisonChart', desktopChart);
    desktopChart = chartInstance;
}

let chartInstance = null;
function renderChart(players, canvasId, existingChart) {
    if (existingChart) existingChart.destroy();
    const labels = ['Goals', 'Assists', 'Clean Sheets', 'Yellow Cards', 'Red Cards'];
    let maxVal = Math.max(...players.flatMap(p => [p.goals, p.assists, p.clean_sheets, p.yellow_cards, p.red_cards]));
    const colors = ['rgb(201,168,76)', 'rgb(239,68,68)', 'rgb(34,197,94)'];
    const bgColors = ['rgba(201,168,76,0.2)', 'rgba(239,68,68,0.2)', 'rgba(34,197,94,0.2)'];
    chartInstance = new Chart(document.getElementById(canvasId), {
        type: 'radar',
        data: {
            labels,
            datasets: players.map((p, i) => ({
                label: p.name,
                data: [p.goals, p.assists, p.clean_sheets, p.yellow_cards, p.red_cards],
                backgroundColor: bgColors[i] || bgColors[0],
                borderColor: colors[i] || colors[0],
                borderWidth: 3,
                pointBackgroundColor: '#f0ede8',
                pointBorderColor: colors[i] || colors[0],
                pointRadius: 5
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: maxVal + 3,
                    ticks: { stepSize: Math.max(1, Math.ceil((maxVal + 3) / 6)), color: '#6b7280', backdropColor: 'transparent' },
                    grid:        { color: 'rgba(0,0,0,0.08)' },
                    angleLines:  { color: 'rgba(0,0,0,0.08)' },
                    pointLabels: { color: '#4b5563', font: { size: 13 } }
                }
            },
            plugins: {
                legend: { labels: { color: '#4b5563', font: { size: 13 } } },
                title:  { display: true, text: 'Player Stats Comparison', color: '#9a6f1e', font: { size: 16, weight: '700' } }
            }
        }
    });
}

function loadLeagueStats(year) {
    document.getElementById('leagueStatsContainer').innerHTML = '<div class="text-center py-5"><div class="spinner-border"></div><p style="color:var(--muted);font-family:\'DM Sans\',sans-serif;margin-top:0.5rem;">Loading...</p></div>';
    fetch(`ajax_player_stats.php?type=league&year=${year}`).then(r => r.text()).then(html => { document.getElementById('leagueStatsContainer').innerHTML = html; loadPlayersForComparison(); });
}
function loadTournamentStats(year, cs_id = '') {
    const container = document.getElementById('tournamentStatsContainer');
    const header    = document.getElementById('tournamentHeaderText');
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border"></div><p style="color:var(--muted);font-family:\'DM Sans\',sans-serif;margin-top:0.5rem;">Loading...</p></div>';
    header.textContent = cs_id ? document.querySelector(`#tournamentSelect option[value="${cs_id}"]`)?.dataset.name || 'Tournament' : `All Tournaments (${year})`;
    fetch(`ajax_player_stats.php?type=tournament&year=${year}${cs_id ? '&cs_id='+cs_id : ''}`).then(r => r.text()).then(html => { container.innerHTML = html; loadPlayersForComparison(); });
}
function openPlayerPanel(playerId, playerName, clubName, clubId, type, year, cs_id = null) {
    document.getElementById('panelPlayerName').textContent = playerName;
    document.getElementById('panelPlayerClub').textContent = clubName || 'No Club';
    document.getElementById('panelContent').innerHTML = '<div class="text-center py-5"><div class="spinner-border"></div><p style="color:var(--muted);font-family:\'DM Sans\',sans-serif;margin-top:0.5rem;">Loading...</p></div>';
    document.getElementById('playerDetailPanel').classList.add('open');
    let url = `ajax_player_actions.php?player_id=${playerId}&player_club_id=${clubId}&type=${type}&year=${year}`;
    if (cs_id) url += `&cs_id=${cs_id}`;
    fetch(url).then(r => r.text()).then(html => document.getElementById('panelContent').innerHTML = html);
}
function closePanel() { document.getElementById('playerDetailPanel').classList.remove('open'); }

document.querySelectorAll('.panel-tabs a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.panel-tabs a').forEach(l => l.classList.remove('active'));
        a.classList.add('active');
        const tab = a.dataset.tab;
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById(tab).classList.add('active');
        document.getElementById('leagueControls').style.display    = tab === 'league'     ? 'flex' : 'none';
        document.getElementById('tournamentControls').style.display = tab === 'tournament' ? 'flex' : 'none';
        if (tab === 'league') loadLeagueStats(document.getElementById('leagueYear').value);
        else loadTournamentStats(document.getElementById('tournamentYear').value, document.getElementById('tournamentSelect').value || '');
    });
});
document.getElementById('leagueYear').addEventListener('change', e => loadLeagueStats(e.target.value));
document.getElementById('tournamentYear').addEventListener('change', () => loadTournamentStats(document.getElementById('tournamentYear').value, document.getElementById('tournamentSelect').value || ''));
document.getElementById('tournamentSelect').addEventListener('change', () => loadTournamentStats(document.getElementById('tournamentYear').value, document.getElementById('tournamentSelect').value || ''));
document.addEventListener('DOMContentLoaded', () => { loadLeagueStats(<?= $currentYear ?>); });
</script>

<?php require_once 'includes/footer.php'; ob_end_flush(); ?>