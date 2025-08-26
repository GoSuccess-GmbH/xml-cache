<?php
/**
 * Enqueue admin and editor assets.
 *
 * @package xml-cache
 */

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Repository;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;

/**
 * Repository to enqueue scripts/styles.
 */
final class Script_Repository {
	/**
	 * Constructor.
	 *
	 * @param Plugin_Configuration $plugin_configuration Plugin config.
	 * @param Menu_Repository      $menu_repository      Menu repository.
	 */
	public function __construct(
		private Plugin_Configuration $plugin_configuration,
		private Menu_Repository $menu_repository
	) {}

	/**
	 * Enqueue admin assets on plugin pages.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function admin_scripts( string $hook_suffix ): void {
		if ( $hook_suffix !== $this->menu_repository::$hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/css/admin.css',
			array( 'wp-components', 'wp-block-editor' ),
			filemtime( $this->plugin_configuration->get_path() . 'assets/css/admin.css' )
		);

		$asset_file = include $this->plugin_configuration->get_path() . 'assets/admin/index.asset.php';

		wp_enqueue_script(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/admin/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}

	/**
	 * Enqueue block editor assets.
	 */
	public function block_editor_assets(): void {
		$asset_file = include $this->plugin_configuration->get_path() . 'assets/settings-panel/index.asset.php';

		wp_enqueue_script(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/settings-panel/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}
}
