<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Entity;

use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use InvalidArgumentException;

/**
 * Сущность: сгенерированное случайное число с уникальным идентификатором.
 */
final class RandomNumber
{
    public const MIN_NUMBER = 1;
    public const MAX_NUMBER = 1000;

    public function __construct(
        private readonly RandomNumberId $id,
        private readonly int $number,
    ) {
        if ($number < self::MIN_NUMBER || $number > self::MAX_NUMBER) {
            throw new InvalidArgumentException(
                'Случайное число должно быть в диапазоне от 1 до 1000.',
            );
        }
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
