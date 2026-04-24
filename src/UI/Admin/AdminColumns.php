<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Admin;

use InsiderLatam\Newsletter\Domain\Model\PostType;

final class AdminColumns
{
    public function registerHooks(): void
    {
        add_filter('manage_' . PostType::BANNER_VERTICAL . '_posts_columns', [$this, 'removeUnusedColumns']);
        add_filter('manage_' . PostType::BANNER_HORIZONTAL . '_posts_columns', [$this, 'removeUnusedColumns']);
        add_filter('manage_' . PostType::NEWSLETTER . '_posts_columns', [$this, 'removeUnusedColumns']);
    }

    public function removeUnusedColumns(array $columns): array
    {
        unset($columns['author'], $columns['categories'], $columns['tags']);

        return $columns;
    }
}
