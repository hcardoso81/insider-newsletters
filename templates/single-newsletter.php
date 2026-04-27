<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$postId = get_queried_object_id();

if (! $postId) {
    status_header(404);
    exit;
}

$logger = new \InsiderLatam\Newsletter\Infrastructure\Logging\ErrorLogger();
$logger->ensureLogDirectory();

echo (new \InsiderLatam\Newsletter\Domain\Service\NewsletterRenderer($logger))->render((int) $postId);
