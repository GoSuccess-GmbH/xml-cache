<?php

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Repository;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;

final class Rewrite_Rules_Repository {
    public function __construct(
        private Plugin_Configuration $plugin_configuration
    ) {}

    public static function add_rewrite_rules(): void {
		add_rewrite_rule(
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
		$xml_cache = get_query_var( 'xml_cache' );

		if ( ! $xml_cache ) {
			return $template;
		}

		return $this->plugin_configuration->get_path() . 'src/Template/XML_Sitemap_Template.php';
	}

	public function redirect( string $redirect_url, string $request_url ): string {
		$xml_cache = get_query_var( 'xml_cache', true );

		if ( $xml_cache ) {
			return $request_url;
		}

		return $redirect_url;
	}
}
