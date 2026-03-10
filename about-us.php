<?php
$page_title = "About Us";
require_once 'config.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM about_us WHERE is_active = 1 AND category = 'about_us' ORDER BY sort_order ASC, id ASC");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($sections)) {
    echo '<div class="container py-5 text-center"><h2 style="color:rgba(255,255,255,0.5);">About Us content coming soon...</h2></div>';
    require_once 'includes/footer.php';
    exit;
}
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    /* ── Design Tokens ── */
    :root {
        --ink:        #1a1a2e;
        --gold:       #c9a84c;
        --gold-light: #f0d080;
        --cream:      #fdf8ef;
        --muted:      rgba(255,255,255,0.45);
        --border:     rgba(201,168,76,0.2);
        --card-bg:    rgba(255,255,255,0.04);
    }

    html, body {
        background-color: #1a1a2e !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.06) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(123,45,139,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: #eee;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .about-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .about-page-wrapper { margin-top: 0; padding: 1rem 0 3rem; width: 100%; }
    }

    /* ── Outer Card ── */
    .about-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
    }
    @media (max-width: 767px) {
        .about-card { border-radius: 0; border-left: none; border-right: none; }
    }

    /* ── Page Header ── */
    .about-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        text-align: center;
    }

    /* ── Tabs ── */
    .tab {
        overflow: hidden;
        overflow-x: auto;
        background: rgba(0,0,0,0.25);
        display: flex;
        flex-wrap: nowrap;
        border-bottom: 1px solid var(--border);
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .tab::-webkit-scrollbar { display: none; }
    .tab button {
        background: transparent;
        border: none;
        color: var(--muted);
        padding: 14px 22px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        flex: 1;
        text-align: center;
        white-space: nowrap;
        position: relative;
        letter-spacing: 0.4px;
        min-width: max-content;
    }
    .tab button:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 25%; height: 50%;
        width: 1px; background: var(--border);
    }
    .tab button:hover { background: rgba(201,168,76,0.08); color: var(--gold-light); }
    .tab button.active {
        background: rgba(201,168,76,0.12);
        color: var(--gold);
        box-shadow: inset 0 -2px 0 var(--gold);
        font-weight: 700;
    }

    /* ── Tab Content ── */
    .tabcontent {
        display: none;
        padding: 2.8rem 2.5rem;
        animation: fadeIn 0.4s ease;
    }
    .tabcontent.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Section Title ── */
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.1rem;
        font-weight: 900;
        color: var(--cream);
        margin-bottom: 1.4rem;
        position: relative;
        padding-bottom: 14px;
        line-height: 1.2;
    }
    .section-title::after {
        content: '';
        position: absolute; bottom: 0; left: 0;
        width: 72px; height: 3px;
        background: var(--gold);
        border-radius: 2px;
    }

    /* ── Section Image ── */
    .section-image {
        width: 100%;
        max-width: 400px;
        height: auto;
        border: 3px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.45);
        object-fit: cover;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .section-image:hover {
        transform: scale(1.03);
        box-shadow: 0 28px 60px rgba(0,0,0,0.55);
    }

    /* ── Section Description — checkmark bullets ── */
    .section-desc {
        font-family: 'DM Sans', sans-serif;
        font-size: 1.02rem;
        line-height: 1.7;
        color: rgba(255,255,255,0.78);
    }
    .section-desc p {
        margin-bottom: 0.8rem !important;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
        padding-left: 44px;
    }
    .section-desc p::before {
        content: "✓";
        position: absolute;
        left: 0; top: 2px;
        background: rgba(201,168,76,0.18);
        border: 1px solid var(--gold);
        color: var(--gold);
        font-weight: bold;
        font-size: 1.1rem;
        width: 30px; height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(201,168,76,0.2);
    }

    /* Responsive */
    @media (max-width: 992px) { .tabcontent { padding: 2.2rem 2rem; } }
    @media (max-width: 576px) {
        .about-header    { font-size: 1.1rem; padding: 0.9rem 1rem; }
        .section-title   { font-size: 1.65rem; }
        .tabcontent      { padding: 1.5rem 1rem; }
        .section-image   { max-width: 100%; }
        .section-desc p  { padding-left: 38px; }
        .section-desc p::before { width: 26px; height: 26px; font-size: 0.95rem; }
        .tab button      { padding: 12px 14px; font-size: 0.85rem; }
    }
</style>

<div class="main-content">
    <div class="about-page-wrapper container">
        <div class="about-card">

            <!-- ── Header ── -->
            <div class="about-header">About 04 Soccer League</div>

            <!-- ── Tabs ── -->
            <div class="tab" id="myTab">
                <?php foreach ($sections as $index => $section):
                    $tabId   = 'tab' . $section['id'];
                    $isFirst = $index === 0;
                ?>
                    <button class="tablinks <?= $isFirst ? 'active' : '' ?>"
                            onclick="openTab(event, '<?= $tabId ?>')"
                            <?= $isFirst ? 'id="defaultOpen"' : '' ?>>
                        <?= htmlspecialchars($section['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- ── Tab Panels ── -->
            <?php foreach ($sections as $index => $section):
                $tabId     = 'tab' . $section['id'];
                $isFirst   = $index === 0;
                $imagePath = !empty($section['image'])
                    ? 'uploads/admin/about_us/' . htmlspecialchars($section['image'])
                    : 'https://via.placeholder.com/800x600/1a1a2e/c9a84c?text=No+Image';
            ?>
                <div class="tabcontent <?= $isFirst ? 'active' : '' ?>" id="<?= $tabId ?>">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-8 order-lg-1">
                            <h2 class="section-title"><?= htmlspecialchars($section['title']) ?></h2>
                            <div class="section-desc">
                                <?php
                                $lines = preg_split('/\r\n|\r|\n/', trim($section['description']));
                                foreach ($lines as $line):
                                    $line = trim($line);
                                    if ($line !== ''):
                                ?>
                                    <p><?= htmlspecialchars($line) ?></p>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-4 order-lg-2 text-center">
                            <img src="<?= $imagePath ?>"
                                 alt="<?= htmlspecialchars($section['title']) ?>"
                                 class="section-image img-fluid">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div><!-- /.about-card -->
    </div><!-- /.about-page-wrapper -->
</div><!-- /.main-content -->

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