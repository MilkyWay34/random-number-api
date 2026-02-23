<?php

declare(strict_types=1);

namespace App\Module\RandomNumber\Infrastructure\Persistence;

use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use App\Module\RandomNumber\Infrastructure\Exception\StorageException;
use PDO;
use PDOException;

/**
 * Реализация репозитория на основе SQLite.
 */
final class SqliteRandomNumberRepository implements RandomNumberRepositoryInterface
{
    private string $filePath;
    private PDO $pdo;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureStorageDirectoryExists();

        try {
            $this->pdo = new PDO('sqlite:' . $this->filePath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec('PRAGMA busy_timeout = 5000');
            $this->pdo->exec('PRAGMA journal_mode = WAL');
            $this->initializeSchema();
        } catch (PDOException $e) {
            throw StorageException::connectionFailed($this->filePath, $e->getMessage(), $e);
        }
    }

    public function save(RandomNumber $randomNumber): void
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO random_numbers (id, number) VALUES (:id, :number)',
            );
            $statement->execute([
                ':id' => $randomNumber->getId()->getValue(),
                ':number' => $randomNumber->getNumber(),
            ]);
        } catch (PDOException $e) {
            throw StorageException::writeFailed($this->filePath, $e->getMessage(), $e);
        }
    }

    public function findById(RandomNumberId $id): ?RandomNumber
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, number FROM random_numbers WHERE id = :id LIMIT 1',
            );
            $statement->execute([':id' => $id->getValue()]);
            $row = $statement->fetch();
        } catch (PDOException $e) {
            throw StorageException::readFailed($this->filePath, $e->getMessage(), $e);
        }

        if ($row === false) {
            return null;
        }

        return new RandomNumber(
            new RandomNumberId((string) $row['id']),
            (int) $row['number'],
        );
    }

    private function initializeSchema(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS random_numbers (
                id TEXT PRIMARY KEY,
                number INTEGER NOT NULL CHECK(number BETWEEN 1 AND 1000)
            )',
        );
    }

    private function ensureStorageDirectoryExists(): void
    {
        $dir = dirname($this->filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
