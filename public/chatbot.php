<?php
// Error reporting for debugging (turn off in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if autoload file exists
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('Error: Composer autoload file not found. Run "composer install" in the project root.');
}

require $autoloadPath;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Dotenv\Dotenv;

// Load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Build credentials array from env
$dialogflowCredentials = [
    "type" => $_ENV['DIALOGFLOW_TYPE'],
    "project_id" => $_ENV['DIALOGFLOW_PROJECT_ID'],
    "private_key_id" => $_ENV['DIALOGFLOW_PRIVATE_KEY_ID'],
    "private_key" => str_replace("\\n", "\n", $_ENV['DIALOGFLOW_PRIVATE_KEY']),
    "client_email" => $_ENV['DIALOGFLOW_CLIENT_EMAIL'],
    "client_id" => $_ENV['DIALOGFLOW_CLIENT_ID'],
    "auth_uri" => $_ENV['DIALOGFLOW_AUTH_URI'],
    "token_uri" => $_ENV['DIALOGFLOW_TOKEN_URI'],
    "auth_provider_x509_cert_url" => $_ENV['DIALOGFLOW_AUTH_PROVIDER_X509_CERT_URL'],
    "client_x509_cert_url" => $_ENV['DIALOGFLOW_CLIENT_X509_CERT_URL'],
    "universe_domain" => $_ENV['DIALOGFLOW_UNIVERSE_DOMAIN']
];

// Dialogflow settings
$projectId = $_ENV['DIALOGFLOW_PROJECT_ID'];
$sessionId = session_id();
$languageCode = 'en-US';

// Debug mode (only in development)
$debug = isset($_GET['debug']);

// Get the message from frontend
$userMessage = $_POST['message'] ?? 'Hi there!';

// Log incoming requests if debug is on
if ($debug) {
    error_log("[Dialogflow] Incoming message: '$userMessage'");
}

try {
    // Enable debug mode for development
    $debug = true;
    
    // Start Dialogflow session using credentials array
    $sessionsClient = new SessionsClient([
        'credentials' => $dialogflowCredentials,
        'transport' => 'rest'
    ]);
    $session = $sessionsClient->sessionName($projectId, $sessionId);
    
    // Log success if debug is on
    if ($debug) {
        error_log("[Dialogflow] Successfully created session client with project: $projectId");
    }

    // Prepare input
    $textInput = new TextInput();
    $textInput->setText($userMessage);
    $textInput->setLanguageCode($languageCode);

    $queryInput = new QueryInput();
    $queryInput->setText($textInput);

    // Send to Dialogflow
    $response = $sessionsClient->detectIntent($session, $queryInput);
    $queryResult = $response->getQueryResult();
    $resultText = $queryResult->getFulfillmentText();
    
    // Add intent detection info in debug mode
    if ($debug && isset($_GET['debug'])) {
        $intentName = $queryResult->getIntent() ? $queryResult->getIntent()->getDisplayName() : 'No intent matched';
        $confidence = $queryResult->getIntentDetectionConfidence();
        
        header('Content-Type: application/json');
        echo json_encode([
            'message' => $resultText,
            'intent' => $intentName,
            'confidence' => $confidence,
            'query' => $userMessage,
            'credentials_path' => $credentialsFile
        ], JSON_PRETTY_PRINT);
    } else {
        // Regular response
        echo $resultText ?: "I didn't catch that. Could you rephrase?";
    }
    
    $sessionsClient->close();

} catch (Exception $e) {
    // Enhanced error reporting
    $errorMsg = 'Error: ' . $e->getMessage();
    error_log("[Dialogflow Error] $errorMsg");
    
    if (strpos($e->getMessage(), 'ApplicationDefaultCredentials') !== false) {
        $credPath = getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: 'Not set';
        $credExists = file_exists($credPath) ? 'Yes' : 'No';
        $errorMsg .= "\nCredential path: $credPath\nFile exists: $credExists";
        
        if ($credExists) {
            $isReadable = is_readable($credPath) ? 'Yes' : 'No';
            $errorMsg .= "\nFile is readable: $isReadable";
            
            $jsonContent = @file_get_contents($credPath);
            $isValidJson = json_decode($jsonContent) !== null ? 'Yes' : 'No';
            $errorMsg .= "\nContains valid JSON: $isValidJson";
        }
    }
    
    echo $errorMsg;
}
?>
