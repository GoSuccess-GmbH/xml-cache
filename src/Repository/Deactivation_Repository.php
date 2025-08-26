<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;

class Deactivation_Repository {
    private static string $plugin_basename;

    public function __construct(
        private Plugin_Configuration $plugin_configuration
    ) {
        self::$plugin_basename = $this->plugin_configuration->get_basename();
    }

    public function deactivation(): void {
		flush_rewrite_rules();
	}

    public static function is_running(): bool {
        // must be on the plugins screen
        if ( ! isset( $GLOBALS['pagenow'] ) || $GLOBALS['pagenow'] !== 'plugins.php' ) {
            return false;
        }

        // WP can send the action in action or action2 (top/bottom bulk selectors)
        $action = $_REQUEST['action'] ?? $_REQUEST['action2'] ?? '';
        $action = is_string( $action ) ? $action : '';

        // single deactivation: ?action=deactivate&plugin=<basename>
        if ( $action === 'deactivate' ) {
            $plugin = $_REQUEST['plugin'] ?? '';
            if ( is_array( $plugin ) ) {
                return false;
            }
            $plugin = urldecode( (string) $plugin );

            return $plugin === self::$plugin_basename;
        }

        // bulk deactivation: ?action=deactivate-selected&checked[]=<basename>...
        if ( $action === 'deactivate-selected' ) {
            $checked = $_REQUEST['checked'] ?? [];
            if ( ! is_array( $checked ) ) {
                return false;
            }

            foreach ( $checked as $p ) {
                if ( is_string( $p ) && urldecode( $p ) === self::$plugin_basename ) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
}
