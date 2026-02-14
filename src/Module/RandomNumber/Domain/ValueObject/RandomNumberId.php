<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\ValueObject;

/**
 * Иммутабельный Value Object для уникального идентификатора случайного числа.
 */
final class RandomNumberId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Сгенерировать новый уникальный идентификатор.
     */
    public static function generate(): self
    {
        return new self(uniqid('', true));
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
