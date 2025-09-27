<?php

use function Pest\Laravel\get;

// ========== HELPER FUNCTIONS ==========

function aboutPageRequest(string $method, string $page = ''): array {
    $baseUrl = 'http://localhost/adamson-ccit/public/index.php';
    $url = $page ? $baseUrl . '?page=' . $page : $baseUrl;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    return [$response, $info];
}

// ========== PAGE LOADING TESTS ==========

test('about history page loads successfully', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
    expect(strlen($response))->toBeGreaterThan(1000);
    expect($response)->toContain('<title>CCIT History | AdU-CCIT</title>');
    expect($response)->toContain('class="subhero"');
});

test('about vision mission page loads successfully', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
    expect(strlen($response))->toBeGreaterThan(1000);
    expect($response)->toContain('Vision &amp; Mission');
    expect($response)->toContain('class="subhero"');
});

// ========== NAVIGATION TESTS ==========

test('about history page has correct subnav structure', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="subnav"');
    expect($response)->toContain('aria-label="About sub-navigation"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('class="is-active"');
    expect($response)->toContain('aria-current="page"');
});

test('about vision mission page has correct subnav structure', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="subnav"');
    expect($response)->toContain('aria-label="About sub-navigation"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('class="is-active"');
    expect($response)->toContain('aria-current="page"');
});

test('about pages have correct active states in navigation', function () {
    // Test history page active state
    [$historyResponse, $historyInfo] = aboutPageRequest('GET', 'about_history');
    expect($historyInfo['http_code'])->toBe(200);
    expect($historyResponse)->toMatch('/<li[^>]*class="is-active"[^>]*>.*?History.*?<\/a>/s');
    
    // Test vision mission page active state
    [$vmResponse, $vmInfo] = aboutPageRequest('GET', 'about_vision_mission');
    expect($vmInfo['http_code'])->toBe(200);
    expect($vmResponse)->toMatch('/<li[^>]*class="is-active"[^>]*>.*?Vision.*?Mission.*?<\/a>/s');
});

test('about subnav links point to correct pages', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=about_history"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=about_vision_mission"');
});

// ========== CONTENT STRUCTURE TESTS ==========

test('about history page has all required content sections', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    // Main sections
    expect($response)->toContain('class="subhero"');
    expect($response)->toContain('class="content"');
    expect($response)->toContain('class="origin"');
    expect($response)->toContain('class="milestones"');
    expect($response)->toContain('class="leaders"');
    expect($response)->toContain('class="identity"');
    expect($response)->toContain('class="cta"');
    
    // Content structure
    expect($response)->toContain('class="content__main"');
    expect($response)->toContain('class="content__aside"');
    expect($response)->toContain('class="fact"');
});

test('about vision mission page has all required content sections', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    // Main sections
    expect($response)->toContain('class="subhero"');
    expect($response)->toContain('class="mv"');
    expect($response)->toContain('class="depts"');
    expect($response)->toContain('class="cta"');
    
    // Vision & Mission cards
    expect($response)->toContain('class="mv__grid"');
    expect($response)->toContain('class="card"');
    expect($response)->toContain('Vision');
    expect($response)->toContain('Mission');
    
    // Departments section
    expect($response)->toContain('class="depts__grid"');
    expect($response)->toContain('class="dept card"');
});

test('about history page timeline structure is correct', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="timeline"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('class="tl__item"');
    expect($response)->toContain('class="tl__dot"');
    expect($response)->toContain('class="tl__card"');
    expect($response)->toContain('class="tl__date"');
    expect($response)->toContain('class="tl__title"');
    expect($response)->toContain('<time datetime=');
});

test('about vision mission departments have correct structure', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    // Department structure
    expect($response)->toContain('class="dept__head"');
    expect($response)->toContain('class="dept__title"');
    expect($response)->toContain('class="dept__tag"');
    expect($response)->toContain('class="dept__body"');
    expect($response)->toContain('class="dept__foot"');
    
    // Department IDs
    expect($response)->toContain('id="it-is"');
    expect($response)->toContain('id="compsci"');
    
    // Department tags
    expect($response)->toContain('IT &amp; IS');
    expect($response)->toContain('CS');
});

// ========== CONTENT VALIDATION TESTS ==========

test('about history page displays key content elements', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    // Page title and eyebrow
    expect($response)->toContain('About CCIT');
    expect($response)->toContain('History');
    expect($response)->toContain('class="eyebrow"');
    expect($response)->toContain('class="subhero__title"');
    
    // Key sections
    expect($response)->toContain('Key Milestones');
    expect($response)->toContain('Defining moments in CCIT');
    expect($response)->toContain('At a Glance');
});

test('about vision mission page displays key content elements', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    // Page title and description
    expect($response)->toContain('Vision &amp; Mission');
    expect($response)->toContain('College of Computing &amp; Information Technology');
    expect($response)->toContain('College-wide mission and vision');
    
    // Departments section
    expect($response)->toContain('Departments');
    expect($response)->toContain('CCIT houses two departments');
    expect($response)->toContain('Information Technology &amp; Information Systems');
    expect($response)->toContain('Computer Science');
});

test('about history leadership section is present', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="leaders"');
    expect($response)->toContain('class="leaders__grid"');
    expect($response)->toContain('class="leaders__list"');
});

test('about history identity section is present', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="identity"');
    expect($response)->toContain('class="id__wrap"');
    expect($response)->toContain('class="id__grid"');
    expect($response)->toContain('role="list"');
});

// ========== BUTTON AND LINK TESTS ==========

test('about history page has working CTA button', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="btn btn--solid"');
    expect($response)->toContain('href=');
});

test('about vision mission page has working department buttons', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="btn btn--outline-blue"');
    expect($response)->toContain('See Programs');
    expect($response)->toContain('Explore BSCS');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=programs_undergraduate"');
});

test('about vision mission page has working main CTA', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="btn btn--solid"');
    expect($response)->toContain('View Programs');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=programs_undergraduate"');
});

test('about history aside links work correctly', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=about_vision_mission"');
    expect($response)->toContain('Vision &amp; Mission');
});

// ========== ACCESSIBILITY TESTS ==========

test('about pages have proper accessibility attributes', function () {
    $pages = ['about_history', 'about_vision_mission'];
    
    foreach ($pages as $page) {
        [$response, $info] = aboutPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // ARIA labels and roles
        expect($response)->toContain('aria-label="About sub-navigation"');
        expect($response)->toContain('role="list"');
        expect($response)->toContain('aria-hidden="true"');
        expect($response)->toContain('aria-current="page"');
        
        // Image alt attributes
        expect($response)->toContain('alt="');
    }
});

test('about history timeline has proper semantic structure', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('<ol class="timeline"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('<time datetime=');
    expect($response)->toContain('aria-hidden="true"');
});

test('about vision mission has proper heading hierarchy', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('<h1');
    expect($response)->toContain('<h2');
    expect($response)->toContain('<h3');
    expect($response)->toContain('<h4');
});

// ========== RESPONSIVE DESIGN TESTS ==========

test('about pages have responsive viewport meta tag', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
});

test('about pages have proper CSS grid and layout classes', function () {
    // History page grids - check for actual classes used
    [$historyResponse, $historyInfo] = aboutPageRequest('GET', 'about_history');
    expect($historyInfo['http_code'])->toBe(200);
    expect($historyResponse)->toContain('class="leaders__grid"');
    expect($historyResponse)->toContain('class="id__grid"');
    
    // Vision mission page grids
    [$vmResponse, $vmInfo] = aboutPageRequest('GET', 'about_vision_mission');
    expect($vmInfo['http_code'])->toBe(200);
    expect($vmResponse)->toContain('class="mv__grid"');
    expect($vmResponse)->toContain('class="depts__grid"');
});

// ========== PERFORMANCE AND VALIDATION TESTS ==========

test('about pages load within reasonable time', function () {
    $startTime = microtime(true);
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    $loadTime = microtime(true) - $startTime;
    
    expect($info['http_code'])->toBe(200);
    expect($loadTime)->toBeLessThan(5.0); // Should load within 5 seconds
    expect($info['total_time'])->toBeLessThan(5.0);
});

test('about pages have valid HTML structure', function () {
    $pages = ['about_history', 'about_vision_mission'];
    
    foreach ($pages as $page) {
        [$response, $info] = aboutPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // Check for basic content structure elements that are actually present
        expect($response)->toContain('<main>');
        expect($response)->toContain('<section');
        expect($response)->toContain('class="subhero"');
        expect($response)->toContain('class="container"');
        expect($response)->toContain('<div');
    }
});

test('about pages have proper CSS and asset links', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history');
    expect($info['http_code'])->toBe(200);
    
    expect($response)->toContain('link rel="stylesheet"');
    expect($response)->toContain('href="/adamson-ccit/public/assets/css/style.css"');
});

// ========== CROSS-PAGE NAVIGATION TESTS ==========

test('navigation between about pages works correctly', function () {
    // Start from history page
    [$historyResponse, $historyInfo] = aboutPageRequest('GET', 'about_history');
    expect($historyInfo['http_code'])->toBe(200);
    expect($historyResponse)->toContain('href="/adamson-ccit/public/index.php?page=about_vision_mission"');
    
    // Navigate to vision mission page
    [$vmResponse, $vmInfo] = aboutPageRequest('GET', 'about_vision_mission');
    expect($vmInfo['http_code'])->toBe(200);
    expect($vmResponse)->toContain('href="/adamson-ccit/public/index.php?page=about_history"');
});

test('about pages link to programs correctly', function () {
    // Vision mission page should link to programs
    [$vmResponse, $vmInfo] = aboutPageRequest('GET', 'about_vision_mission');
    expect($vmInfo['http_code'])->toBe(200);
    expect($vmResponse)->toContain('href="/adamson-ccit/public/index.php?page=programs_undergraduate"');
    
    // History page CTA should also link somewhere relevant
    [$historyResponse, $historyInfo] = aboutPageRequest('GET', 'about_history');
    expect($historyInfo['http_code'])->toBe(200);
    expect($historyResponse)->toContain('href=');
});

// ========== CONTENT INTEGRATION TESTS ==========

test('about pages display dynamic content correctly', function () {
    // Test that PHP variables are being processed
    [$historyResponse, $historyInfo] = aboutPageRequest('GET', 'about_history');
    expect($historyInfo['http_code'])->toBe(200);
    expect($historyResponse)->not->toContain('<?php');
    expect($historyResponse)->not->toContain('htmlspecialchars');
    
    [$vmResponse, $vmInfo] = aboutPageRequest('GET', 'about_vision_mission');
    expect($vmInfo['http_code'])->toBe(200);
    expect($vmResponse)->not->toContain('<?php');
    expect($vmResponse)->not->toContain('htmlspecialchars');
});

test('about pages have consistent branding and styling', function () {
    $pages = ['about_history', 'about_vision_mission'];
    
    foreach ($pages as $page) {
        [$response, $info] = aboutPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // Consistent header structure
        expect($response)->toContain('class="subhero"');
        expect($response)->toContain('class="container"');
        expect($response)->toContain('About CCIT');
        
        // Consistent footer/CTA
        expect($response)->toContain('class="cta"');
        expect($response)->toContain('class="btn');
    }
});
