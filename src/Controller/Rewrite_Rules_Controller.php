<?php

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Controller;

use GoSuccess\XML_Cache\Repository\Rewrite_Rules_Repository;

final class Rewrite_Rules_Controller {
    public function __construct(
        private Rewrite_Rules_Repository $rewrite_rules_repository
    ) {
        add_action(
            'init',
            array( $this->rewrite_rules_repository, 'add_rewrite_rules' )
        );

		add_filter(
            'query_vars',
            array( $this->rewrite_rules_repository, 'add_query_vars' )
        );

		add_filter(
            'template_include',
            array( $this->rewrite_rules_repository, 'add_template' )
        );
        
		add_filter(
            'redirect_canonical',
            array( $this->rewrite_rules_repository, 'redirect' ),
            10,
            2
        );
    }
}
