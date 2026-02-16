<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Generator;

use App\Module\RandomNumber\Domain\ValueObject\GenerationOptions;

/**
 * Контракт генератора случайных значений.
 *
 * Текущая версия возвращает integer, чтобы сохранить совместимость
 * с текущей доменной моделью и API.
 */
interface RandomGeneratorInterface
{
    public function generate(GenerationOptions $options): int;
}
