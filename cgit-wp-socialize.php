<?php

/*

Plugin Name: Castlegate IT WP Socialize
Plugin URI: http://github.com/castlegateit/cgit-wp-socialize
Description: Simple social network sharing URLs, links, and widgets.
Version: 2.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

// Constants
define('CGIT_SOCIALIZE_PLUGIN', __FILE__);

// Load plugin
require_once __DIR__ . '/classes/autoload.php';

// Initialization
\Cgit\Socialize\Plugin::getInstance();
