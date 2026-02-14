<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Persistence;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use App\Module\RandomNumber\Infrastructure\Exception\StorageException;

/**
 * Реализация репозитория на основе JSON-файла.
 *
 * Хранит данные в файле с блокировкой (LOCK_EX) для потокобезопасности.
 */
final class FileRandomNumberRepository implements RandomNumberRepositoryInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureStorageExists();
    }

    public function save(RandomNumber $randomNumber): void
    {
        $data = $this->readAll();

        $id = $randomNumber->getId()->getValue();
        $data[$id] = $randomNumber->getNumber();

        $this->writeAll($data);
    }

    public function findById(RandomNumberId $id): ?RandomNumber
    {
        $data = $this->readAll();
        $key = $id->getValue();

        if (!isset($data[$key])) {
            return null;
        }

        return new RandomNumber(
            new RandomNumberId($key),
            (int) $data[$key],
        );
    }

    /**
     * Прочитать все записи из файла.
     *
     * @return array<string, int>
     */
    private function readAll(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);

        if ($content === false || $content === '') {
            return [];
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * Записать все данные в файл с блокировкой.
     *
     * @param array<string, int> $data
     */
    private function writeAll(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $result = file_put_contents($this->filePath, $json, LOCK_EX);

        if ($result === false) {
            throw StorageException::writeFailed($this->filePath);
        }
    }

    /**
     * Убедиться, что директория хранилища существует.
     */
    private function ensureStorageExists(): void
    {
        $dir = dirname($this->filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
