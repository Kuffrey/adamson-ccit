<?php
// Simple test script — open in browser:
// http://localhost/adamson-ccit/send_test_email.php?to=you@example.com

header('Content-Type: text/plain; charset=utf-8');

$to = filter_var($_GET['to'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$to) {
    echo "Usage: send_test_email.php?to=you@example.com\n";
    exit;
}

// require composer autoload
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "vendor/autoload.php not found. Run 'composer require phpmailer/phpmailer' in project root.\n";
    exit;
}
require_once $autoload;

// load config
$cfgPath = __DIR__ . '/app/config/mail.php';
$cfg = file_exists($cfgPath) ? include $cfgPath : [];

echo "Using SMTP host: " . ($cfg['host'] ?? 'none') . PHP_EOL;

try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $cfg['host'] ?? 'smtp.example.com';
    $mail->SMTPAuth = true;
    $mail->Username = $cfg['username'] ?? '';
    $mail->Password = $cfg['password'] ?? '';
    if (!empty($cfg['secure'])) {
        $mail->SMTPSecure = $cfg['secure'];
    }
    $mail->Port = $cfg['port'] ?? 587;

    // Set SMTP debug level (0 = off, 2 = detailed)
    $mail->SMTPDebug = 0;
    // $mail->SMTPDebug = 2; $mail->Debugoutput = function($str, $level){ echo "[".$level."] ".$str.PHP_EOL; };

    // Use SMTP2GO verified sender email here
    $mail->setFrom('adu_ccit@outlook.ph', 'Adamson CCIT');

    // Set reply-to email (where replies go)
    $mail->addReplyTo('adu_ccit@outlook.ph', 'Adamson CCIT Support');

    // Recipient
    $mail->addAddress($to);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer test';
    $mail->Body = '<p>Test message from Career Pathway Generator setup.</p>';

    $mail->send();
    echo "OK: message sent to {$to}\n";
} catch (\PHPMailer\PHPMailer\Exception $e) {
    echo "PHPMailer Exception: " . $e->getMessage() . "\n";
    echo "PHPMailer ErrorInfo: " . ($mail->ErrorInfo ?? '') . "\n";
} catch (Exception $e) {
    echo "General Exception: " . $e->getMessage() . "\n";
}
