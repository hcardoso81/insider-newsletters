<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\UI\Admin;

use InsiderLatam\Newsletter\Domain\Model\PostType;
use InsiderLatam\Newsletter\Domain\Service\NewsletterRenderer;
use InsiderLatam\Newsletter\Infrastructure\Logging\ErrorLogger;
use WP_Post;

final class ExportAction
{
    private NewsletterRenderer $renderer;
    private ErrorLogger $logger;

    public function __construct(NewsletterRenderer $renderer, ErrorLogger $logger)
    {
        $this->renderer = $renderer;
        $this->logger = $logger;
    }

    public function registerHooks(): void
    {
        add_filter('post_row_actions', [$this, 'addExportLink'], 10, 2);
        add_action('admin_post_insider_newsletter_export', [$this, 'handleExport']);
    }

    public function addExportLink(array $actions, WP_Post $post): array
    {
        if ($post->post_type !== PostType::NEWSLETTER) {
            return $actions;
        }

        $exportUrl = wp_nonce_url(
            add_query_arg(
                [
                    'action' => 'insider_newsletter_export',
                    'post_id' => $post->ID,
                ],
                admin_url('admin-post.php')
            ),
            'insider_newsletter_export_' . $post->ID
        );

        $actions['insider_newsletter_export'] = '<a href="' . esc_url($exportUrl) . '">' . esc_html__('Exportar codigo', 'insider-newsletters') . '</a>';

        return $actions;
    }

    public function handleExport(): void
    {
        $postId = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;

        try {
            if (! $postId || ! current_user_can('edit_post', $postId)) {
                $this->logger->warning('Unauthorized newsletter export attempt.', [
                    'post_id' => $postId,
                    'user_id' => get_current_user_id(),
                ]);

                wp_die(esc_html__('No tienes permisos para exportar este newsletter.', 'insider-newsletters'));
            }

            check_admin_referer('insider_newsletter_export_' . $postId);

            $post = get_post($postId);

            if (! $post instanceof WP_Post || $post->post_type !== PostType::NEWSLETTER) {
                $this->logger->warning('Newsletter export requested for a missing or invalid post.', [
                    'post_id' => $postId,
                ]);

                wp_die(esc_html__('El newsletter solicitado no existe.', 'insider-newsletters'));
            }

            $content = $this->renderer->render($postId);

            if ($content === '') {
                $this->logger->error('Newsletter export failed because renderer returned empty content.', [
                    'post_id' => $postId,
                ]);

                wp_die(esc_html__('No se pudo generar el codigo del newsletter.', 'insider-newsletters'));
            }

            nocache_headers();
            header('Content-Description: File Transfer');
            header('Content-Type: text/html; charset=UTF-8');
            header('Content-Disposition: attachment; filename=newsletter_code_' . $postId . '.html');
            header('Content-Length: ' . strlen($content));

            echo $content;
            exit;
        } catch (\Throwable $throwable) {
            $this->logger->exception($throwable, [
                'post_id' => $postId,
                'operation' => 'export_newsletter',
            ]);

            wp_die(esc_html__('Ocurrio un error al exportar el newsletter. Revisa la carpeta logs del plugin.', 'insider-newsletters'));
        }
    }
}
