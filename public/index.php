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

$request = new Request();
$response = $application->handle($request);
$response->send();
