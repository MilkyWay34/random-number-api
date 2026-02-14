<?php

declare(strict_types=1);

namespace App\Client;

use RuntimeException;

/**
 * HTTP-клиент на основе CURL.
 *
 * Выполняет GET-запросы и возвращает декодированный JSON.
 */
final class CurlHttpClient
{
    private int $timeout;

    public function __construct(int $timeout = 10)
    {
        $this->timeout = $timeout;
    }

    /**
     * Выполнить GET-запрос.
     *
     * @param string $url Базовый URL
     * @param array<string, string> $params Query-параметры
     * @return array Декодированный JSON-ответ
     * @throws RuntimeException при ошибке запроса
     */
    public function get(string $url, array $params = []): array
    {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException("CURL ошибка: {$error}");
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                "Некорректный JSON-ответ (HTTP {$httpCode}): {$response}"
            );
        }

        return $data;
    }
}
