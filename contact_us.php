<?php
require 'config.php';
ob_start();
include 'includes/header.php';
include 'includes/properties.php';
require_once 'includes/class.phpmailer.php';
require_once 'includes/class.smtp.php';

if (!function_exists('getSMTPSettings')) {
    function getSMTPSettings() {
        global $pdo;
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'smtp_%'");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
$smtp = getSMTPSettings();

$form_success = $form_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    // ... (your existing form handling code here - unchanged) ...
}

$all_items = getContactSocialLinks();
$map_url = '';
$social_items = [];

foreach ($all_items as $item) {
    if (stripos($item['platform_name'], 'map') !== false || stripos($item['url'], 'google.com/maps') !== false) {
        $map_url = $item['url'];
    } else {
        $social_items[] = $item;
    }
}

function getContactClass($platform_name) {
    $name = strtolower($platform_name);
    if (strpos($name, 'whatsapp')  !== false) return 'whatsapp';
    if (strpos($name, 'facebook')  !== false) return 'facebook';
    if (strpos($name, 'instagram') !== false) return 'instagram';
    if (strpos($name, 'youtube')   !== false) return 'youtube';
    if (strpos($name, 'tiktok')    !== false) return 'tiktok';
    if (strpos($name, 'email')     !== false) return 'email';
    if (strpos($name, 'phone') !== false || strpos($name, 'call') !== false) return 'phone';
    return '';
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
    .contact-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .contact-page-wrapper { margin-top: 0; padding: 1rem 0 3rem; width: 100%; }
    }

    /* ── Outer Card ── */
    .contact-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
    }
    @media (max-width: 767px) {
        .contact-card { border-radius: 0; border-left: none; border-right: none; }
    }

    /* ── Page Header ── */
    .contact-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        text-align: center;
    }

    /* ── Body ── */
    .contact-body {
        padding: 2rem 2.5rem;
    }
    @media (max-width: 576px) {
        .contact-body { padding: 1.4rem 1rem; }
    }

    /* ── Three-Column Layout ── */
    .bottom-layout {
        display: grid;
        grid-template-columns: 1fr 260px 1fr;
        gap: 1.8rem;
        align-items: end;
        margin-top: 1.2rem;
    }
    .bottom-layout > div {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    @media (max-width: 1200px) {
        .bottom-layout { grid-template-columns: 1fr 240px 1fr; gap: 1.4rem; }
    }
    @media (max-width: 992px) {
        .bottom-layout { grid-template-columns: 1fr; gap: 2rem; }
    }

    /* ── Map ── */
    .map-container {
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 10px 32px rgba(0,0,0,0.4);
        border: 2px solid var(--border);
        height: 490px;
        margin-top: auto;
    }
    .map-container iframe { display: block; }
    @media (max-width: 576px) { .map-container { height: 340px; } }

    /* ── Social Icons Column ── */
    .social-vertical {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
        align-items: center;
        padding: 1.2rem 0;
        justify-content: flex-end;
        margin-top: auto;
    }
    .social-vertical a {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: var(--muted);
        transition: transform 0.3s ease, color 0.2s ease;
    }
    .social-vertical a:hover {
        transform: translateY(-5px);
        color: var(--cream);
    }
    .contact-icon {
        width: 64px; height: 64px;
        background: rgba(255,255,255,0.07);
        border: 1px solid var(--border);
        color: var(--gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.9rem;
        box-shadow: 0 6px 18px rgba(0,0,0,0.3);
        margin-bottom: 0.45rem;
        transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .social-vertical a:hover .contact-icon {
        background: rgba(201,168,76,0.15);
        border-color: var(--gold);
        box-shadow: 0 10px 28px rgba(201,168,76,0.2);
    }
    /* Platform-specific accent colours on hover */
    .whatsapp  a:hover .contact-icon,
    a.whatsapp:hover  .contact-icon  { background: rgba(37,211,102,0.15) !important; border-color: #25D366 !important; color: #25D366 !important; }
    .facebook  a:hover .contact-icon,
    a.facebook:hover  .contact-icon  { background: rgba(24,119,242,0.15) !important; border-color: #1877F2 !important; color: #1877F2 !important; }
    .instagram a:hover .contact-icon,
    a.instagram:hover .contact-icon  { background: rgba(238,42,123,0.15) !important; border-color: #ee2a7b !important; color: #ee2a7b !important; }
    .youtube   a:hover .contact-icon,
    a.youtube:hover   .contact-icon  { background: rgba(255,0,0,0.15)   !important; border-color: #FF0000 !important; color: #FF0000 !important; }
    .tiktok    a:hover .contact-icon,
    a.tiktok:hover    .contact-icon  { background: rgba(105,201,208,0.15) !important; border-color: #69c9d0 !important; color: #69c9d0 !important; }

    .social-vertical .title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.82rem;
        text-align: center;
        color: var(--muted);
        transition: color 0.2s;
    }
    .social-vertical a:hover .title { color: var(--cream); }

    @media (max-width: 992px) {
        .social-vertical {
            flex-direction: row;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.4rem;
        }
    }

    /* ── Contact Form ── */
    .form-card {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.8rem;
        margin-top: auto;
    }
    .form-card h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--cream);
        margin-bottom: 1.4rem;
        text-align: center;
    }

    /* Inputs */
    .form-control {
        background: rgba(255,255,255,0.06) !important;
        border: 1px solid var(--border) !important;
        color: var(--cream) !important;
        font-family: 'DM Sans', sans-serif;
        border-radius: 8px !important;
        padding: 0.65rem 0.9rem;
        transition: border-color 0.2s ease, background 0.2s ease;
    }
    .form-control::placeholder { color: var(--muted) !important; }
    .form-control:focus {
        background: rgba(255,255,255,0.09) !important;
        border-color: var(--gold) !important;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12) !important;
        color: var(--cream) !important;
        outline: none;
    }
    textarea.form-control { resize: vertical; min-height: 140px; }

    /* Submit Button */
    .btn-send {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--ink);
        background: var(--gold);
        border: none;
        border-radius: 50px;
        padding: 0.75rem 3rem;
        transition: all 0.25s ease;
        box-shadow: 0 6px 20px rgba(201,168,76,0.3);
    }
    .btn-send:hover {
        background: var(--gold-light);
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(201,168,76,0.4);
        color: var(--ink);
    }

    /* Alerts */
    .alert-success {
        background: rgba(74,222,128,0.1);
        border: 1px solid rgba(74,222,128,0.3);
        color: #4ade80;
        border-radius: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
    }
    .alert-danger {
        background: rgba(248,113,113,0.1);
        border: 1px solid rgba(248,113,113,0.3);
        color: #f87171;
        border-radius: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
    }
</style>

<div class="main-content">
    <div class="contact-page-wrapper container">
        <div class="contact-card">

            <!-- ── Header ── -->
            <div class="contact-header">Get in Touch With Us</div>

            <div class="contact-body">
                <div class="bottom-layout">

                    <!-- Map -->
                    <div>
                        <?php if ($map_url): ?>
                            <div class="map-container">
                                <iframe src="<?= htmlspecialchars($map_url) ?>"
                                        width="100%" height="490" style="border:0;"
                                        allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Social Icons -->
                    <div class="social-vertical">
                        <?php foreach ($social_items as $item):
                            $class = getContactClass($item['platform_name']);
                        ?>
                            <a href="<?= htmlspecialchars($item['url']) ?>"
                               <?= (!str_contains($item['url'], 'mailto:') && !str_contains($item['url'], 'tel:')) ? 'target="_blank" rel="noopener"' : '' ?>
                               class="text-decoration-none <?= $class ?>">
                                <div class="contact-icon">
                                    <i class="<?= htmlspecialchars($item['icon_class']) ?>"></i>
                                </div>
                                <span class="title"><?= htmlspecialchars($item['platform_name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Contact Form -->
                    <div class="form-card">
                        <h4>Send Us a Message</h4>

                        <?php if ($form_success): ?>
                            <div class="alert alert-success mb-3"><?= $form_success ?></div>
                        <?php endif; ?>
                        <?php if ($form_error): ?>
                            <div class="alert alert-danger mb-3"><?= $form_error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="send_message" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" placeholder="Your Name"
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" class="form-control" placeholder="Your Email"
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="subject" class="form-control" placeholder="Subject"
                                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="message" class="form-control" placeholder="Your message..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-send">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div><!-- /.bottom-layout -->
            </div><!-- /.contact-body -->
        </div><!-- /.contact-card -->
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>