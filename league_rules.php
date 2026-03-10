<?php
$page_title = "League Rules & Regulations";
require_once 'config.php';
require_once 'includes/header.php';

$stmt = $pdo->prepare("SELECT * FROM about_us WHERE category = 'league_rules' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($sections)) {
    echo '<div class="container py-5 text-center"><h2 class="text-muted">League Rules coming soon...</h2></div>';
    require_once 'includes/footer.php';
    exit;
}
?>

<style>
    /* Push footer to bottom */
    html, body { height: 100%; margin: 0; }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    .about-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    /* Clean sharp header – same as training.php */
    .about-header {
        position: relative;
        background: #2c3e50;
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
    }
    .about-header::after {
        content: '';
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: 1px;
        background: rgba(255,255,255,0.45);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .about-card {
        background: white;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        border: 1px solid #e9ecef;
    }

    /* Tabs – sharp corners */
    .tab {
        overflow: hidden;
        background: #2c3e50;
        display: flex;
        flex-wrap: nowrap;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .tab button {
        background: transparent;
        border: none;
        color: #ddd;
        padding: 18px 24px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
        white-space: nowrap;
        position: relative;
    }
    .tab button:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 25%; height: 50%; width: 1px; background: rgba(255,255,255,0.25);
    }
    .tab button:hover { background: #3a5370; color: white; }
    .tab button.active { background: #1a2530; color: white; box-shadow: inset 0 -5px 0 #00d4ff; font-weight: 700; }

    .tabcontent {
        display: none;
        background: white;
        padding: 3rem 2.5rem;
        box-shadow: 0 12px 40px rgba(0,0,0,0.18);
        animation: fadeIn 0.6s ease;
    }
    .tabcontent.active { display: block; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .section-title {
        font-size: 2.3rem;
        font-weight: 800;
        color: #1a2530;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 12px;
    }
    .section-title::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 80px; height: 6px; background: #00d4ff;
    }

    .section-image {
        width: 100%;
        max-width: 400px;
        height: auto;
        border: 10px solid white;
        box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .section-image:hover { transform: scale(1.05); }

    /* RESTORED: Original beautiful icons */
    .rule-content {
        font-size: 1.08rem;
        line-height: 1.65;
        color: #444;
    }
    .rule-content p {
        margin-bottom: 0.7rem !important;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .rule-content p i {
        color: #00d4ff;
        font-size: 1.1rem;
        width: 28px;
        flex-shrink: 0;
        margin-top: 2px;
        text-align: center;
    }
    .rule-content p.rule-title {
        font-weight: 800;
        font-size: 1.4rem;
        color: #1a2530;
        margin: 2rem 0 1rem 0 !important;
        padding-left: 40px;
    }
    .rule-content p.rule-title i {
        color: #e74c3c;
        font-size: 1.3rem;
    }

    @media (max-width: 992px) {
        .tab { overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; }
        .tab::-webkit-scrollbar { display: none; }
        .tab { -ms-overflow-style: none; scrollbar-width: none; }
        .tab button { padding: 16px 14px; font-size: 1rem; }
    }
    @media (max-width: 576px) {
        .about-header { font-size: 1.3rem; padding: 1.4rem 1rem; }
        .section-title { font-size: 1.9rem; }
        .tabcontent { padding: 2rem 1.5rem; }
        .section-image { max-width: 300px; }
        .rule-content p,
        .rule-content p.rule-title { padding-left: 0 0 34px; }
        .rule-content p i { width: 24px; font-size: 1rem; }
    }
</style>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <div class="about-header">League Rules</div>

            <div class="tab" id="myTab">
                <?php foreach ($sections as $index => $section): 
                    $tabId = 'tab' . $section['id'];
                    $isFirst = $index === 0;
                ?>
                    <button class="tablinks <?= $isFirst ? 'active' : '' ?>"
                            onclick="openTab(event, '<?= $tabId ?>')"
                            <?= $isFirst ? 'id="defaultOpen"' : '' ?>>
                        <?= htmlspecialchars($section['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($sections as $index => $section): 
                $tabId = 'tab' . $section['id'];
                $isFirst = $index === 0;
                $imagePath = !empty($section['image'])
                    ? 'uploads/admin/about_us/' . htmlspecialchars($section['image'])
                    : 'https://via.placeholder.com/800x600/2c3e50/white?text=Rules';
            ?>
                <div class="tabcontent <?= $isFirst ? 'active' : '' ?>" id="<?= $tabId ?>">
                    <div class="row align-items-start g-5">
                        <div class="col-lg-8 order-lg-1">
                            <h2 class="section-title"><?= htmlspecialchars($section['title']) ?></h2>
                            <div class="rule-content">
                                <?php
                                $lines = preg_split('/\r\n|\r|\n/', trim($section['description']));
                                foreach ($lines as $line):
                                    $line = trim($line);
                                    if ($line === '') continue;

                                    if (preg_match('/^\*(.+)\*$/', $line, $matches)) {
                                        $title = trim($matches[1]);
                                        echo "<p class='rule-title'><i class='fas fa-gavel'></i> " . htmlspecialchars($title) . "</p>";
                                    } else {
                                        echo "<p><i class='fas fa-caret-right'></i> " . htmlspecialchars($line) . "</p>";
                                    }
                                endforeach;
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-4 order-lg-2 text-center">
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($section['title']) ?>" class="section-image img-fluid">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Font Awesome 6 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>

<script>
function openTab(evt, tabId) {
    document.querySelectorAll('.tabcontent').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tablinks').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('defaultOpen')?.click();
});
</script>

<?php require_once 'includes/footer.php'; ?>