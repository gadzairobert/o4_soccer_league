<?php
// MUST BE THE VERY FIRST LINE
require 'config.php'; // Starts session + DB connection
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

// ====================== CONTACT FORM SUBMISSION ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    // ... (your existing form handling code here - unchanged) ...
}

// ====================== SOCIAL LINKS & MAP ======================
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
    if (strpos($name, 'whatsapp') !== false) return 'whatsapp';
    if (strpos($name, 'facebook') !== false) return 'facebook';
    if (strpos($name, 'instagram') !== false) return 'instagram';
    if (strpos($name, 'youtube') !== false) return 'youtube';
    if (strpos($name, 'tiktok') !== false) return 'tiktok';
    if (strpos($name, 'email') !== false) return 'email';
    if (strpos($name, 'phone') !== false || strpos($name, 'call') !== false) return 'phone';
    return '';
}
?>

<!-- PUSH FOOTER TO BOTTOM OF VIEWPORT -->
<style>
    html, body {
        height: 100%;
        margin: 0;
    }
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .main-content {
        flex: 1 0 auto;
    }
    footer {
        flex-shrink: 0;
    }

    /* Contact Page Styling */
    .contact-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }
    .contact-card {
        background: white;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        border: 1px solid #e9ecef;
    }
    .contact-header {
        background: #2c3e50;
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
    }
    .contact-body {
        padding: 2rem 2.5rem;
    }

    /* Tight 3-column layout - everything aligned to bottom */
    .bottom-layout {
        display: grid;
        grid-template-columns: 1fr 260px 1fr;
        gap: 1.8rem;
        align-items: end;                    /* Bottom alignment */
        margin-top: 1.5rem;
    }
    .bottom-layout > div {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Map */
    .map-container {
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        height: 490px;
        border: 6px solid white;
        margin-top: auto;
    }

    /* Social Icons Column - smaller & tighter */
    .social-vertical {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
        align-items: center;
        padding: 1.5rem 0;
        justify-content: flex-end;
        margin-top: auto;
    }
    .social-vertical a {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #2c3e50;
        transition: transform 0.3s ease;
    }
    .social-vertical a:hover {
        transform: translateY(-5px);
    }
    .social-vertical .contact-icon {
        width: 68px;
        height: 68px;
        background: #2c3e50;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        box-shadow: 0 6px 16px rgba(44,62,80,0.4);
        margin-bottom: 0.5rem;
    }
    .social-vertical.whatsapp .contact-icon { background:#25D366; }
    .social-vertical.facebook .contact-icon { background:#1877F2; }
    .social-vertical.instagram .contact-icon { background:linear-gradient(45deg,#f9ce34,#ee2a7b,#6228d7); }
    .social-vertical.youtube .contact-icon { background:#FF0000; }
    .social-vertical.tiktok .contact-icon { background:#000; }
    .social-vertical .title {
        font-weight: 600;
        font-size: 0.95rem;
        text-align: center;
    }

    /* Form */
    .form-card {
        background: #f8fff9;
        border: 1px solid #d1f0d9;
        padding: 2rem;
        margin-top: auto;
    }
    .btn-send {
        background: #2c3e50;
        color: white;
        border-radius: 50px;
        padding: 0.9rem 3.5rem;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .bottom-layout {
            grid-template-columns: 1fr 240px 1fr;
            gap: 1.5rem;
        }
    }
    @media (max-width: 992px) {
        .bottom-layout {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .social-vertical {
            flex-direction: row;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .social-vertical a { margin: 0 0.8rem; }
    }
    @media (max-width: 576px) {
        .contact-body { padding: 1.5rem; }
        .map-container { height: 360px; }
        .social-vertical .contact-icon {
            width: 60px; height: 60px; font-size: 1.9rem;
        }
    }
</style>

<!-- MAIN CONTENT (pushes footer down) -->
<div class="main-content">
    <div class="container contact-page-wrapper">
        <div class="contact-card">
            <div class="contact-header">Get in touch with us </div>
            <div class="contact-body">

                <div class="bottom-layout">
                    <!-- Map -->
                    <div>
                        <?php if ($map_url): ?>
                            <div class="map-container">
                                <iframe src="<?=htmlspecialchars($map_url)?>"
                                        width="100%" height="490" style="border:0;"
                                        allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Social Icons Column -->
                    <div class="social-vertical">
                        <?php foreach ($social_items as $item):
                            $class = getContactClass($item['platform_name']);
                        ?>
                            <a href="<?=htmlspecialchars($item['url'])?>"
                               <?=(!str_contains($item['url'],'mailto:') && !str_contains($item['url'],'tel:')) ? 'target="_blank" rel="noopener"' : ''?>
                               class="text-decoration-none <?=$class?>">
                                <div class="contact-icon">
                                    <i class="<?=htmlspecialchars($item['icon_class'])?>"></i>
                                </div>
                                <span class="title"><?=htmlspecialchars($item['platform_name'])?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Contact Form -->
                    <div class="form-card">
                        <h4 class="text-center mb-4 fw-bold" style="color:#2c3e50;">Send Us a Message</h4>
                        <?php if ($form_success): ?>
                            <div class="alert alert-success"><?=$form_success?></div>
                        <?php endif; ?>
                        <?php if ($form_error): ?>
                            <div class="alert alert-danger"><?=$form_error?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="send_message" value="1">
                            <div class="row g-3">
                                <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Your Name" value="<?=htmlspecialchars($_POST['name']??'')?>" required></div>
                                <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Your Email" value="<?=htmlspecialchars($_POST['email']??'')?>" required></div>
                                <div class="col-12"><input type="text" name="subject" class="form-control" placeholder="Subject" value="<?=htmlspecialchars($_POST['subject']??'')?>" required></div>
                                <div class="col-12"><textarea name="message" rows="6" class="form-control" placeholder="Your message..." required><?=htmlspecialchars($_POST['message']??'')?></textarea></div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-send">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.bottom-layout -->

            </div>
        </div>
    </div>
</div>
<!-- /.main-content -->

<!-- FOOTER - now properly at the bottom of the page -->
<?php include 'includes/footer.php'; ?>

<?php ob_end_flush(); ?>