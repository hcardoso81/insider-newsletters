<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Admin;

final class RelationshipFieldQuery
{
    public function registerHooks(): void
    {
        add_filter('acf/fields/relationship/query/name=post_1_column', [$this, 'orderByNewest'], 10, 3);
        add_filter('acf/fields/relationship/query/name=post_2_columns_posts', [$this, 'orderByNewest'], 10, 3);
        add_filter('acf/fields/relationship/query/name=post_2_columns_banners', [$this, 'orderByNewest'], 10, 3);
    }

    public function orderByNewest(array $args): array
    {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';

        return $args;
    }
}
