<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;

final class Script_Repository {
    public function __construct(
        private Plugin_Configuration $plugin_configuration,
        private Menu_Repository $menu_repository
    ) {}

    public function admin_scripts( string $hook_suffix ): void {
		if ( $hook_suffix !== $this->menu_repository::$hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/css/admin.css',
			array( 'wp-components', 'wp-block-editor' )
		);

		$asset_file = include $this->plugin_configuration->get_path() . 'assets/admin/index.asset.php';

		wp_enqueue_script(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/admin/index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);
	}

	public function block_editor_assets(): void {
		$asset_file = include $this->plugin_configuration->get_path() . 'assets/settings-panel/index.asset.php';

		wp_enqueue_script(
			'xml-cache',
			$this->plugin_configuration->get_url() . 'assets/settings-panel/index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);
	}
}
