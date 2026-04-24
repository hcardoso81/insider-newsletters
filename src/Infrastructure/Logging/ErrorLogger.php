<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Infrastructure\Logging;

use Throwable;

final class ErrorLogger
{
    private const LOG_DIRECTORY = 'logs';
    private const LOG_PREFIX = 'plugin-errors-';

    public function registerHooks(): void
    {
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function ensureLogDirectory(): void
    {
        $directory = $this->getLogDirectory();

        if (is_dir($directory)) {
            return;
        }

        if (function_exists('wp_mkdir_p')) {
            wp_mkdir_p($directory);
            return;
        }

        mkdir($directory, 0755, true);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function exception(Throwable $throwable, array $context = []): void
    {
        $context['exception'] = [
            'type' => $throwable::class,
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTraceAsString(),
        ];

        $this->write('EXCEPTION', 'Unhandled plugin exception.', $context);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();

        if (! is_array($error) || ! $this->isFatalError((int) $error['type'])) {
            return;
        }

        if (! isset($error['file']) || ! $this->isPluginFile((string) $error['file'])) {
            return;
        }

        $this->write('FATAL', 'Fatal PHP error detected.', [
            'type' => (int) $error['type'],
            'message' => (string) $error['message'],
            'file' => (string) $error['file'],
            'line' => (int) $error['line'],
        ]);
    }

    private function write(string $level, string $message, array $context = []): void
    {
        $this->ensureLogDirectory();

        $timestamp = function_exists('current_time')
            ? (string) current_time('mysql')
            : date('Y-m-d H:i:s');

        $entry = sprintf('[%s] %s: %s', $timestamp, $level, $message);

        if ($context !== []) {
            $encodedContext = function_exists('wp_json_encode')
                ? wp_json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (is_string($encodedContext) && $encodedContext !== '') {
                $entry .= ' ' . $encodedContext;
            }
        }

        $entry .= PHP_EOL;

        error_log($entry, 3, $this->getLogFilePath());
    }

    private function getLogDirectory(): string
    {
        return INSIDER_NEWSLETTERS_PATH . self::LOG_DIRECTORY;
    }

    private function getLogFilePath(): string
    {
        return $this->getLogDirectory() . DIRECTORY_SEPARATOR . self::LOG_PREFIX . date('Y-m-d') . '.log';
    }

    private function isFatalError(int $type): bool
    {
        return in_array($type, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true);
    }

    private function isPluginFile(string $filePath): bool
    {
        return strpos(wp_normalize_path($filePath), wp_normalize_path(INSIDER_NEWSLETTERS_PATH)) === 0;
    }
}
