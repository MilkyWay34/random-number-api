<?php

declare(strict_types=1);

namespace App\Kernel\Logger;

/**
 * Контракт логгера (упрощённый аналог PSR-3).
 */
interface LoggerInterface
{
    public function debug(string $message, array $context = []): void;

    public function info(string $message, array $context = []): void;

    public function warning(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}
