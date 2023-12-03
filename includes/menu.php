<?php

namespace GoSuccess\XML_Cache;

class Menu {
    public static string $hook_suffix = '';

    public function __construct() {
        \add_action( 'admin_menu', [ $this, 'menu' ] );

        \add_filter(
            hook_name: 'plugin_action_links_' . \plugin_basename( XML_CACHE_FILE ),
            callback: [ $this, 'add_action_links' ],
            accepted_args: 4
        );
    }

    public function menu(): void {
        $hook_suffix = \add_submenu_page(
            parent_slug: 'tools.php',
            page_title: __( 'XML Cache', 'xml-cache' ),
            menu_title: __( 'XML Cache', 'xml-cache' ),
            capability: 'manage_options',
            menu_slug: 'xml_cache',
            callback: [ new Admin(), 'settings' ]
        );

        if ( false !== $hook_suffix ) {
            self::$hook_suffix = $hook_suffix;
        }
    }

    public function add_action_links( array $actions, string $plugin_file, array $plugin_data, string $context ): array {
        $actions[] = sprintf(
            '<a href="%s">%s</a>',
            \esc_url( \admin_url( 'tools.php?page=xml_cache' ) ),
            \esc_html__( 'Settings' )
        );

        return $actions;
    }

    public static function is_displayed(): bool {
        $screen = \get_current_screen();

        if ( str_contains( $screen->id, self::$hook_suffix ) ) {
            return true;
        }

        return false;
    }
}
