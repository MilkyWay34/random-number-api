<?php

declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Container\Container;
use App\Kernel\Http\JsonResponse;
use App\Kernel\Http\Request;
use App\Kernel\Http\Router;
use App\Kernel\Module\ModuleInterface;

/**
 * Ядро приложения.
 *
 * Загружает модули, регистрирует сервисы и маршруты, обрабатывает HTTP-запросы.
 */
final class Application
{
    private Router $router;
    private Container $container;

    /** @var ModuleInterface[] */
    private array $modules;

    /**
     * @param ModuleInterface[] $modules
     */
    public function __construct(array $modules)
    {
        $this->modules = $modules;
        $this->router = new Router();
        $this->container = new Container();
    }

    /**
     * Обработать HTTP-запрос: зарегистрировать модули, задиспатчить запрос.
     */
    public function handle(Request $request): JsonResponse
    {
        $this->registerModules();
        $this->bootModules();

        return $this->router->dispatch($request);
    }

    /**
     * Фаза register: каждый модуль регистрирует свои сервисы.
     */
    private function registerModules(): void
    {
        foreach ($this->modules as $module) {
            $module->register($this->container);
        }
    }

    /**
     * Фаза boot: каждый модуль регистрирует свои маршруты.
     */
    private function bootModules(): void
    {
        foreach ($this->modules as $module) {
            $module->boot($this->router, $this->container);
        }
    }
}
