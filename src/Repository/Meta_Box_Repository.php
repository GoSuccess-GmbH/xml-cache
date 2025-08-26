<?php

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Repository;

use WP_Post;

final class Meta_Box_Repository {
    public function __construct() {}

    public function add_meta(): void {
		register_meta(
			'post',
			'_xml_cache_enabled',
			array(
				'type' => 'boolean',
				'single' => true,
				'default' => true,
				'show_in_rest' => true,
				'revisions_enabled' => true,
				'supports' => array(
					'custom-fields',
				),
				'auth_callback' => fn (): bool => current_user_can( 'manage_options' ),
			)
		);
	}

	public function add_classic_meta_box(): void {
		add_meta_box(
			'xml-cache',
			__( 'XML Cache', 'xml-cache' ),
			array( $this, 'render_classic_meta_box' ),
			array(
				'post',
				'page',
			),
			'side',
            'default',
			array(
				'__back_compat_meta_box' => true,
			)
		);
	}

	public function render_classic_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'xml_cache_classic', 'xml_cache_classic_nonce' );

		$meta_value = get_post_meta( absint( $post->ID ), '_xml_cache_enabled', true );
		$is_enabled = wp_validate_boolean( $meta_value );
		$current_value = 1;
		$checked_attr = checked( $is_enabled, true, false );
		$enable_label = esc_html__( 'Enable', 'xml-cache' );
		$description = esc_html__( 'Enable XML cache sitemap for this post?', 'xml-cache' );

		echo '<p class="meta-options">';
		echo '<label for="xml_cache_enabled" class="selectit">';
		printf(
			'<input name="xml_cache_enabled" type="checkbox" id="xml_cache_enabled" value="%d" %s> %s',
			absint( $current_value ),
			$checked_attr,
			$enable_label
		);
		echo '</label>';
		echo '</p>';

		printf(
			'<p class="description" id="xml_cache_enabled-description">%s</p>',
			$description
		);
	}

	public function save_post( int $post_id, WP_Post $post, bool $update ): void {
		// Bail on autosave or revision to avoid unnecessary work and potential conflicts.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Verify nonce if present, otherwise do nothing.
		$nonce = isset( $_POST['xml_cache_classic_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['xml_cache_classic_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'xml_cache_classic' ) ) {
			return;
		}

		// Permissions check.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Checkbox: present when checked; absent when unchecked. Coerce safely to boolean.
		$raw_input = $_POST['xml_cache_enabled'] ?? 0;
		$raw_input = is_scalar( $raw_input ) ? $raw_input : 0;
		$checkbox_value = wp_validate_boolean( absint( $raw_input ) );

		update_post_meta( $post_id, '_xml_cache_enabled', $checkbox_value );
	}

	public static function is_post_cache_enabled( int $object_id ): bool {
		$xml_cache_enabled = get_metadata(
			'post',
			absint( $object_id ),
			'_xml_cache_enabled',
			true
		);

		return wp_validate_boolean( $xml_cache_enabled );
	}

	public static function delete_all(): bool {
		$delete_post_meta = delete_post_meta_by_key( '_xml_cache_enabled' );
		$delete_option = delete_option( 'xml_cache_settings' );

		return $delete_post_meta && $delete_option;
	}
}
