<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Bootstrap;

use InsiderLatam\Newsletter\Domain\Service\NewsletterRenderer;
use InsiderLatam\Newsletter\Infrastructure\Logging\ErrorLogger;
use InsiderLatam\Newsletter\Infrastructure\WordPress\AcfFieldGroupRegistrar;
use InsiderLatam\Newsletter\Infrastructure\WordPress\PostTypeRegistrar;
use InsiderLatam\Newsletter\UI\Admin\AdminAssets;
use InsiderLatam\Newsletter\UI\Admin\AdminColumns;
use InsiderLatam\Newsletter\UI\Admin\ExportAction;
use InsiderLatam\Newsletter\UI\Admin\RelationshipFieldQuery;
use InsiderLatam\Newsletter\UI\Frontend\SingleTemplateLoader;

final class Plugin
{
    public static function boot(): void
    {
        $logger = new ErrorLogger();
        $logger->ensureLogDirectory();
        $logger->registerHooks();

        $renderer = new NewsletterRenderer($logger);

        (new PostTypeRegistrar())->registerHooks();
        (new AcfFieldGroupRegistrar($logger))->registerHooks();
        (new RelationshipFieldQuery())->registerHooks();
        (new AdminAssets())->registerHooks();
        (new AdminColumns())->registerHooks();
        (new ExportAction($renderer, $logger))->registerHooks();
        (new SingleTemplateLoader($renderer))->registerHooks();
    }

    public static function activate(): void
    {
        (new ErrorLogger())->ensureLogDirectory();
        (new PostTypeRegistrar())->registerPostTypes();
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}
