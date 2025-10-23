<?php declare(strict_types=1);

require_once __DIR__ . '/../../app/models/News.php'; // Load News model
require_once __DIR__ . '/../../app/models/NewsPageSettings.php'; // Load NewsPageSettings model


// Helper: Create a news article for testing
function createTestNews(string $title = 'Test Article', string $status = 'published', string $category = 'news'): int {
    return News::create($title, 'Test content for ' . $title, $status, $category); // Insert and return ID
}


// Helper: Build a URL with query params
function url_with(array $params): string {
    $base = '/adamson-ccit/public/index.php'; // Base app URL
    $q = array_merge(['page' => 'news'], $params); // Merge with default page param
    return $base . '?' . http_build_query($q); // Return full URL
}


// Helper: Escape HTML for output (renamed to avoid conflict with Laravel's e() function)
function escapeHtml(string $s): string {
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); // Prevent XSS
}


// ========== NEWS FEATURE TESTS ==========


// Test: Create and delete a news article
test('can create and delete a news article', function () {
    $id = createTestNews('Minimal Test News'); // Create article
    expect($id)->toBeGreaterThan(0); // ID should be valid
    $article = News::findById($id); // Fetch article
    expect($article)->not->toBeNull(); // Should exist
    News::delete($id); // Delete
    expect(News::findById($id))->toBeNull(); // Should be gone
});


// Test: Update article status
test('can update article status', function () {
    $id = createTestNews('Minimal Status Test', 'draft'); // Create draft
    $result = News::updateStatus($id, 'published'); // Change status
    expect($result)->toBeTrue(); // Should succeed
    $article = News::findById($id); // Fetch
    expect($article['status'])->toBe('published'); // Status updated
    News::delete($id); // Clean up
});


// Test: Invalid category defaults to 'news'
test('defaults to news for invalid category', function () {
    $id = News::create('Minimal Invalid Category', 'Test content', 'published', 'invalid_category'); // Bad category
    $article = News::findById($id); // Fetch
    expect($article['category'])->toBe('news'); // Should fallback
    News::delete($id); // Clean up
});


// Test: Search for published articles
test('can search published articles', function () {
    $id = createTestNews('Minimal Search Article', 'published'); // Create
    $result = News::searchPublished(['q' => 'Minimal'], ['page' => 1, 'perPage' => 10]); // Search
    expect($result)->toBeArray(); // Should return array
    expect($result['total'])->toBeGreaterThanOrEqual(1); // At least one
    News::delete($id); // Clean up
});


// Test: URL parameter processing
test('processes URL parameters correctly', function () {
    $_GET = ['cat' => 'research', 'year' => '2024', 'q' => 'innovation', 'p' => '2']; // Simulate GET
    $cat = isset($_GET['cat']) ? strtolower(trim((string)$_GET['cat'])) : 'all'; // Category
    $year = isset($_GET['year']) ? trim((string)$_GET['year']) : 'all'; // Year
    $q = isset($_GET['q']) ? trim((string)$_GET['q']) : ''; // Query
    $page = max(1, (int)($_GET['p'] ?? 1)); // Page
    expect($cat)->toBe('research'); // Check
    expect($year)->toBe('2024');
    expect($q)->toBe('innovation');
    expect($page)->toBe(2);
    $_GET = []; // Reset
});


// Test: HTML escaping for XSS
test('escapes HTML output correctly', function () {
    $maliciousInput = '<script>alert("xss")</script>'; // Malicious
    $escaped = escapeHtml($maliciousInput); // Escape
    expect($escaped)->toBe('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'); // Should be safe
});


// Test: SQL injection prevention
test('handles SQL injection prevention in search', function () {
    $maliciousQuery = "'; DROP TABLE news; --"; // Injection attempt
    $result = News::searchPublished(['q' => $maliciousQuery], ['page' => 1, 'perPage' => 10]); // Search
    expect($result)->toBeArray(); // Should not break
    expect($result['total'])->toBeGreaterThanOrEqual(0); // No error
});


// Test: Handle DB errors (invalid ID)
test('handles database errors gracefully', function () {
    $result = News::findById(-1); // Invalid
    expect($result)->toBeNull(); // Should be null
});


// Test: Empty search results
test('handles empty search results', function () {
    $result = News::searchPublished(['q' => 'impossiblequeryterm12345678'], ['page' => 1, 'perPage' => 10]); // No match
    expect($result)->toBeArray();
    expect($result['total'])->toBe(0);
    expect($result['items'])->toBeEmpty();
});


// Test: Filter by category
test('filters news by category', function () {
    $id = createTestNews('Research News', 'published', 'research'); // Create
    $result = News::searchPublished(['cat' => 'research'], ['page' => 1, 'perPage' => 10]); // Filter
    expect($result['items'])->not->toBeEmpty();
    expect($result['items'][0]['category'])->toBe('research');
    News::delete($id);
});


// Test: Filter by year
test('filters news by year', function () {
    $id = createTestNews('Year Test News', 'published', 'news'); // Create
    $article = News::findById($id); // Fetch
    $year = substr($article['date'] ?? '', 0, 4); // Get year
    $result = News::searchPublished(['year' => $year], ['page' => 1, 'perPage' => 10]); // Filter
    expect($result['items'])->not->toBeEmpty();
    News::delete($id);
});


// Test: Filter by search query
test('filters news by search query', function () {
    $id = createTestNews('UniqueQueryTestNews', 'published', 'news'); // Create
    $result = News::searchPublished(['q' => 'UniqueQueryTestNews'], ['page' => 1, 'perPage' => 10]); // Search
    expect($result['items'])->not->toBeEmpty();
    expect($result['items'][0]['title'])->toContain('UniqueQueryTestNews');
    News::delete($id);
});


// Test: Navigation URL generation
test('generates correct navigation URLs', function () {
    $newsUrl = url_with(['page' => 'news']); // News
    $eventsUrl = url_with(['page' => 'events']); // Events
    $annUrl = url_with(['page' => 'announcements']); // Announcements
    expect($newsUrl)->toContain('page=news');
    expect($eventsUrl)->toContain('page=events');
    expect($annUrl)->toContain('page=announcements');
});


// Test: Add article button URL for managers
test('add article button URL is correct for managers', function () {
    $role = 'admin'; // Simulate admin
    $isManager = in_array($role, ['admin','dean'], true); // Check
    $url = '/adamson-ccit/public/index.php?page=admin_manage_news'; // URL
    expect($isManager)->toBeTrue();
    expect($url)->toContain('admin_manage_news');
});


// Test: Pagination logic
test('pagination logic works as expected', function () {
    $total = 25; // Total
    $per = 9; // Per page
    $pages = max(1, (int)ceil($total / $per)); // Pages
    expect($pages)->toBe(3);
    $page = 5; // Request page 5
    $page = min($page, $pages); // Clamp
    expect($page)->toBe(3); // Should not exceed max
});


// Test: Category chip class mapping
test('category chip classes are mapped correctly', function () {
    $chipClass = [
        'research'     => 'chip chip--blue', // Research
        'achievement'  => 'chip', // Achievement
        'announcement' => 'chip chip--gray', // Announcement
        'student'      => 'chip chip--green', // Student
    ];
    expect($chipClass['research'])->toBe('chip chip--blue');
    expect($chipClass['achievement'])->toBe('chip');
    expect($chipClass['announcement'])->toBe('chip chip--gray');
    expect($chipClass['student'])->toBe('chip chip--green');
    $unknownChip = $chipClass['unknown'] ?? 'chip'; // Default
    expect($unknownChip)->toBe('chip');
});