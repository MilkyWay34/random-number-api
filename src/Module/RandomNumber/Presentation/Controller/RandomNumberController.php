<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Presentation\Controller;

use App\Kernel\Http\JsonResponse;
use App\Kernel\Http\Request;
use App\Module\RandomNumber\Application\UseCase\GenerateRandomNumberUseCase;
use App\Module\RandomNumber\Application\UseCase\GetRandomNumberUseCase;
use RuntimeException;

/**
 * Контроллер API для работы со случайными числами.
 */
final class RandomNumberController
{
    public function __construct(
        private readonly GenerateRandomNumberUseCase $generateUseCase,
        private readonly GetRandomNumberUseCase $getUseCase,
    ) {
    }

    /**
     * GET /api/random — сгенерировать случайное число.
     */
    public function random(Request $request): JsonResponse
    {
        $dto = $this->generateUseCase->execute();

        return new JsonResponse($dto->toArray());
    }

    /**
     * GET /api/get?id=... — получить число по ID.
     */
    public function get(Request $request): JsonResponse
    {
        $id = $request->getQueryParam('id');

        if ($id === null || $id === '') {
            return new JsonResponse(
                ['error' => 'Параметр "id" обязателен.'],
                400,
            );
        }

        try {
            $dto = $this->getUseCase->execute((string) $id);
        } catch (RuntimeException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                404,
            );
        }

        return new JsonResponse($dto->toArray());
    }
}
