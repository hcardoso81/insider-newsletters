<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Admin;

use InsiderLatam\Newsletter\Domain\Model\PostType;
use WP_Post;

final class OembedCacheGuard
{
    public function registerHooks(): void
    {
        add_action('wp_ajax_oembed-cache', [$this, 'shortCircuitForNewsletterPostTypes'], 0);
    }

    public function shortCircuitForNewsletterPostTypes(): void
    {
        $postId = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : 0;

        if (! $postId) {
            return;
        }

        if (! current_user_can('edit_post', $postId)) {
            return;
        }

        $post = get_post($postId);

        if (! $post instanceof WP_Post || ! $this->isNewsletterPostType($post->post_type)) {
            return;
        }

        wp_send_json_success();
    }

    private function isNewsletterPostType(string $postType): bool
    {
        return in_array($postType, [
            PostType::NEWSLETTER,
            PostType::BANNER_VERTICAL,
            PostType::BANNER_HORIZONTAL,
        ], true);
    }
}
