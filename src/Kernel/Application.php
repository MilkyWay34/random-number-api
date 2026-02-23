<?php

declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Container\Container;
use App\Kernel\Exception\NotFoundException;
use App\Kernel\Http\JsonResponse;
use App\Kernel\Http\Request;
use App\Kernel\Http\Router;
use App\Kernel\Logger\LoggerInterface;
use App\Kernel\Module\ModuleInterface;
use Throwable;

/**
 * Ядро приложения.
 *
 * Загружает модули, регистрирует сервисы и маршруты, обрабатывает HTTP-запросы.
 */
final class Application
{
    private Router $router;
    private Container $container;
    private ?LoggerInterface $logger;

    /** @var ModuleInterface[] */
    private array $modules;

    /**
     * @param ModuleInterface[] $modules
     */
    public function __construct(array $modules, ?LoggerInterface $logger = null)
    {
        $this->modules = $modules;
        $this->logger = $logger;
        $this->router = new Router();
        $this->container = new Container();

        if ($this->logger !== null) {
            $this->container->set(LoggerInterface::class, fn () => $this->logger);
        }
    }

    /**
     * Обработать HTTP-запрос: зарегистрировать модули, задиспатчить запрос.
     */
    public function handle(Request $request): JsonResponse
    {
        $this->registerModules();
        $this->bootModules();

        $this->logger?->info('Входящий запрос', [
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
        ]);

        try {
            $response = $this->router->dispatch($request);
        } catch (NotFoundException $e) {
            $this->logger?->warning($e->getMessage(), ['exception' => $e, 'path' => $request->getPath()]);
            $response = new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            $this->logger?->warning($e->getMessage(), ['exception' => $e, 'path' => $request->getPath()]);
            $response = new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\DomainException $e) {
            $this->logger?->warning($e->getMessage(), ['exception' => $e, 'path' => $request->getPath()]);
            $response = new JsonResponse(['error' => $e->getMessage()], 409);
        } catch (Throwable $e) {
            $this->logger?->error('Необработанное исключение', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $response = new JsonResponse(['error' => 'Внутренняя ошибка сервера.'], 500);
        }

        $this->logger?->info('Ответ отправлен', [
            'status' => $response->getStatusCode(),
        ]);

        return $response;
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
