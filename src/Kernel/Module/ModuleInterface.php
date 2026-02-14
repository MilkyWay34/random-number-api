<?php

declare(strict_types=1);

namespace App\Kernel\Module;

use App\Kernel\Container\Container;
use App\Kernel\Http\Router;

/**
 * Контракт для всех модулей приложения.
 *
 * Каждый модуль регистрирует свои сервисы в контейнере
 * и маршруты в роутере.
 */
interface ModuleInterface
{
    /**
     * Зарегистрировать сервисы и зависимости модуля в контейнере.
     */
    public function register(Container $container): void;

    /**
     * Зарегистрировать HTTP-маршруты модуля.
     */
    public function boot(Router $router, Container $container): void;
}
