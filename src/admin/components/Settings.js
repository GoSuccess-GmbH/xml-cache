import apiFetch from '@wordpress/api-fetch';
import { Panel, PanelBody, PanelRow, CheckboxControl, Spinner, Animate, Notice, Snackbar, PanelHeader, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useDispatch, dispatch } from '@wordpress/data';
import Notices from './Notices';

export default function Settings() {
    const rest_namespace = '/xml-cache/v1';
    const nonce = 'xml_cache_nonce';

    apiFetch.use( apiFetch.createNonceMiddleware( nonce ) );
    
    const [options, setOptions] = useState(null);
    const [optionsSaved, setOptionsSaved] = useState(false);
    const [sitemapUrl, setSitemapUrl] = useState(null);

    const saveOptions = () => {
        apiFetch( {
            path: rest_namespace + '/settings',
            method: 'POST',
            data: options,
        } ).then( ( result ) => {
            if ( result.success ) {
                setOptionsSaved(true);

                dispatch( 'core/notices' ).createNotice(
                    'success',
                    __( 'Settings saved.', 'xml-cache' ),
                    {
                        type: 'snackbar',
                        isDismissible: true,
                    }
                );
            } else {
                dispatch( 'core/notices' ).createNotice(
                    'error',
                    __( "Settings could't be saved.", 'xml-cache' ),
                    {
                        type: 'snackbar',
                        isDismissible: true,
                    }
                );
            }
        } ).catch( ( error ) => {
            console.error( error );
        } );
    }

    const onTogglePanel = ( option, value ) => {
        options[1][option] = value;
        setOptions(options);
        saveOptions();
    }

    const onChangeSetting = ( option, value ) => {
        options[0][option] = value;
        setOptions(options);
        saveOptions();
    }

    useEffect(() => {
        apiFetch( {
            path: rest_namespace + '/settings'
        } ).then( ( result ) => {
            setOptions( result );
        } ).catch( ( error ) => {
            console.error( error );
        } );

        apiFetch( {
            path: rest_namespace + '/sitemap-url'
        } ).then( ( result ) => {
            if ( result.success ) {
                setSitemapUrl( result.sitemap_url );
            }
        } ).catch( ( error ) => {
            console.error( error );
        } );

        return () => {
            setOptionsSaved(false);
        }
    }, [optionsSaved]);

    if ( options == null /*|| sitemapUrl == null*/ ) {
        return <Spinner />;
    }

    return (
        <>
        <Panel
            header={ __( 'XML Cache Settings', 'xml-cache' ) }
        >
            { sitemapUrl == null && (
                <Notice status="error" isDismissible={ false }>
                    { __( 'An unknown error occurred.', 'xml-cache' ) }
                </Notice>
            ) }
            <PanelHeader>
                <Button
                    variant='primary'
                    href={ sitemapUrl }
                    icon={ 'admin-links' }
                    size='compact'
                    target='_blank'
                    disabled={ sitemapUrl == null }
                >
                    { __( 'Open Sitemap', 'xml-cache' ) }
                </Button>
            </PanelHeader>
            <PanelBody
                title={ __( 'Posts', 'xml-cache' ) }
                initialOpen={ options[1].is_posts_panel_open }
                onToggle={ ( state ) => onTogglePanel( 'is_posts_panel_open', state ) }
            >
                <PanelRow>
                    <CheckboxControl
                        label={ __( 'Enable', 'xml-cache' ) }
                        help={ __( 'Enable XML cache sitemap for posts?', 'xml-cache' ) }
                        checked={ options[0].posts_enabled }
                        onChange={ ( state ) => onChangeSetting( 'posts_enabled', state ) }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody
                title={ __( 'Categories', 'xml-cache' ) }
                initialOpen={ options[1].is_categories_panel_open }
                onToggle={ ( next ) => onTogglePanel( 'is_categories_panel_open', next ) }
            >
                <PanelRow>
                    <CheckboxControl
                        label={ __( 'Enable', 'xml-cache' ) }
                        help={ __( 'Enable XML cache sitemap for categories?', 'xml-cache' ) }
                        checked={ options[0].categories_enabled }
                        onChange={ ( state ) => onChangeSetting( 'categories_enabled', state ) }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody
                title={ __( 'Archives', 'xml-cache' ) }
                initialOpen={ options[1].is_archives_panel_open }
                onToggle={ ( next ) => onTogglePanel( 'is_archives_panel_open', next ) }
            >
                <PanelRow>
                    <CheckboxControl
                        label={ __( 'Enable', 'xml-cache' ) }
                        help={ __( 'Enable XML cache sitemap for archives?', 'xml-cache' ) }
                        checked={ options[0].archives_enabled }
                        onChange={ ( state ) => onChangeSetting( 'archives_enabled', state ) }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody
                title={ __( 'Tags', 'xml-cache' ) }
                initialOpen={ options[1].is_tags_panel_open }
                onToggle={ ( next ) => onTogglePanel( 'is_tags_panel_open', next ) }
            >
                <PanelRow>
                    <CheckboxControl
                        label={ __( 'Enable', 'xml-cache' ) }
                        help={ __( 'Enable XML cache sitemap for tags?', 'xml-cache' ) }
                        checked={ options[0].tags_enabled }
                        onChange={ ( state ) => onChangeSetting( 'tags_enabled', state ) }
                    />
                </PanelRow>
            </PanelBody>
        </Panel>

        <Notices />
        </>
    )
}