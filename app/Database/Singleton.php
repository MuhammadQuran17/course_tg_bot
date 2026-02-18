<?php

declare(strict_types=1);

namespace App\Database;

/**
 * Singleton Trait: Implements the Singleton pattern for reusable use across classes.
 * Ensures only one instance of the class exists and provides controlled access.
 * Prevents cloning and serialization to maintain singleton integrity.
 */
trait Singleton
{
    private static ?self $instance = null;

    /**
     * Get the singleton instance of the class.
     *
     * @return self The singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prevent cloning of the singleton instance.
     */
    private function __clone()
    {
    }

    /**
     * Prevent serialization of the singleton instance.
     */
    public function __serialize(): array
    {
        throw new \RuntimeException('Cannot serialize a singleton instance');
    }

    /**
     * Prevent unserialization of the singleton instance.
     */
    public function __unserialize(array $data): void
    {
        throw new \RuntimeException('Cannot unserialize a singleton instance');
    }
}
