import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';
import Settings from './components/Settings';

domReady(() => {
    const settingsContainer = document.querySelector( 'xml-cache-settings' );
    if ( settingsContainer !== null ) {
        render( <Settings />, settingsContainer );
    }
});