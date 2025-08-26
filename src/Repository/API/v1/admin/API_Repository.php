<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository\API\v1\Admin;

use GoSuccess\XML_Cache\Repository\API\v1\Admin\Endpoint\Settings\Settings_Repository;
use GoSuccess\XML_Cache\Repository\API\v1\Admin\Endpoint\XML_Sitemap_URL\XML_Sitemap_URL_Repository;

/**
 * Class API_Repository
 *
 * Handles the API repository for the XML Cache plugin.
 */
final class API_Repository
{
    /**
     * API namespace.
     * @var string
     */
    public static string $namespace = 'xml-cache/v1/admin';

    public function __construct() {}
    
    public function register_endpoints(): void {
        new Settings_Repository();
        new XML_Sitemap_URL_Repository();
    }
}
