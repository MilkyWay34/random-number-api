<?php

declare(strict_types=1);

namespace App\Client\Exception;

use RuntimeException;

/**
 * Исключение клиента: ошибка при выполнении API-запроса.
 */
final class ApiRequestException extends RuntimeException
{
    private ?int $httpCode;
    private ?string $responseBody;
    private ?array $responseData;
    private ?string $apiError;

    private function __construct(
        string $message,
        ?int $httpCode = null,
        ?string $responseBody = null,
        ?array $responseData = null,
        ?string $apiError = null,
    ) {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->responseBody = $responseBody;
        $this->responseData = $responseData;
        $this->apiError = $apiError;
    }

    public static function curlError(string $error): self
    {
        return new self("CURL ошибка: {$error}");
    }

    public static function invalidResponse(int $httpCode, string $body): self
    {
        return new self(
            "Некорректный JSON-ответ (HTTP {$httpCode}): {$body}",
            $httpCode,
            $body,
            null,
            null,
        );
    }

    public static function httpError(int $httpCode, array $responseData, string $responseBody): self
    {
        $apiError = is_string($responseData['error'] ?? null) ? $responseData['error'] : null;
        $message = $apiError !== null
            ? "HTTP {$httpCode}: {$apiError}"
            : "HTTP {$httpCode}: API вернул ошибку";

        return new self(
            $message,
            $httpCode,
            $responseBody,
            $responseData,
            $apiError,
        );
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public function getApiError(): ?string
    {
        return $this->apiError;
    }
}
