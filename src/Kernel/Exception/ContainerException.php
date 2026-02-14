<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

use RuntimeException;

/**
 * Исключение контейнера зависимостей: сервис не зарегистрирован.
 */
final class ContainerException extends RuntimeException
{
    public static function serviceNotFound(string $id): self
    {
        return new self("Сервис '{$id}' не зарегистрирован в контейнере.");
    }
}
