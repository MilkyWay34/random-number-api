<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Module\RandomNumber\Application\UseCase\GenerateRandomNumberUseCase;
use App\Module\RandomNumber\Domain\Entity\RandomNumber;
use App\Module\RandomNumber\Domain\Generator\RandomGeneratorInterface;
use App\Module\RandomNumber\Domain\Repository\RandomNumberRepositoryInterface;
use App\Module\RandomNumber\Domain\ValueObject\GenerationOptions;
use App\Module\RandomNumber\Domain\ValueObject\RandomNumberId;
use App\Module\RandomNumber\Infrastructure\Generator\IntUniformRandomGenerator;

try {
    $defaults = GenerationOptions::defaults();
    assertTrue($defaults->getNumberType() === GenerationOptions::TYPE_INT, 'default number type');
    assertTrue($defaults->getMin() === RandomNumber::MIN_NUMBER, 'default min');
    assertTrue($defaults->getMax() === RandomNumber::MAX_NUMBER, 'default max');
    assertTrue($defaults->getPrecision() === 0, 'default precision');
    assertTrue($defaults->getDistribution() === GenerationOptions::DISTRIBUTION_UNIFORM, 'default distribution');
    assertTrue($defaults->isUnique() === false, 'default unique');

    $capture = new class {
        public ?GenerationOptions $options = null;
    };

    $repository = new class implements RandomNumberRepositoryInterface {
        public ?RandomNumber $saved = null;

        public function save(RandomNumber $randomNumber): void
        {
            $this->saved = $randomNumber;
        }

        public function findById(RandomNumberId $id): ?RandomNumber
        {
            return null;
        }
    };

    $generator = new class($capture) implements RandomGeneratorInterface {
        public function __construct(private readonly object $capture)
        {
        }

        public function generate(GenerationOptions $options): int
        {
            $this->capture->options = $options;

            return $options->getMin();
        }
    };

    $useCase = new GenerateRandomNumberUseCase($repository, $generator);
    $dtoDefault = $useCase->execute();

    assertTrue($dtoDefault->number === RandomNumber::MIN_NUMBER, 'execute uses default options');
    assertTrue($capture->options instanceof GenerationOptions, 'options are passed into generator');
    assertTrue($repository->saved instanceof RandomNumber, 'entity is saved by use case');

    $dtoCustom = $useCase->executeWithOptions(GenerationOptions::integerRange(42, 42));
    assertTrue($dtoCustom->number === 42, 'executeWithOptions uses custom range');

    $realGenerator = new IntUniformRandomGenerator();
    $unsupported = new GenerationOptions(
        numberType: GenerationOptions::TYPE_FLOAT,
        min: 1,
        max: 10,
        precision: 2,
        distribution: GenerationOptions::DISTRIBUTION_UNIFORM,
        unique: false,
    );

    $thrown = false;
    try {
        $realGenerator->generate($unsupported);
    } catch (\LogicException) {
        $thrown = true;
    }
    assertTrue($thrown, 'int generator rejects unsupported float mode');

    echo "OK foundation_generation.php passed" . PHP_EOL;
} catch (\Throwable $e) {
    fwrite(STDERR, "FAILED foundation_generation.php: {$e->getMessage()}" . PHP_EOL);
    exit(1);
}

function assertTrue(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException("Assertion failed: {$label}");
    }
}
