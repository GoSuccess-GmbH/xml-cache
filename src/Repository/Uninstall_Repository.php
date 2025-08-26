<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Repository;

final class Uninstall_Repository {
    public function __construct() {}

    public static function uninstall(): void {
		if ( Deactivation_Repository::is_running() ) {
			return;
		}

		Meta_Box_Repository::delete_all();
	}
}
