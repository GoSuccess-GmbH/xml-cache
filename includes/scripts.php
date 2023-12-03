<?php

namespace GoSuccess\XML_Cache;

class Scripts {
    public function __construct() {
        \add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
        \add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );
    }

    public function admin_scripts( string $hook_suffix ): void {
        if ( $hook_suffix !== Menu::$hook_suffix ) {
            return;
        }

        \wp_enqueue_style(
            handle: 'xml-cache',
            src: XML_CACHE_URL . 'assets/css/admin.css',
            deps: ['wp-components', 'wp-block-editor'],
            ver: \wp_rand(1, 9999999999)
        );

        $asset_file = include( XML_CACHE_PATH . 'assets/admin/index.asset.php');

        \wp_enqueue_script(
            handle: 'xml-cache',
            src: XML_CACHE_URL . 'assets/admin/index.js',
            deps: $asset_file['dependencies'],
            ver: $asset_file['version']
        );
    }

    public function block_editor_assets(): void {
        $asset_file = include( XML_CACHE_PATH . 'assets/settings-panel/index.asset.php');

        \wp_enqueue_script(
            handle: 'xml-cache',
            src: XML_CACHE_URL . 'assets/settings-panel/index.js',
            deps: $asset_file['dependencies'],
            ver: $asset_file['version']
        );
    }
}
