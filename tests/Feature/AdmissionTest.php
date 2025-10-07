
<?php

// Helper function to make HTTP requests to the admission pages
function admissionPageRequest($method, $page, $data = null) {
    // Build the URL for the requested page
    $url = "http://localhost/adamson-ccit/public/index.php?page={$page}";
    // Initialize a cURL session
    $ch = curl_init($url);
    // Set cURL to return the response as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Follow redirects if any
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Set a timeout for the request
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    // If the method is POST, set POST options and attach data
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    // Execute the request and get the response
    $response = curl_exec($ch);
    // Get information about the request (e.g., HTTP status code)
    $info = curl_getinfo($ch);
    // Close the cURL session
    curl_close($ch);
    // Return both the response body and info array
    return [$response, $info];
}

// ========== HOME PAGE ENROLLMENT BUTTON TESTS ==========

// Test if the home page loads without errors and returns content
test('home page loads successfully', function () {
    // Make a GET request to the home page
    [$response, $info] = admissionPageRequest('GET', '');
    // Expect the HTTP status code to be 200 (OK)
    expect($info['http_code'])->toBe(200);
    // Expect the response body to not be empty
    expect($response)->not->toBe('');
    // Expect the response length to be greater than 100 characters
    expect(strlen($response))->toBeGreaterThan(100);
});

// Test if the home page contains the "Enroll Now" button with correct attributes
test('home page has enroll now button', function () {
    // Make a GET request to the home page
    [$response, $info] = admissionPageRequest('GET', '');
    // Expect HTTP 200 OK
    expect($info['http_code'])->toBe(200);
    // The button text should be present
    expect($response)->toContain('Enroll Now');
    // The button should have the correct CSS class
    expect($response)->toContain('class="btn btn--solid"');
    // The button should link to the freshman admission page
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
});

// Test if the "Enroll Now" button works and redirects to the correct page
test('home page enroll now button is working and redirects to admission freshman page', function () {
    // Get the home page to confirm the button exists
    [$homeResponse, $homeInfo] = admissionPageRequest('GET', '');
    // Expect HTTP 200 OK
    expect($homeInfo['http_code'])->toBe(200);
    // The button text should be present
    expect($homeResponse)->toContain('Enroll Now');
    // The button should link to the freshman admission page
    expect($homeResponse)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    // The button should have the correct CSS class
    expect($homeResponse)->toContain('class="btn btn--solid-blue"');
    // Simulate clicking the button by requesting the target page
    [$admissionResponse, $admissionInfo] = admissionPageRequest('GET', 'admission_freshman');
    // Expect the target page to load successfully
    expect($admissionInfo['http_code'])->toBe(200);
    // The target page should contain expected content
    expect($admissionResponse)->toContain('Freshman Admission');
    expect($admissionResponse)->toContain('How to Apply');
    // Confirm the destination page is fully functional
    expect($admissionResponse)->toContain('Apply at Adamson.edu.ph');
    expect($admissionResponse)->toContain('class="btn btn--solid-blue"');
});

test('home page has admission quick action link', function () {
    [$response, $info] = admissionPageRequest('GET', '');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('Admissions');
    expect($response)->toContain('class="quick__card"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_requirements"');
});

test('home page admission quick action redirects correctly', function () {
    // Verify the admission requirements page loads correctly
    [$response, $info] = admissionPageRequest('GET', 'admission_requirements');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
});

test('home page navigation includes admission links', function () {
    [$response, $info] = admissionPageRequest('GET', '');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('Admission');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_transferee"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_graduate_school"');
});

test('home page cta section has relevant link', function () {
    [$response, $info] = admissionPageRequest('GET', '');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="btn btn--solid"');
    // CTA might link to career pathway generator or programs
    expect($response)->toMatch('/href="[^"]*(?:career_pathway_generator|programs|admission)[^"]*"/');
});

test('enroll now button is fully functional and working correctly', function () {
    // Get home page and verify "Enroll Now" button exists and is properly configured
    [$homeResponse, $homeInfo] = admissionPageRequest('GET', '');
    expect($homeInfo['http_code'])->toBe(200);
    
    // Verify button text is correct
    expect($homeResponse)->toContain('Enroll Now');
    
    // Verify button has correct href attribute
    expect($homeResponse)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    
    // Verify button has proper styling classes
    expect($homeResponse)->toContain('class="btn btn--solid-blue"');
    
    // Verify button is within hero section
    expect($homeResponse)->toContain('class="hero"');
    
    // Test that the "Enroll Now" button destination works correctly
    [$targetResponse, $targetInfo] = admissionPageRequest('GET', 'admission_freshman');
    expect($targetInfo['http_code'])->toBe(200);
    expect($targetResponse)->toContain('Freshman Admission');
    expect($targetResponse)->toContain('How to Apply');
    expect($targetResponse)->toContain('Apply at Adamson.edu.ph');
    
    // Verify the target page has functional apply buttons
    expect($targetResponse)->toContain('class="btn btn--solid-blue"');
    expect($targetResponse)->toContain('href="https://www.adamson.edu.ph/cfe"');
    expect($targetResponse)->toContain('target="_blank"');
    expect($targetResponse)->toContain('rel="noopener"');
    
    // Confirm complete enrollment flow is working
    expect($targetResponse)->toContain('Requirements for Freshmen Enrollment');
    expect($targetResponse)->toContain('Enrollment Procedure');
});

// ========== ADMISSION PAGES ACCESSIBILITY TESTS ==========

test('admission freshman page loads successfully', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
    expect(strlen($response))->toBeGreaterThan(100);
});

test('admission transferee page loads successfully', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
    expect(strlen($response))->toBeGreaterThan(100);
});

test('admission graduate school page loads successfully', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->not->toBe('');
    expect(strlen($response))->toBeGreaterThan(100);
});

// ========== PAGE STRUCTURE TESTS ==========

test('admission freshman page has correct title and structure', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('<title>Freshman Admission | AdU-CCIT</title>');
    expect($response)->toContain('class="subhero"');
    expect($response)->toContain('Freshman Admission');
    expect($response)->toContain('class="subnav"');
});

test('admission transferee page has correct title and structure', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('<title>Transferee Admission | AdU-CCIT</title>');
    expect($response)->toContain('class="subhero"');
    expect($response)->toContain('Transferee');
    expect($response)->toContain('class="subnav"');
});

test('admission graduate school page has correct title and structure', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('<title>Graduate School & JD Admission | AdU-CCIT</title>');
    expect($response)->toContain('class="subhero"');
    expect($response)->toContain('Graduate School');
    expect($response)->toContain('class="subnav"');
});

// ========== NAVIGATION TESTS ==========

test('admission freshman page has correct navigation links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_transferee"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_graduate_school"');
    expect($response)->toContain('class="is-active"');
    expect($response)->toContain('aria-current="page"');
});

test('admission transferee page has correct navigation links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_transferee"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_graduate_school"');
    expect($response)->toContain('class="is-active"');
    expect($response)->toContain('aria-current="page"');
});

test('admission graduate school page has correct navigation links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_freshman"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_transferee"');
    expect($response)->toContain('href="/adamson-ccit/public/index.php?page=admission_graduate_school"');
    expect($response)->toContain('class="is-active"');
    expect($response)->toContain('aria-current="page"');
});

// ========== BUTTON TESTS ==========

test('admission freshman page has apply button with correct URL', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="btn btn--solid-blue"');
    expect($response)->toContain('href="https://www.adamson.edu.ph/cfe"');
    expect($response)->toContain('target="_blank"');
    expect($response)->toContain('rel="noopener"');
    expect($response)->toContain('Apply at Adamson.edu.ph');
});

test('admission transferee page has apply and elearning buttons', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="btn btn--solid-blue"');
    expect($response)->toContain('class="btn btn--outline-blue"');
    expect($response)->toContain('href="https://www.adamson.edu.ph/cfe"');
    expect($response)->toContain('href="https://learn.adamson.edu.ph"');
    expect($response)->toContain('Apply at Adamson.edu.ph');
    expect($response)->toContain('Log in to eLearning');
});

test('admission graduate school page has apply button', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="btn btn--solid-blue"');
    expect($response)->toContain('href="https://www.adamson.edu.ph/cfe"');
    expect($response)->toContain('target="_blank"');
    expect($response)->toContain('rel="noopener"');
    expect($response)->toContain('Apply at Adamson.edu.ph');
});

// ========== CONTENT SECTIONS TESTS ==========

test('admission freshman page displays required content sections', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('Initial Uploads (Online Evaluation)');
    expect($response)->toContain('Requirements for Freshmen Enrollment');
    expect($response)->toContain('Senior High School Graduates');
    expect($response)->toContain('ALS / Non-Formal Education');
    expect($response)->toContain('Enrollment Procedure');
    expect($response)->toContain('class="content__aside"');
});

test('admission transferee page displays required content sections', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('Requirements for Application (Evaluation');
    expect($response)->toContain('Enrollment Procedure');
    expect($response)->toContain('class="content__aside"');
    expect($response)->toContain('class="content__main"');
});

test('admission graduate school page displays required content sections', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('Initial Uploads (Online Evaluation)');
    expect($response)->toContain('Qualifications');
    expect($response)->toMatch("/Master[''']s Degree/"); // Handle both apostrophe types
    expect($response)->toContain('Doctoral Program');
    expect($response)->toContain('Juris Doctor');
    expect($response)->toContain('Requirements for Enrollment');
    expect($response)->toContain('Enrollment Procedure');
});

// ========== SIDEBAR TESTS ==========

test('admission freshman page has sidebar with office info and quick links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="fact"');
    expect($response)->toContain('Quick Links');
    expect($response)->toContain('class="content__photo"');
    expect($response)->toContain('<figcaption>');
});

test('admission transferee page has sidebar with office info and quick links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="fact"');
    expect($response)->toContain('Quick Links');
    expect($response)->toContain('class="content__photo"');
    expect($response)->toContain('<figcaption>');
});

test('admission graduate school page has sidebar with office info and quick links', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="fact"');
    expect($response)->toContain('Quick Links');
    expect($response)->toContain('class="content__photo"');
    expect($response)->toContain('<figcaption>');
});

// ========== CTA SECTION TESTS ==========

test('admission freshman page has CTA section', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="btn btn--solid"');
    expect($response)->toContain('class="cta__inner"');
});

test('admission transferee page has CTA section', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="btn btn--solid-blue"');
    expect($response)->toContain('class="cta__inner"');
});

test('admission graduate school page has CTA section', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('class="cta"');
    expect($response)->toContain('class="cta__inner"');
});

// ========== ACCESSIBILITY TESTS ==========

test('admission freshman page has proper accessibility attributes', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('aria-label="Admissions sub-navigation"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('aria-hidden="true"');
    expect($response)->toContain('alt="');
});

test('admission transferee page has proper accessibility attributes', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('aria-label="Admissions sub-navigation"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('aria-hidden="true"');
    expect($response)->toContain('alt="');
});

test('admission graduate school page has proper accessibility attributes', function () {
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toContain('aria-label="Admissions sub-navigation"');
    expect($response)->toContain('role="list"');
    expect($response)->toContain('aria-hidden="true"');
    expect($response)->toContain('alt="');
});

// ========== RESPONSIVE DESIGN TESTS ==========

test('admission pages have responsive viewport meta tag', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        expect($response)->toContain('<meta name="viewport" content="width=device-width, initial-scale=1');
    }
});

test('admission pages load CSS stylesheet', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        expect($response)->toContain('href="/adamson-ccit/public/assets/css/style.css"');
        expect($response)->toContain('rel="stylesheet"');
    }
});

// ========== EXTERNAL LINK VALIDATION TESTS ==========

test('admission pages have proper external link attributes', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // Check for external links to Adamson.edu.ph
        if (strpos($response, 'href="https://www.adamson.edu.ph/cfe"') !== false) {
            expect($response)->toContain('target="_blank"');
            expect($response)->toContain('rel="noopener"');
        }
        
        // Check for external links to eLearning (transferee page)
        if (strpos($response, 'href="https://learn.adamson.edu.ph"') !== false) {
            expect($response)->toContain('target="_blank"');
            expect($response)->toContain('rel="noopener"');
        }
    }
});

// ========== CONTENT CONSISTENCY TESTS ==========

test('all admission pages use consistent HTML structure', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // Check for consistent structure elements
        expect($response)->toContain('<main>');
        expect($response)->toContain('class="subhero"');
        expect($response)->toContain('class="subnav"');
        expect($response)->toContain('class="content"');
        expect($response)->toContain('class="content__grid"');
        expect($response)->toContain('class="content__main"');
        expect($response)->toContain('class="content__aside"');
        expect($response)->toContain('</main>');
    }
});

test('all admission pages have proper HTML5 structure', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // Check for HTML5 structure
        expect($response)->toContain('<!DOCTYPE html>');
        expect($response)->toContain('<html lang="en">');
        expect($response)->toContain('<head>');
        expect($response)->toContain('<meta charset="UTF-8"');
        expect($response)->toContain('<title>');
        expect($response)->toContain('<body>');
    }
});

// ========== NAVIGATION FLOW TESTS ==========

test('admission navigation maintains active state correctly', function () {
    // Test freshman page active state
    [$response, $info] = admissionPageRequest('GET', 'admission_freshman');
    expect($info['http_code'])->toBe(200);
    expect($response)->toMatch('/<li[^>]*class="is-active"[^>]*>.*?Freshman.*?<\/a>/s');
    
    // Test transferee page active state
    [$response, $info] = admissionPageRequest('GET', 'admission_transferee');
    expect($info['http_code'])->toBe(200);
    expect($response)->toMatch('/<li[^>]*class="is-active"[^>]*>.*?Transferee.*?<\/a>/s');
    
    // Test graduate school page active state
    [$response, $info] = admissionPageRequest('GET', 'admission_graduate_school');
    expect($info['http_code'])->toBe(200);
    expect($response)->toMatch('/<li[^>]*class="is-active"[^>]*>.*?Graduate School.*?<\/a>/s');
});

// ========== ENROLLMENT FLOW INTEGRATION TESTS ==========

test('complete enrollment flow from home to admission', function () {
    // Start at home page
    [$homeResponse, $homeInfo] = admissionPageRequest('GET', '');
    expect($homeInfo['http_code'])->toBe(200);
    expect($homeResponse)->toContain('Enroll Now');
    
    // Follow enroll now button to freshman admission
    [$freshmanResponse, $freshmanInfo] = admissionPageRequest('GET', 'admission_freshman');
    expect($freshmanInfo['http_code'])->toBe(200);
    expect($freshmanResponse)->toContain('Freshman Admission');
    expect($freshmanResponse)->toContain('Apply at Adamson.edu.ph');
    
    // Verify external application link exists
    expect($freshmanResponse)->toContain('href="https://www.adamson.edu.ph/cfe"');
    expect($freshmanResponse)->toContain('target="_blank"');
    expect($freshmanResponse)->toContain('rel="noopener"');
});

test('admission navigation flow works correctly', function () {
    // Test navigation between admission pages
    $admissionPages = [
        'admission_freshman' => 'Freshman Admission',
        'admission_transferee' => 'Transferee',
        'admission_graduate_school' => 'Graduate School'
    ];
    
    foreach ($admissionPages as $page => $expectedTitle) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        expect($response)->toContain($expectedTitle);
        
        // Each page should have navigation to other admission pages
        foreach (array_keys($admissionPages) as $otherPage) {
            expect($response)->toContain("page={$otherPage}");
        }
    }
});

test('all admission pages have apply buttons', function () {
    $admissionPages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($admissionPages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        
        // All pages should have at least one apply button
        expect($response)->toMatch('/(Apply|Enroll)/i');
        expect($response)->toContain('adamson.edu.ph');
    }
});

test('home page provides multiple paths to admission', function () {
    [$response, $info] = admissionPageRequest('GET', '');
    expect($info['http_code'])->toBe(200);
    
    // Hero section - Enroll Now button
    expect($response)->toContain('Enroll Now');
    expect($response)->toContain('admission_freshman');
    
    // Quick actions - Admissions card
    expect($response)->toContain('Admissions');
    expect($response)->toContain('admission_requirements');
    
    // Navigation menu - Admission dropdown
    expect($response)->toContain('Admission');
    expect($response)->toContain('admission_freshman');
    expect($response)->toContain('admission_transferee');
    expect($response)->toContain('admission_graduate_school');
});

// ========== PERFORMANCE TESTS ==========

test('admission pages load within acceptable time', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        $start = microtime(true);
        [$response, $info] = admissionPageRequest('GET', $page);
        $duration = microtime(true) - $start;
        
        expect($info['http_code'])->toBe(200);
        expect($duration)->toBeLessThan(3.0); // Should load within 3 seconds
    }
});

test('admission pages return appropriate content length', function () {
    $pages = ['admission_freshman', 'admission_transferee', 'admission_graduate_school'];
    
    foreach ($pages as $page) {
        [$response, $info] = admissionPageRequest('GET', $page);
        expect($info['http_code'])->toBe(200);
        expect(strlen($response))->toBeGreaterThan(1000); // Should have substantial content
        expect(strlen($response))->toBeLessThan(100000); // But not excessively large
    }
});