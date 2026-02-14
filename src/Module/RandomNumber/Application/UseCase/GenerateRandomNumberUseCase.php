<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Application\UseCase;

use App\Module\RandomNumber\Application\DTO\RandomNumberDTO;
use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;

/**
 * Сценарий использования: сгенерировать случайное число и сохранить его.
 */
final class GenerateRandomNumberUseCase
{
    private const MIN_VALUE = 1;
    private const MAX_VALUE = 1000;

    public function __construct(
        private readonly RandomNumberRepositoryInterface $repository,
    ) {
    }

    public function execute(): RandomNumberDTO
    {
        $id = RandomNumberId::generate();
        $number = random_int(self::MIN_VALUE, self::MAX_VALUE);

        $entity = new RandomNumber($id, $number);
        $this->repository->save($entity);

        return RandomNumberDTO::fromEntity($entity);
    }
}
