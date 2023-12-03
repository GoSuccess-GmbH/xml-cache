<?php

namespace GoSuccess\XML_Cache;

require_once XML_CACHE_PATH . '/includes/sitemap.php';

$sitemap = new Sitemap();
$sitemap_urls = $sitemap->sitemap_urls;

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

if ( ! empty( $sitemap_urls ) ) {
    foreach ( $sitemap_urls as $url ) {
        $xml .= '<url>';
        $xml .= '<loc>' . $url . '</loc>';
        $xml .= '</url>';
    }
}

$xml.= '</urlset>';

header( 'Content-Type: text/xml' );
header( 'X-Robots-Tag: noindex, nofollow' );

echo $xml;
