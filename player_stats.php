<?php
ob_start();
require_once 'config.php';
require_once 'includes/header.php';
include 'includes/properties.php';
$years = getAvailableSeasons();
$currentYear = date('Y');
?>
<style>
    /* LIGHT THEME WITH #defcfc BACKGROUND - CONSISTENT WITH FIXTURES.PHP & LEAGUE.PHP */
    html, body {
        background-color: #defcfc !important;
        color: #333333;
        height: 100%;
        margin: 0;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }
    .about-page-wrapper { 
        margin-top: -50px; 
        padding-top: 20px; 
    }
    .about-card {
        background: #ffffff;
        box-shadow: 0 8px 28px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
    }
    /* Header */
    .about-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 12px 12px 0 0;
    }
    /* TABS - FULL DARK HEADER ACROSS ALL THREE SECTIONS */
    .panel-tabs {
        display: flex;
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        flex-direction: column;
        border-bottom: none;
    }
    .panel-tabs a {
        padding: 16px 20px;
        color: #bdc3c7;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        transition: background 0.3s;
    }
    .panel-tabs a::after {
        content: ''; position: absolute; left: 20px; right: 20px; bottom: 0;
        height: 1px; background: rgba(255,255,255,0.15);
    }
    .panel-tabs a:last-child::after { display: none; }
    .panel-tabs a:hover { 
        background: rgba(255,255,255,0.1); 
        color: white; 
    }
    .panel-tabs a.active {
        background: #0d6efd;
        color: white;
        box-shadow: inset 0 -5px 0 #0b5ed7;
    }
    .tab-controls { 
        display: flex; 
        gap: 10px; 
        flex-wrap: wrap; 
    }
    .form-select {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        flex: 1;
        min-width: 140px;
    }
    .form-select option {
        color: #333 !important;
        background: #2c3e50;
    }
    @media (min-width: 768px) {
        .panel-tabs { flex-direction: row; }
        .panel-tabs a { flex-direction: row; justify-content: space-between; align-items: center; }
        .panel-tabs a::after { left: auto; right: 0; top: 20%; bottom: 20%; width: 1px; background: rgba(255,255,255,0.15); }
    }
    /* THREE-PANEL LAYOUT - Desktop */
    @media (min-width: 992px) {
        .stats-layout {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 0;
            height: calc(100vh - 220px);
        }
        .stats-table-container {
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            background: #ffffff;
        }
        #playerDetailPanel, #comparisonPanel {
            background: #ffffff;
            border-left: 1px solid #dee2e6;
            box-shadow: -6px 0 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        #mobileComparisonPanel { display: none; }
    }
    /* Mobile - Comparison ABOVE tabs */
    @media (max-width: 991.98px) {
        .stats-layout { display: flex; flex-direction: column; }
        #comparisonPanel { display: none; }
        #mobileComparisonPanel {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 600px;
        }
        #playerDetailPanel {
            position: fixed; top: 0; right: 0; width: 100%; height: 100vh;
            z-index: 9999; background: #ffffff;
            transform: translateY(100%);
            transition: transform 0.4s ease;
            box-shadow: 0 -8px 30px rgba(0,0,0,0.15);
        }
        #playerDetailPanel.open { transform: translateY(0); }
    }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .tournament-result-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 0.9rem 1.4rem;
        font-size: 1.22rem;
        font-weight: 700;
        text-align: center;
        border-radius: 8px 8px 0 0;
        margin: 0 1rem 0 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-bottom: 2px solid #fff;
    }
    #tournamentStatsContainer { padding: 0 1rem 1rem !important; margin-top: 0 !important; }
    .player-thumb {
        width: 36px; height: 36px; object-fit: cover; border-radius: 50%;
        border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .club-thumb {
        width: 32px; height: 32px; object-fit: contain; background: white;
        padding: 3px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); border: 1px solid #ccc;
    }
    .pos-badge {
        background: #1a2530; color: white; padding: 4px 8px; border-radius: 6px;
        font-size: 0.75rem; font-weight: 700; min-width: 34px;
    }
    .panel-header {
        background: #0d6efd;
        color: white;
        padding: 1.2rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .close-panel { 
        font-size: 1.9rem; 
        cursor: pointer; 
        opacity: 0.9; 
    }
    .close-panel:hover { 
        color: #ffc107; 
    }
    /* Comparison Panel */
    .comparison-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: calc(100vh - 300px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 1.2rem;
        background: #ffffff;
    }
    #mobileComparisonChart, #desktopComparisonChart {
        width: 100% !important;
        height: 350px !important;
        min-height: 350px;
        max-height: 350px;
        flex-shrink: 0;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .player-search-container { 
        position: relative; 
        margin-bottom: 12px; 
        flex-shrink: 0; 
    }
    .player-search-container input {
        width: 100%; 
        padding: 10px;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 0.95rem;
        color: #333;
    }
    .player-search-container input::placeholder { 
        color: #6c757d; 
    }
    .player-search-dropdown {
        position: absolute; top: 100%; left: 0; right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        max-height: 200px; overflow-y: auto;
        z-index: 10; display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .player-search-dropdown div {
        padding: 10px 12px; 
        cursor: pointer; 
        color: #333;
    }
    .player-search-dropdown div:hover { 
        background: #e9ecef; 
    }
    .player-search-dropdown.active { 
        display: block; 
    }
    /* Buttons */
    .btn-primary {
        background: #0d6efd;
        border: none;
    }
    .btn-primary:hover {
        background: #0b5ed7;
    }
    /* Text colors */
    .text-danger { color: #e74c3c !important; }
    .text-muted { color: #6c757d !important; }
    .text-primary { color: #0d6efd !important; }
    /* PLAYER NAMES IN TABLES */
    #leagueStatsContainer a, #tournamentStatsContainer a,
    #leagueStatsContainer .fw-semibold, #tournamentStatsContainer .fw-semibold,
    #leagueStatsContainer td, #tournamentStatsContainer td {
        color: #2c3e50 !important;
    }
    #leagueStatsContainer a:hover, #tournamentStatsContainer a:hover {
        color: #0d6efd !important;
    }
    #leagueStatsContainer tr:hover, #tournamentStatsContainer tr:hover {
        background: #f8f9fa !important;
    }
    #leagueStatsContainer table, #tournamentStatsContainer table {
        background: #ffffff;
    }
    #leagueStatsContainer thead th, #tournamentStatsContainer thead th {
        background: #1a2530 !important;
        color: white !important;
    }
    /* Chart text colors for light theme */
    .chartjs-tooltip { 
        background: rgba(0,0,0,0.7) !important; 
        color: white !important; 
    }
</style>
<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <div class="about-header">Player Statistics</div>
            <!-- Mobile: Player Comparison - ABOVE tabs -->
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
                            <div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>
                        </div>
                    </div>
                    <div id="tournament" class="tab-pane">
                        <div class="tournament-result-header" id="tournamentHeaderText">All Tournaments (<?= $currentYear ?>)</div>
                        <div id="tournamentStatsContainer" style="padding:0 1rem 1rem;">
                            <div class="text-center py-5 text-muted"><p class="lead">Select year & tournament</p></div>
                        </div>
                    </div>
                </div>
                <!-- Middle: Player Detail Panel (Mobile slides up) -->
                <div id="playerDetailPanel">
                    <div class="panel-header">
                        <div><div id="panelPlayerName">Select a player</div><small class="opacity-90" id="panelPlayerClub">-</small></div>
                        <span class="close-panel" onclick="closePanel()">×</span>
                    </div>
                    <div id="panelContent" style="padding:0 1.6rem 1.6rem; overflow-y:auto; flex:1;">
                        <p class="text-center text-muted mt-5 fs-5">Click any player to view their match actions</p>
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
            </div>
        </div>
    </div>
</div>
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
            id: match[1],
            name: match[2],
            goals: parseInt(cells[4].textContent.trim()) || 0,
            assists: parseInt(cells[5].textContent.trim()) || 0,
            yellow_cards: parseInt(cells[7].textContent.trim()) || 0,
            red_cards: parseInt(cells[8].textContent.trim()) || 0,
            clean_sheets: parseInt(cells[9].textContent.trim()) || 0
        };
    }).filter(Boolean);
}
function filterCompPlayers(n) {
    const input = document.getElementById(`compPlayer${n}Input`);
    const dropdown = document.getElementById(`compPlayer${n}Dropdown`);
    dropdown.innerHTML = '';
    const filtered = allPlayers.filter(p => p.name.toLowerCase().includes(input.value.toLowerCase()));
    filtered.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => {
            input.value = p.name;
            document.getElementById(`compPlayer${n}Id`).value = p.id;
            dropdown.classList.remove('active');
        };
        dropdown.appendChild(div);
    });
    dropdown.classList.toggle('active', filtered.length > 0);
}
function showCompDropdown(n) {
    const dropdown = document.getElementById(`compPlayer${n}Dropdown`);
    dropdown.innerHTML = '';
    allPlayers.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => {
            document.getElementById(`compPlayer${n}Input`).value = p.name;
            document.getElementById(`compPlayer${n}Id`).value = p.id;
            dropdown.classList.remove('active');
        };
        dropdown.appendChild(div);
    });
    dropdown.classList.add('active');
}
function compareSelectedPlayers() {
    const selectedNames = [1,2,3].map(n => document.getElementById(`compPlayer${n}Input`).value.trim()).filter(Boolean);
    if (selectedNames.length < 2) {
        document.getElementById('compErrorMessage').textContent = 'Please select at least 2 players';
        return;
    }
    const selectedPlayers = allPlayers.filter(p => selectedNames.includes(p.name));
    if (selectedPlayers.length < 2) {
        document.getElementById('compErrorMessage').textContent = 'Player data not found';
        return;
    }
    document.getElementById('compErrorMessage').textContent = '';
    renderChart(selectedPlayers, 'mobileComparisonChart', mobileChart);
    mobileChart = chartInstance;
}
function filterCompPlayersDesktop(n) {
    const input = document.getElementById(`compPlayer${n}InputDesktop`);
    const dropdown = document.getElementById(`compPlayer${n}DropdownDesktop`);
    dropdown.innerHTML = '';
    const filtered = allPlayers.filter(p => p.name.toLowerCase().includes(input.value.toLowerCase()));
    filtered.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => {
            input.value = p.name;
            document.getElementById(`compPlayer${n}IdDesktop`).value = p.id;
            dropdown.classList.remove('active');
        };
        dropdown.appendChild(div);
    });
    dropdown.classList.toggle('active', filtered.length > 0);
}
function showCompDropdownDesktop(n) {
    const dropdown = document.getElementById(`compPlayer${n}DropdownDesktop`);
    dropdown.innerHTML = '';
    allPlayers.forEach(p => {
        const div = document.createElement('div');
        div.textContent = p.name;
        div.onclick = () => {
            document.getElementById(`compPlayer${n}InputDesktop`).value = p.name;
            document.getElementById(`compPlayer${n}IdDesktop`).value = p.id;
            dropdown.classList.remove('active');
        };
        dropdown.appendChild(div);
    });
    dropdown.classList.add('active');
}
function compareSelectedPlayersDesktop() {
    const selectedNames = [1,2,3].map(n => document.getElementById(`compPlayer${n}InputDesktop`).value.trim()).filter(Boolean);
    if (selectedNames.length < 2) {
        document.getElementById('compErrorMessageDesktop').textContent = 'Please select at least 2 players';
        return;
    }
    const selectedPlayers = allPlayers.filter(p => selectedNames.includes(p.name));
    if (selectedPlayers.length < 2) {
        document.getElementById('compErrorMessageDesktop').textContent = 'Player data not found';
        return;
    }
    document.getElementById('compErrorMessageDesktop').textContent = '';
    renderChart(selectedPlayers, 'desktopComparisonChart', desktopChart);
    desktopChart = chartInstance;
}
let chartInstance = null;
function renderChart(players, canvasId, existingChart) {
    if (existingChart) existingChart.destroy();
    const labels = ['Goals', 'Assists', 'Clean Sheets', 'Yellow Cards', 'Red Cards'];
    const datasets = [];
    let maxVal = Math.max(...players.flatMap(p => [p.goals, p.assists, p.clean_sheets, p.yellow_cards, p.red_cards]));
    players.forEach((p, i) => {
        datasets.push({
            label: p.name,
            data: [p.goals, p.assists, p.clean_sheets, p.yellow_cards, p.red_cards],
            backgroundColor: i===0 ? 'rgba(59,130,246,0.3)' : i===1 ? 'rgba(239,68,68,0.3)' : 'rgba(34,197,94,0.3)',
            borderColor: i===0 ? 'rgb(59,130,246)' : i===1 ? 'rgb(239,68,68)' : 'rgb(34,197,94)',
            borderWidth: 3,
            pointBackgroundColor: 'white',
            pointBorderColor: i===0 ? 'rgb(59,130,246)' : i===1 ? 'rgb(239,68,68)' : 'rgb(34,197,94)',
            pointRadius: 5
        });
    });
    chartInstance = new Chart(document.getElementById(canvasId), {
        type: 'radar',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: maxVal + 3,
                    ticks: {
                        stepSize: Math.max(1, Math.ceil((maxVal + 3) / 6)),
                        color: '#333333',
                        backdropColor: 'transparent'
                    },
                    grid: { color: '#dee2e6' },
                    angleLines: { color: '#dee2e6' },
                    pointLabels: { color: '#2c3e50', font: { size: 14 } }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#333333',
                        font: { size: 14 }
                    }
                },
                title: {
                    display: true,
                    text: 'Player Stats Comparison',
                    color: '#2c3e50',
                    font: { size: 18 }
                }
            }
        }
    });
}
function loadLeagueStats(year) {
    document.getElementById('leagueStatsContainer').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>';
    fetch(`ajax_player_stats.php?type=league&year=${year}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById('leagueStatsContainer').innerHTML = html;
            loadPlayersForComparison();
        });
}
function loadTournamentStats(year, cs_id = '') {
    const container = document.getElementById('tournamentStatsContainer');
    const header = document.getElementById('tournamentHeaderText');
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>';
    header.textContent = cs_id ? document.querySelector(`#tournamentSelect option[value="${cs_id}"]`)?.dataset.name || 'Tournament' : `All Tournaments (${year})`;
    fetch(`ajax_player_stats.php?type=tournament&year=${year}${cs_id ? '&cs_id='+cs_id : ''}`)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            loadPlayersForComparison();
        });
}
function openPlayerPanel(playerId, playerName, clubName, clubId, type, year, cs_id = null) {
    document.getElementById('panelPlayerName').textContent = playerName;
    document.getElementById('panelPlayerClub').textContent = clubName || 'No Club';
    document.getElementById('panelContent').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>';
    document.getElementById('playerDetailPanel').classList.add('open');
    let url = `ajax_player_actions.php?player_id=${playerId}&player_club_id=${clubId}&type=${type}&year=${year}`;
    if (cs_id) url += `&cs_id=${cs_id}`;
    fetch(url).then(r => r.text()).then(html => document.getElementById('panelContent').innerHTML = html);
}
function closePanel() {
    document.getElementById('playerDetailPanel').classList.remove('open');
}
document.querySelectorAll('.panel-tabs a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.panel-tabs a').forEach(l => l.classList.remove('active'));
        a.classList.add('active');
        const tab = a.dataset.tab;
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById(tab).classList.add('active');
        document.getElementById('leagueControls').style.display = tab === 'league' ? 'flex' : 'none';
        document.getElementById('tournamentControls').style.display = tab === 'tournament' ? 'flex' : 'none';
        if (tab === 'league') {
            loadLeagueStats(document.getElementById('leagueYear').value);
        } else {
            loadTournamentStats(
                document.getElementById('tournamentYear').value,
                document.getElementById('tournamentSelect').value || ''
            );
        }
    });
});
document.getElementById('leagueYear').addEventListener('change', e => loadLeagueStats(e.target.value));
document.getElementById('tournamentYear').addEventListener('change', () => loadTournamentStats(document.getElementById('tournamentYear').value, document.getElementById('tournamentSelect').value || ''));
document.getElementById('tournamentSelect').addEventListener('change', () => loadTournamentStats(document.getElementById('tournamentYear').value, document.getElementById('tournamentSelect').value || ''));
document.addEventListener('DOMContentLoaded', () => {
    loadLeagueStats(<?= $currentYear ?>);
});
</script>
<?php require_once 'includes/footer.php'; ob_end_flush(); ?>