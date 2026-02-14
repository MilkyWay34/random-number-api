<?php

declare(strict_types=1);

namespace App\Module\RandomNumber;

use App\Kernel\Container\Container;
use App\Kernel\Http\Router;
use App\Kernel\Logger\LoggerInterface;
use App\Kernel\Module\ModuleInterface;
use App\Module\RandomNumber\Application\UseCase\GenerateRandomNumberUseCase;
use App\Module\RandomNumber\Application\UseCase\GetRandomNumberUseCase;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Infrastructure\Persistence\FileRandomNumberRepository;
use App\Module\RandomNumber\Presentation\Controller\RandomNumberController;

/**
 * Модуль генерации случайных чисел.
 *
 * Регистрирует свои сервисы и маршруты в ядре приложения.
 */
final class RandomNumberModule implements ModuleInterface
{
    private const STORAGE_FILE = __DIR__ . '/../../../storage/random_numbers.json';

    public function register(Container $container): void
    {
        $container->set(
            RandomNumberRepositoryInterface::class,
            fn () => new FileRandomNumberRepository(self::STORAGE_FILE),
        );

        $container->set(
            GenerateRandomNumberUseCase::class,
            fn (Container $c) => new GenerateRandomNumberUseCase(
                $c->get(RandomNumberRepositoryInterface::class),
            ),
        );

        $container->set(
            GetRandomNumberUseCase::class,
            fn (Container $c) => new GetRandomNumberUseCase(
                $c->get(RandomNumberRepositoryInterface::class),
            ),
        );

        $container->set(
            RandomNumberController::class,
            fn (Container $c) => new RandomNumberController(
                $c->get(GenerateRandomNumberUseCase::class),
                $c->get(GetRandomNumberUseCase::class),
                $c->has(LoggerInterface::class) ? $c->get(LoggerInterface::class) : null,
            ),
        );
    }

    public function boot(Router $router, Container $container): void
    {
        /** @var RandomNumberController $controller */
        $controller = $container->get(RandomNumberController::class);

        $router->addRoute('GET', '/api/random', [$controller, 'random']);
        $router->addRoute('GET', '/api/get', [$controller, 'get']);
    }
}
