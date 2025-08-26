<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository\API\v1\Admin\Endpoint\Settings;

/**
 * Class Settings_Repository
 *
 * Handles the settings endpoint for the XML Cache plugin.
 */
final class Settings_Repository
{
    /** @var string $route */
    // The route for the settings endpoint
    public static string $route = 'settings';

    /**
     * Constructor to initialize the settings endpoint.
     */
    public function __construct() {
        new Read();
        new Create();
    }
}
