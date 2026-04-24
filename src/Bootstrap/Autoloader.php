<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Bootstrap;

final class Autoloader
{
    private const PREFIX = 'InsiderLatam\\Newsletter\\';

    public static function register(): void
    {
        \spl_autoload_register([self::class, 'autoload']);
    }

    private static function autoload(string $class): void
    {
        if (\strpos($class, self::PREFIX) !== 0) {
            return;
        }

        $relativeClass = \substr($class, \strlen(self::PREFIX));
        $relativePath = \str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        $file = INSIDER_NEWSLETTERS_PATH . 'src/' . $relativePath;

        if (\is_readable($file)) {
            require_once $file;
        }
    }
}
