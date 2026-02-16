<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\ValueObject;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use InvalidArgumentException;

/**
 * Параметры генерации случайного значения.
 *
 * Сейчас используется только integer/uniform режим, остальные поля
 * сохранены как фундамент для будущего расширения API.
 */
final class GenerationOptions
{
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';

    public const DISTRIBUTION_UNIFORM = 'uniform';

    public function __construct(
        private readonly string $numberType,
        private readonly int $min,
        private readonly int $max,
        private readonly int $precision,
        private readonly string $distribution,
        private readonly bool $unique,
    ) {
        $this->validate();
    }

    public static function defaults(): self
    {
        return new self(
            numberType: self::TYPE_INT,
            min: RandomNumber::MIN_NUMBER,
            max: RandomNumber::MAX_NUMBER,
            precision: 0,
            distribution: self::DISTRIBUTION_UNIFORM,
            unique: false,
        );
    }

    public static function integerRange(int $min, int $max): self
    {
        return new self(
            numberType: self::TYPE_INT,
            min: $min,
            max: $max,
            precision: 0,
            distribution: self::DISTRIBUTION_UNIFORM,
            unique: false,
        );
    }

    public function getNumberType(): string
    {
        return $this->numberType;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function getDistribution(): string
    {
        return $this->distribution;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    private function validate(): void
    {
        if (!\in_array($this->numberType, [self::TYPE_INT, self::TYPE_FLOAT], true)) {
            throw new InvalidArgumentException('Неподдерживаемый тип случайного значения.');
        }

        if ($this->distribution !== self::DISTRIBUTION_UNIFORM) {
            throw new InvalidArgumentException('Неподдерживаемое распределение случайного значения.');
        }

        if ($this->min > $this->max) {
            throw new InvalidArgumentException('Минимальное значение не может быть больше максимального.');
        }

        if ($this->precision < 0) {
            throw new InvalidArgumentException('Точность не может быть отрицательной.');
        }

        if ($this->numberType === self::TYPE_INT && $this->precision !== 0) {
            throw new InvalidArgumentException('Для integer-режима точность должна быть равна 0.');
        }
    }
}
