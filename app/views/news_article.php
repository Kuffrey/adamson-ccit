<?php
require_once __DIR__ . '/../models/News.php';

if (!function_exists('e')) {
    function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

// Get article ID from URL
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('HTTP/1.0 404 Not Found');
    echo '<main><div class="container" style="padding: 2rem 0;"><h1>Article not found</h1><p><a href="/adamson-ccit/public/index.php?page=news">← Back to News</a></p></div></main>';
    exit;
}

// Fetch the article
try {
    $article = null;
    $articles = News::all();
    foreach ($articles as $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            $article = $item;
            break;
        }
    }
    
    if (!$article || strtolower($article['status'] ?? '') !== 'published') {
        throw new Exception('Article not found or not published');
    }
} catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    echo '<main><div class="container" style="padding: 2rem 0;"><h1>Article not found</h1><p><a href="/adamson-ccit/public/index.php?page=news">← Back to News</a></p></div></main>';
    exit;
}

// Process article data
$title = $article['title'] ?? 'Untitled Article';
$content = $article['body'] ?? $article['content'] ?? '';
$excerpt = $article['excerpt'] ?? $article['summary'] ?? '';
$image = $article['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample1.jpg';
$category = ucfirst($article['category'] ?? 'News');
$author = $article['author'] ?? 'CCIT Communications';
$publishedAt = $article['published_at'] ?? $article['date'] ?? date('Y-m-d');
$formattedDate = date('F j, Y', strtotime($publishedAt));

// Get related articles (same category, excluding current)
$relatedArticles = [];
try {
    $allArticles = array_filter(News::all(), function($item) use ($id, $article) {
        return (int)($item['id'] ?? 0) !== $id && 
               strtolower($item['status'] ?? '') === 'published' &&
               strtolower($item['category'] ?? '') === strtolower($article['category'] ?? '');
    });
    $relatedArticles = array_slice($allArticles, 0, 3);
} catch (Exception $e) {
    $relatedArticles = [];
}

// Note: header is already included by index.php for non-admin pages
?>

<style>
.article-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.article-breadcrumb {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.article-breadcrumb a {
    color: #1e40af;
    text-decoration: none;
    font-size: 0.9rem;
}

.article-breadcrumb a:hover {
    text-decoration: underline;
}

.article-header {
    margin-bottom: 2rem;
}

.article-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #6b7280;
}

.article-category {
    background: #1e40af;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.article-title {
    font-size: 2.5rem;
    line-height: 1.2;
    color: #111827;
    margin: 0 0 1rem 0;
    font-weight: 700;
}

.article-excerpt {
    font-size: 1.2rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 2rem 0;
    font-style: italic;
}

.article-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin: 0 0 2rem 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.article-content {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #374151;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content h2 {
    font-size: 1.75rem;
    margin: 2.5rem 0 1rem 0;
    color: #111827;
    font-weight: 600;
}

.article-content h3 {
    font-size: 1.5rem;
    margin: 2rem 0 1rem 0;
    color: #111827;
    font-weight: 600;
}

.article-content h4 {
    font-size: 1.25rem;
    margin: 1.5rem 0 1rem 0;
    color: #111827;
    font-weight: 600;
}

.article-content blockquote {
    border-left: 4px solid #1e40af;
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #6b7280;
}

.article-content ul, .article-content ol {
    padding-left: 2rem;
    margin: 1.5rem 0;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-footer {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.related-articles {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.related-articles h3 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: #111827;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.related-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    color: inherit;
}

.related-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    text-decoration: none;
}

.related-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.related-card-body {
    padding: 1rem;
}

.related-card h4 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
    color: #111827;
}

.related-card p {
    font-size: 0.85rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .article-container {
        padding: 1rem;
    }
    
    .article-title {
        font-size: 2rem;
    }
    
    .article-excerpt {
        font-size: 1.1rem;
    }
    
    .article-image {
        height: 250px;
    }
    
    .article-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<main>
    <article class="article-container">
        <div class="article-breadcrumb">
            <a href="/adamson-ccit/public/index.php?page=news">← Back to News</a>
        </div>
        
        <header class="article-header">
            <div class="article-meta">
                <span class="article-category"><?= e($category) ?></span>
                <time datetime="<?= e($publishedAt) ?>"><?= e($formattedDate) ?></time>
                <span>By <?= e($author) ?></span>
            </div>
            <h1 class="article-title"><?= e($title) ?></h1>
            <?php if ($excerpt): ?>
                <p class="article-excerpt"><?= e($excerpt) ?></p>
            <?php endif; ?>
        </header>
        
        <?php if ($image): ?>
            <img src="<?= e($image) ?>" alt="<?= e($title) ?>" class="article-image">
        <?php endif; ?>
        
        <div class="article-content">
            <?= $content ? $content : '<p>No content available for this article.</p>' ?>
        </div>
        
        <footer class="article-footer">
            <a href="/adamson-ccit/public/index.php?page=news" class="btn btn--outline-blue">← Back to All News</a>
        </footer>
        
        <?php if (!empty($relatedArticles)): ?>
        <section class="related-articles">
            <h3>Related Articles</h3>
            <div class="related-grid">
                <?php foreach ($relatedArticles as $related): ?>
                    <a href="/adamson-ccit/public/index.php?page=news_article&id=<?= e($related['id'] ?? '') ?>" class="related-card">
                        <img src="<?= e($related['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample1.jpg') ?>" alt="<?= e($related['title'] ?? '') ?>">
                        <div class="related-card-body">
                            <h4><?= e($related['title'] ?? 'Untitled') ?></h4>
                            <p><?= e($related['excerpt'] ?? mb_substr(strip_tags($related['body'] ?? ''), 0, 100) . '...') ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </article>
</main>

<!-- Note: footer is already included by index.php for non-admin pages -->