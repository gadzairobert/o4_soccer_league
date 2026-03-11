<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
include 'includes/properties.php';
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

    /* ── Wrapper — preserves original margin/padding ── */
    .container.py-4 {
        margin-top: -38px !important;
        padding-top: 6px !important;
    }

    /* ── Gallery Card — LIGHT ── */
    .gallery-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    @media (max-width: 575.98px) {
        .gallery-card { border-radius: 0; border-left: none; border-right: none; }
    }

    /* ── Gallery Header — DARK ── */
    .gallery-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        color: var(--cream);
        padding: 1rem 1.8rem;
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
    }
    @media (max-width: 575.98px) {
        .gallery-header { font-size: 1.2rem; padding: 1rem; }
    }

    /* ── Gallery Body — LIGHT ── */
    .gallery-body {
        padding: 2rem;
        background: #ffffff;
    }
    @media (max-width: 575.98px) {
        .gallery-body { padding: 1.2rem; }
    }

    /* ── Gallery Grid ── */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1.2rem;
    }
    @media (max-width: 1399.98px) { .gallery-grid { grid-template-columns: repeat(5, 1fr); } }
    @media (max-width: 1199.98px) { .gallery-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 991.98px)  { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 767.98px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575.98px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 0.85rem; } }

    /* ── Gallery Item — LIGHT ── */
    .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        transition: all 0.35s ease;
        cursor: pointer;
        aspect-ratio: 1 / 1;
        background: #f9f7f2;
    }
    .gallery-item:hover {
        transform: translateY(-8px);
        border-color: rgba(201,168,76,0.5);
        box-shadow: 0 16px 36px rgba(0,0,0,0.12), 0 0 0 1px rgba(201,168,76,0.2);
    }
    @media (max-width: 575.98px) {
        .gallery-item { border-radius: 8px; }
    }

    /* Image */
    .gallery-item img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.45s ease;
        display: block;
    }
    .gallery-item:hover img { transform: scale(1.1); }

    /* Caption overlay */
    .gallery-caption {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.75));
        color: white;
        padding: 2rem 0.8rem 0.75rem;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        text-align: center;
        font-size: 0.88rem;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    .gallery-item:hover .gallery-caption { opacity: 1; }

    /* Date tag */
    .gallery-date {
        position: absolute;
        top: 8px; right: 8px;
        background: rgba(0,0,0,0.55);
        border: 1px solid rgba(201,168,76,0.3);
        color: var(--gold);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 20px;
        letter-spacing: 0.3px;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    .gallery-item:hover .gallery-date { opacity: 1; }

    /* ── Empty State ── */
    .gallery-empty {
        text-align: center;
        padding: 5rem 2rem;
        font-family: 'DM Sans', sans-serif;
        color: var(--muted);
    }
    .gallery-empty i {
        font-size: 3.5rem;
        color: rgba(154,111,30,0.3);
        display: block;
        margin-bottom: 1rem;
    }

    /* ── Modal ── */
    .gallery-modal-content {
        background: transparent;
        border: none;
        box-shadow: none;
    }
    .gallery-modal-close {
        position: absolute;
        top: 12px; right: 12px;
        width: 40px; height: 40px;
        background: rgba(0,0,0,0.7);
        border: 1px solid rgba(201,168,76,0.4);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--gold);
        font-size: 1.3rem;
        z-index: 10;
        transition: all 0.2s;
        line-height: 1;
    }
    .gallery-modal-close:hover {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
    }
    .modal-title-text {
        font-family: 'Playfair Display', serif;
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }
    .modal-desc-text {
        font-family: 'DM Sans', sans-serif;
        color: rgba(255,255,255,0.75);
        font-size: 0.9rem;
        background: rgba(0,0,0,0.55);
        border: 1px solid rgba(201,168,76,0.25);
        border-radius: 8px;
        display: inline-block;
        padding: 0.5rem 1.2rem;
    }
    .modal-date-text {
        font-family: 'DM Sans', sans-serif;
        color: var(--gold);
        font-size: 0.82rem;
        margin-top: 0.5rem;
        opacity: 0.85;
    }
</style>

<div class="container py-4 pb-5">
    <div class="gallery-card">
        <div class="gallery-header">Photo Gallery</div>

        <div class="gallery-body">
            <?php
            $regularImages = getAllGalleryImages();

            $stmt = $pdo->query("
                SELECT
                    image AS filename,
                    caption AS title,
                    '' AS description,
                    created_at AS uploaded_at
                FROM tournament_images
                WHERE image IS NOT NULL AND image != ''
                ORDER BY created_at DESC
            ");
            $tournamentImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $allImages = [];
            foreach ($regularImages as $img) {
                $allImages[] = [
                    'path'        => 'uploads/gallery/' . $img['image'],
                    'title'       => $img['title'] ?? '',
                    'description' => $img['description'] ?? '',
                    'date'        => $img['uploaded_at'],
                    'source'      => 'gallery'
                ];
            }
            foreach ($tournamentImages as $img) {
                $allImages[] = [
                    'path'        => 'uploads/tournaments/' . $img['filename'],
                    'title'       => $img['title'] ?? '',
                    'description' => $img['description'],
                    'date'        => $img['uploaded_at'],
                    'source'      => 'tournament'
                ];
            }
            usort($allImages, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
            ?>

            <?php if (empty($allImages)): ?>
                <div class="gallery-empty">
                    <i class="bi bi-images"></i>
                    <p>No images in the gallery yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($allImages as $index => $img):
                        $fullPath = $img['path'];
                        $title    = htmlspecialchars($img['title'] ?: 'Untitled');
                        $desc     = htmlspecialchars($img['description']);
                        $date     = date('j M Y', strtotime($img['date']));
                    ?>
                        <div class="gallery-item"
                             data-bs-toggle="modal"
                             data-bs-target="#galleryModal<?= $index ?>">
                            <img src="<?= $fullPath ?>" alt="<?= $title ?>" loading="lazy">
                            <?php if ($img['title']): ?>
                                <div class="gallery-caption"><?= $title ?></div>
                            <?php endif; ?>
                            <div class="gallery-date"><?= $date ?></div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="galleryModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content gallery-modal-content position-relative">
                                    <button type="button"
                                            class="gallery-modal-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close">
                                        &times;
                                    </button>
                                    <img src="<?= $fullPath ?>"
                                         class="img-fluid"
                                         alt="<?= $title ?>"
                                         style="max-height:82vh;width:auto;margin:0 auto;display:block;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.7);">
                                    <div class="text-center mt-3 pb-2">
                                        <?php if ($img['title']): ?>
                                            <div class="modal-title-text"><?= $title ?></div>
                                        <?php endif; ?>
                                        <?php if ($desc): ?>
                                            <div class="modal-desc-text"><?= nl2br($desc) ?></div>
                                        <?php endif; ?>
                                        <div class="modal-date-text"><?= $date ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>