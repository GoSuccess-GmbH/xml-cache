<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;
use GoSuccess\XML_Cache\Repository\Activation_Repository;

final class Activation_Controller {
    public function __construct(
        private Plugin_Configuration $plugin_configuration,
        private Activation_Repository $activation_repository
    ) {
        register_activation_hook(
            $this->plugin_configuration->get_file(),
            array( $this->activation_repository, 'activation' )
        );
    }
}
