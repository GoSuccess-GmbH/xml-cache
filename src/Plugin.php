<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class Plugin
 *
 * Initializes the XML Cache plugin and registers services using Symfony's Dependency Injection component.
 */
final class Plugin {

	/**
	 * Singleton instance reference.
	 */
	private static ?self $instance = null;

	/**
	 * Initializes the plugin and registers services.
	 */
	public function __construct() {
		$container = new ContainerBuilder();

		$loader = new PhpFileLoader(
			$container,
			new FileLocator( [ __DIR__ . '/Configuration' ] )
		);

		$loader->load( 'Service_Configuration.php' );

		foreach ( $container->findTaggedServiceIds( 'xml_cache.service' ) as $id => $tags ) {
			$container->get( $id );
		}
	}

	/**
	 * Retrieve (and lazily create) singleton instance.
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
