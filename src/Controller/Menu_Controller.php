<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Configuration\Plugin_Configuration;
use GoSuccess\XML_Cache\Repository\Menu_Repository;

final class Menu_Controller {
    public function __construct(
        private Plugin_Configuration $plugin_configuration,
        private Menu_Repository $menu_repository
    ) {
        // Register the menu and action links
        add_action(
            'admin_menu',
            [$this->menu_repository, 'menu']
        );

        // Add action links to the plugin's action links
        add_filter(
            'plugin_action_links_' . $this->plugin_configuration->get_basename(),
            [$this->menu_repository, 'add_action_links'],
            10,
            4
        );
    }
}
