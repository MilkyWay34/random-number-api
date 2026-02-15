<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Иммутабельный Value Object для уникального идентификатора случайного числа.
 */
final class RandomNumberId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Неверный формат идентификатора случайного числа.');
        }

        $this->value = $value;
    }

    /**
     * Сгенерировать новый уникальный идентификатор.
     */
    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
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

    private function isValid(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        return preg_match('/\A(?:[a-f0-9]{32}|[0-9a-f]{13}\.[0-9]{8})\z/i', $value) === 1;
    }
}
