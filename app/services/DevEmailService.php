<?php
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
}