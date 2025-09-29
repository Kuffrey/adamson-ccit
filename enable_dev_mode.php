<?php
/**
 * Enable Development Mode for Password Reset Testing
 * This allows you to test password reset without needing email configuration
 */

echo "🔧 Password Reset Development Mode\n";
echo "=================================\n\n";

echo "Since email authentication is failing, let's enable development mode.\n";
echo "This will log reset URLs so you can test the functionality immediately.\n\n";

// Create a development email service
$devEmailService = '<?php
declare(strict_types=1);

/**
 * Development Email Service - Logs emails instead of sending them
 */
class DevEmailService
{
    public function sendPasswordReset(string $email, string $token, string $username = ""): bool
    {
        $resetUrl = "http://localhost/adamson-ccit/public/index.php?page=reset-password&token=" . urlencode($token);
        
        $logMessage = "\n" . str_repeat("=", 80) . "\n";
        $logMessage .= "🔔 PASSWORD RESET EMAIL (Development Mode)\n";
        $logMessage .= "To: $email\n";
        $logMessage .= "Username: $username\n";
        $logMessage .= "Reset URL: $resetUrl\n";
        $logMessage .= "Token: $token\n";
        $logMessage .= "Expires: " . date("Y-m-d H:i:s", strtotime("+1 hour")) . "\n";
        $logMessage .= str_repeat("=", 80) . "\n";
        
        // Log to file
        $logFile = __DIR__ . "/../../password_reset_urls.log";
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Also log to PHP error log
        error_log("PASSWORD RESET URL: $resetUrl");
        
        echo "📧 Password reset email logged! Check password_reset_urls.log\n";
        echo "🔗 Reset URL: $resetUrl\n";
        
        return true;
    }
    
    public function testConfiguration(): array
    {
        return [
            "success" => true,
            "message" => "Development mode enabled",
            "details" => ["Emails will be logged instead of sent"]
        ];
    }
}';

file_put_contents(__DIR__ . '/app/services/DevEmailService.php', $devEmailService);

// Update PasswordReset to use DevEmailService temporarily
$passwordResetFile = __DIR__ . '/app/models/PasswordReset.php';
$content = file_get_contents($passwordResetFile);

// Check if already using dev service
if (strpos($content, 'DevEmailService') === false) {
    // Add dev service include
    $content = str_replace(
        'require_once __DIR__ . \'/../services/EmailService.php\';',
        'require_once __DIR__ . \'/../services/EmailService.php\';\nrequire_once __DIR__ . \'/../services/DevEmailService.php\';',
        $content
    );
    
    // Replace EmailService with DevEmailService
    $content = str_replace(
        '$emailService = new EmailService();',
        '$emailService = new DevEmailService(); // Development mode - emails logged instead of sent',
        $content
    );
    
    file_put_contents($passwordResetFile, $content);
}

echo "✅ Development mode enabled!\n\n";

echo "🧪 Now test the password reset:\n";
echo "1. Go to: http://localhost/adamson-ccit/public/index.php?page=forgot-password\n";
echo "2. Enter any email address from your users table\n";
echo "3. Check the file: password_reset_urls.log for the reset URL\n";
echo "4. Copy the URL and paste it in your browser to test\n\n";

// Test it immediately
echo "Would you like to test it now? (y/n): ";
$handle = fopen("php://stdin", "r");
$response = trim(fgets($handle));
fclose($handle);

if (strtolower($response) === 'y') {
    echo "\nEnter test email address: ";
    $handle = fopen("php://stdin", "r");
    $testEmail = trim(fgets($handle));
    fclose($handle);
    
    require_once __DIR__ . '/app/models/PasswordReset.php';
    
    echo "\n🔄 Testing password reset for: $testEmail\n";
    
    $token = PasswordReset::createToken($testEmail);
    if ($token) {
        $sent = PasswordReset::sendResetEmail($testEmail, $token);
        if ($sent) {
            echo "\n✅ Password reset test successful!\n";
            echo "📁 Check password_reset_urls.log for the reset URL\n";
            
            // Show the latest log entry
            if (file_exists(__DIR__ . '/password_reset_urls.log')) {
                $logContent = file_get_contents(__DIR__ . '/password_reset_urls.log');
                $lines = explode("\n", $logContent);
                foreach ($lines as $line) {
                    if (strpos($line, 'Reset URL:') !== false) {
                        echo "🔗 " . trim($line) . "\n";
                        break;
                    }
                }
            }
        } else {
            echo "❌ Test failed\n";
        }
    } else {
        echo "❌ User not found or token creation failed\n";
    }
}

echo "\n🎉 Development mode is now active!\n";
echo "Your password reset system will log URLs instead of sending emails.\n";
echo "This lets you test the complete functionality immediately.\n\n";

echo "To re-enable real email sending later:\n";
echo "1. Fix your email configuration (Gmail or Outlook)\n";
echo "2. Edit app/models/PasswordReset.php\n";
echo "3. Change DevEmailService back to EmailService\n";
?>