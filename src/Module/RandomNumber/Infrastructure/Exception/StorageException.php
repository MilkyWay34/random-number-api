<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Exception;

use RuntimeException;

/**
 * Исключение инфраструктурного слоя: ошибка работы с хранилищем.
 */
final class StorageException extends RuntimeException
{
    public static function writeFailed(string $filePath): self
    {
        return new self("Не удалось записать данные в файл: {$filePath}");
    }
}
