<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Exception;

use App\Kernel\Exception\NotFoundException;

/**
 * Доменное исключение: случайное число с указанным ID не найдено.
 */
final class RandomNumberNotFoundException extends NotFoundException
{
    public static function withId(string $id): self
    {
        return new self("Случайное число с ID '{$id}' не найдено.");
    }
}
