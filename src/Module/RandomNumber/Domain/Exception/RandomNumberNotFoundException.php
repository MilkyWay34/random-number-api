<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Exception;

use RuntimeException;

/**
 * Доменное исключение: случайное число с указанным ID не найдено.
 */
final class RandomNumberNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Случайное число с ID '{$id}' не найдено.");
    }
}
