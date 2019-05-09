<?php

/*

Plugin Name: Castlegate IT WP Socialize
Plugin URI: http://github.com/castlegateit/cgit-wp-socialize
Description: Simple social network sharing URLs, links, and widgets.
Version: 2.2
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: AGPLv3

*/

if (!defined('ABSPATH')) {
    wp_die('Access denied');
}

define('CGIT_SOCIALIZE_PLUGIN', __FILE__);

require_once __DIR__ . '/classes/autoload.php';

$plugin = new \Cgit\Socialize\Plugin();

do_action('cgit_socialize_loaded');
