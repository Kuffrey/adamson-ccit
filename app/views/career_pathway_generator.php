<?php
/* career_pathway_generator.php */


/* ========== MANUAL REMINDER TRIGGER (FOR DEMO/TESTING) ========== */
if (isset($_GET['send_reminders']) && $_GET['send_reminders'] === 'now') {
    
    // Force reminder season to TRUE
    function isReminderSeason() { return true; }
    
    // Include all the reminder functions here...
    function getCurrentSemester() {
        $month = (int)date('n');
        $year = date('Y');
        if ($month >= 8 && $month <= 12) {
            return "1st Semester SY " . $year . "-" . ($year + 1);
        } elseif ($month >= 1 && $month <= 5) {
            return "2nd Semester SY " . ($year - 1) . "-" . $year;
        } else {
            return "Summer SY " . $year;
        }
    }
    
    function getReminderLog() {
        $file = __DIR__ . '/../data/reminder_log.json';
        if (!file_exists($file)) return [];
        $raw = @file_get_contents($file);
        return $raw ? json_decode($raw, true) : [];
    }
    
    function addReminderLog($email, $semester) {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $log = getReminderLog();
        $log[$email] = ['sent' => date('c'), 'semester' => $semester];
        @file_put_contents($dir . '/reminder_log.json', json_encode($log, JSON_PRETTY_PRINT));
    }
    
    function shouldSendReminder($email, $semester) {
        // DEMO MODE: Always send (ignore previous sends)
        return true;
    }
    
    function getNotifyList() {
        $file = __DIR__ . '/../data/notify_list.json';
        if (!file_exists($file)) return [];
        $raw = @file_get_contents($file);
        $list = $raw ? json_decode($raw, true) : [];
        return is_array($list) ? $list : [];
    }
    
    echo "<!DOCTYPE html><html><head><title>Sending Reminders...</title></head><body>";
    echo "<h2>🚀 Sending Semester Reminders NOW...</h2>";
    echo "<pre>";
    
    $list = getNotifyList();
    
    if (empty($list)) {
        echo "❌ No emails in notification list.\n";
        echo "Make sure someone used the generator with an @adamson.edu.ph email first.\n";
        echo "</pre></body></html>";
        exit;
    }
    
    $semester = getCurrentSemester();
    echo "📅 Current Semester: {$semester}\n\n";
    
    // Load config
    $cfg = [];
    $configPaths = [
        __DIR__ . '/../../app/config/mail.local.php',
        __DIR__ . '/../../app/config/mail.php',
    ];
    
    foreach ($configPaths as $path) {
        if (file_exists($path)) {
            $inc = include $path;
            if (is_array($inc)) {
                $cfg = $inc;
                break;
            }
        }
    }
    
    if (empty($cfg)) {
        echo "❌ Mail config not found!\n";
        echo "</pre></body></html>";
        exit;
    }
    
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
        }
    }
    
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        echo "❌ PHPMailer not loaded!\n";
        echo "</pre></body></html>";
        exit;
    }
    
    $sent = 0;
    $failed = 0;
    
    foreach ($list as $entry) {
        $email = $entry['email'] ?? '';
        
        // Only Adamson emails
        if (!preg_match('/@adamson\.edu\.ph$/i', $email)) {
            echo "⏭️  SKIP: {$email} (not an Adamson email)\n";
            continue;
        }
        
        echo "📧 Sending to: {$email}... ";
        
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            $mail->isSMTP();
            $mail->Host = $cfg['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $cfg['username'];
            $mail->Password = $cfg['password'];
            $mail->SMTPSecure = $cfg['secure'] ?? 'tls';
            $mail->Port = $cfg['port'] ?? 587;
            $mail->SMTPDebug = 0;
            
            $fromAddr = $cfg['from'][0] ?? 'adu_ccit@outlook.ph';
            $fromName = $cfg['from'][1] ?? 'Adamson CCIT';
            $mail->setFrom($fromAddr, $fromName);
            
            $mail->addAddress($email);
            $mail->Subject = 'Kamusta, Klasmeyt! Time to Retake Your Career Pathway 🎓';
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url = $protocol . '://' . $domain . '/adamson-ccit/public/index.php?page=career_pathway_generator';
            
            $mail->Body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0b234c 0%, #00713D 100%); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .greeting { font-size: 20px; font-weight: bold; color: #0b234c; margin-bottom: 15px; }
        .message { font-size: 16px; color: #555; margin-bottom: 20px; }
        .highlight { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .highlight h3 { margin: 0 0 10px; color: #856404; }
        .reasons { background: #f8f9fa; padding: 20px; border-radius: 4px; margin: 20px 0; }
        .reasons ul { margin: 10px 0; padding-left: 20px; }
        .reasons li { margin-bottom: 8px; }
        .cta { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background: #00713D; color: white; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Career Pathway Check-In</h1>
            <p>' . htmlspecialchars($semester) . '</p>
        </div>
        <div class="content">
            <div class="greeting">Kamusta, Klasmeyt! 👋</div>
            <p class="message">
                Hope you\'re doing great this semester! Remember when you took the 
                <strong>Career Pathway Generator</strong>? A lot can change in just a few months — 
                new subjects, fresh experiences, updated goals!
            </p>
            <div class="highlight">
                <h3>🤔 Still sure about your path?</h3>
                <p>With midterms approaching, this is the <strong>perfect time</strong> to 
                re-evaluate your interests and make sure you\'re heading in the right direction.</p>
            </div>
            <div class="reasons">
                <h4>Why retake now?</h4>
                <ul>
                    <li>📚 <strong>You\'ve learned new things</strong> — Your interests might have shifted</li>
                    <li>💡 <strong>New career trends</strong> — The IT industry evolves fast</li>
                    <li>🎯 <strong>Refine your goals</strong> — Get clearer on certifications and skills</li>
                    <li>🚀 <strong>Plan ahead</strong> — Know what to focus on next</li>
                </ul>
            </div>
            <div class="cta">
                <a href="' . $url . '" class="btn">🔄 Retake Career Pathway Generator</a>
            </div>
            <p style="font-size: 14px; color: #666; margin-top: 20px;">
                <strong>P.S.</strong> This is a friendly semester reminder for Adamson students. 
                You can retake the assessment anytime — no pressure! 😊
            </p>
        </div>
        <div class="footer">
            <p><strong>Adamson University</strong><br>College of Computing and Information Technologies</p>
            <p>© ' . date('Y') . ' AdU-CCIT</p>
        </div>
    </div>
</body>
</html>';
            
            if ($mail->send()) {
                echo "✅ SUCCESS!\n";
                addReminderLog($email, $semester);
                $sent++;
            } else {
                echo "❌ FAILED: " . $mail->ErrorInfo . "\n";
                $failed++;
            }
            
        } catch (Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\n========================================\n";
    echo "✅ Successfully sent: {$sent}\n";
    echo "❌ Failed: {$failed}\n";
    echo "========================================\n";
    
    echo "</pre>";
    echo "<p><a href='?page=career_pathway_generator'>← Back to Career Pathway Generator</a></p>";
    echo "</body></html>";
    exit;
}

/* ========== PRODUCTION MODE: AUTO SCHEDULED REMINDERS (COMMENTED FOR NOW) CODE 2 REMINDERS ========== */
/*
// UNCOMMENT THIS AFTER DEMO, THEN DELETE THE MANUAL TRIGGER CODE ABOVE

// Check if it's time to send reminders (once per day max)
function shouldRunReminderCheck() {
    $lastRun = __DIR__ . '/../data/last_reminder_check.txt';
    if (!file_exists($lastRun)) return true;
    $lastDate = @file_get_contents($lastRun);
    return $lastDate !== date('Y-m-d');
}

function markReminderCheckDone() {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($dir . '/last_reminder_check.txt', date('Y-m-d'));
}

function getCurrentSemester() {
    $month = (int)date('n');
    $year = date('Y');
    
    if ($month >= 8 && $month <= 12) {
        return "1st Semester SY " . $year . "-" . ($year + 1);
    } elseif ($month >= 1 && $month <= 5) {
        return "2nd Semester SY " . ($year - 1) . "-" . $year;
    } else {
        return "Summer SY " . $year;
    }
}

function isReminderSeason() {
    $month = (int)date('n');
    $day = (int)date('j');
    
    // CUSTOMIZE: Change these dates based on your schedule
    // Send reminders during:
    // - November 1-7 (1st semester midterm)
    // - March 1-7 (2nd semester midterm)
    
    if ($month === 11 && $day >= 1 && $day <= 7) return true;
    if ($month === 3 && $day >= 1 && $day <= 7) return true;
    
    return false;
}

function getReminderLog() {
    $file = __DIR__ . '/../data/reminder_log.json';
    if (!file_exists($file)) return [];
    $raw = @file_get_contents($file);
    return $raw ? json_decode($raw, true) : [];
}

function addReminderLog($email, $semester) {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $log = getReminderLog();
    $log[$email] = ['sent' => date('c'), 'semester' => $semester];
    @file_put_contents($dir . '/reminder_log.json', json_encode($log, JSON_PRETTY_PRINT));
}

function shouldSendReminder($email, $semester) {
    $log = getReminderLog();
    if (!isset($log[$email])) return true;
    $lastSemester = $log[$email]['semester'] ?? '';
    return $lastSemester !== $semester;
}

function getNotifyList() {
    $file = __DIR__ . '/../data/notify_list.json';
    if (!file_exists($file)) return [];
    $raw = @file_get_contents($file);
    $list = $raw ? json_decode($raw, true) : [];
    return is_array($list) ? $list : [];
}

function sendSemesterReminders() {
    // Only run once per day
    if (!shouldRunReminderCheck()) return;
    
    // Only during reminder season
    if (!isReminderSeason()) return;
    
    $list = getNotifyList();
    if (empty($list)) return;
    
    $semester = getCurrentSemester();
    
    // Load config
    $cfg = [];
    $configPaths = [
        __DIR__ . '/../../app/config/mail.local.php',
        __DIR__ . '/../../app/config/mail.php',
    ];
    
    foreach ($configPaths as $path) {
        if (file_exists($path)) {
            $inc = include $path;
            if (is_array($inc)) {
                $cfg = $inc;
                break;
            }
        }
    }
    
    if (empty($cfg)) return;
    
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
        }
    }
    
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) return;
    
    $sent = 0;
    
    foreach ($list as $entry) {
        $email = $entry['email'] ?? '';
        
        // Only Adamson emails
        if (!preg_match('/@adamson\.edu\.ph$/i', $email)) continue;
        
        // Check if already sent this semester
        if (!shouldSendReminder($email, $semester)) continue;
        
        // Limit to 5 emails per run to avoid timeout
        if ($sent >= 5) break;
        
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            $mail->isSMTP();
            $mail->Host = $cfg['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $cfg['username'];
            $mail->Password = $cfg['password'];
            $mail->SMTPSecure = $cfg['secure'] ?? 'tls';
            $mail->Port = $cfg['port'] ?? 587;
            $mail->SMTPDebug = 0;
            
            $fromAddr = $cfg['from'][0] ?? 'adu_ccit@outlook.ph';
            $fromName = $cfg['from'][1] ?? 'Adamson CCIT';
            $mail->setFrom($fromAddr, $fromName);
            
            $mail->addAddress($email);
            $mail->Subject = 'Kamusta, Klasmeyt! Time to Retake Your Career Pathway 🎓';
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url = $protocol . '://' . $domain . '/adamson-ccit/public/index.php?page=career_pathway_generator';
            
            $mail->Body = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;line-height:1.6;color:#333;margin:0;padding:0;background:#f4f4f4}
.container{max-width:600px;margin:20px auto;background:white;border-radius:8px;overflow:hidden}
.header{background:linear-gradient(135deg,#0b234c 0%,#00713D 100%);padding:30px;text-align:center;color:white}
.header h1{margin:0;font-size:24px}
.content{padding:30px}
.greeting{font-size:20px;font-weight:bold;color:#0b234c;margin-bottom:15px}
.message{font-size:16px;color:#555;margin-bottom:20px}
.highlight{background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0;border-radius:4px}
.highlight h3{margin:0 0 10px;color:#856404}
.reasons{background:#f8f9fa;padding:20px;border-radius:4px;margin:20px 0}
.reasons ul{margin:10px 0;padding-left:20px}
.reasons li{margin-bottom:8px}
.cta{text-align:center;margin:30px 0}
.btn{display:inline-block;background:#00713D;color:white;text-decoration:none;padding:12px 30px;border-radius:5px;font-weight:bold}
.footer{background:#f8f9fa;padding:20px;text-align:center;font-size:12px;color:#666}
</style></head>
<body>
<div class="container">
<div class="header"><h1>🎓 Career Pathway Check-In</h1><p>' . htmlspecialchars($semester) . '</p></div>
<div class="content">
<div class="greeting">Kamusta, Klasmeyt! 👋</div>
<p class="message">Hope you\'re doing great this semester! Remember when you took the <strong>Career Pathway Generator</strong>? A lot can change in just a few months — new subjects, fresh experiences, updated goals!</p>
<div class="highlight"><h3>🤔 Still sure about your path?</h3><p>With midterms approaching, this is the <strong>perfect time</strong> to re-evaluate your interests and make sure you\'re heading in the right direction.</p></div>
<div class="reasons"><h4>Why retake now?</h4><ul>
<li>📚 <strong>You\'ve learned new things</strong> — Your interests might have shifted</li>
<li>💡 <strong>New career trends</strong> — The IT industry evolves fast</li>
<li>🎯 <strong>Refine your goals</strong> — Get clearer on certifications and skills</li>
<li>🚀 <strong>Plan ahead</strong> — Know what to focus on next</li>
</ul></div>
<div class="cta"><a href="' . $url . '" class="btn">🔄 Retake Career Pathway Generator</a></div>
<p style="font-size:14px;color:#666;margin-top:20px"><strong>P.S.</strong> This is a friendly semester reminder for Adamson students. You can retake the assessment anytime — no pressure! 😊</p>
</div>
<div class="footer"><p><strong>Adamson University</strong><br>College of Computing and Information Technologies</p><p>© ' . date('Y') . ' AdU-CCIT</p></div>
</div></body></html>';
            
            if ($mail->send()) {
                addReminderLog($email, $semester);
                $sent++;
            }
            
        } catch (Exception $e) {
            // Silent fail - don't break the page
            continue;
        }
    }
    
    // Mark that we ran today
    markReminderCheckDone();
}

// AUTO-RUN: Send reminders in background when page loads normally
if (!isset($_POST['action'])) {
    @sendSemesterReminders();
}
*/



/* ========== STRICT EMAIL VALIDATION FUNCTION (NEW) ========== */
/**
 * Strict server-side email validation
 * Matches the JavaScript validation rules
 */
function validateEmailStrict($email) {
    $errors = [];
    
    // 1. Basic checks
    if (empty($email) || !is_string($email)) {
        return ['valid' => false, 'error' => 'Email is required'];
    }
    
    $email = strtolower(trim($email));
    
    // 2. Basic format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    // 3. Split into parts
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    list($localPart, $domain) = $parts;
    
    // 4. Domain whitelist
    $allowedDomains = ['gmail.com', 'outlook.com', 'adamson.edu.ph'];
    if (!in_array($domain, $allowedDomains)) {
        return [
            'valid' => false,
            'error' => 'Only ' . implode(', ', $allowedDomains) . ' emails are accepted. "' . $domain . '" is not allowed.'
        ];
    }
    
    // 5. Local part length (3-64 characters)
    if (strlen($localPart) < 3) {
        return [
            'valid' => false,
            'error' => 'Email username is too short (minimum 3 characters before @)'
        ];
    }
    
    if (strlen($localPart) > 64) {
        return [
            'valid' => false,
            'error' => 'Email username is too long (maximum 64 characters before @)'
        ];
    }
    
    // 6. Must contain at least one letter
    if (!preg_match('/[a-z]/i', $localPart)) {
        return [
            'valid' => false,
            'error' => 'Email username must contain at least one letter (a-z)'
        ];
    }
    
    // 7. Valid characters only (alphanumeric, dots, hyphens, underscores)
    if (!preg_match('/^[a-z0-9._-]+$/i', $localPart)) {
        return [
            'valid' => false,
            'error' => 'Email username contains invalid characters. Only letters, numbers, dots (.), hyphens (-), and underscores (_) are allowed.'
        ];
    }
    
    // 8. Cannot start or end with special characters
    if (preg_match('/^[._-]|[._-]$/', $localPart)) {
        return [
            'valid' => false,
            'error' => 'Email username cannot start or end with a dot, hyphen, or underscore'
        ];
    }
    
    // 9. No consecutive dots
    if (strpos($localPart, '..') !== false) {
        return [
            'valid' => false,
            'error' => 'Email username cannot contain consecutive dots (..)'
        ];
    }
    
    // 10. Block suspicious patterns
    $suspiciousPatterns = [
        '/^test\d*$/i',
        '/^temp\d*$/i',
        '/^fake\d*$/i',
        '/^user\d*$/i',
        '/^admin\d*$/i',
        '/^sample\d*$/i',
        '/^demo\d*$/i',
        '/^abc\d*$/i',
        '/^xyz\d*$/i',
        '/^asdf\d*$/i',
        '/^qwerty\d*$/i',
        '/^\d+$/',              // pure numbers
        '/^[a-z]{1,2}$/i',      // single/double letters
        '/^noreply$/i',
        '/^no[-_]?reply$/i',
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $localPart)) {
            return [
                'valid' => false,
                'error' => 'This email appears to be a test or temporary address. Please use your real email address.'
            ];
        }
    }
    
    // 11. Check for repeated characters
    if (preg_match('/^(.)\1+$/', $localPart)) {
        return [
            'valid' => false,
            'error' => 'Email username cannot be the same character repeated'
        ];
    }
    
    // 12. Institutional email requirements
    if ($domain === 'adamson.edu.ph' && strlen($localPart) < 4) {
        return [
            'valid' => false,
            'error' => 'Adamson email addresses must have at least 4 characters before @'
        ];
    }
    
    // All checks passed
    return ['valid' => true, 'email' => $email];
}

// ===== BULLETPROOF EMAIL HANDLER - STOPS EVERYTHING =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_email') {
    
    // CRITICAL: Clear ALL output buffers and prevent any further output
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // CRITICAL: Start fresh output buffer that we control
    ob_start();
    
    // Set headers IMMEDIATELY
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Load dependencies
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
    }
    
    // Helper: write to mail log
    function mail_log($msg){
        $logDir = __DIR__ . '/../../app/logs';
        if(!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $line = date('Y-m-d H:i:s') . " - " . $msg . PHP_EOL;
        @file_put_contents($logDir . '/mail.log', $line, FILE_APPEND|LOCK_EX);
    }
    
    // Helper: add adamson emails to notification list
    function addNotifyEmail($email){
        $listDir = __DIR__ . '/../data';
        $listFile = $listDir . '/notify_list.json';
        if(!is_dir($listDir)) @mkdir($listDir, 0755, true);
        
        $list = [];
        if (file_exists($listFile)) {
            $raw = @file_get_contents($listFile);
            $list = $raw ? json_decode($raw, true) : [];
            if(!is_array($list)) $list = [];
        }
        
        $email = strtolower(trim($email));
        $exists = false;
        foreach($list as $item){
            if (isset($item['email']) && strtolower($item['email']) === $email) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $list[] = ['email'=>$email, 'added'=>date('c')];
            @file_put_contents($listFile, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            mail_log("Added {$email} to notification list");
        }
    }
    
    // Function to send JSON and DIE completely
    function sendJsonAndDie($data) {
        ob_clean(); // Clear any accumulated output
        echo json_encode($data);
        ob_end_flush(); // Send the output
        die(); // HARD STOP - nothing after this runs
    }
    
    mail_log("=== EMAIL REQUEST RECEIVED ===");
    
    // STRICT EMAIL VALIDATION (NEW)
    $emailInput = $_POST['email'] ?? '';
    $validation = validateEmailStrict($emailInput);
    
    if (!$validation['valid']) {
        mail_log("ERROR: Email validation failed - " . $validation['error']);
        sendJsonAndDie([
            'ok' => false, 
            'error' => 'invalid_email',
            'message' => $validation['error']
        ]);
    }
    
    $email = $validation['email']; // Use the cleaned/validated email
    mail_log("Processing email for: {$email}");
    
    $results = $_POST['results'] ?? '{}';
    $trace = $_POST['trace'] ?? '{}';
    
    // Parse the scores and trace to create readable content
    $scoresData = json_decode($results, true);
    $traceData = json_decode($trace, true);
    
    // Sort scores by value (highest first)
    arsort($scoresData);
    $total = array_sum($scoresData) ?: 1;
    
    // Get top 3 matches
    $topMatches = array_slice($scoresData, 0, 3, true);
    
    // Category labels
    $categoryLabels = [
        'dev' => 'Software & Web Development',
        'mobile' => 'Mobile & Enterprise Apps',
        'uiux' => 'UI/UX & Front-end',
        'game' => 'Game Dev & Multimedia',
        'data' => 'Data & Databases',
        'sec' => 'Cybersecurity & Networks',
        'sys' => 'Systems, Cloud & DevOps',
        'ai' => 'AI & Machine Learning',
        'cloud' => 'Cloud, Blockchain & Emerging Tech',
        'pm' => 'IT Project & Business Analysis'
    ];
    
    // Build the beautiful email HTML
    $message = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #0b234c 0%, #00713D 100%); padding: 40px 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 28px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 16px; }
        .content { padding: 40px 30px; }
        .intro { font-size: 16px; color: #475569; margin-bottom: 30px; line-height: 1.8; }
        .match-card { background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 100%); border: 2px solid #00713D; border-radius: 12px; padding: 24px; margin-bottom: 20px; }
        .match-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; }
        .match-title { font-size: 20px; font-weight: 700; color: #0b234c; margin: 0; }
        .match-badge { background: #00713D; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .match-score { font-size: 24px; font-weight: 800; color: #00713D; }
        .progress-bar { background: #e5e7eb; border-radius: 10px; height: 12px; overflow: hidden; margin: 16px 0; }
        .progress-fill { background: linear-gradient(90deg, #00713D 0%, #10b981 100%); height: 100%; border-radius: 10px; }
        .why-section { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin-top: 16px; }
        .why-section h4 { margin: 0 0 12px; color: #92400e; font-size: 14px; font-weight: 700; }
        .why-section ul { margin: 0; padding-left: 20px; color: #78350f; }
        .why-section li { margin-bottom: 6px; font-size: 14px; }
        .other-matches { margin-top: 40px; }
        .other-matches h3 { color: #0b234c; font-size: 18px; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        .small-card { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px; }
        .small-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .small-card-title { font-size: 16px; font-weight: 600; color: #0b234c; margin: 0; }
        .small-card-score { font-size: 18px; font-weight: 700; color: #0080c9; }
        .small-progress { background: #e5e7eb; border-radius: 6px; height: 8px; overflow: hidden; }
        .small-progress-fill { background: #0080c9; height: 100%; border-radius: 6px; }
        .cta-box { background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 100%); border: 2px solid #0080c9; border-radius: 12px; padding: 30px; text-align: center; margin-top: 40px; }
        .cta-box h3 { color: #0b234c; font-size: 20px; margin: 0 0 12px; }
        .cta-box p { color: #475569; margin: 0 0 24px; font-size: 15px; }
        .btn { display: inline-block; background: #00713D; color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; margin: 0 6px 6px; font-size: 15px; }
        .footer { background: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #6b7280; font-size: 13px; margin: 6px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Your Career Pathway Results</h1>
            <p>AdU-CCIT Career Pathway Generator</p>
        </div>
        <div class="content">
            <p class="intro">
                Thank you for using the Career Pathway Generator! Based on your responses, 
                we\'ve identified the IT career paths that align best with your interests, 
                skills, and aspirations.
            </p>';
    
    // Top Match Card
    if (!empty($topMatches)) {
        $topKey = array_key_first($topMatches);
        $topScore = $topMatches[$topKey];
        $topPct = round(($topScore / $total) * 100);
        $topLabel = $categoryLabels[$topKey] ?? ucfirst($topKey);
        
        $message .= '
            <div class="match-card">
                <div class="match-header">
                    <h2 class="match-title">🏆 ' . htmlspecialchars($topLabel) . '</h2>
                    <span class="match-badge">Top Match</span>
                </div>
                <div class="match-score">' . $topPct . '% Match</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ' . $topPct . '%"></div>
                </div>
                <p style="color: #475569; margin: 12px 0 0; font-size: 15px;">
                    This area aligns most strongly with your responses.
                </p>';
        
        // Why section
        if (isset($traceData[$topKey]) && is_array($traceData[$topKey]) && count($traceData[$topKey]) > 0) {
            usort($traceData[$topKey], function($a, $b) {
                return ($b['w'] ?? 0) - ($a['w'] ?? 0);
            });
            $topReasons = array_slice($traceData[$topKey], 0, 5);
            
            $message .= '
                <div class="why-section">
                    <h4>💡 Why you got this match:</h4>
                    <ul>';
            foreach ($topReasons as $reason) {
                $message .= '<li>' . htmlspecialchars($reason['reason'] ?? '') . '</li>';
            }
            $message .= '</ul>
                </div>';
        }
        
        $message .= '</div>';
        
        // Other matches
        $otherMatches = array_slice($topMatches, 1, 2, true);
        if (!empty($otherMatches)) {
            $message .= '
            <div class="other-matches">
                <h3>📊 Other Strong Matches</h3>';
            
            foreach ($otherMatches as $key => $score) {
                $pct = round(($score / $total) * 100);
                $label = $categoryLabels[$key] ?? ucfirst($key);
                
                $message .= '
                <div class="small-card">
                    <div class="small-card-header">
                        <h4 class="small-card-title">' . htmlspecialchars($label) . '</h4>
                        <span class="small-card-score">' . $pct . '%</span>
                    </div>
                    <div class="small-progress">
                        <div class="small-progress-fill" style="width: ' . $pct . '%"></div>
                    </div>
                </div>';
            }
            
            $message .= '</div>';
        }
    }
    
    // Get the actual domain
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $resultsUrl = $protocol . '://' . $domain . '/adamson-ccit/public/index.php?page=career_pathway_results';
    
    $message .= '
            <div class="cta-box">
                <h3>🚀 Ready to Explore Your Path?</h3>
                <p>Visit your results page to see detailed program recommendations and learning paths.</p>
                <a href="' . $resultsUrl . '" class="btn">View Detailed Results</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>Need help?</strong> Contact the AdU-CCIT program office.</p>
            <p style="margin-top: 16px;">If you did not request this, please ignore this email.</p>
            <p style="margin-top: 16px; color: #9ca3af;">
                © ' . date('Y') . ' Adamson University<br>
                College of Computing and Information Technologies
            </p>
        </div>
    </div>
</body>
</html>';
    
    try {
        if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            mail_log("ERROR: PHPMailer class not found");
            sendJsonAndDie(['ok' => false, 'error' => 'PHPMailer not loaded']);
        }
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Load config
        $cfg = [];
        $configPaths = [
            __DIR__ . '/../../app/config/mail.local.php',
            __DIR__ . '/../../app/config/mail.php',
        ];
        
        foreach ($configPaths as $cfgPath) {
            if (file_exists($cfgPath)) {
                $inc = include $cfgPath;
                if (is_array($inc)) {
                    $cfg = $inc;
                    break;
                }
            }
        }
        
        if (empty($cfg)) {
            mail_log("ERROR: No config found");
            sendJsonAndDie(['ok' => false, 'error' => 'SMTP config missing']);
        }
        
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = $cfg['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $cfg['username'];
        $mail->Password = $cfg['password'];
        $mail->SMTPSecure = $cfg['secure'] ?? 'tls';
        $mail->Port = $cfg['port'] ?? 587;
        $mail->SMTPDebug = 0; // Set to 0 for production
        
        $fromAddr = $cfg['from'][0] ?? 'adu_ccit@outlook.ph';
        $fromName = $cfg['from'][1] ?? 'Adamson CCIT Pathway';
        $mail->setFrom($fromAddr, $fromName);
        
        if (isset($cfg['reply_to'])) {
            $mail->addReplyTo($cfg['reply_to'][0], $cfg['reply_to'][1] ?? '');
        }
        
        $mail->addAddress($email);
        $mail->Subject = 'Your Career Pathway Generator Results — AdU-CCIT';
        $mail->isHTML(true);
        $mail->Body = $message;
        $mail->CharSet = 'UTF-8';
        
        $sendResult = $mail->send();
        
        if ($sendResult) {
            mail_log("✅ Email sent successfully to {$email}");
            
            if (preg_match('/@adamson\.edu\.ph$/i', $email)) {
                addNotifyEmail($email);
            }
            
            sendJsonAndDie(['ok' => true, 'message' => 'Email sent successfully']);
        } else {
            mail_log("❌ Send failed: " . $mail->ErrorInfo);
            sendJsonAndDie(['ok' => false, 'error' => 'send_failed', 'detail' => $mail->ErrorInfo]);
        }
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        mail_log("❌ PHPMailer Exception: " . $e->getMessage());
        sendJsonAndDie(['ok' => false, 'error' => 'mail_exception', 'message' => $e->getMessage()]);
        
    } catch (Exception $ex) {
        mail_log("❌ General Exception: " . $ex->getMessage());
        sendJsonAndDie(['ok' => false, 'error' => 'exception', 'message' => $ex->getMessage()]);
    }
}

// ===== If we reach here, render the normal HTML page =====
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Career Pathway Generator | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <style>
    /* keep EXACT same page spacing as before */
    .page-career .content > .container{padding-block:clamp(22px,4vw,44px)}

    /* card + question spacing from original */
    .q{border:1px solid var(--edgec);border-radius:14px;background:#fff;padding:16px}
    .q + .q{margin-top:14px}
    .q h3{margin:0 0 8px;color:#0b234c;font-weight:900}
    .q p{margin:0 0 10px;color:#475569}
    .opts{display:grid;gap:10px}
    .opts.cols-2{grid-template-columns:1fr 1fr}
    .opts.cols-3{grid-template-columns:1fr 1fr 1fr}
    @media (max-width:860px){ .opts.cols-3{grid-template-columns:1fr 1fr} }
    @media (max-width:580px){ .opts.cols-2,.opts.cols-3{grid-template-columns:1fr} }
    .opt{
      display:flex;gap:10px;align-items:flex-start;background:#f8fafc;border:1px solid var(--edgec);
      padding:10px;border-radius:12px;cursor:pointer
    }
    .opt:hover{border-color:#d8dde8}
    .opt input{margin-top:3px}
    .q .helper{color:#6b7280;font-size:13px;margin-top:6px}
    .pillhint{font:800 11px/1 "Inter",system-ui;text-transform:uppercase;letter-spacing:.05em;color:#475569}
    .note-inline{margin-top:10px;border:1px dashed var(--edgec);background:#fafbfd;color:#6b7280;border-radius:12px;padding:10px}

    /* stepper container — match original feel (no extra margins), keep bottom padding for sticky nav */


    /* show one step, no extra outer margins */
    .step{display:none}
    .step.is-active{display:block;animation:fade .25s ease}
    @keyframes fade{from{opacity:.2;transform:translateY(4px)}to{opacity:1;transform:none}}

    /* progress — trimmed spacing to match original compact rhythm */
    .progress{
      margin:0 0 14px; /* same block gap (14px) */
      background:#fff;border:1px solid var(--edgec);border-radius:12px;padding:12px 14px;
      display:flex;align-items:center;gap:12px
    }
    .progress__text{font-weight:800;color:#0b234c}
    .progress__bar{flex:1;height:10px;background:#eef2f7;border-radius:999px;overflow:hidden}
    .progress__fill{height:100%;width:0;background:#0080c9;border-radius:999px;transition:width .3s ease}

    /* nav — keep compact spacing (12px) like original actions row, no extra gradients pushing space */
    .nav{
      display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px;
      position:sticky;bottom:0;background:#ffffff; /* flat to avoid “extra margin” look */
      padding-top:10px;border-top:1px solid var(--edgec)
    }
    .nav .left, .nav .right{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:10px 14px;font-weight:800;border:1px solid #cfd6e2;background:#fff;color:#0b234c}
    .btn:hover{background:#f8fafc}
    .btn--solid{background:#00713D;border-color:#00713D;color:#fff}
    .btn--solid:hover{background:#005A2F;border-color:#005A2F}
    .btn[disabled]{opacity:.5;cursor:not-allowed}
    .btn--ghost{border-color:transparent;background:transparent}

    /* (unchanged result helpers you already had) */
    .results{display:none}
    .res__grid{display:grid;gap:16px;grid-template-columns:repeat(3,1fr)}
    @media (max-width:960px){ .res__grid{grid-template-columns:1fr 1fr} }
    @media (max-width:640px){ .res__grid{grid-template-columns:1fr} }
    .res{border:1px solid var(--edgec);border-radius:14px;background:#fff;padding:16px}
    .res__head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
    .score{margin-left:auto;background:#eef2ff;border:1px solid #d8dcef;border-radius:999px;padding:6px 10px;font-weight:800;color:#0b234c}
    .mini{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
    .mini .chip{position:static;background:#f8fafc;border:1px solid var(--edgec);color:#0b234c}
    .muted{color:#6b7280}
    .hr{height:1px;background:var(--edgec);margin:10px 0}
  </style>
</head>
<body>

<main class="page-career">

  <!-- SUB-HERO -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Planner</p>
      <h1 class="subhero__title">Career Pathway Generator</h1>
      <p class="subhero__lead">Answer a quick survey and get tailored IT/CS career pathways with skills, roles, and next steps.</p>
    </div>
  </section>

  <!-- FORM (stepper) -->
  <section class="content section-sep">
    <div class="container subhero__inner" style="padding-block:clamp(22px,4vw,44px);">
      <form id="cpForm" class="stepper" novalidate>

        <!-- NEW: Consent / DPA step (must be first) -->
        <div class="step is-active" data-step="0" data-requires="consent">
          <div class="q">
            <h3>Before you start — Data Protection & Consent</h3>
            <p class="muted">Privacy notice: Your answers will be used to generate tailored career pathways. We store minimal responses temporarily and may use your email to send results or semester reminders if you use an institutional address. By providing your email and agreeing below you consent to these uses. You may request deletion by contacting the program office. Lastly, in order to have credible results - you may only take this for only 3 times per email/adamson mail.</p>

            <div style="margin-top:12px;display:grid;gap:8px;max-width:620px;">
              <label for="emailInput">Email (required) — accepted domains: <strong>@gmail.com</strong> , <strong>@outlook.com</strong> or <strong>@adamson.edu.ph</strong></label>
              <input id="emailInput" name="email" type="email" placeholder="you@gmail.com, you@outlook.com or you@adamson.edu.ph" style="padding:10px;border-radius:8px;border:1px solid var(--edgec)" required>
              <div style="font-size:13px;color:#6b7280">Note: Gmail addresses will receive the result once. Adamson addresses will be flagged to receive semester reminder emails to retake the survey. You may only submit the generator 3 times; attempts automatically reset after 3 days.</div>

              <label class="opt" style="margin-top:6px">
                <input id="agreeConsent" name="agree" type="checkbox" style="margin-top:3px">
                <span style="font-weight:800;margin-left:8px">I have read the DPA/policy and agree to the uses described.</span>
              </label>

              <div id="consentHelp" class="helper" style="margin-top:8px">You must provide an accepted email and agree to proceed.</div>

              <div id="attemptInfo" class="note-inline" style="display:none"></div>
            </div>
          </div>
        </div>

        <!-- progress -->
        <div class="progress" aria-live="polite">
          <div class="progress__text" id="progressText">Question 1 of 10</div>
          <div class="progress__bar" aria-hidden="true"><div class="progress__fill" id="progressFill"></div></div>
        </div>

        <!-- Step 1 -->
        <div class="step" data-step="1">
          <div class="q">
            <h3>1) Which areas in computing are you most drawn to? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q1 = [
                  ['dev','Software or web development'],
                  ['game','Game design and animation'],
                  ['sec','Cybersecurity and network defense'],
                  ['uiux','User interface and user experience (UI/UX) design'],
                  ['data','Data analytics and databases'],
                  ['mobile','Mobile or enterprise application development'],
                  ['sys','Systems administration and IT infrastructure'],
                  ['ai','Artificial intelligence or machine learning'],
                  ['cloud','Blockchain, cloud, or emerging technologies'],
                ];
                foreach($q1 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q1[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
            <div class="helper">Tip: Pick the topics you naturally click on or read about.</div>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="step" data-step="2" data-requires="radio">
          <div class="q">
            <h3>2) What type of IT tasks do you enjoy the most?</h3>
            <div class="opts">
              <?php
                $q2 = [
                  ['uiux','Designing interactive systems or mobile apps'],
                  ['dev','Building programs and writing code'],
                  ['uiux','Designing user-friendly interfaces and web pages'],
                  ['sec','Managing networks or securing systems'],
                  ['data','Analyzing trends or large data sets'],
                  ['sys','Working with hardware or virtual machines'],
                  ['pm','Leading technical projects and teams'],
                  ['game','Developing games or digital environments'],
                ];
                foreach($q2 as $i=>$opt){
                  echo '<label class="opt"><input type="radio" name="q2" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="step" data-step="3" data-requires="radio">
          <div class="q">
            <h3>3) How do you feel about solving technical problems like bugs or errors in code?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q3" value="high"><span>Very confident – I enjoy debugging and solving issues</span></label>
              <label class="opt"><input type="radio" name="q3" value="mid"><span>Somewhat confident – I like following logic and solving puzzles</span></label>
              <label class="opt"><input type="radio" name="q3" value="low"><span>Not confident – I prefer creative or design-oriented work</span></label>
            </div>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="step" data-step="4">
          <div class="q">
            <h3>4) Which of these subjects or tasks do you enjoy most? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q4 = [
                  ['dev','Coding or software development'],
                  ['uiux','Creating websites or mobile apps'],
                  ['game','Drawing, animation, or game design'],
                  ['sec','Setting up networks or securing systems'],
                  ['logic','Solving logic or math-based problems'],
                  ['data','Working with data or making reports'],
                  ['pm','Organizing group work and managing tasks'],
                ];
                foreach($q4 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q4[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 5 -->
        <div class="step" data-step="5" data-requires="radio">
          <div class="q">
            <h3>5) When working on IT-related projects, what role do you usually prefer?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q5" value="dev"><span>Writing the code or logic</span></label>
              <label class="opt"><input type="radio" name="q5" value="uiux"><span>Designing how the app or interface looks</span></label>
              <label class="opt"><input type="radio" name="q5" value="data"><span>Making sure data is accurate and organized</span></label>
              <label class="opt"><input type="radio" name="q5" value="sys"><span>Setting up or troubleshooting the tech</span></label>
              <label class="opt"><input type="radio" name="q5" value="pm"><span>Planning the timeline or managing the team</span></label>
            </div>
          </div>
        </div>

        <!-- Step 6 -->
        <div class="step" data-step="6" data-requires="radio">
          <div class="q">
            <h3>6) Where do you see yourself working in the future?</h3>
            <div class="opts">
              <?php
                $q6 = [
                  ['dev','In a software or web development company'],
                  ['game','In a game studio or multimedia design firm'],
                  ['sec','In a cybersecurity or IT security role'],
                  ['startup','In a tech startup or freelancing business'],
                  ['data','In a data-driven company or analytics team'],
                  ['sys','In a network administration or systems support team'],
                  ['pm','In a project management or IT consulting role'],
                  ['ai','In a research lab focused on AI or emerging tech'],
                ];
                foreach($q6 as $opt){
                  echo '<label class="opt"><input type="radio" name="q6" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 7 -->
        <div class="step" data-step="7">
          <div class="q">
            <h3>7) Which of the following careers appeal most to you? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q7 = [
                  ['dev','Software or application developer'],
                  ['game','Game developer or multimedia artist'],
                  ['sec','Network administrator or security analyst'],
                  ['uiux','UI/UX designer or front-end developer'],
                  ['data','Data analyst or database administrator'],
                  ['ai','AI or machine learning specialist'],
                  ['sys','Systems engineer or DevOps technician'],
                  ['pm','IT project manager or business analyst'],
                ];
                foreach($q7 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q7[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 8 -->
        <div class="step" data-step="8">
          <div class="q">
            <h3>8) Have you had any practical IT experience? <span class="muted">(Select all that apply)</span></h3>
            <div class="opts cols-2">
              <label class="opt"><input type="checkbox" name="q8[]" value="intern"><span>Yes, internship or immersion in a tech company</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="freelance"><span>Yes, freelance or self-initiated coding projects</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="school"><span>Yes, school-based group tech projects or hackathons</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="none"><span>No, but I’m eager to gain experience</span></label>
            </div>
          </div>
        </div>

        <!-- Step 9 -->
        <div class="step" data-step="9">
          <div class="q">
            <h3>9) Have you earned any certificates or digital credentials? <span class="muted">(Select all that apply)</span></h3>
            <div class="opts cols-3">
              <label class="opt"><input type="checkbox" name="q9[]" value="prog"><span>Programming or web development</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="sec"><span>Cybersecurity or networking</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="uiux"><span>UI/UX or graphic design</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="dbcloud"><span>Database or cloud platforms</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="game"><span>Game development tools</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="pm"><span>Project or business management</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="none"><span>None yet, but I’m interested</span></label>
            </div>
          </div>
        </div>

        <!-- Step 10 -->
        <div class="step" data-step="10" data-requires="radio">
          <div class="q">
            <h3>10) Which of these activities has felt most rewarding or natural for you?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q10" value="dev"><span>Writing code or solving algorithm problems</span></label>
              <label class="opt"><input type="radio" name="q10" value="uiux"><span>Creating designs, wireframes, or animations</span></label>
              <label class="opt"><input type="radio" name="q10" value="sec"><span>Setting up networks or troubleshooting systems</span></label>
              <label class="opt"><input type="radio" name="q10" value="data"><span>Analyzing and interpreting data</span></label>
              <label class="opt"><input type="radio" name="q10" value="pm"><span>Planning tasks or leading a group project</span></label>
              <label class="opt"><input type="radio" name="q10" value="game"><span>Creating games or interactive apps</span></label>
              <label class="opt"><input type="radio" name="q10" value="undecided"><span>I’m still figuring it out</span></label>
            </div>
          </div>
        </div>

        <!-- nav -->
        <div class="nav" aria-label="Question navigation">
          <div class="left">
            <button type="button" class="btn" id="btnPrev" disabled>&larr; Previous</button>
          </div>
          <div class="right">
            <button type="button" class="btn btn--solid" id="btnNext">Next &rarr;</button>
            <button type="submit" class="btn btn--solid" id="btnSubmit" style="display:none;">Generate Pathway</button>
            <button type="button" class="btn btn--ghost" id="btnReset">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

<script>
/* ---------- helpers: limit multi-select to N per group ---------- */
document.querySelectorAll('.opts[data-limit]').forEach(group=>{
  const limit = parseInt(group.getAttribute('data-limit'),10)||3;
  const boxes = Array.from(group.querySelectorAll('input[type="checkbox"]'));
  function enforce(){
    const picked = boxes.filter(b=>b.checked);
    const disable = picked.length >= limit;
    boxes.forEach(b=>{ if(!b.checked) b.disabled = disable; });
  }
  boxes.forEach(b=> b.addEventListener('change', enforce));
});

{ 
// ---------- new: exclusive "none"/"no" option handler ----------
// For any .opts group that contains a checkbox with value "none" (case-insensitive),
// ensure selecting "none" clears/disables other options and selecting any other option
// clears/disables the "none" option. This plays nicely with the existing limit helper
// because we dispatch change events so previously attached listeners run.
document.querySelectorAll('.opts').forEach(group=>{
  const boxes = Array.from(group.querySelectorAll('input[type="checkbox"]'));
  if(!boxes.length) return;
  const noneBox = boxes.find(b => String(b.value).toLowerCase() === 'none' || String(b.value).toLowerCase() === 'no');
  if(!noneBox) return;

  function applyNoneState(){
    if(noneBox.checked){
      boxes.forEach(b=>{
        if(b !== noneBox){
          if(b.checked){ b.checked = false; }
          b.disabled = true;
          b.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    } else {
      boxes.forEach(b=>{ if(b !== noneBox) b.disabled = false; });
    }
  }

  function applyOthersState(){
    const anyOther = boxes.some(b => b !== noneBox && b.checked);
    if(anyOther){
      if(noneBox.checked){ noneBox.checked = false; }
      if(!noneBox.disabled){ noneBox.disabled = true; }
      noneBox.dispatchEvent(new Event('change', { bubbles: true }));
    } else {
      noneBox.disabled = false;
    }
  }

  noneBox.addEventListener('change', ()=> { applyNoneState(); });
  boxes.forEach(b=>{
    if(b === noneBox) return;
    b.addEventListener('change', ()=> { applyOthersState(); });
  });

  // initialize state on load
  applyOthersState();
  applyNoneState();
});
}

/* ---------- model: categories & content ---------- */
const CATS = {
  dev:{label:'Software & Web Development',roles:['Software Developer','Full-stack Developer','Back-end Developer','Mobile App Developer'],learn:['Programming fundamentals (OOP, DSA)','Web frameworks & APIs','Version control & testing'],certs:['AWS Cloud Practitioner','Microsoft AZ-900','Oracle Java','GitHub Foundations'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  mobile:{label:'Mobile & Enterprise Apps',roles:['Android/iOS Developer','Enterprise App Developer','Integration Engineer'],learn:['Mobile frameworks (Flutter/React Native)','REST/GraphQL APIs','CI/CD basics'],certs:['Google Associate Android Dev','Apple App Dev (Swift)','Scrum Fundamentals'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  uiux:{label:'UI/UX & Front-end',roles:['UI/UX Designer','Front-end Developer','Product Designer'],learn:['HCI & accessibility','Prototyping & design systems','HTML/CSS/JS fundamentals'],certs:['Google UX Certificate','Adobe ACA','freeCodeCamp Responsive Web'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Web Science']},
  game:{label:'Game Dev & Multimedia',roles:['Game Developer','Technical Artist','Interactive Media Dev'],learn:['Game engines (Unity/Unreal)','2D/3D assets & animation','Gameplay programming'],certs:['Unity User/Associate','Autodesk/Adobe badges'],programs:['BSIT - Game Development']},
  data:{label:'Data & Databases',roles:['Data Analyst','BI Developer','Database Administrator'],learn:['SQL & data modeling','Python for data/ETL','Dashboards & storytelling'],certs:['Google Data Analytics','Microsoft DP-900','Oracle Database Foundations'],programs:['BSCS - Data Science','BSIS - Business Analytics']},
  sec:{label:'Cybersecurity & Networks',roles:['Security Analyst','Network Admin','SOC Tier 1'],learn:['Networking & OS','Threats, vuln scanning','Hardening & incident basics'],certs:['CompTIA Security+','Cisco CCNA','(ISC)² CC'],programs:['BSIT - Network Infrastructure & Data Security']},
  sys:{label:'Systems, Cloud & DevOps',roles:['Systems Admin','DevOps Tech','Cloud Support Associate'],learn:['Linux/Windows admin','Scripting & automation','Containers & cloud basics'],certs:['AWS Cloud Practitioner','Linux Essentials','Docker/CKA (later)'],programs:['BSIT - Network Infrastructure & Data Security','BSCS - Software Engineering']},
  ai:{label:'AI & Machine Learning',roles:['ML Engineer (entry)','Data Scientist (jr)','Research Assistant'],learn:['Linear algebra & stats','ML workflows & tooling','Responsible AI basics'],certs:['Google ML/AI courses','Microsoft AI-900'],programs:['BSCS - Data Science','BSCS - Computer Vision','Dual Degree - CS & Engineering']},
  cloud:{label:'Cloud, Blockchain & Emerging Tech',roles:['Cloud Practitioner','Blockchain Dev (jr)','Tech Innovator'],learn:['Cloud services & IaC','Distributed ledgers (intro)','APIs & integrations'],certs:['AWS/Microsoft Fundamentals','Blockchain foundations'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  pm:{label:'IT Project & Business Analysis',roles:['IT Project Coordinator','Business Analyst (jr)','Product Ops'],learn:['Project lifecycles & Agile','Requirements & documentation','Stakeholder comms'],certs:['Scrum Master (PSM I)','CAPM','Agile Fundamentals'],programs:['BSIS - Business Analytics','Dual Degree - CS & Business Administration']}
};

/* ---------- labels (for rationale) ---------- */
const LABELS = {
  q1:{dev:'Software or web development',game:'Game design and animation',sec:'Cybersecurity and network defense',uiux:'User interface and user experience (UI/UX) design',data:'Data analytics and databases',mobile:'Mobile or enterprise application development',sys:'Systems administration and IT infrastructure',ai:'Artificial intelligence or machine learning',cloud:'Blockchain, cloud, or emerging technologies'},
  q2:{uiux:'Designing interactive systems or mobile apps',dev:'Building programs and writing code',sec:'Managing networks or securing systems',data:'Analyzing trends or large data sets',sys:'Working with hardware or virtual machines',pm:'Leading technical projects and teams',game:'Developing games or digital environments'},
  q3:{high:'Very confident – I enjoy debugging and solving issues',mid:'Somewhat confident – I like following logic and solving puzzles',low:'Not confident – I prefer creative or design-oriented work'},
  q4:{dev:'Coding or software development',uiux:'Creating websites or mobile apps',game:'Drawing, animation, or game design',sec:'Setting up networks or securing systems',logic:'Solving logic or math-based problems',data:'Working with data or making reports',pm:'Organizing group work and managing tasks'},
  q5:{dev:'Writing the code or logic',uiux:'Designing how the app or interface looks',data:'Making sure data is accurate and organized',sys:'Setting up or troubleshooting the tech',pm:'Planning the timeline or managing the team'},
  q6:{dev:'In a software or web development company',game:'In a game studio or multimedia design firm',sec:'In a cybersecurity or IT security role',startup:'In a tech startup or freelancing business',data:'In a data-driven company or analytics team',sys:'In a network administration or systems support team',pm:'In a project management or IT consulting role',ai:'In a research lab focused on AI or emerging tech'},
  q7:{dev:'Software or application developer',game:'Game developer or multimedia artist',sec:'Network administrator or security analyst',uiux:'UI/UX designer or front-end developer',data:'Data analyst or database administrator',ai:'AI or machine learning specialist',sys:'Systems engineer or DevOps technician',pm:'IT project manager or business analyst'},
  q8:{intern:'Internship/immersion',freelance:'Freelance/self-initiated coding',school:'School-based tech projects/hackathons',none:'No experience yet'},
  q9:{prog:'Programming/web dev credential',sec:'Cybersecurity/networking credential',uiux:'UI/UX/graphic design credential',dbcloud:'Database/cloud credential',game:'Game development credential',pm:'Project/business management credential',none:'No credential yet'},
  q10:{dev:'Writing code or solving algorithm problems',uiux:'Creating designs, wireframes, or animations',sec:'Setting up networks or troubleshooting systems',data:'Analyzing and interpreting data',pm:'Planning tasks or leading a group project',game:'Creating games or interactive apps',undecided:'Undecided'}
};

/* ========== STRICT EMAIL VALIDATION (NEW) ========== */
/**
 * Validates email addresses with strict rules:
 * - Only allows @gmail.com, @outlook.com, @adamson.edu.ph
 * - Local part (before @) must be 3-64 characters
 * - Must contain letters and/or numbers (not just symbols)
 * - Cannot be simple patterns like "hi@", "123@", "test@"
 * - Follows RFC 5322 standards
 */
function validateEmailStrict(email) {
  const errors = [];
  
  // 1. Basic format check
  if (!email || typeof email !== 'string') {
    return { valid: false, error: 'Email is required' };
  }
  
  email = email.trim().toLowerCase();
  
  // 2. Check basic email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return { valid: false, error: 'Invalid email format' };
  }
  
  // 3. Split into local and domain parts
  const parts = email.split('@');
  if (parts.length !== 2) {
    return { valid: false, error: 'Invalid email format' };
  }
  
  const [localPart, domain] = parts;
  
  // 4. Check allowed domains (whitelist)
  const allowedDomains = ['gmail.com', 'outlook.com', 'adamson.edu.ph'];
  if (!allowedDomains.includes(domain)) {
    return { 
      valid: false, 
      error: `Only ${allowedDomains.join(', ')} emails are accepted. "${domain}" is not allowed.` 
    };
  }
  
  // 5. Local part length check (3-64 characters)
  if (localPart.length < 3) {
    return { 
      valid: false, 
      error: 'Email username is too short (minimum 3 characters before @)' 
    };
  }
  
  if (localPart.length > 64) {
    return { 
      valid: false, 
      error: 'Email username is too long (maximum 64 characters before @)' 
    };
  }
  
  // 6. Local part must contain at least one letter
  if (!/[a-z]/i.test(localPart)) {
    return { 
      valid: false, 
      error: 'Email username must contain at least one letter (a-z)' 
    };
  }
  
  // 7. Check for valid characters (alphanumeric, dots, hyphens, underscores)
  const validLocalRegex = /^[a-z0-9._-]+$/i;
  if (!validLocalRegex.test(localPart)) {
    return { 
      valid: false, 
      error: 'Email username contains invalid characters. Only letters, numbers, dots (.), hyphens (-), and underscores (_) are allowed.' 
    };
  }
  
  // 8. Cannot start or end with dot, hyphen, or underscore
  if (/^[._-]|[._-]$/.test(localPart)) {
    return { 
      valid: false, 
      error: 'Email username cannot start or end with a dot, hyphen, or underscore' 
    };
  }
  
  // 9. Cannot have consecutive dots
  if (/\.\./.test(localPart)) {
    return { 
      valid: false, 
      error: 'Email username cannot contain consecutive dots (..)' 
    };
  }
  
  // 10. Block common throwaway/test patterns
  const suspiciousPatterns = [
    /^test\d*$/i,           // test, test1, test123
    /^temp\d*$/i,           // temp, temp1, temp123
    /^fake\d*$/i,           // fake, fake1, fake123
    /^user\d*$/i,           // user, user1, user123
    /^admin\d*$/i,          // admin, admin1
    /^sample\d*$/i,         // sample, sample1
    /^demo\d*$/i,           // demo, demo1
    /^abc\d*$/i,            // abc, abc123
    /^xyz\d*$/i,            // xyz, xyz123
    /^asdf\d*$/i,           // asdf, asdf123
    /^qwerty\d*$/i,         // qwerty, qwerty123
    /^\d+$/,                // pure numbers: 123, 12345
    /^[a-z]{1,2}$/i,        // single/double letters: a, hi, ab
    /^noreply$/i,           // noreply
    /^no[-_]?reply$/i,      // no-reply, no_reply
  ];
  
  for (const pattern of suspiciousPatterns) {
    if (pattern.test(localPart)) {
      return { 
        valid: false, 
        error: 'This email appears to be a test or temporary address. Please use your real email address.' 
      };
    }
  }
  
  // 11. Must have reasonable mix of characters (not all numbers or all same letter)
  // Check if it's all the same character repeated
  if (/^(.)\1+$/.test(localPart)) {
    return { 
      valid: false, 
      error: 'Email username cannot be the same character repeated (e.g., "aaa@gmail.com")' 
    };
  }
  
  // 12. For adamson.edu.ph, enforce institutional format (optional but recommended)
  if (domain === 'adamson.edu.ph') {
    // Typically institutional emails follow patterns like: firstname.lastname or student.id
    // This is optional - remove if not needed
    if (localPart.length < 4) {
      return {
        valid: false,
        error: 'Adamson email addresses must have at least 4 characters before @'
      };
    }
  }
  
  // All checks passed!
  return { valid: true, email: email };
}

/* ---------- scoring with rationale/trace ---------- */
function baseScores(){ return {dev:0,mobile:0,uiux:0,game:0,data:0,sec:0,sys:0,ai:0,cloud:0,pm:0}; }
function baseTrace(){ return {dev:[],mobile:[],uiux:[],game:[],data:[],sec:[],sys:[],ai:[],cloud:[],pm:[]}; }

function add(scores, key, w, trace, reason){
  if(key in scores){ scores[key]+=w; if(trace && reason){ trace[key].push({w, reason}); } }
}
function addAll(scores, map, trace, source){
  Object.entries(map).forEach(([k,v])=>{
    add(scores, k, v, trace, `${source} → +${v} ${CATS[k]?.label||k}`);
  });
}

function scoreFromForm(fd){
  const s = baseScores();
  const t = baseTrace();

  (fd.getAll('q1[]')||[]).forEach(v=>{
    add(s, v, 3, t, `Q1 interest: “${LABELS.q1[v]}”`);
    if(v==='mobile') add(s,'dev',1,t,`Q1 related: mobile supports development`);
    if(v==='cloud') add(s,'sys',1,t,`Q1 related: cloud links to systems/infrastructure`);
  });

  const q2 = fd.get('q2');
  if(q2){ add(s, q2, 2, t, `Q2 task preference: “${LABELS.q2[q2]}”`); }

  const q3 = fd.get('q3');
  if(q3==='high') addAll(s,{dev:2,data:1,sec:1,sys:1,ai:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);
  else if(q3==='mid') addAll(s,{dev:1,data:1,sys:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);
  else if(q3==='low') addAll(s,{uiux:2,pm:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);

  (fd.getAll('q4[]')||[]).forEach(v=>{
    if(v==='logic'){ addAll(s,{dev:1,data:1,ai:1,sec:1}, t, `Q4 subject: “${LABELS.q4[v]}”`); }
    else { add(s, v==='prog' ? 'dev' : v, 2, t, `Q4 subject: “${LABELS.q4[v]}”`); }
  });

  const q5 = fd.get('q5'); if(q5) add(s,q5,2,t,`Q5 project role: “${LABELS.q5[q5]}”`);

  const q6 = fd.get('q6');
  if(q6==='startup'){ addAll(s,{dev:1,uiux:1,cloud:1,pm:1}, t, `Q6 workplace: “${LABELS.q6[q6]}”`); }
  else if(q6){ add(s,q6,2,t,`Q6 workplace: “${LABELS.q6[q6]}”`); }

  (fd.getAll('q7[]')||[]).forEach(v=> add(s,v,3,t,`Q7 career interest: “${LABELS.q7[v]}”`));

  (fd.getAll('q8[]')||[]).forEach(v=>{
    if(v==='intern') addAll(s,{dev:1,uiux:1,data:1,sec:1,sys:1,cloud:1,pm:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
    if(v==='freelance') addAll(s,{dev:2,uiux:1,mobile:1,game:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
    if(v==='school') addAll(s,{dev:1,pm:1,data:1,sec:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
  });

  (fd.getAll('q9[]')||[]).forEach(v=>{
    if(v==='prog') addAll(s,{dev:2,uiux:1,mobile:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='sec') addAll(s,{sec:2,sys:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='uiux') addAll(s,{uiux:2,game:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='dbcloud') addAll(s,{data:2,cloud:1,sys:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='game') addAll(s,{game:2,dev:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='pm') addAll(s,{pm:2}, t, `Q9 credential: ${LABELS.q9[v]}`);
  });

  const q10 = fd.get('q10');
  if(q10 && q10!=='undecided') add(s,q10,2,t,`Q10 natural activity: “${LABELS.q10[q10]}”`);
  if(q10==='undecided') addAll(s,{pm:1,uiux:1,dev:1}, t, `Q10: undecided → broad starters`);

  return {scores:s, trace:t};
}

/* ---------- stepper logic ---------- */
const form = document.getElementById('cpForm');
const steps = Array.from(document.querySelectorAll('.step'));
const total = steps.length;
let current = 0;

const btnPrev   = document.getElementById('btnPrev');
const btnNext   = document.getElementById('btnNext');
const btnSubmit = document.getElementById('btnSubmit');
const btnReset  = document.getElementById('btnReset');

const progressText = document.getElementById('progressText');
const progressFill = document.getElementById('progressFill');

/* Consent inputs */
const emailInput = document.getElementById('emailInput');
const agreeConsent = document.getElementById('agreeConsent');
const consentHelp = document.getElementById('consentHelp');
const attemptInfo = document.getElementById('attemptInfo');

function updateProgress(){
  const n = current + 1;
  progressText.textContent = `Question ${n} of ${total}`;
  const pct = Math.max(6, Math.round((n-1)/(total-1)*100));
  progressFill.style.width = pct + '%';
}

function showStep(i){
  steps.forEach((s, idx)=> s.classList.toggle('is-active', idx===i));
  current = i;
  btnPrev.disabled = (current === 0);
  const last = (current === total - 1);
  btnNext.style.display   = last ? 'none' : '';
  btnSubmit.style.display = last ? '' : 'none';
  enforceStepRequirement();
  updateProgress();
  // focus first control for accessibility
  const focusable = steps[current].querySelector('input,button,select,textarea');
  if (focusable) setTimeout(()=>focusable.focus(), 50);
}

function stepHasRequirement(stepEl){
  return stepEl.hasAttribute('data-requires');
}
function isValidConsent(){
  const email = (emailInput && emailInput.value || '').trim();
  const agree = (agreeConsent && agreeConsent.checked);
  
  // Validate email with strict rules
  const validation = validateEmailStrict(email);
  
  if (!validation.valid) {
    // Customize domain error message
    let errorMessage = validation.error;
    
    if (validation.error.includes('Only') && validation.error.includes('not allowed')) {
      errorMessage = 'Please provide a valid gmail.com, outlook.com or adamson.edu.ph address.';
    }
    
    consentHelp.textContent = errorMessage;
    consentHelp.style.color = '#dc2626';
    return false;
  }
  
  if (!agree) {
    consentHelp.textContent = 'You must agree to the DPA/policy to proceed.';
    consentHelp.style.color = '#dc2626';
    return false;
  }
  
  // Check attempts (with expiry)
  const rec = getAttemptRecord(email);
  const attempts = rec.count;
  
  if (attempts >= 3) {
    consentHelp.textContent = 'You have reached the maximum of 3 attempts for this email. Attempts will reset after 3 days from your first attempt.';
    consentHelp.style.color = '#dc2626';
    return false;
  }
  
  // Success!
  consentHelp.textContent = '✓ Email validated successfully. You may proceed.';
  consentHelp.style.color = '#16a34a';
  
  // Show attempt info
  const left = Math.max(0, 3 - attempts);
  attemptInfo.style.display = 'block';
  attemptInfo.textContent = `Attempts remaining for ${email}: ${left} of 3. Attempts reset automatically after 3 days.`;
  
  return true;
}


function stepIsSatisfied(stepEl){
  if (!stepHasRequirement(stepEl)) return true;
  const type = stepEl.getAttribute('data-requires');
  if (type === 'radio'){
    const radios = stepEl.querySelectorAll('input[type="radio"]');
    return Array.from(radios).some(r => r.checked);
  }
  if (type === 'consent'){
    return isValidConsent();
  }
  return true;
}
function enforceStepRequirement(){
  const ok = stepIsSatisfied(steps[current]);
  btnNext.disabled = !ok && (current < total - 1);
  btnSubmit.disabled = !ok && (current === total - 1);
}

/* ========== REAL-TIME EMAIL VALIDATION FEEDBACK (NEW) ========== */
if (emailInput) {
  emailInput.addEventListener('input', function() {
    // Clear previous styling
    emailInput.style.borderColor = '';
    
    const email = this.value.trim();
    
    // Don't validate until user has typed at least 3 characters
    if (email.length < 3) {
      consentHelp.textContent = 'You must provide an accepted email and agree to proceed.';
      consentHelp.style.color = '#6b7280';
      enforceStepRequirement();
      return;
    }
    
    const validation = validateEmailStrict(email);
    
    if (!validation.valid) {
      consentHelp.textContent = validation.error;
      consentHelp.style.color = '#dc2626';
      emailInput.style.borderColor = '#dc2626';
    } else {
      consentHelp.textContent = '✓ Valid email format';
      consentHelp.style.color = '#16a34a';
      emailInput.style.borderColor = '#16a34a';
    }
    
    // Trigger requirement check
    enforceStepRequirement();
  });
  
  // Also validate on blur (when user leaves the field)
  emailInput.addEventListener('blur', function() {
    const email = this.value.trim();
    if (email.length > 0) {
      const validation = validateEmailStrict(email);
      if (!validation.valid) {
        consentHelp.textContent = validation.error;
        consentHelp.style.color = '#dc2626';
        emailInput.style.borderColor = '#dc2626';
      }
    }
  });
}

if(agreeConsent) agreeConsent.addEventListener('change', ()=>{ enforceStepRequirement(); });

steps.forEach(s=> s.addEventListener('change', enforceStepRequirement));

btnPrev.addEventListener('click', ()=> showStep(Math.max(0, current-1)));
btnNext.addEventListener('click', ()=>{
  if (!stepIsSatisfied(steps[current])) { enforceStepRequirement(); return; }
  showStep(Math.min(total-1, current+1));
});
btnReset.addEventListener('click', ()=>{
  form.reset();
  document.querySelectorAll('.opts[data-limit] input[type="checkbox"]').forEach(b=> b.disabled=false);
  // clear consent UI
  attemptInfo.style.display = 'none';
  consentHelp.textContent = 'You must provide an accepted email and agree to proceed.';
  showStep(0);
});

form.addEventListener('keydown', (e)=>{
  if (e.key === 'Enter'){
    const isText = ['TEXTAREA','INPUT'].includes(e.target.tagName) && e.target.type==='text';
    if (isText) return;
    e.preventDefault();
    if (current < total-1) btnNext.click();
  }
});

showStep(0);

/* ---------- attempts tracking helpers (localStorage) ---------- */
// new: store record {count, last} so we can expire attempts after 3 days
function attemptsKey(email){ return `cp_attempts_${(email||'').toLowerCase()}`; }

function getAttemptRecord(email){
  if(!email) return {count:0, last:null};
  try{
    const raw = localStorage.getItem(attemptsKey(email));
    if(!raw) return {count:0, last:null};
    const obj = JSON.parse(raw);
    if(!obj || typeof obj !== 'object') return {count:0, last:null};
    const last = obj.last ? new Date(obj.last) : null;
    if(last){
      const ageMs = Date.now() - last.getTime();
      const threeDaysMs = 3 * 24 * 60 * 60 * 1000;
      if(ageMs > threeDaysMs){
        // expired: clear and treat as zero attempts
        localStorage.removeItem(attemptsKey(email));
        return {count:0, last:null};
      }
    }
    return {count: parseInt(obj.count||0,10) || 0, last: obj.last || null};
  }catch(e){
    return {count:0, last:null};
  }
}

function getAttempts(email){ return getAttemptRecord(email).count; }

function setAttemptRecord(email, count, isoDate){
  if(!email) return;
  const rec = {count: count||0, last: isoDate || (new Date()).toISOString()};
  localStorage.setItem(attemptsKey(email), JSON.stringify(rec));
}

function incAttempts(email){
  if(!email) return;
  const rec = getAttemptRecord(email);
  const next = (rec.count || 0) + 1;
  setAttemptRecord(email, next, (new Date()).toISOString());
}

/* ---------- FORM SUBMIT HANDLER ---------- */
form.addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(form);

  // STRICT EMAIL VALIDATION (NEW)
  const email = (fd.get('email') || '').trim();
  const validation = validateEmailStrict(email);
  
  if (!validation.valid) {
    alert('Please provide a valid gmail.com or adamson.edu.ph email before submitting.\n\n' + validation.error);
    showStep(0); // Go back to consent page
    if (emailInput) {
      emailInput.focus();
      emailInput.style.borderColor = '#dc2626';
    }
    return;
  }

  // Check consent
  if (!fd.get('agree')) {
    alert('You must agree to the DPA/policy before submitting.');
    showStep(0);
    return;
  }

  // Check attempts
  const rec = getAttemptRecord(email);
  const attempts = rec.count;
  if (attempts >= 3) {
    alert('This email has already used up 3 attempts. Attempts will reset after 3 days from your earlier submissions.');
    showStep(0);
    return;
  }

  // Check for responses
  const anySeed = (fd.getAll('q1[]').length + fd.getAll('q4[]').length + fd.getAll('q7[]').length) > 0;
  if (!anySeed) {
    alert('Please select at least one interest (Q1, Q4 or Q7) so we can tailor your results.');
    showStep(1);
    return;
  }

  // Calculate scores
  const { scores, trace } = scoreFromForm(fd);

  // Show loading state
  btnSubmit.disabled = true;
  btnSubmit.textContent = 'Sending email...';
  console.log('Starting email send process...');

  // Send email
  let mailOk = false;
  let errorDetail = null;

  try {
    // Build the endpoint URL (same page, POST request)
    const formData = new URLSearchParams({
      action: 'send_email',
      email: email,
      results: JSON.stringify(scores),
      trace: JSON.stringify(trace)
    });

    console.log('Sending to:', window.location.href);
    console.log('Email:', email);

    const resp = await fetch(window.location.href, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      body: formData.toString()
    });

    console.log('Response status:', resp.status);
    const responseText = await resp.text();
    console.log('Response text:', responseText);

    // Try to parse JSON
    let j;
    try {
      j = JSON.parse(responseText);
      console.log('Parsed response:', j);
    } catch (parseErr) {
      console.error('Failed to parse JSON:', parseErr);
      console.log('Raw response (first 500 chars):', responseText.substring(0, 500));
      j = { ok: false, error: 'invalid_response', detail: 'Server returned non-JSON response' };
    }

    mailOk = !!j.ok;

    if (!mailOk) {
      errorDetail = j;
      console.warn('Email failed:', j);
    } else {
      console.log('✅ Email sent successfully!');
    }

  } catch (err) {
    console.error('Network error:', err);
    errorDetail = { error: 'network_error', message: err.message };
  }

  // Reset button
  btnSubmit.disabled = false;
  btnSubmit.textContent = 'Generate Pathway';

  // Increment attempts
  incAttempts(email);

  // Store results
  sessionStorage.setItem('pathwayResults', JSON.stringify(scores));
  sessionStorage.setItem('pathwayTrace', JSON.stringify(trace));
  sessionStorage.setItem('pathwayEmail', email);

  if (/@adamson\.edu\.ph$/i.test(email)) {
    sessionStorage.setItem('pathwayNotifySemester', '1');
  } else {
    sessionStorage.removeItem('pathwayNotifySemester');
  }

  // Handle result
  if (!mailOk) {
    console.error('Email sending failed. Error details:', errorDetail);
    
    // Show detailed error in development
    let errorMsg = 'We could not send the results email at this time.\n\n';
    if (errorDetail) {
      errorMsg += 'Error: ' + (errorDetail.error || 'unknown') + '\n';
      if (errorDetail.message) errorMsg += 'Message: ' + errorDetail.message + '\n';
      if (errorDetail.detail) errorMsg += 'Detail: ' + errorDetail.detail + '\n';
    }
    errorMsg += '\nYour results are still saved and available on the next page.';
    errorMsg += '\n\nWould you like to continue to see your results?';

    if (confirm(errorMsg)) {
      window.location.href = '/adamson-ccit/public/index.php?page=career_pathway_results';
    }
  } else {
    // Success!
    alert('✅ Results sent to ' + email + '!\n\nCheck your inbox (and spam folder). Redirecting to results page...');
    window.location.href = '/adamson-ccit/public/index.php?page=career_pathway_results';
  }
});
</script>

</body>
</html>