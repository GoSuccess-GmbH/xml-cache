<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Abstract;

use GoSuccess\XML_Cache\Interface\API_Endpoint_Interface;
use WP_REST_Request;

abstract class API_Endpoint_Abstract implements API_Endpoint_Interface {
    public function __construct() {
        $this->register();
    }

    public function permission_callback(WP_REST_Request $request): bool {
        return current_user_can('manage_options');
    }
}
