<?php
// public/chatbot.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/**
 * TEMP DEBUG: set to true while fixing.
 * - true  => returns real err$userMessage = trim((string)($_POST['message'] ?? ''));
if ($userMessage === '') fail(400, 'Empty message.', $DEBUG);

// Try Dialogflow first, fallback to simple chatbot if it fails
try {
  $client  = new SessionsClient(['transport' => 'rest']); // REST = no gRPC ext needed
  $session = $client->sessionName($projectId, $sessionId);

  $ti = (new TextInput())->setText($userMessage)->setLanguageCode($languageCode);
  $qi = (new QueryInput())->setText($ti);

  $resp = $client->detectIntent($session, $qi);
  $txt  = trim((string)$resp->getQueryResult()->getFulfillmentText());
  $client->close();

  header('Content-Type: text/plain; charset=UTF-8');
  echo $txt !== '' ? $txt : "I didn't catch that.";

} catch (\Throwable $e) {
  // If Dialogflow fails and fallback is enabled, use simple responses
  if ($USE_SIMPLE_FALLBACK) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo getSimpleResponse($userMessage);
  } else {
    fail(500, 'Error: '.$e->getMessage(), $DEBUG);
  }
}ou see what's wrong)
 * - false => hides details (use after it works)
 */
$DEBUG = true;
$USE_SIMPLE_FALLBACK = true; // Enable simple fallback when Dialogflow fails

/* ---------- helper ---------- */
function fail($code, $msg, $debug) {
  http_response_code($code);
  header('Content-Type: text/plain; charset=UTF-8');
  echo $debug ? $msg : 'Server error.';
  exit;
}

/* ---------- Simple chatbot fallback ---------- */
function getSimpleResponse($userMessage) {
  $responses = [
    'hello|hi|hey|good morning|good afternoon|good evening' => [
      "Hello! I'm your CCIT assistant. How can I help you today?",
      "Hi there! What would you like to know about our college?",
      "Hello! I'm here to help with any questions about CCIT."
    ],
    'about|what is|tell me about|information about' => [
      "CCIT stands for College of Computer and Information Technology. We offer excellent programs in IT, Computer Science, and Information Systems.",
      "We're a leading technology college offering undergraduate and graduate programs in computer-related fields."
    ],
    'programs|courses|degrees|what can i study|majors' => [
      "We offer several programs:\n• Bachelor of Science in Information Technology\n• Bachelor of Science in Computer Science\n• Bachelor of Science in Information Systems\n• Graduate programs are also available!",
      "Our main programs include IT, Computer Science, and Information Systems. Would you like details about any specific program?"
    ],
    'admission|apply|requirements|how to apply|enroll' => [
      "For admission requirements:\n• High school diploma or equivalent\n• Entrance exam scores\n• Application form\n• Required documents\n\nVisit our admissions office for detailed requirements!",
      "You can apply online or visit our admissions office. Requirements vary by program - would you like specific details?"
    ],
    'contact|phone|email|address|location|where' => [
      "You can reach us at:\n• Visit our campus\n• Check our official website\n• Call our main office\n• Email our admissions team\n\nWould you like specific contact details for any department?",
      "We're located at our main campus. For specific contact information, please visit our contact page or admissions office."
    ],
    'thank you|thanks|thank u' => [
      "You're welcome! Feel free to ask if you have any other questions.",
      "Happy to help! Is there anything else you'd like to know about CCIT?"
    ],
    'bye|goodbye|see you|take care' => [
      "Goodbye! Feel free to return anytime if you have more questions about CCIT.",
      "Take care! We're always here to help with your CCIT inquiries."
    ]
  ];

  $message = strtolower($userMessage);
  foreach ($responses as $pattern => $possibleResponses) {
    $patterns = explode('|', $pattern);
    foreach ($patterns as $p) {
      if (strpos($message, trim($p)) !== false) {
        return $possibleResponses[array_rand($possibleResponses)];
      }
    }
  }
  
  return "I'm not sure about that. Could you ask about our programs, admission requirements, facilities, or contact information? I'm here to help with CCIT-related questions!";
}

/* ---------- GET: health & selftest ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // /chatbot.php?selftest=1 => runs a live Dialogflow ping and reports the real error if any
  if (isset($_GET['selftest'])) {
    // locate autoload (root or public)
    $autoloads = [__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'];
    $autoload = null;
    foreach ($autoloads as $p) { if (file_exists($p)) { $autoload = $p; break; } }
    if (!$autoload) fail(500, 'Autoloader not found. Run "composer install" in project root (C:\xampp\htdocs\adamson-ccit).', $DEBUG);
    require $autoload;

    // locate credentials (prefer root)
    $cred = file_exists(__DIR__.'/../dialogflow-access.json')
      ? __DIR__.'/../dialogflow-access.json'
      : (file_exists(__DIR__.'/dialogflow-access.json') ? __DIR__.'/dialogflow-access.json' : null);
    if (!$cred) fail(500, 'dialogflow-access.json not found. Place it in project root: C:\xampp\htdocs\adamson-ccit\dialogflow-access.json', $DEBUG);
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.$cred);

    // basic PHP deps check
    $missing = [];
    if (!extension_loaded('openssl')) $missing[] = 'openssl';
    if (!extension_loaded('curl'))    $missing[] = 'curl';
    if ($missing) fail(500, 'Missing PHP extensions: '.implode(', ', $missing).' (enable in php.ini and restart Apache)', $DEBUG);

    // Try a real detectIntent so we surface the real error (permissions, SSL, API disabled, etc.)
    try {
      $projectId = 'ccitasssistant-kuvg';
      $sessionId = 'selftest_'.substr(session_id(),0,8);
      $languageCode = 'en-US';

      // Add debugging for credentials
      if ($DEBUG) {
        $credData = json_decode(file_get_contents($cred), true);
        if (!$credData) fail(500, 'Invalid JSON in credentials file', $DEBUG);
        if ($credData['project_id'] !== $projectId) {
          fail(500, "Project ID mismatch: credentials have '{$credData['project_id']}', code expects '{$projectId}'", $DEBUG);
        }
      }

      $client = new \Google\Cloud\Dialogflow\V2\SessionsClient(['transport' => 'rest']);
      $session = $client->sessionName($projectId, $sessionId);

      $ti = (new \Google\Cloud\Dialogflow\V2\TextInput())
              ->setText('ping')
              ->setLanguageCode($languageCode);
      $qi = (new \Google\Cloud\Dialogflow\V2\QueryInput())->setText($ti);

      $resp = $client->detectIntent($session, $qi);
      $txt  = trim((string)$resp->getQueryResult()->getFulfillmentText());
      $client->close();

      header('Content-Type: application/json; charset=UTF-8');
      echo json_encode([
        'ok' => true,
        'message' => 'selftest ok',
        'fulfillment' => $txt
      ]);
      exit;

    } catch (\Throwable $e) {
      $msg = $e->getMessage();
      
      // Provide specific guidance for common authentication errors
      if (strpos($msg, 'UNAUTHENTICATED') !== false || strpos($msg, 'invalid authentication') !== false) {
        $guidance = "\n\nTo fix this:\n";
        $guidance .= "1. Enable Dialogflow API: https://console.cloud.google.com/apis/library/dialogflow.googleapis.com?project=ccitasssistant-kuvg\n";
        $guidance .= "2. Ensure service account has 'Dialogflow API Client' role\n";
        $guidance .= "3. Verify project ID 'ccitasssistant-kuvg' is correct\n";
        $guidance .= "4. Check that the service account key is not expired";
        $msg .= $guidance;
      }
      
      fail(500, 'Error: '.$msg, $DEBUG);
    }
  }

  // Simple health ping
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['ok' => true, 'message' => 'pong']);
  exit;
}

/* ---------- POST: normal chat ---------- */
// Check if we should use simple mode
if ($USE_SIMPLE_FALLBACK) {
  $userMessage = trim((string)($_POST['message'] ?? ''));
  if ($userMessage === '') fail(400, 'Empty message.', $DEBUG);
  
  // Try to use Dialogflow first, but if anything goes wrong, use simple fallback
  $autoloads = [__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'];
  $autoload = null;
  foreach ($autoloads as $p) { if (file_exists($p)) { $autoload = $p; break; } }
  
  if (!$autoload) {
    // No composer - use simple fallback
    header('Content-Type: text/plain; charset=UTF-8');
    echo getSimpleResponse($userMessage);
    exit;
  }
  
  $cred = file_exists(__DIR__.'/../dialogflow-access.json')
    ? __DIR__.'/../dialogflow-access.json'
    : (file_exists(__DIR__.'/dialogflow-access.json') ? __DIR__.'/dialogflow-access.json' : null);
  
  if (!$cred) {
    // No credentials - use simple fallback
    header('Content-Type: text/plain; charset=UTF-8');
    echo getSimpleResponse($userMessage);
    exit;
  }
  
  try {
    require $autoload;
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.$cred);

    $projectId    = 'ccitasssistant-kuvg';
    $sessionId    = session_id();
    $languageCode = 'en-US';

    $client  = new \Google\Cloud\Dialogflow\V2\SessionsClient(['transport' => 'rest']);
    $session = $client->sessionName($projectId, $sessionId);

    $ti = (new \Google\Cloud\Dialogflow\V2\TextInput())->setText($userMessage)->setLanguageCode($languageCode);
    $qi = (new \Google\Cloud\Dialogflow\V2\QueryInput())->setText($ti);

    $resp = $client->detectIntent($session, $qi);
    $txt  = trim((string)$resp->getQueryResult()->getFulfillmentText());
    $client->close();

    header('Content-Type: text/plain; charset=UTF-8');
    echo $txt !== '' ? $txt : "I didn't catch that.";
    exit;
    
  } catch (\Throwable $e) {
    // Dialogflow failed - use simple fallback
    header('Content-Type: text/plain; charset=UTF-8');
    echo getSimpleResponse($userMessage);
    exit;
  }
}

// Original Dialogflow-only code (when fallback is disabled)
// locate autoload
$autoloads = [__DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'];
$autoload = null;
foreach ($autoloads as $p) { if (file_exists($p)) { $autoload = $p; break; } }
if (!$autoload) fail(500, 'Autoloader not found. Run "composer install" in project root (C:\xampp\htdocs\adamson-ccit).', $DEBUG);
require $autoload;

// locate credentials
$cred = file_exists(__DIR__.'/../dialogflow-access.json')
  ? __DIR__.'/../dialogflow-access.json'
  : (file_exists(__DIR__.'/dialogflow-access.json') ? __DIR__.'/dialogflow-access.json' : null);
if (!$cred) fail(500, 'dialogflow-access.json not found. Place it in project root: C:\xampp\htdocs\adamson-ccit\dialogflow-access.json', $DEBUG);
putenv('GOOGLE_APPLICATION_CREDENTIALS='.$cred);

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

$projectId    = 'ccitasssistant-kuvg';
$sessionId    = session_id();
$languageCode = 'en-US';

$userMessage = trim((string)($_POST['message'] ?? ''));
if ($userMessage === '') fail(400, 'Empty message.', $DEBUG);

try {
  $client  = new SessionsClient(['transport' => 'rest']); // REST = no gRPC ext needed
  $session = $client->sessionName($projectId, $sessionId);

  $ti = (new TextInput())->setText($userMessage)->setLanguageCode($languageCode);
  $qi = (new QueryInput())->setText($ti);

  $resp = $client->detectIntent($session, $qi);
  $txt  = trim((string)$resp->getQueryResult()->getFulfillmentText());
  $client->close();

  header('Content-Type: text/plain; charset=UTF-8');
  echo $txt !== '' ? $txt : "I didn’t catch that.";

} catch (\Throwable $e) {
  fail(500, 'Error: '.$e->getMessage(), $DEBUG);
}
