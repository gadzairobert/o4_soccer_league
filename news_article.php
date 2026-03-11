<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/properties.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    ob_end_clean();
    header('Location: news.php');
    exit;
}
$article = getPublishedArticleById($id);
if (!$article) {
    ob_end_clean();
    http_response_code(404);
    include 'includes/header.php';
    echo "<div class='container py-5 text-center'><h2>Article not found</h2><a href='news.php' class='btn btn-outline-primary mt-3'>Back to News</a></div>";
    include 'includes/footer.php';
    ob_end_flush();
    exit;
}
$otherNews = getOtherPublishedArticles($id, 6);
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
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .article-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .article-page-wrapper {
            margin-top: 0;
            padding: 1rem 0 3rem;
            width: 100%;
        }
    }

    /* ── Outer Card — LIGHT ── */
    .article-outer-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    @media (max-width: 767px) {
        .article-outer-card {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
    }

    /* ── Article Header Bar — DARK ── */
    .article-header-bar {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .article-header-bar .back-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--gold);
        background: rgba(201,168,76,0.1);
        border: 1px solid rgba(201,168,76,0.3);
        padding: 0.25rem 0.85rem;
        border-radius: 20px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .article-header-bar .back-link:hover {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
        color: var(--gold-light);
    }
    .article-header-bar .header-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Body Layout ── */
    .article-body {
        display: flex;
        gap: 0;
    }

    /* ── Main Article — LIGHT ── */
    .article-main {
        flex: 1;
        min-width: 0;
        padding: 2.5rem;
        border-right: 1px solid #e5e7eb;
        background: #ffffff;
    }
    @media (max-width: 992px) {
        .article-body  { flex-direction: column; }
        .article-main  { border-right: none; border-bottom: 1px solid #e5e7eb; padding: 2rem; }
    }
    @media (max-width: 576px) {
        .article-main  { padding: 1.2rem 1rem; }
    }

    /* ── Featured Image ── */
    .featured-image-wrapper {
        width: 100%;
        height: 500px;
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        border: 1px solid #e5e7eb;
    }
    .article-featured-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .featured-image-wrapper:hover .article-featured-img { transform: scale(1.02); }

    @media (max-width: 1199px) { .featured-image-wrapper { height: 420px; } }
    @media (max-width: 992px)  { .featured-image-wrapper { height: 360px; } }
    @media (max-width: 768px)  { .featured-image-wrapper { height: 280px; } }
    @media (max-width: 576px)  { .featured-image-wrapper { height: 230px; border-radius: 8px; } }

    /* ── Article Title ── */
    .article-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.2rem;
        font-weight: 900;
        color: var(--text-main);
        line-height: 1.25;
        margin: 0 0 1.2rem;
    }
    @media (max-width: 992px) { .article-title { font-size: 1.9rem; } }
    @media (max-width: 576px) { .article-title { font-size: 1.55rem; } }

    /* ── Meta Row ── */
    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        padding-bottom: 1.4rem;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 2.2rem;
    }
    .meta-pill {
        color: var(--gold-dark);
        background: rgba(201,168,76,0.1);
        border: 1px solid rgba(201,168,76,0.25);
        padding: 0.2rem 0.75rem;
        border-radius: 20px;
        letter-spacing: 0.4px;
    }

    /* ── Article Text — LIGHT ── */
    .article-text {
        font-family: 'DM Sans', sans-serif;
        font-size: 1.05rem;
        line-height: 1.9;
        color: var(--text-soft);
    }
    .article-text p { margin-bottom: 1.6rem; }

    /* ── Sidebar — slightly off-white ── */
    .article-sidebar {
        width: 340px;
        flex-shrink: 0;
        padding: 2rem 1.6rem;
        background: #f9f7f2;
        border-left: 1px solid #e5e7eb;
    }
    @media (max-width: 992px) {
        .article-sidebar { width: 100%; padding: 1.8rem; border-left: none; }
    }
    @media (max-width: 576px) {
        .article-sidebar { padding: 1.2rem 1rem; }
    }

    .sidebar-heading {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 1.4rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
        display: inline-block;
    }

    /* ── Sidebar Item — LIGHT ── */
    .sidebar-news-item {
        display: flex;
        gap: 1rem;
        padding: 0.9rem 0.7rem;
        border-bottom: 1px solid #e5e7eb;
        text-decoration: none;
        color: inherit;
        border-radius: 8px;
        transition: background 0.2s ease, transform 0.2s ease;
        align-items: center;
    }
    .sidebar-news-item:last-child { border-bottom: none; }
    .sidebar-news-item:hover {
        background: #fdf9f0;
        transform: translateX(4px);
    }
    .sidebar-thumb, .sidebar-thumb-placeholder {
        width: 80px;
        height: 80px;
        object-fit: cover;
        flex-shrink: 0;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .sidebar-thumb-placeholder {
        background: #f0ede8;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-thumb-placeholder i { color: rgba(154,111,30,0.35); font-size: 1.4rem; }
    .sidebar-item-title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-soft);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.4rem;
        transition: color 0.2s;
    }
    .sidebar-news-item:hover .sidebar-item-title { color: var(--gold-dark); }
    .sidebar-item-date {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--gold-dark);
        letter-spacing: 0.4px;
    }
</style>

<div class="main-content">
    <div class="article-page-wrapper">
        <div class="article-outer-card">

            <!-- ── Header Bar ── -->
            <div class="article-header-bar">
                <a href="news.php" class="back-link">← All News</a>
                <span class="header-title"><?= htmlspecialchars($article['title']) ?></span>
            </div>

            <!-- ── Body ── -->
            <div class="article-body">

                <!-- Main Content -->
                <div class="article-main">
                    <?php if ($article['image']): ?>
                        <div class="featured-image-wrapper">
                            <img src="uploads/news/<?= htmlspecialchars($article['image']) ?>"
                                 class="article-featured-img"
                                 alt="<?= htmlspecialchars($article['title']) ?>"
                                 onerror="this.src='https://via.placeholder.com/1200x600/f0ede8/9a6f1e?text=Featured+Image'">
                        </div>
                    <?php endif; ?>

                    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>

                    <div class="article-meta">
                        <span class="meta-pill">
                            <?= date('F j, Y', strtotime($article['publish_date'])) ?>
                        </span>
                        <span class="meta-pill">
                            <?= max(1, round(str_word_count(strip_tags($article['content'])) / 200)) ?> min read
                        </span>
                    </div>

                    <div class="article-text">
                        <?= nl2br(htmlspecialchars($article['content'])) ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <span class="sidebar-heading">More Articles</span>
                    <?php if (empty($otherNews)): ?>
                        <p style="font-family:'DM Sans',sans-serif;font-size:0.88rem;color:var(--muted);">No other articles at the moment.</p>
                    <?php else: ?>
                        <?php foreach ($otherNews as $n): ?>
                            <a href="news_article.php?id=<?= $n['id'] ?>" class="sidebar-news-item">
                                <?php if ($n['image']): ?>
                                    <img src="uploads/news/<?= htmlspecialchars($n['image']) ?>"
                                         class="sidebar-thumb"
                                         alt="<?= htmlspecialchars($n['title']) ?>"
                                         onerror="this.src='https://via.placeholder.com/80/f0ede8/9a6f1e?text=N'">
                                <?php else: ?>
                                    <div class="sidebar-thumb-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="sidebar-item-title"><?= htmlspecialchars($n['title']) ?></div>
                                    <div class="sidebar-item-date"><?= date('M j, Y', strtotime($n['publish_date'])) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </aside>

            </div><!-- /.article-body -->

        </div><!-- /.article-outer-card -->
    </div><!-- /.article-page-wrapper -->
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>