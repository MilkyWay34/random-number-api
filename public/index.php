<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel\Application;
use App\Kernel\Http\Request;
use App\Module\RandomNumber\RandomNumberModule;

$application = new Application([
    new RandomNumberModule(),
]);

$request = new Request();
$response = $application->handle($request);
$response->send();
