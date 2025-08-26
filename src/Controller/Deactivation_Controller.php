<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;
use GoSuccess\XML_Cache\Repository\Deactivation_Repository;

final class Deactivation_Controller {
    public function __construct(
        private Plugin_Configuration $plugin_configuration,
        private Deactivation_Repository $deactivation_repository
    ) {
        register_deactivation_hook(
            $this->plugin_configuration->get_file(),
            array( $this->deactivation_repository, 'deactivation' )
        );
    }
}
