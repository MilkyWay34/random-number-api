<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Application\UseCase;

use App\Module\RandomNumber\Application\DTO\RandomNumberDTO;
use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Generator\RandomGeneratorInterface;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\GenerationOptions;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;

/**
 * Сценарий использования: сгенерировать случайное число и сохранить его.
 */
final class GenerateRandomNumberUseCase
{
    public function __construct(
        private readonly RandomNumberRepositoryInterface $repository,
        private readonly RandomGeneratorInterface $generator,
    ) {
    }

    public function execute(): RandomNumberDTO
    {
        return $this->executeWithOptions(GenerationOptions::defaults());
    }

    public function executeWithOptions(GenerationOptions $options): RandomNumberDTO
    {
        $id = RandomNumberId::generate();
        $number = $this->generator->generate($options);

        $entity = new RandomNumber($id, $number);
        $this->repository->save($entity);

        return RandomNumberDTO::fromEntity($entity);
    }
}
