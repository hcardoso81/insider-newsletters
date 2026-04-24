<?php
/**
 * Plugin Name: Insider Newsletters
 * Description: Centraliza la gestion de newsletters y banners con exportacion HTML.
 * Version: 1.0.0
 * Author: Hernan Cardoso
 * Author URI: https://www.linkedin.com/in/cardosohernan/
 * Text Domain: insider-newsletters
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('INSIDER_NEWSLETTERS_VERSION', '1.0.0');
define('INSIDER_NEWSLETTERS_FILE', __FILE__);
define('INSIDER_NEWSLETTERS_PATH', plugin_dir_path(__FILE__));
define('INSIDER_NEWSLETTERS_URL', plugin_dir_url(__FILE__));

require_once INSIDER_NEWSLETTERS_PATH . 'src/Bootstrap/Autoloader.php';

\InsiderLatam\Newsletter\Bootstrap\Autoloader::register();
\InsiderLatam\Newsletter\Bootstrap\Plugin::boot();

register_activation_hook(__FILE__, [\InsiderLatam\Newsletter\Bootstrap\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\InsiderLatam\Newsletter\Bootstrap\Plugin::class, 'deactivate']);
