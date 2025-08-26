<?php
/**
 * Activation tasks repository.
 *
 * @package xml-cache
 */

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Repository;

/**
 * Handles plugin activation logic.
 */
final class Activation_Repository {
	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Run on plugin activation.
	 */
	public function activation(): void {
		if ( Deactivation_Repository::is_running() ) {
			return;
		}

		add_option( 'xml_cache_settings', self::get_default_settings() );

		Rewrite_Rules_Repository::add_rewrite_rules();
		flush_rewrite_rules();
	}

	/**
	 * Default plugin settings.
	 *
	 * @return array Default settings structure.
	 */
	public static function get_default_settings(): array {
		return array(
			array(
				'posts_enabled'      => true,
				'categories_enabled' => true,
				'archives_enabled'   => true,
				'tags_enabled'       => true,
			),
			array(
				'is_posts_panel_open'      => false,
				'is_categories_panel_open' => false,
				'is_archives_panel_open'   => false,
				'is_tags_panel_open'       => false,
			),
		);
	}
}
