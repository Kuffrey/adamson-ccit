<?php

/**
 * Helper that performs a POST request to the guest student login endpoint.
 * It returns a tuple: [$responseBody, $transferInfo].
 *
 * $transferInfo is the array from curl_getinfo($ch) which includes:
 *   - 'http_code'   => final HTTP status code (after redirects if enabled)
 *   - 'url'         => the final effective URL (after redirects)
 *   - ...and other cURL transfer diagnostics
 */
function postLogin($data) {
    // Initialize a new cURL session pointing to the login page
    $ch = curl_init('http://localhost/adamson-ccit/public/index.php?page=login_guest_student');

    // Return the response body as a string (instead of outputting it directly)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Tell cURL this is a POST request
    curl_setopt($ch, CURLOPT_POST, true);

    // Provide the POST fields (e.g., ['email' => '...', 'password' => '...'])
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Follow HTTP redirects (302/303/etc.) so we land on the final page after login
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute the HTTP request and capture the response body
    $response = curl_exec($ch);

    // Collect useful details about the transfer (status code, final URL, timings, etc.)
    $info = curl_getinfo($ch);

    // Always close the handle to free resources
    curl_close($ch);

    // Return both the body and the metadata
    return [$response, $info];
}

/**
 * ✅ Positive path: a student logs in with valid credentials.
 * Expectations:
 *   1) Final HTTP status should be 200 (after following redirects).
 *   2) The final URL should contain the app's index page, indicating success.
 */
test('student can login with correct credentials', function () {
    [$response, $info] = postLogin([
        'username' => 'student',
        'password' => 'student123',
    ]);
    expect($info['http_code'])->toBe(200);
    expect($info['url'])->toContain('/adamson-ccit/public/index.php');
});

test('student login fails with non-existing account', function () {
    [$response, $info] = postLogin([
        'username' => 'sampletest',
        'password' => 'sampletestPass002',
    ]);
    expect($response)->toContain('Incorrect username or password.');
});

test('student login fails with empty credentials', function () {
    [$response, $info] = postLogin([
        'username' => '',
        'password' => '',
    ]);
    expect($response)->toContain('Please enter your username and password.');
});

test('student login fails with empty username', function () {
    [$response, $info] = postLogin([
        'username' => '',
        'password' => 'student123',
    ]);
    expect($response)->toContain('Please enter your username.');
});

test('student login fails with empty password', function () {
    [$response, $info] = postLogin([
        'username' => 'student',
        'password' => '',
    ]);
    expect($response)->toContain('Please enter your password.');
});

test('student login fails with whitespace username', function () {
    [$response, $info] = postLogin([
        'username' => '   ',
        'password' => 'student123',
    ]);
    expect($response)->toContain('Please enter your username.');
});


test('student login fails with both whitespace', function () {
    [$response, $info] = postLogin([
        'username' => '   ',
        'password' => '   ',
    ]);
    expect($response)->toContain('Please enter your username and password.');
});
