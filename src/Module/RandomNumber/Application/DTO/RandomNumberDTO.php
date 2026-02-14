<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Application\DTO;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;

/**
 * DTO для передачи данных о случайном числе между слоями.
 */
final class RandomNumberDTO
{
    public function __construct(
        public readonly string $id,
        public readonly int $number,
    ) {
    }

    /**
     * Создать DTO из доменной сущности.
     */
    public static function fromEntity(RandomNumber $entity): self
    {
        return new self(
            id: $entity->getId()->getValue(),
            number: $entity->getNumber(),
        );
    }

    /**
     * Преобразовать в массив для JSON-ответа.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
        ];
    }
}
