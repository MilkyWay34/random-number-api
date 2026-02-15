<?php

declare(strict_types=1);

/**
 * Демонстрация работы клиентской библиотеки.
 *
 * Перед запуском убедитесь, что сервер запущен:
 *   php -S localhost:8080 -t public/
 *
 * Запуск демо:
 *   php demo.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Client\Exception\ApiRequestException;
use App\Client\RandomNumberApiClient;

$baseUrl = $argv[1] ?? 'http://localhost:8080';

$client = new RandomNumberApiClient($baseUrl);

echo "=== Демонстрация REST API клиента ===" . PHP_EOL;
echo "Сервер: {$baseUrl}" . PHP_EOL;
echo PHP_EOL;

try {
    // 1. Сгенерировать случайное число
    echo "1. Генерация случайного числа..." . PHP_EOL;
    $result = $client->generateRandom();
    echo "   ID:    {$result['id']}" . PHP_EOL;
    echo "   Число: {$result['number']}" . PHP_EOL;
    echo PHP_EOL;

    // 2. Получить число по ID
    $id = $result['id'];
    echo "2. Получение числа по ID ({$id})..." . PHP_EOL;
    $result = $client->getById($id);
    echo "   ID:    {$result['id']}" . PHP_EOL;
    echo "   Число: {$result['number']}" . PHP_EOL;
    echo PHP_EOL;

    // 3. Попытка получить несуществующее число
    echo "3. Попытка получить число с несуществующим ID..." . PHP_EOL;
    try {
        $client->getById('ffffffffffffffffffffffffffffffff');
        echo "   Неожиданный ответ без ошибки." . PHP_EOL;
    } catch (ApiRequestException $e) {
        printApiError($e);
    }

    echo PHP_EOL;
    echo "=== Готово ===" . PHP_EOL;
} catch (ApiRequestException $e) {
    echo "Ошибка запроса к API:" . PHP_EOL;
    printApiError($e);
    exit(1);
}

function printApiError(ApiRequestException $exception): void
{
    $httpCode = $exception->getHttpCode();
    $apiError = $exception->getApiError();

    if ($httpCode !== null) {
        echo "   HTTP: {$httpCode}" . PHP_EOL;
    }

    echo '   Ошибка: ' . ($apiError ?? $exception->getMessage()) . PHP_EOL;
}
