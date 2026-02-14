<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Entity;

use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;

/**
 * Сущность: сгенерированное случайное число с уникальным идентификатором.
 */
final class RandomNumber
{
    public function __construct(
        private readonly RandomNumberId $id,
        private readonly int $number,
    ) {
    }

    public function getId(): RandomNumberId
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}
