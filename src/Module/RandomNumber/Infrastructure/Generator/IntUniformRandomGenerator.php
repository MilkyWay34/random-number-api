<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Generator;

use App\Module\RandomNumber\Domain\Generator\RandomGeneratorInterface;
use App\Module\RandomNumber\Domain\ValueObject\GenerationOptions;
use InvalidArgumentException;

/**
 * Базовый генератор integer-значений с равномерным распределением.
 */
final class IntUniformRandomGenerator implements RandomGeneratorInterface
{
    public function generate(GenerationOptions $options): int
    {
        if ($options->getNumberType() !== GenerationOptions::TYPE_INT) {
            throw new InvalidArgumentException('Текущий генератор поддерживает только integer-режим.');
        }

        if ($options->getDistribution() !== GenerationOptions::DISTRIBUTION_UNIFORM) {
            throw new InvalidArgumentException('Текущий генератор поддерживает только uniform-распределение.');
        }

        if ($options->getPrecision() !== 0) {
            throw new InvalidArgumentException('Текущий генератор не поддерживает дробную точность.');
        }

        if ($options->isUnique()) {
            throw new InvalidArgumentException('Текущий генератор не поддерживает режим уникальности.');
        }

        return random_int($options->getMin(), $options->getMax());
    }
}
