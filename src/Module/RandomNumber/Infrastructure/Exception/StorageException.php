<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Exception;

use RuntimeException;

/**
 * Исключение инфраструктурного слоя: ошибка работы с хранилищем.
 */
final class StorageException extends RuntimeException
{
    public static function connectionFailed(string $filePath, ?string $reason = null): self
    {
        return self::withReason("Не удалось подключиться к хранилищу: {$filePath}", $reason);
    }

    public static function readFailed(string $filePath, ?string $reason = null): self
    {
        return self::withReason("Не удалось прочитать данные из хранилища: {$filePath}", $reason);
    }

    public static function writeFailed(string $filePath, ?string $reason = null): self
    {
        return self::withReason("Не удалось записать данные в хранилище: {$filePath}", $reason);
    }

    private static function withReason(string $message, ?string $reason): self
    {
        if ($reason === null || $reason === '') {
            return new self($message);
        }

        return new self("{$message}. Причина: {$reason}");
    }
}
