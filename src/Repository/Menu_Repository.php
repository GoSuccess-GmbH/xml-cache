<?php
/**
 * Menu repository for admin menu integration.
 *
 * @package   GoSuccess\XML_Cache
 */

// phpcs:disable WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;

/**
 * Class MenuRepository
 *
 * Handles the menu management for the XML_Cache plugin.
 */
final class Menu_Repository {

	/**
	 * Static variable to hold the hook suffix for the menu.
	 *
	 * @var string
	 */
	public static string $hook_suffix = '';

	/**
	 * Constructor to initialize the menu repository.
	 *
	 * @param Plugin_Configuration $plugin_configuration The plugin configuration instance.
	 */
	public function __construct(
		private Plugin_Configuration $plugin_configuration
	) {}

	/**
	 * Registers the menu for the XML_Cache plugin.
	 *
	 * @return void
	 */
	public function menu(): void {
		$hook_suffix = add_submenu_page(
			'tools.php',
			__( 'XML Cache', 'xml-cache' ),
			__( 'XML Cache', 'xml-cache' ),
			'manage_options',
			$this->plugin_configuration->get_slug(),
			function (): void {
				if ( self::is_displayed() ) {
					echo '<xml-cache></xml-cache>';
				}
			}
		);

		if ( false !== $hook_suffix ) {
			self::$hook_suffix = $hook_suffix;
		}
	}

	/**
	 * Adds action links to the plugin's action links.
	 *
	 * @param array  $actions     The existing action links.
	 * @param string $plugin_file The plugin file name.
	 * @param array  $plugin_data The plugin data.
	 * @param string $context     The context of the action links.
	 * @return array The modified action links.
	 */
	public function add_action_links( array $actions, string $plugin_file, array $plugin_data, string $context ): array {
		// Mark currently unused parameters as used for PHPCS.
		unset( $plugin_file, $plugin_data, $context );
		$actions[] = sprintf(
			'<a href="%s">%s</a>',
			$this->plugin_configuration->get_admin_url(),
			esc_html__( 'Settings', 'xml-cache' )
		);

		return $actions;
	}

	/**
	 * Checks if the menu is displayed on the current screen.
	 *
	 * @return bool True if the menu is displayed, false otherwise.
	 */
	public static function is_displayed(): bool {
		return str_contains(
			( get_current_screen() )->id,
			self::$hook_suffix
		);
	}
}
