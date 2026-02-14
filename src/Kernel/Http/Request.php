<?php

declare(strict_types=1);

namespace App\Kernel\Http;

/**
 * HTTP request abstraction over PHP superglobals.
 */
final class Request
{
    private string $method;
    private string $path;
    private array $queryParams;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->queryParams = $_GET;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQueryParam(string $name, mixed $default = null): mixed
    {
        return $this->queryParams[$name] ?? $default;
    }
}
