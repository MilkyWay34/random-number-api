<?php

declare(strict_types=1);

$baseUrl = rtrim($argv[1] ?? 'http://127.0.0.1:18080', '/');

try {
    $generated = requestJson("{$baseUrl}/api/random");
    assertStatus($generated['status'], 200, 'GET /api/random');
    assertArrayHasString($generated['data'], 'id', 'GET /api/random');
    assertArrayHasInt($generated['data'], 'number', 'GET /api/random');
    assertNumberRange($generated['data']['number'], 1, 1000, 'GET /api/random');

    $id = rawurlencode($generated['data']['id']);

    $found = requestJson("{$baseUrl}/api/get?id={$id}");
    assertStatus($found['status'], 200, 'GET /api/get?id=<valid>');
    assertArrayHasString($found['data'], 'id', 'GET /api/get?id=<valid>');
    assertArrayHasInt($found['data'], 'number', 'GET /api/get?id=<valid>');
    if ($found['data']['id'] !== $generated['data']['id']) {
        throw new RuntimeException('GET /api/get?id=<valid>: вернулся другой id.');
    }

    $missingId = requestJson("{$baseUrl}/api/get");
    assertStatus($missingId['status'], 400, 'GET /api/get (missing id)');
    assertArrayHasString($missingId['data'], 'error', 'GET /api/get (missing id)');

    $invalidId = requestJson("{$baseUrl}/api/get?id=%FF");
    assertStatus($invalidId['status'], 400, 'GET /api/get (invalid id)');
    assertArrayHasString($invalidId['data'], 'error', 'GET /api/get (invalid id)');

    $notFound = requestJson("{$baseUrl}/api/get?id=ffffffffffffffffffffffffffffffff");
    assertStatus($notFound['status'], 404, 'GET /api/get (not found)');
    assertArrayHasString($notFound['data'], 'error', 'GET /api/get (not found)');

    $unknownRoute = requestJson("{$baseUrl}/api/unknown");
    assertStatus($unknownRoute['status'], 404, 'GET /api/unknown');
    assertArrayHasString($unknownRoute['data'], 'error', 'GET /api/unknown');

    echo "OK smoke_api.php passed" . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "FAILED smoke_api.php: {$e->getMessage()}" . PHP_EOL);
    exit(1);
}

/**
 * @return array{status: int, data: array}
 */
function requestJson(string $url): array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 10,
            'header' => "Accept: application/json\r\n",
        ],
    ]);

    $body = file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];

    if ($body === false) {
        throw new RuntimeException("Не удалось выполнить HTTP-запрос: {$url}");
    }

    $status = 0;
    foreach ($headers as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches) === 1) {
            $status = (int) $matches[1];
            break;
        }
    }

    $decoded = json_decode($body, true);

    if (!is_array($decoded)) {
        throw new RuntimeException("Некорректный JSON-ответ ({$status}): {$body}");
    }

    return [
        'status' => $status,
        'data' => $decoded,
    ];
}

function assertStatus(int $actual, int $expected, string $label): void
{
    if ($actual !== $expected) {
        throw new RuntimeException("{$label}: ожидался HTTP {$expected}, получен {$actual}.");
    }
}

function assertArrayHasString(array $data, string $key, string $label): void
{
    if (!isset($data[$key]) || !is_string($data[$key]) || $data[$key] === '') {
        throw new RuntimeException("{$label}: поле '{$key}' отсутствует или не строка.");
    }
}

function assertArrayHasInt(array $data, string $key, string $label): void
{
    if (!isset($data[$key]) || !is_int($data[$key])) {
        throw new RuntimeException("{$label}: поле '{$key}' отсутствует или не integer.");
    }
}

function assertNumberRange(int $value, int $min, int $max, string $label): void
{
    if ($value < $min || $value > $max) {
        throw new RuntimeException(
            "{$label}: число {$value} вне диапазона {$min}..{$max}.",
        );
    }
}
