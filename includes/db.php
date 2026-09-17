<?php
/**
 * GK224.COM - Database Connection Handler (PDO MySQL)
 * Features auto-fallback to graceful mode if DB is not yet configured.
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $pdo = null;
    private $connected = false;

    private function __construct() {
        if (!defined('DB_NAME') || empty(DB_NAME)) {
            return;
        }

        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->connected = true;
        } catch (PDOException $e) {
            $this->connected = false;
            if (APP_ENV === 'development') {
                error_log("DB Connection failed: " . $e->getMessage());
            }
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

    public function isConnected() {
        return $this->connected;
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}
