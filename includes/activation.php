<?php

namespace GoSuccess\XML_Cache;

class Activation {
    public function __construct() {
        \register_activation_hook( XML_CACHE_FILE, [ $this, 'activation' ] );
    }

    public function activation(): void {
        if ( Deactivation::is_running() ) {
            return;
        }

        \add_option( 'xml_cache_settings', self::get_default_settings() );

        Rewrite_Rules::add_rewrite_rules();
        \flush_rewrite_rules();
    }

    public static function get_default_settings(): array {
        return [
            [
                'posts_enabled'         => true,
                'categories_enabled'    => true,
                'archives_enabled'      => true,
                'tags_enabled'          => true,
            ],
            [
                'is_posts_panel_open'       => false,
                'is_categories_panel_open'  => false,
                'is_archives_panel_open'    => false,
                'is_tags_panel_open'        => false,
            ]
        ];
    }
}
