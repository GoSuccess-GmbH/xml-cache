<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Repository\Script_Repository;

final class Script_Controller {

    public function __construct(
        private Script_Repository $script_repository
    ) {
        add_action(
            'admin_enqueue_scripts',
            [$this->script_repository, 'admin_scripts']
        );

		add_action(
            'enqueue_block_editor_assets',
            array( $this->script_repository, 'block_editor_assets' )
        );
    }
}
