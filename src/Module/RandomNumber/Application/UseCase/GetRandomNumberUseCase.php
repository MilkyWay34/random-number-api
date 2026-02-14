<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Application\UseCase;

use App\Module\RandomNumber\Application\DTO\RandomNumberDTO;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use App\Module\RandomNumber\Domain\Exception\RandomNumberNotFoundException;

/**
 * Сценарий использования: получить ранее сгенерированное число по ID.
 */
final class GetRandomNumberUseCase
{
    public function __construct(
        private readonly RandomNumberRepositoryInterface $repository,
    ) {
    }

    /**
     * @throws RandomNumberNotFoundException если число с данным ID не найдено
     */
    public function execute(string $id): RandomNumberDTO
    {
        $randomNumberId = new RandomNumberId($id);
        $entity = $this->repository->findById($randomNumberId);

        if ($entity === null) {
            throw RandomNumberNotFoundException::withId($id);
        }

        return RandomNumberDTO::fromEntity($entity);
    }
}
