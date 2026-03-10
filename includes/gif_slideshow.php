<?php
// includes/gif_slideshow.php
// ORIGINAL FULL-BLEED RESTORED exactly as you want on desktop (touches far left/right edges)
// Fixed horizontal scroll (common 100vw issue)
// Fixed mobile: no black gap below, full image visible, no side cropping

$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'footer_logo' AND is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
$stmt->execute();
$banner = $stmt->fetch(PDO::FETCH_ASSOC);

$bannerImage = '';
if ($banner && !empty($banner['filename']) && file_exists('uploads/admin/logos/' . $banner['filename'])) {
    $bannerImage = 'uploads/admin/logos/' . htmlspecialchars($banner['filename']);
}
?>

<?php if ($bannerImage): ?>
<div class="full-width-slideshow">
    <img src="<?= $bannerImage ?>" alt="League Banner" class="slideshow-image">
</div>

<style>
    /* Original full-bleed container - restored exactly */
    .full-width-slideshow {
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        overflow: hidden;
        line-height: 0;
        margin-top: -40px !important;   /* Your perfect no-gap value */
        margin-bottom: 40px;            /* Space before content */
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        z-index: 100;
    }

    .slideshow-image {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;            /* FULL image visible - no cropping or black gaps */
        object-position: center top;    /* Text/logos stay at top */
        background: transparent;        /* No black background */
        border: 6px solid #ffffff;
        outline: 2px solid #dc3545;
        outline-offset: -4px;
        box-sizing: border-box;
    }

    /* Mobile adjustments - no black gap, full visibility */
    @media (max-width: 768px) {
        .full-width-slideshow {
            margin-top: -30px !important;
            margin-bottom: 30px;
        }
        .slideshow-image {
            border-width: 4px;
            outline-offset: -3px;
        }
    }

    @media (max-width: 576px) {
        .full-width-slideshow {
            margin-top: -25px !important;
            margin-bottom: 25px;
        }
    }

    /* FIX HORIZONTAL SCROLL (caused by 100vw including scrollbar) */
    body {
        overflow-x: hidden;
    }
</style>
<?php endif; ?>