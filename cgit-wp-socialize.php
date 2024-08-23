<?php

/**
 * Plugin Name:  Castlegate IT WP Socialize
 * Plugin URI:   https://github.com/castlegateit/cgit-wp-socialize
 * Description:  Simple social network sharing URLs.
 * Version:      3.0.1
 * Requires PHP: 8.2
 * Author:       Castlegate IT
 * Author URI:   https://www.castlegateit.co.uk/
 * License:      MIT
 * Update URI:   https://github.com/castlegateit/cgit-wp-socialize
 */

if (!defined('ABSPATH')) {
    wp_die('Access denied');
}

define('CGIT_WP_SOCIALIZE_VERSION', '3.0.1');
define('CGIT_WP_SOCIALIZE_PLUGIN_FILE', __FILE__);
define('CGIT_WP_SOCIALIZE_PLUGIN_DIR', __DIR__);

require_once __DIR__ . '/vendor/autoload.php';
