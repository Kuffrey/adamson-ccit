<?php
// test_credentials.php - Script to verify Dialogflow credentials
ini_set('display_errors', 1);
error_reporting(E_ALL);

$credentialsFile = __DIR__ . '/app/dialogflow-access.json';
echo "Checking credentials file at: $credentialsFile\n\n";

// Step 1: Check if file exists
if (!file_exists($credentialsFile)) {
    die("ERROR: Credentials file does not exist at: $credentialsFile\n");
} else {
    echo "✓ Credentials file exists\n";
}

// Step 2: Check if file is readable
if (!is_readable($credentialsFile)) {
    die("ERROR: Credentials file is not readable. Check permissions.\n");
} else {
    echo "✓ Credentials file is readable\n";
}

// Step 3: Check if it contains valid JSON
$jsonContent = file_get_contents($credentialsFile);
$credentials = json_decode($jsonContent);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("ERROR: Credentials file does not contain valid JSON. Error: " . json_last_error_msg() . "\n");
} else {
    echo "✓ Credentials file contains valid JSON\n";
}

// Step 4: Check if JSON has required keys
$requiredKeys = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
$missingKeys = [];

foreach ($requiredKeys as $key) {
    if (!property_exists($credentials, $key) || empty($credentials->$key)) {
        $missingKeys[] = $key;
    }
}

if (!empty($missingKeys)) {
    die("ERROR: Credentials file is missing required keys: " . implode(', ', $missingKeys) . "\n");
} else {
    echo "✓ Credentials file has all required fields\n";
}

// Step 5: Set up environment variable
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsFile);
$_SERVER['GOOGLE_APPLICATION_CREDENTIALS'] = $credentialsFile;

echo "✓ GOOGLE_APPLICATION_CREDENTIALS environment variable set\n";
echo "\nCredentials appear valid and properly formatted.\n";

// Try loading the Google client library if available
if (class_exists('Google\Cloud\Dialogflow\V2\SessionsClient')) {
    try {
        echo "\nAttempting to initialize SessionsClient...\n";
        $client = new Google\Cloud\Dialogflow\V2\SessionsClient(['transport' => 'rest']);
        echo "✓ Successfully initialized SessionsClient\n";
        $client->close();
    } catch (Exception $e) {
        echo "ERROR initializing SessionsClient: " . $e->getMessage() . "\n";
    }
} else {
    echo "\nGoogle Cloud Dialogflow library not available. Install with: composer require google/cloud-dialogflow\n";
}

echo "\nTest completed.\n";