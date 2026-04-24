<?php

declare(strict_types=1);

namespace InsiderLatam\Newsletter\Domain\Service;

use InsiderLatam\Newsletter\Domain\Model\PostType;
use InsiderLatam\Newsletter\Domain\Support\Acf;
use InsiderLatam\Newsletter\Infrastructure\Logging\ErrorLogger;
use WP_Post;

final class NewsletterRenderer
{
    private ErrorLogger $logger;

    public function __construct(ErrorLogger $logger)
    {
        $this->logger = $logger;
    }

    public function render(int $newsletterId): string
    {
        try {
            $newsletter = get_post($newsletterId);

            if (! $newsletter instanceof WP_Post || $newsletter->post_type !== PostType::NEWSLETTER) {
                $this->logger->warning('Newsletter render requested with an invalid post.', [
                    'newsletter_id' => $newsletterId,
                ]);

                return '';
            }

            $context = $this->buildContext($newsletter);

            ob_start();
            $view = $this;
            include INSIDER_NEWSLETTERS_PATH . 'templates/newsletter.php';

            return (string) ob_get_clean();
        } catch (\Throwable $throwable) {
            $this->logger->exception($throwable, [
                'newsletter_id' => $newsletterId,
                'operation' => 'render_newsletter',
            ]);

            return '';
        }
    }

    public function buildContext(WP_Post $newsletter): array
    {
        $imageHeader = Acf::getField('news_header_image', $newsletter->ID);

        return [
            'newsletter' => $newsletter,
            'image_header' => $imageHeader,
            'image_header_url' => is_array($imageHeader) && isset($imageHeader['ID']) ? wp_get_attachment_image_url((int) $imageHeader['ID'], 'full') : '',
            'image_header_alt' => is_array($imageHeader) && ! empty($imageHeader['alt']) ? (string) $imageHeader['alt'] : 'Header image',
            'link_header' => (string) Acf::getField('link_header', $newsletter->ID, ''),
            'description' => (string) Acf::getField('news_description', $newsletter->ID, ''),
            'posts_highlight' => $this->normalizeIds(Acf::getField('post_1_column', $newsletter->ID, [])),
            'posts_vertical' => $this->normalizeIds(Acf::getField('post_2_columns_banners', $newsletter->ID, [])),
            'posts_embedded' => $this->normalizeIds(Acf::getField('post_2_columns_posts', $newsletter->ID, [])),
        ];
    }

    public function createNodePostVertical(int $postId): void
    {
        $imageAttributes = $this->getImageAttributes($postId);
        $link = (string) Acf::getField('banner_vertical_link', $postId, '');
        ?>
        <tr>
            <td style="vertical-align: top; padding: 0;">
                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo esc_url($imageAttributes['image_url']); ?>" alt="<?php echo esc_attr($imageAttributes['image_alt']); ?>" style="width: 100%; margin-bottom: 10px; display: block; height: auto;">
                </a>
            </td>
        </tr>
        <?php
    }

    public function createNodeSeparator(): void
    {
        ?>
        <tr>
            <td class="section-padding" style="padding-left: 10px; padding-right: 10px;">
                <div class="divider" style="width: 100%; height: 2px; background-color: #604c8d; margin: 25px 0;"></div>
            </td>
        </tr>
        <?php
    }

    public function createNodeSeparatorEmbedded(): void
    {
        ?>
        <tr>
            <td class="section-padding" style="padding-left: 10px; padding-right: 10px;">
                <div class="divider-embedded" style="width: 95%; height: 2px; background-color: #604c8d; margin-top: 25px; margin-bottom: 25px;"></div>
            </td>
        </tr>
        <?php
    }

    public function createNodePostEmbedded(int $postId): void
    {
        $post = get_post($postId);

        if ($post instanceof WP_Post) {
            if ($post->post_type === 'post') {
                $this->createNodePostFeaturedEmbedded($post);
            } else {
                $this->createNodePostHorizontalEmbedded($post);
            }
        }

        $this->createNodeSeparatorEmbedded();
    }

    public function createNodePostFeatured(WP_Post $post): void
    {
        $imageAttributes = $this->getImageAttributes($post->ID);
        $specialDataActive = (bool) Acf::getField('newslettter_title_description_active', $post->ID, false);
        ?>
        <tr>
            <td>
                <table role="presentation" class="two-column-row" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="two-column-cell section-padding" width="50%" valign="top" style="padding-left: 10px; padding-right: 10px;">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($imageAttributes['image_url']); ?>" alt="<?php echo esc_attr($imageAttributes['image_alt']); ?>" style="max-width: 100%; height: auto; display: block;"></a>
                        </td>
                        <td class="two-column-cell section-padding" width="50%" valign="top" style="padding-left: 10px; padding-right: 10px;">
                            <a class="link-reset" href="<?php echo esc_url(get_permalink($post->ID)); ?>" style="text-decoration: none;">
                                <h2 class="title-primary h1-mobile" style="margin-top: 0; color:#604c8d;font-size: 1.5em; font-family: Helvetica, Arial, sans-serif;"><?php echo wp_kses_post($this->getTitleForNewsletter($post->ID, $post->post_title, $specialDataActive)); ?></h2>
                            </a>
                            <p class="text-base text-mobile" style="color: #58595b; font-family: Helvetica, Arial, sans-serif; font-size: 15px; line-height: 20px; margin: 10px 0;">
                                <?php echo wp_kses_post($this->getExtractForNewsletter($post->ID, $this->getExcerptMaxLength($post->ID, 240), $specialDataActive)); ?>
                            </p>
                            <a class="newsletter-button" href="<?php echo esc_url(get_permalink($post->ID)); ?>" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #604c8d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-family: Helvetica, Arial, sans-serif;">+ info</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php
    }

    public function createNodePostFeaturedEmbedded(WP_Post $post): void
    {
        $imageAttributes = $this->getImageAttributes($post->ID);
        $specialDataActive = (bool) Acf::getField('newslettter_title_description_active', $post->ID, false);
        ?>
        <tr>
            <td>
                <table role="presentation" class="two-column-row" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="two-column-cell section-padding" width="50%" valign="top" style="padding-left: 10px; padding-right: 10px;">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($imageAttributes['image_url']); ?>" alt="<?php echo esc_attr($imageAttributes['image_alt']); ?>" style="max-width: 100%; height: auto; display: block;"></a>
                        </td>
                        <td class="two-column-cell section-padding" width="50%" valign="top" style="padding-left: 10px; padding-right: 10px;">
                            <a class="link-reset" href="<?php echo esc_url(get_permalink($post->ID)); ?>" style="text-decoration: none;">
                                <h2 class="title-secondary h2-mobile" style="margin-top: 0; color:#604c8d; font-size: 1.3em; font-family: Helvetica, Arial, sans-serif;"><?php echo wp_kses_post($this->getTitleForNewsletter($post->ID, $post->post_title, $specialDataActive)); ?></h2>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="section-padding" colspan="2" style="padding-left: 10px; padding-right: 10px;">
                            <p class="text-base text-mobile" style="color: #58595b; font-family: Helvetica, Arial, sans-serif; font-size: 15px; line-height: 20px; margin: 10px 0;">
                                <?php echo wp_kses_post($this->getExtractForNewsletter($post->ID, $this->getExcerptMaxLength($post->ID, 240), $specialDataActive)); ?>
                            </p>
                            <a class="newsletter-button" href="<?php echo esc_url(get_permalink($post->ID)); ?>" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #604c8d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-family: Helvetica, Arial, sans-serif;">+ info</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php
    }

    public function createNodePostHorizontal(WP_Post $post): void
    {
        $imageAttributes = $this->getImageAttributes($post->ID);
        $link = (string) Acf::getField('banner_horizonal_link', $post->ID, '');
        ?>
        <tr>
            <td class="mobile-padding section-padding" style="padding-left: 10px; padding-right: 10px;">
                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url($imageAttributes['image_url']); ?>" alt="<?php echo esc_attr($imageAttributes['image_alt']); ?>" style="width: 100%; height: auto; display: block;"></a>
            </td>
        </tr>
        <?php
    }

    public function createNodePostHorizontalEmbedded(WP_Post $post): void
    {
        $imageAttributes = $this->getImageAttributes($post->ID);
        $link = (string) Acf::getField('banner_horizonal_link', $post->ID, '');
        ?>
        <tr>
            <td class="section-padding" style="padding-left: 10px; padding-right: 10px;">
                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url($imageAttributes['image_url']); ?>" alt="<?php echo esc_attr($imageAttributes['image_alt']); ?>" style="width: 95%; height: auto; display: block;"></a>
            </td>
        </tr>
        <?php
    }

    public function createFooter(): void
    {
        ?>
        <tr>
            <td bgcolor="#636262" style="padding:10px;">
                <table role="presentation" align="center" bgcolor="#636262" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td width="650" style="padding: 10px;">
                                <p class="footer-text" style="margin:0; padding:0; text-align: center; color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size:13px; line-height:20px;">
                                    INSIDER LatAm - Prensa: <a class="footer-link" style="color:#FFFFFF; text-decoration: none;" href="mailto:prensa@insiderlatam.com">prensa@insiderlatam.com</a> | Pauta publicitaria: <a class="footer-link" style="color:#FFFFFF; text-decoration: none;" href="mailto:marketing@insiderlatam.com">marketing@insiderlatam.com</a>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
    }

    private function normalizeIds($items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(static function ($item): int {
            if ($item instanceof WP_Post) {
                return (int) $item->ID;
            }

            return (int) $item;
        }, $items)));
    }

    private function getNewExtract(int $id, string $extractPost): string
    {
        $extract = Acf::getField('post_newsletter_description', $id, '');

        return $extract !== '' ? (string) $extract : $extractPost;
    }

    private function getExtractForNewsletter(int $id, string $extract, bool $specialDataActive): string
    {
        return $specialDataActive ? $this->getNewExtract($id, $extract) : esc_html($extract);
    }

    private function getNewTitle(int $id, string $titlePost): string
    {
        $title = Acf::getField('post_newsletter_title', $id, '');

        return $title !== '' ? (string) $title : $titlePost;
    }

    private function getTitleForNewsletter(int $id, string $title, bool $specialDataActive): string
    {
        return $specialDataActive ? $this->getNewTitle($id, $title) : esc_html($title);
    }

    private function getExcerptMaxLength(int $id, int $charlength): string
    {
        $excerpt = (string) get_the_excerpt($id);
        $charlength++;

        if (mb_strlen($excerpt) <= $charlength) {
            return $excerpt;
        }

        $subex = mb_substr($excerpt, 0, $charlength - 5);
        $exwords = explode(' ', $subex);
        $lastWordLength = mb_strlen((string) end($exwords));
        $cut = -$lastWordLength;
        $result = $cut < 0 ? mb_substr($subex, 0, $cut) : $subex;

        return $result . '...';
    }

    private function getImageAttributes(int $postId): array
    {
        $postThumbnailId = get_post_thumbnail_id($postId);

        if (! $postThumbnailId) {
            return [
                'image_url' => '',
                'image_alt' => '',
            ];
        }

        $postThumbnailSrc = wp_get_attachment_image_src($postThumbnailId, 'large');
        $postThumbnailUrl = is_array($postThumbnailSrc) ? (string) $postThumbnailSrc[0] : '';
        $altText = (string) get_post_meta($postThumbnailId, '_wp_attachment_image_alt', true);

        if ($altText === '') {
            $attachmentPost = get_post($postThumbnailId);
            $altText = $attachmentPost instanceof WP_Post ? $attachmentPost->post_title : '';
        }

        return [
            'image_url' => $postThumbnailUrl,
            'image_alt' => $altText,
        ];
    }
}
