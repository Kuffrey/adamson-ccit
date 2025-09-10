<?php
// public/chatbot.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/**
 * TEMP DEBUG: set to true while fixing.
 * - true  => returns real error messages (helps you see what's wrong)
 * - false => hides details (use after it works)
 */
$DEBUG = true;

/* ---------- helper ---------- */
function fail($code, $msg, $debug) {
  http_response_code($code);
  header('Content-Type: text/plain; charset=UTF-8');
  echo $debug ? $msg : 'Server error.';
  exit;
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
      fail(500, 'Error: '.$e->getMessage(), $DEBUG);
    }
  }

  // Simple health ping
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['ok' => true, 'message' => 'pong']);
  exit;
}

/* ---------- POST: normal chat ---------- */
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
