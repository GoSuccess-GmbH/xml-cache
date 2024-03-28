<?php

namespace GoSuccess\XML_Cache;

class Sitemap {
    public array $sitemap_urls = [];
    
    public function __construct() {
        $option = \get_option( 'xml_cache_settings', false );
        
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

    private function get_post_urls(): void {
        $post_ids = \get_posts([
            'numberposts'   => -1,
            'fields'        => 'ids',
            'orderby'       => 'ID',
            'post_status'   => 'publish',
            'post_type'     => $this->get_public_post_types(),
        ]);

        $this->get_urls( 'get_permalink', $post_ids );
    }

    private function get_category_urls(): void {
        $category_ids = \get_categories([
            'fields'        => 'ids',
            'orderby'       => 'id',
            'cache_results' => false,
        ]);

        $this->get_urls( 'get_category_link', $category_ids );
    }

    private function get_tag_urls(): void {
        $tag_ids = \get_tags([
            'fields'        => 'ids',
            'orderby'       => 'term_id',
            'cache_results' => false,
        ]);

        $this->get_urls( 'get_tag_link', $tag_ids );
    }

    private function get_archive_urls(): void {
        $archives = \wp_get_archives([
            'format' => 'custom',
            'before' => '',
            'after' => '|',
            'echo' => false,
            'order' => 'ASC',
        ]);
        
        if ( ! empty( $archives ) ) {
            $archives = explode( '|', $archives );
            $archives = array_filter( $archives, function( $item ) {
                return trim( $item ) !== '';
            } );
        
            foreach ($archives as $archive) {
                preg_match('/href=["\']?([^"\'>]+)["\']>(.+)<\/a>/', $archive, $matches);
                $this->sitemap_urls[] = $matches[1];
            }
        }
    }

    private function get_public_post_types(): array {
        return array_values(
            \get_post_types([
                'public' => true,
            ])
        );
    }

    private function get_urls( callable $permalink_callable, array $url_ids ): void {
        if ( ! is_callable( $permalink_callable ) || empty( $url_ids ) ) {
            return;
        }

        $permalinks_enabled = \get_option( 'permalink_structure' );
        $page_for_posts = \absint( \get_option( 'page_for_posts' ) );
        $posts_per_page = \absint( \get_option( 'posts_per_page' ) );

        foreach ( $url_ids as $id ) {
            $is_post_cache_enabled = Meta::is_post_cache_enabled( $id );

            if ( ! $is_post_cache_enabled ) {
                continue;
            }
            
            $permalink = $permalink_callable( $id );

            if ( ! empty( $permalink ) ) {
                $this->sitemap_urls[] = $permalink;

                $seperator = $permalinks_enabled
                    ? ( str_ends_with( $permalink, '/' ) ? '' : '/' )
                    : ( str_contains( $permalink, '?' ) ? '&' : '?' );
                
                $args = [
                    'numberposts'   => -1,
                    'fields'        => 'ids',
                    'orderby'       => 'ID',
                    'post_status'   => 'publish',
                ];

                if ( 'get_permalink' === $permalink_callable ) { // Add pages
                    if ( $page_for_posts === $id ) { // Blog page
                        $seperator .= $permalinks_enabled ? 'page/' : 'paged=';
                        $args['post_type'] = 'post';
                        $posts_found = \get_posts( $args );
                        $numpage = ceil( count( $posts_found ) / $posts_per_page );
                    } else { // Posts
                        $postdata = \generate_postdata( $id );
                        $seperator .= $permalinks_enabled ? 'page/' : 'page=';

                        if ( false !== $postdata && 1 === $postdata['multipage'] ) {
                            $numpage = $postdata['numpages'];
                        }
                    }
                } else { // Add pages categories and tag pages.
                    if ( 'get_category_link' === $permalink_callable ) {
                        $args['category'] = $id;
                    } else if ( 'get_tag_link' === $permalink_callable ) {
                        $args['tag_id'] = $id;
                    }

                    $posts_found = \get_posts( $args );
                    $numpage = ceil( count( $posts_found ) / $posts_per_page );
                    $seperator .= $permalinks_enabled ? 'page/' : 'paged=';
                }

                while ( $numpage > 1 ) {
                    $this->sitemap_urls[] = $permalink . $seperator . $numpage;
                    $numpage--;
                }
            }
        }
    }
}
