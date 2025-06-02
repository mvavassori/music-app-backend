<?php
namespace Database;

use PDO;
use PDOException;

class Connection {
    private static ?PDO $instance = null;

    private function __construct() {
        // Private constructor to prevent direct instantiation
        //  prevents you from doing new Connection()
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'mysql-music-app-db'; // container name
                $dbname = $_ENV['DB_NAME'] ?? 'musicappdb';
                $username = $_ENV['DB_USER'] ?? 'appuser';
                $password = $_ENV['DB_PASS'] ?? 'apppassword';

                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Throw exceptions on errors
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Return arrays, not objects
                        PDO::ATTR_EMULATE_PREPARES => false,                // Use real prepared statements
                    ]
                );
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new PDOException("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}