<?php

declare(strict_types=1);

namespace App\Kernel\Container;

use App\Kernel\Exception\ContainerException;

/**
 * Простой DI-контейнер с lazy singleton-инстанцированием.
 *
 * Регистрирует фабрики сервисов и возвращает единственный экземпляр при первом запросе.
 */
final class Container
{
    /** @var array<string, callable> */
    private array $factories = [];

    /** @var array<string, object> */
    private array $instances = [];

    /**
     * Зарегистрировать фабрику сервиса.
     *
     * @param callable(Container): object $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * Получить экземпляр сервиса (lazy singleton).
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->factories[$id])) {
            throw ContainerException::serviceNotFound($id);
        }

        $this->instances[$id] = ($this->factories[$id])($this);

        return $this->instances[$id];
    }

    /**
     * Проверить, зарегистрирован ли сервис.
     */
    public function has(string $id): bool
    {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }
}
