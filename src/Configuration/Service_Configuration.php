<?php
/**
 * Service configuration for the XML Cache plugin.
 *
 * @package xml-cache
 */

declare( strict_types=1 );

namespace GoSuccess\XML_Cache\Configuration;

use GoSuccess\XML_Cache\Controller\Activation_Controller;
use GoSuccess\XML_Cache\Controller\API_Controller;
use GoSuccess\XML_Cache\Controller\Deactivation_Controller;
use GoSuccess\XML_Cache\Controller\Menu_Controller;
use GoSuccess\XML_Cache\Controller\Meta_Box_Controller;
use GoSuccess\XML_Cache\Controller\Rewrite_Rules_Controller;
use GoSuccess\XML_Cache\Controller\Script_Controller;
use GoSuccess\XML_Cache\Controller\Uninstall_Controller;
use GoSuccess\XML_Cache\Repository\Activation_Repository;
use GoSuccess\XML_Cache\Repository\API\V1\Admin\API_Repository;
use GoSuccess\XML_Cache\Repository\Deactivation_Repository;
use GoSuccess\XML_Cache\Repository\Menu_Repository;
use GoSuccess\XML_Cache\Repository\Meta_Box_Repository;
use GoSuccess\XML_Cache\Repository\Rewrite_Rules_Repository;
use GoSuccess\XML_Cache\Repository\Script_Repository;
use GoSuccess\XML_Cache\Repository\Uninstall_Repository;
use GoSuccess\XML_Cache\Repository\XML_Sitemap_Repository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * Class ServiceConfiguration
 *
 * Configures services for the XML Cache plugin.
 */
return static function ( ContainerConfigurator $container ): void {
	$services = $container->services();

	$services
		->set( 'Plugin_Configuration', Plugin_Configuration::class )
		->arg( '$file', XML_CACHE_FILE )
		->arg( '$slug', 'xml_cache' )
		->arg( '$title', 'XML Cache' )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Activation_Repository', Activation_Repository::class )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Activation_Controller', Activation_Controller::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->arg( '$activation_repository', service( 'Activation_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Deactivation_Repository', Deactivation_Repository::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Deactivation_Controller', Deactivation_Controller::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->arg( '$deactivation_repository', service( 'Deactivation_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Uninstall_Repository', Uninstall_Repository::class )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Uninstall_Controller', Uninstall_Controller::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->arg( '$uninstall_repository', service( 'Uninstall_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Menu_Repository', Menu_Repository::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Menu_Controller', Menu_Controller::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->arg( '$menu_repository', service( 'Menu_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Script_Repository', Script_Repository::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->arg( '$menu_repository', service( 'Menu_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Script_Controller', Script_Controller::class )
		->arg( '$script_repository', service( 'Script_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Rewrite_Rules_Repository', Rewrite_Rules_Repository::class )
		->arg( '$plugin_configuration', service( 'Plugin_Configuration' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Rewrite_Rules_Controller', Rewrite_Rules_Controller::class )
		->arg( '$rewrite_rules_repository', service( 'Rewrite_Rules_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'XML_Sitemap_Repository', XML_Sitemap_Repository::class )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Meta_Box_Repository', Meta_Box_Repository::class )
		->tag( 'xml_cache.service' );

	$services
		->set( 'Meta_Box_Controller', Meta_Box_Controller::class )
		->arg( '$meta_box_repository', service( 'Meta_Box_Repository' ) )
		->tag( 'xml_cache.service' );

	$services
		->set( 'API_Repository', API_Repository::class )
		->tag( 'xml_cache.service' );

	$services
		->set( 'API_Controller', API_Controller::class )
		->arg( '$api_repository', service( 'API_Repository' ) )
		->tag( 'xml_cache.service' );
};
