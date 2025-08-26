<?php
declare(strict_types=1);

/**
 * Minimal base model. If config/db isn’t present, DB calls will throw,
 * and callers can catch and show an empty state.
 */
abstract class Model
{
    protected static ?PDO $db = null;

    protected static function db(): PDO
    {
        if (self::$db instanceof PDO) {
            return self::$db;
        }

        // Load DB config (adjust path if your config lives elsewhere)
        $cfgFile = __DIR__ . '/../config/database.php';
        if (!is_file($cfgFile)) {
            throw new RuntimeException('DB config missing at app/config/database.php');
        }
        $cfg = require $cfgFile;

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $cfg['host'] ?? '127.0.0.1',
            $cfg['dbname'] ?? 'adamson_ccit'
        );

        self::$db = new PDO(
            $dsn,
            $cfg['user'] ?? 'root',
            $cfg['pass'] ?? '',
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return self::$db;
    }
}
