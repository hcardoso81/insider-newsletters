<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Admin;

use InsiderLatam\Newsletter\Domain\Model\PostType;

final class AdminAssets
{
    public function registerHooks(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_head', [$this, 'printRelationshipCss']);
    }

    public function enqueueScripts(string $hookSuffix): void
    {
        if (! is_admin() || ! in_array($hookSuffix, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (! $screen || ! in_array($screen->post_type, [PostType::NEWSLETTER, PostType::BANNER_VERTICAL, PostType::BANNER_HORIZONTAL], true)) {
            return;
        }

        wp_enqueue_script(
            'insider-newsletters-acf-filter',
            INSIDER_NEWSLETTERS_URL . 'assets/js/acf-custom-filter.js',
            ['jquery'],
            INSIDER_NEWSLETTERS_VERSION,
            true
        );
    }

    public function printRelationshipCss(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (! $screen || ! in_array($screen->post_type, [PostType::NEWSLETTER, PostType::BANNER_VERTICAL, PostType::BANNER_HORIZONTAL], true)) {
            return;
        }

        echo '<style>.acf-relationship .list{margin:0;padding:5px;height:500px!important;overflow:auto;}</style>';
    }
}
