<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Persistence;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use App\Module\RandomNumber\Infrastructure\Exception\StorageException;
use JsonException;

/**
 * Реализация репозитория на основе JSON-файла.
 *
 * Хранит данные в файле с полной блокировкой на read-modify-write.
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
        $handle = $this->openStorageHandle();

        try {
            if (!flock($handle, LOCK_EX)) {
                throw StorageException::writeFailed($this->filePath, 'Не удалось установить LOCK_EX.');
            }

            $data = $this->readAllFromHandle($handle);
            $data[$randomNumber->getId()->getValue()] = $randomNumber->getNumber();
            $this->writeAllToHandle($handle, $data);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function findById(RandomNumberId $id): ?RandomNumber
    {
        $handle = $this->openStorageHandle();

        try {
            if (!flock($handle, LOCK_SH)) {
                throw StorageException::readFailed($this->filePath, 'Не удалось установить LOCK_SH.');
            }

            $data = $this->readAllFromHandle($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        $key = $id->getValue();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        return new RandomNumber(
            new RandomNumberId($key),
            (int) $data[$key],
        );
    }

    /**
     * @return array<string, int>
     */
    private function readAllFromHandle($handle): array
    {
        if (rewind($handle) === false) {
            throw StorageException::readFailed($this->filePath, 'Не удалось перемотать файл.');
        }

        $content = stream_get_contents($handle);
        if ($content === false) {
            throw StorageException::readFailed($this->filePath, 'Не удалось прочитать содержимое файла.');
        }

        if (trim($content) === '') {
            return [];
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw StorageException::readFailed($this->filePath, $e->getMessage(), $e);
        }

        if (!is_array($data)) {
            throw StorageException::readFailed($this->filePath, 'Содержимое хранилища не является JSON-объектом.');
        }

        return $data;
    }

    /**
     * @param array<string, int> $data
     */
    private function writeAllToHandle($handle, array $data): void
    {
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw StorageException::writeFailed($this->filePath, $e->getMessage(), $e);
        }

        if (rewind($handle) === false) {
            throw StorageException::writeFailed($this->filePath, 'Не удалось перемотать файл.');
        }

        if (!ftruncate($handle, 0)) {
            throw StorageException::writeFailed($this->filePath, 'Не удалось очистить файл перед записью.');
        }

        $bytesWritten = fwrite($handle, $json);
        if ($bytesWritten === false || $bytesWritten < strlen($json)) {
            throw StorageException::writeFailed($this->filePath, 'Не удалось полностью записать данные.');
        }

        if (!fflush($handle)) {
            throw StorageException::writeFailed($this->filePath, 'Не удалось выполнить flush после записи.');
        }
    }

    private function ensureStorageExists(): void
    {
        $dir = dirname($this->filePath);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw StorageException::connectionFailed($this->filePath, 'Не удалось создать директорию хранилища.');
            }
        }

        if (!file_exists($this->filePath)) {
            $result = file_put_contents($this->filePath, '{}');
            if ($result === false) {
                throw StorageException::connectionFailed($this->filePath, 'Не удалось создать файл хранилища.');
            }
        }
    }

    /**
     * @return resource
     */
    private function openStorageHandle()
    {
        $handle = fopen($this->filePath, 'c+');
        if ($handle === false) {
            throw StorageException::connectionFailed($this->filePath, 'Не удалось открыть файл хранилища.');
        }

        return $handle;
    }
}
