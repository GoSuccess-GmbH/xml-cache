<?php

namespace GoSuccess\XML_Cache;

class Admin {
    public function settings() {
        if ( ! Menu::is_displayed() ) {
            return;
        }

        echo '<xml-cache-settings></xml-cache-settings>';
    }
}
