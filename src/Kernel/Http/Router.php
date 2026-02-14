<?php

declare(strict_types=1);

namespace App\Kernel\Http;

/**
 * Simple HTTP router: registers routes and dispatches requests to handlers.
 */
final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    /**
     * Register a route handler for a given HTTP method and path.
     *
     * @param callable(Request): JsonResponse $handler
     */
    public function addRoute(string $method, string $path, callable $handler): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $handler;
    }

    /**
     * Dispatch the request to the matching handler.
     * Returns a 404 response if no route matches.
     */
    public function dispatch(Request $request): JsonResponse
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            return new JsonResponse(
                ['error' => 'Not Found', 'message' => "No route matches {$method} {$path}"],
                404
            );
        }

        return $handler($request);
    }
}
