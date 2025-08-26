<?php
declare(strict_types=1);

abstract class Model
{
    /** @var ?PDO */
    protected static ?PDO $pdo = null;

    /** Instance-level convenience so `$this->db` also works */
    protected PDO $db;

    public function __construct()
    {
        $this->db = self::db();
    }

    /** Shared PDO connection */
    public static function db(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // 1) Prefer config file returning ['host'=>..,'dbname'=>..,'user'=>..,'pass'=>..,'charset'=>..]
        $cfg = null;
        foreach ([
            __DIR__ . '/../config/database.php',
            __DIR__ . '/../../config/database.php',
        ] as $path) {
            if (is_file($path)) { /** @noinspection PhpIncludeInspection */
                $cfg = require $path; break;
            }
        }

        // 2) Else read environment variables
        $host    = $cfg['host']    ?? getenv('DB_HOST')    ?: '127.0.0.1';
        $dbname  = $cfg['dbname']  ?? getenv('DB_NAME')    ?: 'adamson_ccit';
        $user    = $cfg['user']    ?? getenv('DB_USER')    ?: 'root';
        $pass    = $cfg['pass']    ?? getenv('DB_PASS')    ?: '';
        $charset = $cfg['charset'] ?? getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        self::$pdo = new PDO($dsn, $user, $pass, $opt);
        return self::$pdo;
    }

    /**
     * Simple paginator that appends LIMIT/OFFSET
     * Returns the rows; build your own total if needed.
     */
    protected function paginate(string $sql, array $params, int $page = 1, int $per = 20): array
    {
        $page = max(1, $page);
        $per  = max(1, min(200, $per));
        $off  = ($page - 1) * $per;
        $sql .= " LIMIT :__off, :__per";
        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->bindValue(':__off', $off, PDO::PARAM_INT);
        $st->bindValue(':__per', $per, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }
}
