<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Application\UseCase;

use App\Module\RandomNumber\Application\DTO\RandomNumberDTO;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use RuntimeException;

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
     * @throws RuntimeException если число с данным ID не найдено
     */
    public function execute(string $id): RandomNumberDTO
    {
        $randomNumberId = new RandomNumberId($id);
        $entity = $this->repository->findById($randomNumberId);

        if ($entity === null) {
            throw new RuntimeException("Случайное число с ID '{$id}' не найдено.");
        }

        return RandomNumberDTO::fromEntity($entity);
    }
}
