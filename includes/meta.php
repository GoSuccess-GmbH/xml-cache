<?php

namespace GoSuccess\XML_Cache;

use WP_Post;

class Meta {
    public function __construct() {
        \add_action( 'init', [ $this, 'add_meta' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_classic_meta_box' ] );
        \add_action(
            hook_name: 'save_post',
            callback: [ $this, 'save_post' ],
            accepted_args: 3
        );
    }

    public function add_meta(): void {
        \register_meta(
            object_type: 'post',
            meta_key: '_xml_cache_enabled',
            args: [
                'type'              => 'boolean',
                'single'            => true,
                'default'           => true,
                'show_in_rest'      => true,
                'revisions_enabled' => true,
                'supports'          => [
                    'custom-fields',
                ],
                'auth_callback' => function() {
                    return \current_user_can( 'manage_options' );
                }
            ]
        );
    }

    public function add_classic_meta_box(): void {
        \add_meta_box(
            id: 'xml-cache',
            title: __( 'XML Cache', 'xml-cache' ),
            callback: [ $this, 'render_classic_meta_box' ],
            screen: [
                'post',
                'page',
            ],
            context: 'side',
            callback_args: [
                '__back_compat_meta_box' => true,
            ]
        );
    }

    public function render_classic_meta_box( WP_Post $post ): void {
        wp_nonce_field(
            action: 'xml_cache_classic',
            name: 'xml_cache_classic_nonce'
        );

        $xml_cache_enabled = \get_post_meta(
            post_id: $post->ID,
            key: '_xml_cache_enabled',
            single: true
        );

        $current_value = 1;

        printf(
            '<p class="meta-options">
                <label for="xml_cache_enabled" class="selectit">
                    <input name="xml_cache_enabled" type="checkbox" id="xml_cache_enabled" value="%d" %s> %s
                </label>
                <p class="description" id="xml_cache_enabled-description">%s</p>
            </p>',
            $current_value,
            \checked( $xml_cache_enabled, $current_value, false ),
            esc_html__( 'Enable', 'xml-cache' ),
            esc_html__( 'Enable XML cache sitemap for this post?', 'xml-cache' )
        );
    }

    public function save_post( int $post_id, WP_Post $post, bool $update ): void {
        if ( ! isset( $_POST['xml_cache_classic_nonce'] )
            || ! \wp_verify_nonce( $_POST['xml_cache_classic_nonce'], 'xml_cache_classic' )
            || ( defined( 'DOING_AUTOSAVE' ) && \DOING_AUTOSAVE )
            || ! \current_user_can( 'manage_options' ) ) {
            return;
        }

        $checkbox_value = \wp_validate_boolean( $_POST['xml_cache_enabled'] );

        \update_post_meta(
            post_id: $post_id,
            meta_key: '_xml_cache_enabled',
            meta_value: $checkbox_value
        );
    }

    public static function is_post_cache_enabled( int $object_id ): bool {
        $xml_cache_enabled = \get_metadata(
            meta_type: 'post',
            object_id: absint( $object_id ),
            meta_key: '_xml_cache_enabled',
            single: true
        );

        return \wp_validate_boolean( $xml_cache_enabled );
    }

    public static function delete_all(): bool {
        $delete_post_meta = \delete_post_meta_by_key( '_xml_cache_enabled' );
        $delete_option = \delete_option( 'xml_cache_settings' );

        return $delete_post_meta && $delete_option;
    }
}
