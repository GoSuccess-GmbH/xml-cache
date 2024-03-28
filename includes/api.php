<?php

namespace GoSuccess\XML_Cache;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use wpdb;

class Api {
    private string $route_namespace = 'xml-cache/v1';
    private wpdb $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        \add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        \register_rest_route(
            route_namespace: $this->route_namespace,
            route: '/settings',
            args: [
                'methods'               => WP_REST_Server::ALLMETHODS,
                'callback'              => [ $this, 'settings' ],
                'permission_callback'   => [ $this, 'permission' ],
            ]
        );

        \register_rest_route(
            route_namespace: $this->route_namespace,
            route: '/sitemap-url',
            args: [
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => [ $this, 'sitemap_url' ],
                'permission_callback'   => [ $this, 'permission' ],
            ]
        );
    }

    public function settings( WP_REST_Request $request ): WP_REST_Response {
        if ( 'GET' === $request->get_method() ) {
            $options = \get_option( 'xml_cache_settings', Activation::get_default_settings() );

            return $this->result( $options );
        }

        if ( 'POST' === $request->get_method() ) {
            $options = rest_sanitize_array( $request->get_json_params() );
            \update_option( 'xml_cache_settings', $options );

            return $this->result( [
                'success' => true,
            ] );
        }

        return $this->result();
    }

    public function sitemap_url( WP_REST_Request $request ): WP_REST_Response {
        if ( 'GET' === $request->get_method() ) {
            $home_url = \trailingslashit( \home_url() );
            $sitemap_url = get_option( 'permalink_structure' )
                ? $home_url . 'cache.xml'
                : $home_url . '?xml_cache=true';

            return $this->result( [
                'success' => true,
                'sitemap_url' => $sitemap_url,
            ] );
        }

        return $this->result( [
            'success' => false,
        ] );
    }

    public function permission(): bool {
        return \current_user_can( 'manage_options' );
    }

    private function result( array $array = [] ): WP_REST_Response {
        return new WP_REST_Response( $array );
    }
}
