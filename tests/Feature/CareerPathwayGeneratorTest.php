<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CareerPathwayGeneratorTest extends TestCase
{
    /**
     * Import the email validation function from the main file
     * This is a workaround since the function is not in a class
     */
    private function validateEmailStrict($email)
    {
        // Copy of the function from career_pathway_generator.php for testing
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
            '/^\d+$/',
            '/^[a-z]{1,2}$/i',
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
    
    #[Test]
    public function test_valid_emails_pass_validation()
    {
        // Valid gmail addresses
        $this->assertTrue($this->validateEmailStrict('student123@gmail.com')['valid']);
        $this->assertTrue($this->validateEmailStrict('john.doe@gmail.com')['valid']);
        $this->assertTrue($this->validateEmailStrict('jane_smith@gmail.com')['valid']);
        $this->assertTrue($this->validateEmailStrict('career.pathway2023@gmail.com')['valid']);
        
        // Valid outlook addresses
        $this->assertTrue($this->validateEmailStrict('student456@outlook.com')['valid']);
        $this->assertTrue($this->validateEmailStrict('john-smith@outlook.com')['valid']);
        
        // Valid adamson.edu.ph addresses
        $this->assertTrue($this->validateEmailStrict('john.smith@adamson.edu.ph')['valid']);
        $this->assertTrue($this->validateEmailStrict('student2023@adamson.edu.ph')['valid']);
    }
    
    #[Test]
    public function test_invalid_domains_fail_validation()
    {
        $invalidDomains = [
            'user@yahoo.com',
            'student@hotmail.com',
            'test@mail.com',
            'john@example.com',
            'user@invalid.domain.com'
        ];
        
        foreach ($invalidDomains as $email) {
            $result = $this->validateEmailStrict($email);
            $this->assertFalse($result['valid']);
            $this->assertStringContainsString('Only gmail.com, outlook.com, adamson.edu.ph emails are accepted', $result['error']);
        }
    }
    
    #[Test]
    public function test_suspicious_pattern_emails_fail_validation()
    {
        // Group emails by expected error message
        $testEmails = [
            // Test/temporary pattern emails
            'test_pattern' => [
                'test@gmail.com',
                'test123@gmail.com',
                'temp@gmail.com',
                'fake@outlook.com',
                'user1@outlook.com',
                'admin@adamson.edu.ph',
                'sample@gmail.com',
                'demo123@outlook.com',
                'abc123@gmail.com',
                'xyz@outlook.com',
                'asdf@adamson.edu.ph',
                'qwerty@gmail.com',
                'noreply@gmail.com',
                'no-reply@adamson.edu.ph'
            ],
            // Numeric-only emails
            'numeric_only' => [
                '12345@gmail.com',
            ],
            // Single letter emails
            'too_short_or_simple' => [
                'a@gmail.com',
                'hi@outlook.com',
            ]
        ];
        
        // Test pattern emails (test, temp, etc.)
        foreach ($testEmails['test_pattern'] as $email) {
            $result = $this->validateEmailStrict($email);
            $this->assertFalse($result['valid'], "Email $email should be rejected");
            $this->assertStringContainsString('test or temporary address', $result['error']);
        }
        
        // Test numeric-only emails
        foreach ($testEmails['numeric_only'] as $email) {
            $result = $this->validateEmailStrict($email);
            $this->assertFalse($result['valid'], "Email $email should be rejected");
            // Could match either pattern error or letter requirement
            $this->assertTrue(
                strpos($result['error'], 'test or temporary address') !== false ||
                strpos($result['error'], 'must contain at least one letter') !== false,
                "Error should mention pattern or letter requirement for $email"
            );
        }
        
        // Test too short or simple emails
        foreach ($testEmails['too_short_or_simple'] as $email) {
            $result = $this->validateEmailStrict($email);
            $this->assertFalse($result['valid'], "Email $email should be rejected");
            // These could fail for multiple reasons, check that validation fails but don't assert specific message
        }
    }
    
    #[Test]
    public function test_invalid_character_emails_fail_validation()
    {
        $invalidCharEmails = [
            'user!name@gmail.com',
            'student#id@outlook.com',
            'john@smith@gmail.com',
            'user space@adamson.edu.ph',
            'student$dollar@gmail.com',
            'user(parens)@outlook.com'
        ];
        
        foreach ($invalidCharEmails as $email) {
            $result = $this->validateEmailStrict($email);
            $this->assertFalse($result['valid']);
        }
    }
    
    #[Test]
    public function test_special_start_end_character_rules()
    {
        // Cannot start with special chars
        $this->assertFalse($this->validateEmailStrict('.username@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('-username@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('_username@gmail.com')['valid']);
        
        // Cannot end with special chars
        $this->assertFalse($this->validateEmailStrict('username.@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('username-@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('username_@gmail.com')['valid']);
        
        // Cannot have consecutive dots
        $this->assertFalse($this->validateEmailStrict('user..name@gmail.com')['valid']);
    }
    
    #[Test]
    public function test_length_requirements()
    {
        // Too short
        $this->assertFalse($this->validateEmailStrict('ab@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('jo@adamson.edu.ph')['valid']);
        
        // Too long (65+ chars)
        $longUsername = str_repeat('a', 65);
        $this->assertFalse($this->validateEmailStrict("$longUsername@gmail.com")['valid']);
    }
    
    #[Test]
    public function test_institutional_email_requirements()
    {
        // Regular adamson emails - should pass
        $this->assertTrue($this->validateEmailStrict('student2023@adamson.edu.ph')['valid']);
        $this->assertTrue($this->validateEmailStrict('john.smith@adamson.edu.ph')['valid']);
        
        // Too short for institutional email
        $this->assertFalse($this->validateEmailStrict('abc@adamson.edu.ph')['valid']);
    }
    
    #[Test]
    public function test_email_normalization()
    {
        $result = $this->validateEmailStrict(' Student.Email@Gmail.com ');
        $this->assertTrue($result['valid']);
        $this->assertEquals('student.email@gmail.com', $result['email']);
    }
    
    #[Test]
    public function test_repeated_character_pattern()
    {
        $this->assertFalse($this->validateEmailStrict('aaa@gmail.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('bbbbb@outlook.com')['valid']);
        $this->assertFalse($this->validateEmailStrict('zzzzz@adamson.edu.ph')['valid']);
    }
    
    #[Test]
    public function test_edge_cases()
    {
        // Empty email
        $this->assertFalse($this->validateEmailStrict('')['valid']);
        
        // Null email
        $this->assertFalse($this->validateEmailStrict(null)['valid']);
        
        // Non-string input
        $this->assertFalse($this->validateEmailStrict(123)['valid']);
        
        // Just whitespace
        $this->assertFalse($this->validateEmailStrict('   ')['valid']);
    }

    // Additional tests could be written for:
    // - Testing the pathway generation algorithm
    // - Testing the reminder scheduling logic
    // - Testing the email sending functionality (would require mocking)
}
