<?php

namespace Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->load();

            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dbName = $_ENV['DB_DATABASE'] ?? 'real_estate';
            $username = $_ENV['DB_USERNAME'] ?? 'user';
            $password = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Ошибка подключения: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
