<?php

declare(strict_types=1);

namespace App\Client;

use App\Client\Exception\ApiRequestException;

/**
 * Клиент для REST API генерации случайных чисел.
 *
 * Использует CurlHttpClient для HTTP-запросов.
 */
final class RandomNumberApiClient
{
    private CurlHttpClient $httpClient;
    private string $baseUrl;

    public function __construct(string $baseUrl, ?CurlHttpClient $httpClient = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = $httpClient ?? new CurlHttpClient();
    }

    /**
     * Сгенерировать случайное число.
     *
     * @return array{id: string, number: int}
     * @throws ApiRequestException
     */
    public function generateRandom(): array
    {
        return $this->httpClient->get("{$this->baseUrl}/api/random");
    }

    /**
     * Получить ранее сгенерированное число по ID.
     *
     * @return array{id: string, number: int}
     * @throws ApiRequestException
     */
    public function getById(string $id): array
    {
        return $this->httpClient->get("{$this->baseUrl}/api/get", ['id' => $id]);
    }
}
