<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\Repository;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;

/**
 * Контракт репозитория для хранения и получения случайных чисел.
 */
interface RandomNumberRepositoryInterface
{
    /**
     * Сохранить сгенерированное случайное число.
     */
    public function save(RandomNumber $randomNumber): void;

    /**
     * Найти случайное число по идентификатору.
     *
     * @return RandomNumber|null null если не найдено
     */
    public function findById(RandomNumberId $id): ?RandomNumber;
}
