<?php

declare(strict_types=1);

namespace App\Database;

/**
 * DatabaseConnection: Handles database connection instantiation and configuration.
 * Responsible for creating and managing PDO connections using environment variables.
 * Implements the Singleton pattern to ensure a single database connection.
 */
class DatabaseConnection
{
    use Singleton;

    private \PDO $pdo;

    /**
     * Initialize database connection with environment variables.
     *
     * @throws \RuntimeException If connection fails
     */
    private function __construct()
    {
        try {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
            $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
            $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
            $database = $_ENV['DB_NAME'] ?? getenv('DB_NAME');

            $this->pdo = new \PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the PDO connection instance.
     *
     * @return \PDO The database connection
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }
}
