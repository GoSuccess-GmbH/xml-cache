<?php

namespace GoSuccess\XML_Cache;

final class Plugin {

    public function __construct() {
        $this->load_dependencies();
        $this->init();
    }
    
    private function load_dependencies(): void {
        require_once XML_CACHE_PATH . 'includes/menu.php';
        require_once XML_CACHE_PATH . 'includes/rewrite-rules.php';
        require_once XML_CACHE_PATH . 'includes/scripts.php';
        require_once XML_CACHE_PATH . 'includes/meta.php';
        require_once XML_CACHE_PATH . 'includes/admin.php';
        require_once XML_CACHE_PATH . 'includes/api.php';
        require_once XML_CACHE_PATH . 'includes/activation.php';
        require_once XML_CACHE_PATH . 'includes/deactivation.php';
        require_once XML_CACHE_PATH . 'includes/uninstall.php';
    }

    private function init(): void {
        new Menu();
        new Rewrite_Rules();
        new Scripts();
        new Meta();
        new Api();
        new Activation();
        new Deactivation();
        new Uninstall();
    }
}
