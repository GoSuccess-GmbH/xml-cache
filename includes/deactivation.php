<?php

namespace GoSuccess\XML_Cache;

class Deactivation {
    public function __construct() {
        \register_deactivation_hook( XML_CACHE_FILE, [ $this, 'deactivation' ] );
    }

    public function deactivation(): void {
        \flush_rewrite_rules();
    }

    public static function is_running(): bool {
        $plugin = \plugin_basename( XML_CACHE_FILE );

        return isset( $GLOBALS['pagenow'] )
                && 'plugins.php' === $GLOBALS['pagenow']
                && isset( $_REQUEST['action'] )
                && 'deactivate' === $_REQUEST['action']
                && isset( $_REQUEST['plugin'] )
                && $plugin === $_REQUEST['plugin'];
    }
}
