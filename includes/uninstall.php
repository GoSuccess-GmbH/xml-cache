<?php

namespace GoSuccess\XML_Cache;

class Uninstall {
    public function __construct() {
        register_uninstall_hook( XML_CACHE_FILE, [ __CLASS__, 'uninstall' ] );
    }

    public static function uninstall() {
        if ( Deactivation::is_running() ) {
            return;
        }

        Meta::delete_all();
    }
}
