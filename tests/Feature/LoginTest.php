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

    // Provide the POST fields (e.g., ['username' => '...', 'password' => '...'])
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

    // Assert final HTTP status is OK
    expect($info['http_code'])->toBe(200);

    // Assert we ended up at the main index (typical post-login redirect)
    expect($info['url'])->toContain('/adamson-ccit/public/index.php');
});

/**
 * ❌ Negative path: login should fail with an incorrect password.
 * Expectation:
 *   - The response body should show the error message "Invalid credentials".
 *     (Many apps return 200 with an inline error on the same page.)
 */
test('student login fails with wrong credentials', function () {
    [$response, $info] = postLogin([
        'username' => 'student',
        'password' => 'wrongpassword',
    ]);

    // Assert the page displays a clear error message
    expect($response)->toContain('Invalid credentials');
});
