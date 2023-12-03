<?php

namespace GoSuccess\XML_Cache;

/**
 * Plugin Name:       XML Cache
 * Description:       Generates an XML sitemap for cache plugins.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            GoSuccess
 * Author URI:        https://gosuccess.io
 * Text Domain:       xml-cache
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if( ! defined( 'ABSPATH' ) ) {
    exit();
}

define( 'XML_CACHE_FILE', __FILE__ );
define( 'XML_CACHE_URL', \plugin_dir_url( XML_CACHE_FILE ) );
define( 'XML_CACHE_PATH', \trailingslashit( \plugin_dir_path( XML_CACHE_FILE ) ) );

require_once XML_CACHE_PATH . 'includes/plugin.php';

new Plugin();
