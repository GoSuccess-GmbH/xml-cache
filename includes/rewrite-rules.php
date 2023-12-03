<?php

namespace GoSuccess\XML_Cache;

class Rewrite_Rules {
    public function __construct() {
        \add_action( 'init', [ $this, 'add_rewrite_rules' ] );

        \add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
        \add_filter( 'template_include', [ $this, 'add_template' ] );
        \add_filter( 'redirect_canonical', [ $this, 'redirect' ], 10, 2 );
    }

    public static function add_rewrite_rules(): void {
        \add_rewrite_rule(
            '^cache\.xml$',
            'index.php?xml_cache=true',
            'top'
        );
    }

    public function add_query_vars( array $query_vars ): array {
        $query_vars[] = 'xml_cache';
        return $query_vars;
    }

    public function add_template( string $template ): string {
        $xml_cache = \get_query_var( 'xml_cache' );

        if ( ! $xml_cache ) {
            return $template;
        }

        return XML_CACHE_PATH . 'includes/templates/xml.php';
    }

    public function redirect( string $redirect_url, string $request_url ): string {
        $xml_cache = \get_query_var( 'xml_cache', true );

        if ( $xml_cache ) {
            return $request_url;
        }

        return $redirect_url;
    }
}
