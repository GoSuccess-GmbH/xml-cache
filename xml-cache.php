<?php
/**
 * Plugin Name:       XML Cache
 * Description:       Generates an XML sitemap for cache plugins.
 * Version:           2.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            GoSuccess
 * Author URI:        https://gosuccess.io
 * Text Domain:       xml-cache
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package xml-cache
 */

declare(strict_types=1);

use GoSuccess\XML_Cache\Plugin;

defined( 'ABSPATH' ) || exit;

define( 'XML_CACHE_FILE', __FILE__ );

require_once __DIR__ . '/vendor/autoload.php';

Plugin::get_instance();
