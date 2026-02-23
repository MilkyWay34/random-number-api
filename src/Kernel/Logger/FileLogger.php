<?php

declare(strict_types=1);

namespace App\Kernel\Logger;

/**
 * Логгер, записывающий сообщения в файл.
 *
 * Формат строки: [YYYY-MM-DD HH:MM:SS] LEVEL: Сообщение {"context":"data"}
 */
final class FileLogger implements LoggerInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureDirectoryExists();
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    private function log(string $level, string $message, array $context): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context)
            ? ' ' . json_encode($this->normalizeContext($context), JSON_UNESCAPED_UNICODE)
            : '';

        $line = "[{$timestamp}] {$level}: {$message}{$contextString}" . PHP_EOL;

        file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX);
    }

    private function normalizeContext(array $context): array
    {
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $e = $context['exception'];
            $context['exception'] = [
                'class'   => get_class($e),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ];

            if ($e->getPrevious() !== null) {
                $prev = $e->getPrevious();
                $context['exception']['previous'] = [
                    'class'   => get_class($prev),
                    'message' => $prev->getMessage(),
                    'file'    => $prev->getFile(),
                    'line'    => $prev->getLine(),
                ];
            }
        }

        return $context;
    }

    private function ensureDirectoryExists(): void
    {
        $dir = dirname($this->filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
