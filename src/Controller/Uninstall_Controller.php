<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;
use GoSuccess\XML_Cache\Repository\Uninstall_Repository;

final class Uninstall_Controller {
    public function __construct(
        private Plugin_Configuration $plugin_configuration,
        private Uninstall_Repository $uninstall_repository
    ) {
        register_uninstall_hook(
            $this->plugin_configuration->get_file(),
            array( $this->uninstall_repository, 'uninstall' )
        );
    }
}
