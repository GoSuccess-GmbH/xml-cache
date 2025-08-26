<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository\API\v1\Admin\Endpoint\XML_Sitemap_URL;

/**
 * Class XML_Sitemap_URL_Repository
 *
 * Handles the XML Sitemap URL endpoint for the XML Cache plugin.
 */
final class XML_Sitemap_URL_Repository
{
    /** @var string $route */
    // The route for the XML Sitemap URL endpoint
    public static string $route = 'xml-sitemap-url';

    /**
     * Constructor to initialize the XML Sitemap URL endpoint.
     */
    public function __construct() {
        new Read();
    }
}
