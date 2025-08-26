<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Repository\API\v1\Admin\API_Repository;

final class API_Controller
{
    public function __construct(
        private API_Repository $api_repository
    ) {
        add_action(
            'rest_api_init',
            [$this->api_repository, 'register_endpoints']
        );
    }
}
