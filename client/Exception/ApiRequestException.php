<?php

declare(strict_types=1);

namespace App\Client\Exception;

use RuntimeException;

/**
 * Исключение клиента: ошибка при выполнении API-запроса.
 */
final class ApiRequestException extends RuntimeException
{
    public static function curlError(string $error): self
    {
        return new self("CURL ошибка: {$error}");
    }

    public static function invalidResponse(int $httpCode, string $body): self
    {
        return new self("Некорректный JSON-ответ (HTTP {$httpCode}): {$body}");
    }
}
