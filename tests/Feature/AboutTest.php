
<?php
// AboutTest.php - Pest feature tests for About pages (History and Vision/Mission)

// Helper: Send HTTP request to about page and return response/info
function aboutPageRequest(string $method, string $page = ''): array {
    $baseUrl = 'http://localhost/adamson-ccit/public/index.php'; // Base URL for the about pages
    $url = $page ? $baseUrl . '?page=' . $page : $baseUrl; // Add ?page= if a subpage is given
    $ch = curl_init(); // Initialize cURL session
    curl_setopt($ch, CURLOPT_URL, $url); // Set the URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout to 30 seconds
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // Set HTTP method (GET, POST, etc.)
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'); // Set user agent
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (for localhost)
    $response = curl_exec($ch); // Execute the request
    $info = curl_getinfo($ch); // Get info about the request (status, timing, etc.)
    curl_close($ch); // Close cURL session
    return [$response, $info]; // Return response and info as array
}


// ========== PAGE LOAD TESTS ==========


// Test: About history page loads and has correct title and section
test('about history page loads and has correct title', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history'); // Request the history page
    expect($info['http_code'])->toBe(200); // Should return HTTP 200
    expect($response)->toContain('<h1 class="subhero__title">History</h1>'); // Should have correct H1
    expect($response)->toContain('About CCIT'); // Should mention About CCIT
});


// Test: About vision mission page loads and has correct title and section
test('about vision mission page loads and has correct title', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission'); // Request the vision/mission page
    expect($info['http_code'])->toBe(200); // Should return HTTP 200
    expect($response)->toContain('<h1 class="subhero__title">Vision &amp; Mission</h1>'); // Should have correct H1
    expect($response)->toContain('About CCIT'); // Should mention About CCIT
});


// ========== NAVIGATION TESTS ==========


// Test: Both about pages have correct subnav, active state, and cross-links
test('about pages have correct subnav and active state', function () {
    [$history, $info1] = aboutPageRequest('GET', 'about_history'); // Request history page
    expect($info1['http_code'])->toBe(200); // HTTP 200
    expect($history)->toContain('aria-label="About sub-navigation"'); // Subnav ARIA label
    expect($history)->toContain('class="is-active"'); // Active class present
    expect($history)->toContain('aria-current="page"'); // ARIA current page
    expect($history)->toContain('href="/adamson-ccit/public/index.php?page=about_vision_mission"'); // Link to vision/mission

    [$vision, $info2] = aboutPageRequest('GET', 'about_vision_mission'); // Request vision/mission page
    expect($info2['http_code'])->toBe(200); // HTTP 200
    expect($vision)->toContain('aria-label="About sub-navigation"'); // Subnav ARIA label
    expect($vision)->toContain('class="is-active"'); // Active class present
    expect($vision)->toContain('aria-current="page"'); // ARIA current page
    expect($vision)->toContain('href="/adamson-ccit/public/index.php?page=about_history"'); // Link to history
});


// ========== BUTTONS & CTA TESTS ==========


// Test: History page has a working CTA button to programs
test('about history page has working CTA button', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history'); // Request history page
    expect($info['http_code'])->toBe(200); // HTTP 200
    expect($response)->toContain('class="cta"'); // CTA section present
    expect($response)->toMatch('/<a[^>]+class="btn btn--solid"[^>]+href="[^"]+programs_undergraduate[^"]*"/'); // Button to programs
});


// Test: Vision/Mission page has a working CTA button to programs
test('about vision mission page has working CTA button', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission'); // Request vision/mission page
    expect($info['http_code'])->toBe(200); // HTTP 200
    expect($response)->toContain('class="cta"'); // CTA section present
    expect($response)->toMatch('/<a[^>]+class="btn btn--solid"[^>]+href="[^"]+programs_undergraduate[^"]*"/'); // Button to programs
});


// ========== QUICK LINKS TESTS ==========


// Test: Vision/Mission page quick links are present and correct
test('about vision mission page quick links are present', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_vision_mission'); // Request vision/mission page
    expect($info['http_code'])->toBe(200); // HTTP 200
    expect($response)->toContain('Quick Links'); // Quick Links section
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=about_history"'); // Link to history
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=programs_undergraduate"'); // Link to programs
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"'); // Link to admissions
});


// Test: History page quick facts are present and link to vision/mission
test('about history page quick facts are present', function () {
    [$response, $info] = aboutPageRequest('GET', 'about_history'); // Request history page
    expect($info['http_code'])->toBe(200); // HTTP 200
    expect($response)->toContain('Quick Facts'); // Quick Facts section
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=about_vision_mission"'); // Link to vision/mission
});
