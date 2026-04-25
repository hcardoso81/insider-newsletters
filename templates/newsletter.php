<?php
/**
 * @var array $context
 * @var \InsiderLatam\Newsletter\Domain\Service\NewsletterRenderer $view
 */

declare(strict_types=1);

$imageHeader = $context['image_header'];
$imageHeaderUrl = $context['image_header_url'];
$imageHeaderAlt = $context['image_header_alt'];
$linkHeader = $context['link_header'];
$description = $context['description'];
$postsHighlight = $context['posts_highlight'];
$postsVertical = $context['posts_vertical'];
$postsEmbedded = $context['posts_embedded'];
$preheaderText = mb_substr(trim(wp_strip_all_tags((string) $description)), 0, 140);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Insider Newsletter</title>
    <style type="text/css">
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        table {
            border-collapse: collapse !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
            height: auto;
            max-width: 100%;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            background-color: #f4f4f4;
        }
        .email-container {
            width: 100%;
            max-width: 650px;
            background-color: #ffffff;
        }
        .section-padding {
            padding-left: 10px;
            padding-right: 10px;
        }
        .content-padding {
            padding: 10px;
        }
        .divider {
            width: 100%;
            height: 2px;
            background-color: #604c8d;
            margin: 25px 0 30px;
        }
        .divider-embedded {
            width: 95%;
            height: 2px;
            background-color: #604c8d;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .text-base {
            color: #58595b;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 15px;
            line-height: 20px;
        }
        .title-primary {
            margin: 0 0 10px;
            color: #604c8d;
            font-size: 24px;
            line-height: 29px;
            font-weight: 700;
            font-family: Helvetica, Arial, sans-serif;
        }
        .title-secondary {
            margin: 0 0 8px;
            color: #604c8d;
            font-size: 20px;
            line-height: 25px;
            font-weight: 700;
            font-family: Helvetica, Arial, sans-serif;
        }
        .link-reset {
            text-decoration: none;
        }
        .newsletter-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #604c8d;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-family: Helvetica, Arial, sans-serif;
        }
        .footer-text {
            margin: 0;
            padding: 0;
            text-align: center;
            color: #ffffff;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 20px;
        }
        .footer-link {
            color: #ffffff;
            text-decoration: none;
        }
        .preheader {
            display: none;
            font-size: 1px;
            color: #f4f4f4;
            line-height: 1px;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .mobile-full {
                width: 100% !important;
                display: block !important;
                box-sizing: border-box !important;
            }
            .two-column-row {
                display: block !important;
                width: 100% !important;
            }
            .two-column-cell {
                display: block !important;
                width: 100% !important;
                padding: 10px !important;
                box-sizing: border-box !important;
            }
            .h1-mobile {
                font-size: 23px !important;
                line-height: 28px !important;
                font-weight: 700 !important;
                margin-bottom: 10px !important;
            }
            .h2-mobile {
                font-size: 20px !important;
                line-height: 25px !important;
                font-weight: 700 !important;
                margin-bottom: 8px !important;
            }
            .text-mobile {
                font-size: 15px !important;
                line-height: 21px !important;
            }
            .sidebar-hide {
                display: none !important;
            }
            .mobile-padding {
                padding: 10px !important;
            }
            .divider-embedded {
                width: 100% !important;
            }
            .sidebar-column {
                width: 100% !important;
                display: block !important;
            }
            .content-column {
                width: 100% !important;
                display: block !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div class="preheader"><?php echo esc_html($preheaderText); ?></div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper" style="background-color: #f4f4f4;">
        <tr>
            <td align="center">
                <table role="presentation" class="email-container" border="0" cellpadding="0" cellspacing="0" width="650" style="width: 100%; max-width: 650px; background-color: #ffffff;">
                    <tr>
                        <td style="padding: 0; text-align: center; width: 100%;" colspan="2">
                            <?php if ($imageHeader) : ?>
                                <a href="<?php echo esc_url($linkHeader); ?>" target="_blank" rel="noopener noreferrer" style="display: block; width: 100%;">
                                    <img src="<?php echo esc_url($imageHeaderUrl); ?>" alt="<?php echo esc_attr($imageHeaderAlt); ?>" style="width: 100%; height: auto; display: block; max-width: 100%;">
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="mobile-padding content-padding" style="padding: 10px; width: 100%;">
                            <p class="text-base text-mobile" style="margin-top: 20px; margin-bottom: 10px; font-size: 15px; color: #58595b; line-height: 20px; font-family: Helvetica, Arial, sans-serif; max-width: 100%; word-wrap: break-word;">
                                <?php echo wp_kses_post($description); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="section-padding" style="padding-left: 10px; padding-right: 10px; width: 100%;">
                            <div class="divider" style="width: 100%; height: 2px; background-color: #604c8d; margin: 25px 0 30px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <?php foreach ($postsHighlight as $postId) : ?>
                                    <?php
                                    $post = get_post($postId);
                                    if ($post instanceof \WP_Post) {
                                        if ($post->post_type === 'post') {
                                            $view->createNodePostFeatured($post);
                                        } else {
                                            $view->createNodePostHorizontal($post);
                                        }
                                    }
                                    $view->createNodeSeparator();
                                    ?>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td class="content-column mobile-full" width="70%" valign="top">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <?php foreach ($postsEmbedded as $postEmbeddedId) : ?>
                                                <?php $view->createNodePostEmbedded($postEmbeddedId); ?>
                                            <?php endforeach; ?>
                                        </table>
                                    </td>
                                    <td class="sidebar-column sidebar-hide" width="30%" valign="top">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <?php foreach ($postsVertical as $postVerticalId) : ?>
                                                <?php $view->createNodePostVertical($postVerticalId); ?>
                                            <?php endforeach; ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php $view->createFooter(); ?>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
