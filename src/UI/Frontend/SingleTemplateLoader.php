<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Frontend;

use InsiderLatam\Newsletter\Domain\Model\PostType;
use InsiderLatam\Newsletter\Domain\Service\NewsletterRenderer;

final class SingleTemplateLoader
{
    private NewsletterRenderer $renderer;

    public function __construct(NewsletterRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function registerHooks(): void
    {
        add_filter('template_include', [$this, 'loadSingleTemplate']);
    }

    public function loadSingleTemplate(string $template): string
    {
        if (! is_singular(PostType::NEWSLETTER)) {
            return $template;
        }

        return INSIDER_NEWSLETTERS_PATH . 'templates/single-newsletter.php';
    }
}
