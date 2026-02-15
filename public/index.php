<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel\Application;
use App\Kernel\Http\Request;
use App\Kernel\Logger\FileLogger;
use App\Module\RandomNumber\RandomNumberModule;

$logger = new FileLogger(__DIR__ . '/../storage/app.log');

$application = new Application(
    modules: [new RandomNumberModule()],
    logger: $logger,
);

try {
    $request = new Request();
    $response = $application->handle($request);
    $response->send();
} catch (\Throwable $e) {
    $logger->error('Критическая ошибка на этапе отправки ответа', [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
    ]);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo '{"error":"Внутренняя ошибка сервера."}';
}
