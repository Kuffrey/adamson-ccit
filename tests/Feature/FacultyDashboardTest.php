<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

// Mock Auth class for testing
class Auth
{
    public static $mockUser = null;
    public static $requiredRoles = [];
    public static $redirectUrl = '';

    public static function requireRole($roles, $redirect)
    {
        self::$requiredRoles = $roles;
        self::$redirectUrl = $redirect;
        // Simulate role check: if no user or wrong role, exit or redirect
        if (!self::$mockUser || !in_array(self::$mockUser['role'], $roles)) {
            // For testing, just output a message instead of exit/redirect
            echo "Access Denied";
        }
    }

    public static function user()
    {
        return self::$mockUser;
    }
}

class FacultyDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        Auth::$mockUser = null;
        $_SESSION = [];
        // Alias our mock Auth to global namespace for the view
        if (!class_exists('\\Auth', false)) {
            class_alias(__NAMESPACE__ . '\\Auth', '\\Auth');
        }
    }

    protected function includeDashboardView()
    {
        $viewFile = file_get_contents(__DIR__ . '/../../app/views/faculty/faculty_dashboard.php');
        // Remove or comment out the require_once line
        $viewFile = preg_replace('/require_once\s+__DIR__\s*\.\s*\'\/\.\.\/\.\.\/lib\/Auth\.php\'\s*;/', '// require_once Auth.php;', $viewFile);
        // Correct regex to replace sidebar include with harmless stub
        $viewFile = preg_replace(
            '/<\?php\s+include\s+__DIR__\s*\.\s*\'\/_faculty_sidebar\.php\';\s*\?>/',
            '<div id="sidebar-stub"></div>',
            $viewFile
        );
        // Save to temp file and include
        $tmp = tempnam(sys_get_temp_dir(), 'faculty_dashboard');
        file_put_contents($tmp, $viewFile);
        include $tmp;
        unlink($tmp);
    }

    public function testDashboardRequiresFacultyRole()
    {
        Auth::$mockUser = null;
        ob_start();
        $this->includeDashboardView();
        $output = ob_get_clean();

        $this->assertStringContainsString('Access Denied', $output);
    }

    public function testDashboardDisplaysWelcomeMessage()
    {
        Auth::$mockUser = [
            'username' => 'jdoe',
            'role' => 'faculty'
        ];
        $_SESSION['user'] = Auth::$mockUser;

        ob_start();
        $this->includeDashboardView();
        $output = ob_get_clean();

        $this->assertStringContainsString('Welcome,', $output);
        $this->assertStringContainsString('Faculty Dashboard', $output);
        $this->assertStringContainsString('Submit Research', $output);
        $this->assertStringContainsString('Submit Certifications', $output);
        $this->assertStringContainsString('Submit News', $output);
        $this->assertStringContainsString('Portfolio', $output);
    }

    public function testDashboardLinksArePresent()
    {
        Auth::$mockUser = [
            'username' => 'jdoe',
            'role' => 'faculty'
        ];
        $_SESSION['user'] = Auth::$mockUser;

        ob_start();
        $this->includeDashboardView();
        $output = ob_get_clean();

        $this->assertStringContainsString('?page=faculty_manage_research', $output);
        $this->assertStringContainsString('?page=faculty_manage_certifications', $output);
        $this->assertStringContainsString('?page=faculty_manage_news', $output);
        $this->assertStringContainsString('?page=faculty_portfolio', $output);
    }

    public function testSidebarLinksRedirectToCorrectPages()
    {
        Auth::$mockUser = [
            'username' => 'jdoe',
            'role' => 'faculty'
        ];
        $_SESSION['user'] = Auth::$mockUser;

        ob_start();
        // Include the sidebar directly
        $viewFile = file_get_contents(__DIR__ . '/../../app/views/faculty/_faculty_sidebar.php');
        $tmp = tempnam(sys_get_temp_dir(), 'faculty_sidebar');
        file_put_contents($tmp, $viewFile);
        include $tmp;
        unlink($tmp);
        $output = ob_get_clean();

        $this->assertStringContainsString('?page=faculty_dashboard', $output);
        $this->assertStringContainsString('?page=faculty_manage_news', $output);
        $this->assertStringContainsString('?page=faculty_manage_research', $output);
        $this->assertStringContainsString('?page=faculty_manage_certifications', $output);
        $this->assertStringContainsString('?page=faculty_portfolio', $output);
    }
}
