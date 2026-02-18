<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * UserRegistrationDTO: Data Transfer Object for user registration data.
 */
class UserRegistrationDTO
{
    public function __construct(
        public readonly int $telegramId,
        public readonly string $name,
        public readonly string $programmingLanguage,
    ) {
    }

    /**
     * Create a DTO from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            telegramId: (int)$data['telegram_id'],
            name: (string)$data['name'],
            programmingLanguage: (string)$data['programming_language'],
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'telegram_id' => $this->telegramId,
            'name' => $this->name,
            'programming_language' => $this->programmingLanguage,
        ];
    }
}
