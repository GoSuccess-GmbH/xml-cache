<?php
/**
 * Collects and renders XML sitemap URLs for the plugin endpoint.
 *
 * @package xml-cache
 */

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Repository;

/**
 * Repository to assemble sitemap URLs and render XML.
 */
final class XML_Sitemap_Repository {
	/**
	 * Collected sitemap URLs.
	 *
	 * @var array<int,string>
	 */
	public array $sitemap_urls = array();

	/**
	 * Constructor kept intentionally light; heavy lifting is deferred.
	 */
	public function __construct() {}

	/**
	 * Collect URLs based on saved settings. Call this at runtime, not on bootstrap.
	 */
	public function collect_urls(): void {
		$option = get_option( 'xml_cache_settings', false );

		if ( false === $option ) {
			return;
		}

		if ( ! empty( $option[0]['posts_enabled'] ) ) {
			$this->get_post_urls();
		}

		if ( ! empty( $option[0]['categories_enabled'] ) ) {
			$this->get_category_urls();
		}

		if ( ! empty( $option[0]['archives_enabled'] ) ) {
			$this->get_archive_urls();
		}

		if ( ! empty( $option[0]['tags_enabled'] ) ) {
			$this->get_tag_urls();
		}
	}

	/**
	 * Collect post and page URLs.
	 */
	private function get_post_urls(): void {
		$post_ids = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids',
				'orderby'     => 'ID',
				'post_status' => 'publish',
				'post_type'   => $this->get_public_post_types(),
			)
		);

		$this->get_urls( 'get_permalink', $post_ids );
	}

	/**
	 * Collect category URLs.
	 */
	private function get_category_urls(): void {
		$category_ids = get_categories(
			array(
				'fields'        => 'ids',
				'orderby'       => 'id',
				'cache_results' => false,
			)
		);

		$this->get_urls( 'get_category_link', $category_ids );
	}

	/**
	 * Collect tag URLs.
	 */
	private function get_tag_urls(): void {
		$tag_ids = get_tags(
			array(
				'fields'        => 'ids',
				'orderby'       => 'term_id',
				'cache_results' => false,
			)
		);

		$this->get_urls( 'get_tag_link', $tag_ids );
	}

	/**
	 * Collect archive URLs.
	 */
	private function get_archive_urls(): void {
		$archives = wp_get_archives(
			array(
				'format' => 'custom',
				'before' => '',
				'after'  => '|',
				'echo'   => false,
				'order'  => 'ASC',
			)
		);

		if ( ! empty( $archives ) ) {
			$archives = explode( '|', $archives );
			$archives = array_filter(
				$archives,
				function ( $item ) {
					return '' !== trim( $item );
				}
			);

			foreach ( $archives as $archive ) {
				preg_match( '/href=["\']?([^"\'>]+)["\']>(.+)<\/a>/', $archive, $matches );
				$this->sitemap_urls[] = $matches[1];
			}
		}
	}

	/**
	 * Get public post types list.
	 *
	 * @return array List of public post types.
	 */
	private function get_public_post_types(): array {
		return array_values(
			get_post_types(
				array(
					'public' => true,
				)
			)
		);
	}

	/**
	 * Resolve URLs for given IDs using a permalink-like callable.
	 *
	 * @param callable $permalink_callable Function to resolve a URL by ID.
	 * @param array    $url_ids            IDs to resolve.
	 */
	private function get_urls( callable $permalink_callable, array $url_ids ): void {
		if ( ! is_callable( $permalink_callable ) || empty( $url_ids ) ) {
			return;
		}

		$permalinks_structure = get_option( 'permalink_structure' );
		$permalinks_enabled   = ! empty( $permalinks_structure );
		$page_for_posts     = absint( get_option( 'page_for_posts' ) );
		$posts_per_page     = absint( get_option( 'posts_per_page' ) );

		foreach ( $url_ids as $id ) {
			$is_post_cache_enabled = Meta_Box_Repository::is_post_cache_enabled( $id );

			if ( ! $is_post_cache_enabled ) {
				continue;
			}

			$permalink = $permalink_callable( $id );

			if ( ! empty( $permalink ) ) {
				$this->sitemap_urls[] = $permalink;

				$args = array(
					'numberposts' => -1,
					'fields'      => 'ids',
					'orderby'     => 'ID',
					'post_status' => 'publish',
				);

				$numpage = 1;

				$context = 'archive';
				if ( 'get_permalink' === $permalink_callable ) { // Singular or posts page.
					if ( $page_for_posts === $id ) {
						// Posts page behaves like an archive for pagination.
						$args['post_type'] = 'post';
						$posts_found       = get_posts( $args );
						$numpage           = (int) ceil( count( $posts_found ) / max( 1, $posts_per_page ) );
						$context           = 'archive';
					} else {
						// Multipage singular content.
						$postdata = generate_postdata( $id );
						if ( false !== $postdata && 1 === $postdata['multipage'] ) {
							$numpage = (int) $postdata['numpages'];
						}
						$context = 'singular';
					}
				} else {
					// Category or tag archives.
					if ( 'get_category_link' === $permalink_callable ) {
						$args['category'] = $id;
					} elseif ( 'get_tag_link' === $permalink_callable ) {
						$args['tag_id'] = $id;
					}

					$posts_found = get_posts( $args );
					$numpage     = (int) ceil( count( $posts_found ) / max( 1, $posts_per_page ) );
					$context     = 'archive';
				}

				while ( $numpage > 1 ) {
					$this->sitemap_urls[] = $this->build_paginated_url( (string) $permalink, (bool) $permalinks_enabled, (int) $numpage, (string) $context );
					--$numpage;
				}
			}
		}
	}

	/**
	 * Build a paginated URL based on permalink structure and context.
	 *
	 * @param string $permalink           Base permalink.
	 * @param bool   $permalinks_enabled  Whether pretty permalinks are enabled.
	 * @param int    $page                Page number.
	 * @param string $context             'singular' for posts/pages, 'archive' for blog/category/tag.
	 * @return string                     Final paginated URL.
	 */
	private function build_paginated_url( string $permalink, bool $permalinks_enabled, int $page, string $context ): string {
		if ( $permalinks_enabled ) {
			$base = trailingslashit( $permalink );
			if ( 'singular' === $context ) {
				// Singular multipage posts use /2/ style.
				return user_trailingslashit( (string) $base . $page );
			}
			// Archives (blog, category, tag) use /page/2/ style.
			return user_trailingslashit( (string) $base . 'page/' . $page );
		}

		// Fallback to query args when pretty permalinks are disabled.
		$param = ( 'singular' === $context ) ? 'page' : 'paged';
		return (string) add_query_arg( $param, $page, $permalink );
	}

	/**
	 * Render the XML sitemap and send appropriate headers.
	 */
	public static function render(): void {
		$sitemap = new self();
		$sitemap->collect_urls();
		$sitemap_urls = $sitemap->sitemap_urls;

		$xml    = '';
		$writer = null;

		if ( class_exists( '\\XMLWriter' ) ) {
			$writer = new \XMLWriter();
			$writer->openMemory();
			$writer->startDocument( '1.0', 'UTF-8' );
			$writer->startElement( 'urlset' );
			$writer->writeAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );

			if ( ! empty( $sitemap_urls ) ) {
				foreach ( $sitemap_urls as $url ) {
					if ( ! is_string( $url ) || '' === $url ) {
						continue;
					}

					$loc = \esc_url_raw( $url );
					if ( '' === $loc ) {
						continue;
					}

					$writer->startElement( 'url' );
					$writer->writeElement( 'loc', $loc );
					$writer->endElement(); // url.
				}
			}

			$writer->endElement(); // urlset.
			$xml = $writer->outputMemory();
		} else {
			// Fallback if ext-xmlwriter is not available.
			$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			if ( ! empty( $sitemap_urls ) ) {
				foreach ( $sitemap_urls as $url ) {
					if ( ! is_string( $url ) || '' === $url ) {
						continue;
					}
					$loc = \esc_url_raw( $url );
					if ( '' === $loc ) {
						continue;
					}
					$xml .= '<url><loc>' . htmlspecialchars( $loc, ENT_QUOTES | ENT_XML1, 'UTF-8' ) . '</loc></url>';
				}
			}

			$xml .= '</urlset>';
		}

		if ( ! headers_sent() ) {
			header( 'Content-Type: application/xml; charset=UTF-8' );
			header( 'X-Robots-Tag: noindex, nofollow' );
		}

		echo $xml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XML is constructed safely above.
	}
}
