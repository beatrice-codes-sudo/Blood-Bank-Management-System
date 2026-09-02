<?php
/**
 * Database Connection Class
 * Singleton PDO connection to MySQL
 * Reads credentials from .env file
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Load .env file
        $envPath = __DIR__ . '/../.env';
        if (!file_exists($envPath)) {
            die("Error: .env file not found at " . realpath(__DIR__ . '/..'));
        }
        $env = function_exists('loadEnvFile') ? loadEnvFile($envPath) : (parse_ini_file($envPath) ?: []);

        $host    = $env['DB_HOST'] ?? 'localhost';
        $dbname  = $env['DB_NAME'] ?? 'bms_db';
        $username = $env['DB_USER'] ?? 'root';
        $password = $env['DB_PASS'] ?? '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning
    private function __clone() {}
}
