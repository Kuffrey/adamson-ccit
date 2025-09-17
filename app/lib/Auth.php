<?php
declare(strict_types=1);

final class Auth
{
    /** Ensure a session exists without double-start notices */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string
    {
        self::start();
        return $_SESSION['user']['role'] ?? null;
    }

    public static function login(array $user): void
    {
        self::start();
        // Regenerate on privilege change to mitigate fixation
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'username' => $user['username'] ?? '',
            'role'     => $user['role']     ?? '',
        ];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        // Don't remove the remember me cookies - they should persist after logout
        // if they exist, so credentials are remembered on next visit
        session_destroy();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function is(string $role): bool
    {
        return self::role() === $role;
    }

    /**
     * Require one of the roles; redirect if unauthorized.
     */
    public static function requireRole(array $roles, string $redirectUrl): void
    {
        self::start();
        $role = $_SESSION['user']['role'] ?? null;
        if (!$role || !in_array($role, $roles, true)) {
            if (!headers_sent()) { header('Location: ' . $redirectUrl); exit; }
            echo '<script>location.href=' . json_encode($redirectUrl) . ';</script>';
            exit;
        }
    }
}
