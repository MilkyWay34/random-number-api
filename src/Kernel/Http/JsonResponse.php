<?php

declare(strict_types=1);

namespace App\Kernel\Http;

use JsonException;

/**
 * JSON HTTP response with proper headers and status code.
 */
final class JsonResponse
{
    private array $data;
    private int $statusCode;

    public function __construct(array $data, int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            http_response_code(500);
            echo '{"error":"Внутренняя ошибка сервера."}';
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
