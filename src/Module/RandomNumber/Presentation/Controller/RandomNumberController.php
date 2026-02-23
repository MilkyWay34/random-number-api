<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Presentation\Controller;

use App\Kernel\Http\JsonResponse;
use App\Kernel\Http\Request;
use App\Kernel\Logger\LoggerInterface;
use App\Module\RandomNumber\Application\UseCase\GenerateRandomNumberUseCase;
use App\Module\RandomNumber\Application\UseCase\GetRandomNumberUseCase;

/**
 * Контроллер API для работы со случайными числами.
 */
final class RandomNumberController
{
    public function __construct(
        private readonly GenerateRandomNumberUseCase $generateUseCase,
        private readonly GetRandomNumberUseCase $getUseCase,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * GET /api/random — сгенерировать случайное число.
     */
    public function random(Request $request): JsonResponse
    {
        $dto = $this->generateUseCase->execute();

        $this->logger?->info('Сгенерировано случайное число', [
            'id' => $dto->id,
            'number' => $dto->number,
        ]);

        return new JsonResponse($dto->toArray());
    }

    /**
     * GET /api/get?id=... — получить число по ID.
     */
    public function get(Request $request): JsonResponse
    {
        $id = $request->getQueryParam('id');

        if (!is_string($id) || $id === '') {
            $this->logger?->warning('Запрос без обязательного параметра id');

            return new JsonResponse(
                ['error' => 'Параметр "id" обязателен.'],
                400,
            );
        }

        $dto = $this->getUseCase->execute((string) $id);

        $this->logger?->info('Получено число по ID', [
            'id' => $dto->id,
            'number' => $dto->number,
        ]);

        return new JsonResponse($dto->toArray());
    }
}
