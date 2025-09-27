<?php
// validate-chatbot.php - Chatbot troubleshooting script
echo "=== CCIT Chatbot Validation Script ===\n\n";

// Check 1: Credentials file
echo "1. Checking credentials file...\n";
$credFile = __DIR__ . '/dialogflow-access.json';
if (!file_exists($credFile)) {
    echo "❌ FAIL: dialogflow-access.json not found\n";
    echo "   Place your service account key at: $credFile\n\n";
    exit(1);
}

$credData = json_decode(file_get_contents($credFile), true);
if (!$credData) {
    echo "❌ FAIL: Invalid JSON in credentials file\n\n";
    exit(1);
}

echo "✅ PASS: Credentials file found and valid JSON\n";
echo "   Project ID: {$credData['project_id']}\n";
echo "   Service Account: {$credData['client_email']}\n\n";

// Check 2: Required fields
echo "2. Checking required credential fields...\n";
$required = ['type', 'project_id', 'private_key', 'client_email'];
$missing = [];
foreach ($required as $field) {
    if (empty($credData[$field])) {
        $missing[] = $field;
    }
}

if ($missing) {
    echo "❌ FAIL: Missing required fields: " . implode(', ', $missing) . "\n\n";
    exit(1);
}
echo "✅ PASS: All required credential fields present\n\n";

// Check 3: PHP extensions
echo "3. Checking PHP extensions...\n";
$extensions = ['openssl', 'curl', 'json'];
$missing = [];
foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}

if ($missing) {
    echo "❌ FAIL: Missing PHP extensions: " . implode(', ', $missing) . "\n";
    echo "   Enable these in php.ini and restart Apache\n\n";
    exit(1);
}
echo "✅ PASS: Required PHP extensions loaded\n\n";

// Check 4: Composer dependencies
echo "4. Checking Composer dependencies...\n";
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "❌ FAIL: Composer autoload not found\n";
    echo "   Run: composer install\n\n";
    exit(1);
}

require $autoload;

try {
    if (!class_exists('Google\Cloud\Dialogflow\V2\SessionsClient')) {
        echo "❌ FAIL: Dialogflow client class not found\n";
        echo "   Run: composer require google/cloud-dialogflow\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ FAIL: Error loading Dialogflow client: " . $e->getMessage() . "\n\n";
    exit(1);
}
echo "✅ PASS: Dialogflow client library available\n\n";

// Check 5: Environment variable
echo "5. Setting up environment...\n";
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credFile);
echo "✅ PASS: Environment variable set\n\n";

// The actual problem analysis
echo "=== DIAGNOSIS ===\n";
echo "Based on the authentication error, the issue is likely:\n\n";

echo "🔧 REQUIRED FIXES:\n\n";

echo "1. Enable Dialogflow API:\n";
echo "   • Go to: https://console.cloud.google.com/apis/library/dialogflow.googleapis.com?project={$credData['project_id']}\n";
echo "   • Click 'ENABLE' if not already enabled\n\n";

echo "2. Check Service Account Permissions:\n";
echo "   • Go to: https://console.cloud.google.com/iam-admin/iam?project={$credData['project_id']}\n";
echo "   • Find: {$credData['client_email']}\n";
echo "   • Ensure it has role: 'Dialogflow API Client' or 'Dialogflow API Admin'\n\n";

echo "3. Verify Project Settings:\n";
echo "   • Go to: https://console.cloud.google.com/home/dashboard?project={$credData['project_id']}\n";
echo "   • Confirm this is the correct project\n";
echo "   • Check that billing is enabled (required for Dialogflow)\n\n";

echo "4. Test the service account:\n";
echo "   • Run: php chatbot.php?selftest=1\n";
echo "   • Should return success message\n\n";

echo "=== VALIDATION COMPLETE ===\n";
echo "If you've completed steps 1-3 above, try the chatbot again.\n";