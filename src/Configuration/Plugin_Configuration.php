<?php
/**
 * Plugin configuration value object.
 *
 * @package xml-cache
 */

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Configuration;

/**
 * Class PluginConfiguration
 *
 * Provides configuration settings for the XML Cache plugin, including file path, directory path,
 * URL, slug, and admin URL.
 */
final class Plugin_Configuration {

	/**
	 * Constructor.
	 *
	 * @param string $file  Plugin file path.
	 * @param string $slug  Plugin slug used for admin pages and URLs.
	 * @param string $title Plugin title.
	 */
	public function __construct(
		/**
		 * Plugin file path.
		 */
		private string $file,
		/**
		 * Plugin slug used for admin pages and URLs.
		 */
		private string $slug,
		/**
		 * Plugin title.
		 */
		private string $title,
	) {}

	/**
	 * Returns the plugin title.
	 *
	 * @return string The plugin title.
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Returns the plugin file path.
	 *
	 * @return string The plugin file path.
	 */
	public function get_file(): string {
		return $this->file;
	}

	/**
	 * Returns the plugin basename.
	 *
	 * @return string The plugin basename.
	 */
	public function get_basename(): string {
		return plugin_basename( $this->get_file() );
	}

	/**
	 * Returns the plugin directory path.
	 *
	 * @return string The plugin directory path.
	 */
	public function get_path(): string {
		return plugin_dir_path( $this->get_file() );
	}

	/**
	 * Returns the plugin URL.
	 *
	 * @return string The plugin URL.
	 */
	public function get_url(): string {
		return plugin_dir_url( $this->get_file() );
	}

	/**
	 * Returns the plugin slug.
	 *
	 * @return string The plugin slug.
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Returns the admin URL for the XML Cache plugin settings page.
	 *
	 * @return string The admin URL.
	 */
	public function get_admin_url(): string {
		return esc_url(
			admin_url( (string) 'tools.php?page=' . $this->get_slug() )
		);
	}
}
