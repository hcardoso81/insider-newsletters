<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Infrastructure\WordPress;

use InsiderLatam\Newsletter\Domain\Model\PostType;
use InsiderLatam\Newsletter\Infrastructure\Logging\ErrorLogger;

final class AcfFieldGroupRegistrar
{
    private ErrorLogger $logger;

    public function __construct(ErrorLogger $logger)
    {
        $this->logger = $logger;
    }

    public function registerHooks(): void
    {
        add_action('acf/init', [$this, 'registerFieldGroups']);
    }

    public function registerFieldGroups(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            $this->logger->warning('ACF is not available. Newsletter field groups were not registered.');
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_insider_newsletter_layout',
            'title' => 'Newsletter Layout',
            'fields' => [
                [
                    'key' => 'field_insider_news_header_image',
                    'label' => 'Imagen de cabecera',
                    'name' => 'news_header_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_insider_link_header',
                    'label' => 'Link de cabecera',
                    'name' => 'link_header',
                    'type' => 'url',
                ],
                [
                    'key' => 'field_insider_news_description',
                    'label' => 'Descripcion',
                    'name' => 'news_description',
                    'type' => 'wysiwyg',
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_insider_post_1_column',
                    'label' => 'Bloque principal',
                    'name' => 'post_1_column',
                    'type' => 'relationship',
                    'post_type' => ['post', PostType::BANNER_HORIZONTAL],
                    'filters' => ['search', 'post_type', 'taxonomy'],
                    'elements' => ['featured_image'],
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_insider_post_2_columns_posts',
                    'label' => 'Contenido columna principal',
                    'name' => 'post_2_columns_posts',
                    'type' => 'relationship',
                    'post_type' => ['post', PostType::BANNER_HORIZONTAL],
                    'filters' => ['search', 'post_type', 'taxonomy'],
                    'elements' => ['featured_image'],
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_insider_post_2_columns_banners',
                    'label' => 'Banners columna lateral',
                    'name' => 'post_2_columns_banners',
                    'type' => 'relationship',
                    'post_type' => [PostType::BANNER_VERTICAL],
                    'filters' => ['search', 'post_type', 'taxonomy'],
                    'elements' => ['featured_image'],
                    'return_format' => 'id',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => PostType::NEWSLETTER,
                    ],
                ],
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_insider_newsletter_post_overrides',
            'title' => 'Newsletter Overrides',
            'fields' => [
                [
                    'key' => 'field_insider_newsletter_override_active',
                    'label' => 'Usar titulo y descripcion para newsletter',
                    'name' => 'newslettter_title_description_active',
                    'type' => 'true_false',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_insider_post_newsletter_title',
                    'label' => 'Titulo para newsletter',
                    'name' => 'post_newsletter_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_insider_post_newsletter_description',
                    'label' => 'Descripcion para newsletter',
                    'name' => 'post_newsletter_description',
                    'type' => 'textarea',
                    'rows' => 4,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ],
                ],
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_insider_newsletter_vertical_banner',
            'title' => 'Banner Vertical',
            'fields' => [
                [
                    'key' => 'field_insider_banner_vertical_link',
                    'label' => 'Link del banner',
                    'name' => 'banner_vertical_link',
                    'type' => 'url',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => PostType::BANNER_VERTICAL,
                    ],
                ],
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_insider_newsletter_horizontal_banner',
            'title' => 'Banner Horizontal',
            'fields' => [
                [
                    'key' => 'field_insider_banner_horizontal_link',
                    'label' => 'Link del banner',
                    'name' => 'banner_horizonal_link',
                    'type' => 'url',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => PostType::BANNER_HORIZONTAL,
                    ],
                ],
            ],
        ]);
    }
}
