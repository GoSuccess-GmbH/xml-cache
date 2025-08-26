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
	 * Constructor.
	 */
	public function __construct() {
		$option = get_option( 'xml_cache_settings', false );

		if ( false === $option ) {
			return;
		}

		if ( $option[0]['posts_enabled'] ) {
			$this->get_post_urls();
		}

		if ( $option[0]['categories_enabled'] ) {
			$this->get_category_urls();
		}

		if ( $option[0]['archives_enabled'] ) {
			$this->get_archive_urls();
		}

		if ( $option[0]['tags_enabled'] ) {
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

		$permalinks_enabled = get_option( 'permalink_structure' );
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

				$seperator = $permalinks_enabled
					? ( str_ends_with( $permalink, '/' ) ? '' : '/' )
					: ( str_contains( $permalink, '?' ) ? '&' : '?' );

				$args = array(
					'numberposts' => -1,
					'fields'      => 'ids',
					'orderby'     => 'ID',
					'post_status' => 'publish',
				);

				$numpage = 1;

				if ( 'get_permalink' === $permalink_callable ) { // Add pages.
					if ( $page_for_posts === $id ) { // Blog page.
						$seperator        .= $permalinks_enabled ? 'page/' : 'paged=';
						$args['post_type'] = 'post';
						$posts_found       = get_posts( $args );
						$numpage           = ceil( count( $posts_found ) / $posts_per_page );
					} else { // Posts.
						$postdata   = generate_postdata( $id );
						$seperator .= $permalinks_enabled ? 'page/' : 'page=';

						if ( false !== $postdata && 1 === $postdata['multipage'] ) {
							$numpage = $postdata['numpages'];
						}
					}
				} else { // Add pages, categories and tag pages.
					if ( 'get_category_link' === $permalink_callable ) {
						$args['category'] = $id;
					} elseif ( 'get_tag_link' === $permalink_callable ) {
						$args['tag_id'] = $id;
					}

					$posts_found = get_posts( $args );
					$numpage     = ceil( count( $posts_found ) / $posts_per_page );
					$seperator  .= $permalinks_enabled ? 'page/' : 'paged=';
				}

				while ( $numpage > 1 ) {
					$this->sitemap_urls[] = $permalink . $seperator . $numpage;
					--$numpage;
				}
			}
		}
	}

	/**
	 * Render the XML sitemap and send appropriate headers.
	 */
	public static function render(): void {
		$sitemap      = new self();
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
