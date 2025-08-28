import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import Settings from './components/Settings';

domReady(() => {
    const settingsContainer = document.querySelector( 'xml-cache' );
    if ( settingsContainer !== null ) {
        const root = createRoot( settingsContainer );
        root.render( <Settings /> );
    }
});