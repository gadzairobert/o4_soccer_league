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
    /* DARKISH THEME - FULLY CONSISTENT */
    html, body {
        background-color: #1e272e !important;
        color: #e0e0e0;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    .about-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    .about-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    .about-card {
        background: #2c3e50;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.4);
        border: 1px solid #444;
    }

    .article-content { padding: 2.8rem; }

    .featured-image-wrapper {
        width: 100%;
        height: 520px;
        overflow: hidden;
        background: #1a2530;
        margin-bottom: 2rem;
        box-shadow: 0 12px 36px rgba(0,0,0,0.4);
    }
    .article-featured-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        image-rendering: -webkit-optimize-contrast;
    }

    .article-title {
        font-size: 2.4rem;
        font-weight: 800;
        color: #ecf0f1;
        line-height: 1.25;
        margin: 2rem 0 1.2rem;
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        color: #bdc3c7;
        font-size: 0.98rem;
        padding-bottom: 1.4rem;
        border-bottom: 2px dashed #555;
        margin-bottom: 2.4rem;
    }

    .article-text {
        font-size: 1.12rem;
        line-height: 1.9;
        color: #ecf0f1;
        text-align: justify;
    }
    .article-text p { margin-bottom: 1.7rem; }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        color: white;
        padding: 0.8rem 2rem;
        border-radius: 2rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: 2.5rem;
    }
    .back-btn:hover {
        background: #c82333;
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(220,53,69,0.5);
        color: white;
    }

    /* SIDEBAR */
    .sidebar-card {
        background: #34495e;
        border: 1px solid #444;
        padding: 2rem;
        overflow: hidden;
    }
    .sidebar-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #ecf0f1;
        margin-bottom: 1.8rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #00d4ff;
        display: inline-block;
    }

    .other-news-item {
        display: flex;
        gap: 1.2rem;
        padding: 1.2rem;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid #444;
        align-items: center;
    }
    .other-news-item:last-child { border-bottom: none; margin-bottom: 0; }
    .other-news-item:hover {
        background: #3d566e;
        transform: translateX(6px);
    }

    .other-news-img, .other-news-img-placeholder {
        width: 96px;
        height: 96px;
        object-fit: cover;
        flex-shrink: 0;
        box-shadow: 0 5px 16px rgba(0,0,0,0.4);
        border-radius: 8px;
    }
    .other-news-img-placeholder {
        background: #2c3e50;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #444;
    }
    .other-news-img-placeholder i {
        color: #777;
    }

    .other-news-title {
        font-weight: 600;
        font-size: 1.02rem;
        color: #ecf0f1;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    .other-news-date {
        font-size: 0.88rem;
        color: #95a5a6;
    }

    /* RESPONSIVE */
    @media (max-width: 1199px) {
        .featured-image-wrapper { height: 460px; }
        .article-title { font-size: 2.1rem; }
    }
    @media (max-width: 992px) {
        .article-content { padding: 2rem; }
        .featured-image-wrapper { height: 380px; }
        .sidebar-card { margin-top: 2.5rem; }
    }
    @media (max-width: 768px) {
        .article-content { padding: 1.6rem; }
        .featured-image-wrapper { height: 320px; }
        .sidebar-card { padding: 1.6rem; }
    }
    @media (max-width: 576px) {
        .about-page-wrapper { margin-top: -30px; padding-top: 10px; }
        .about-header { font-size: 1.35rem; padding: 1.4rem 1rem; }
        .container, .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }
        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .row > [class^="col-"], .row > [class*=" col-"] {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .about-card {
            border-radius: 0;
            border-left: none;
            border-right: none;
            box-shadow: none;
        }
        .article-content { padding: 1rem !important; }
        .featured-image-wrapper { height: 260px !important; }
        .article-title { font-size: 1.7rem; margin: 1.5rem 0 1rem; }
        .article-text { font-size: 1.06rem; }
        .sidebar-card {
            margin-top: 2rem;
            padding: 1.4rem;
            border-left: none;
            border-right: none;
            border-radius: 0;
        }
        .other-news-img, .other-news-img-placeholder {
            width: 80px;
            height: 80px;
        }
        .other-news-item {
            padding: 0.9rem;
            gap: 1rem;
        }
        .other-news-title {
            font-size: 0.98rem;
            -webkit-line-clamp: 2;
        }
    }
</style>

<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <div class="about-header">
                <?= htmlspecialchars($article['title']) ?>
            </div>

            <div class="row g-5">
                <!-- MAIN ARTICLE -->
                <div class="col-lg-8">
                    <div class="article-content">
                        <?php if ($article['image']): ?>
                            <div class="featured-image-wrapper">
                                <img src="uploads/news/<?= htmlspecialchars($article['image']) ?>"
                                     class="article-featured-img"
                                     alt="<?= htmlspecialchars($article['title']) ?>"
                                     onerror="this.src='https://via.placeholder.com/1200x600/2c3e50/ffffff?text=Featured+Image'">
                            </div>
                        <?php endif; ?>

                        <h1 class="article-title">
                            <?= htmlspecialchars($article['title']) ?>
                        </h1>

                        <div class="article-meta">
                            <span><?= date('F j, Y', strtotime($article['publish_date'])) ?></span>
                            <span><?= max(1, round(str_word_count(strip_tags($article['content'])) / 200)) ?> min read</span>
                        </div>

                        <div class="article-text">
                            <?= nl2br(htmlspecialchars($article['content'])) ?>
                        </div>

                        <div class="mt-5">
                            <a href="news.php" class="back-btn">
                                ← All News
                            </a>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="col-lg-4">
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">More Articles</h3>
                        <?php if (empty($otherNews)): ?>
                            <p class="text-muted small">No other articles at the moment.</p>
                        <?php else: ?>
                            <div class="d-flex flex-column">
                                <?php foreach ($otherNews as $n): ?>
                                    <a href="news_article.php?id=<?= $n['id'] ?>" class="other-news-item text-decoration-none">
                                        <?php if ($n['image']): ?>
                                            <img src="uploads/news/<?= htmlspecialchars($n['image']) ?>"
                                                 class="other-news-img"
                                                 alt="<?= htmlspecialchars($n['title']) ?>"
                                                 onerror="this.src='https://via.placeholder.com/96/2c3e50/ffffff?text=News'">
                                        <?php else: ?>
                                            <div class="other-news-img-placeholder">
                                                <i class="bi bi-image text-muted fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="other-news-title">
                                                <?= htmlspecialchars($n['title']) ?>
                                            </div>
                                            <div class="other-news-date">
                                                <?= date('M j, Y', strtotime($n['publish_date'])) ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>