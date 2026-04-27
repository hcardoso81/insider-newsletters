<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Infrastructure\WordPress;

use InsiderLatam\Newsletter\Domain\Model\PostType;

final class PostTypeRegistrar
{
    public function registerHooks(): void
    {
        add_action('init', [$this, 'registerPostTypes']);
    }

    public function registerPostTypes(): void
    {
        register_post_type(PostType::NEWSLETTER, [
            'labels' => $this->buildLabels('Newsletters', 'Newsletter', 'Anadir nuevo', 'Anadir nuevo Newsletter'),
            'description' => __('Gestion de newsletters.', 'insider-newsletters'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'newsletter'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-email-alt',
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
            'taxonomies' => ['category', 'post_tag'],
            'show_in_rest' => true,
        ]);

        register_post_type(PostType::BANNER_VERTICAL, [
            'labels' => $this->buildLabels('Banners Verticales', 'Banner Vertical', 'Anadir nuevo', 'Anadir nuevo Banner Vertical'),
            'description' => __('Gestion de banners verticales.', 'insider-newsletters'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'banner-verticales'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-align-right',
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
            'taxonomies' => ['category', 'post_tag'],
            'show_in_rest' => true,
        ]);

        register_post_type(PostType::BANNER_HORIZONTAL, [
            'labels' => $this->buildLabels('Banners Horizontales', 'Banner Horizontal', 'Anadir nuevo', 'Anadir nuevo Banner Horizontal'),
            'description' => __('Gestion de banners horizontales.', 'insider-newsletters'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'banner-horizontal'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-align-wide',
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
            'taxonomies' => ['category', 'post_tag'],
            'show_in_rest' => true,
        ]);
    }

    private function buildLabels(string $plural, string $singular, string $addNew, string $addNewItem): array
    {
        return [
            'name' => _x($plural, 'post type general name', 'insider-newsletters'),
            'singular_name' => _x($singular, 'post type singular name', 'insider-newsletters'),
            'menu_name' => _x($plural, 'admin menu', 'insider-newsletters'),
            'name_admin_bar' => _x($singular, 'add new on admin bar', 'insider-newsletters'),
            'add_new' => _x($addNew, strtolower($singular), 'insider-newsletters'),
            'add_new_item' => __($addNewItem, 'insider-newsletters'),
            'new_item' => sprintf(__('Nuevo %s', 'insider-newsletters'), $singular),
            'edit_item' => sprintf(__('Editar %s', 'insider-newsletters'), $singular),
            'view_item' => sprintf(__('Ver %s', 'insider-newsletters'), $singular),
            'all_items' => sprintf(__('Todos los %s', 'insider-newsletters'), $plural),
            'search_items' => sprintf(__('Buscar %s', 'insider-newsletters'), $plural),
            'not_found' => sprintf(__('No se encontraron %s.', 'insider-newsletters'), strtolower($plural)),
            'not_found_in_trash' => sprintf(__('No se encontraron %s en la papelera.', 'insider-newsletters'), strtolower($plural)),
        ];
    }
}
