<?php

declare(strict_types=1);

namespace App\Database;

use App\DTOs\UserRegistrationDTO;

/**
 * UserRepository: Handles all database operations for user registration.
 */
class UserRepository
{
    private \PDO $pdo;

    /**
     * Initialize repository with database connection.
     * Gets the singleton database connection instance.
     */
    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Save a registered user to the database.
     *
     * @param UserRegistrationDTO $userData User registration data
     * @return int The ID of the inserted record
     */
    public function saveUser(UserRegistrationDTO $userData): int
    {
        $query = <<<SQL
            INSERT INTO users (telegram_id, name, programming_language, registered_at)
            VALUES (:telegram_id, :name, :programming_language, NOW())
        SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':telegram_id' => $userData->telegramId,
            ':name' => $userData->name,
            ':programming_language' => $userData->programmingLanguage,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Check if a user already exists by Telegram ID.
     *
     * @param int $telegramId User's Telegram ID
     * @return bool
     */
    public function userExists(int $telegramId): bool
    {
        $query = 'SELECT COUNT(*) FROM users WHERE telegram_id = :telegram_id';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':telegram_id' => $telegramId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Get user by Telegram ID.
     *
     * @param int $telegramId User's Telegram ID
     * @return UserRegistrationDTO|null User data or null if not found
     */
    public function getUserByTelegramId(int $telegramId): ?UserRegistrationDTO
    {
        $query = 'SELECT * FROM users WHERE telegram_id = :telegram_id LIMIT 1';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':telegram_id' => $telegramId]);

        $result = $stmt->fetch();
        return $result ? UserRegistrationDTO::fromArray($result) : null;
    }
}
