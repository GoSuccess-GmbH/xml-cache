<?php

declare(strict_types=1);

namespace GoSuccess\XML_Cache\Interface;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Interface ApiEndpointInterface
 *
 * Defines the methods required for API endpoints in the XML Cache plugin.
 */
interface API_Endpoint_Interface {

	/**
	 * Constructor to initialize the endpoint.
	 */
	public function __construct();

	/**
	 * Register the endpoint with WordPress REST API.
	 *
	 * @return bool
	 */
	public function register(): bool;

	/**
	 * Callback for the endpoint.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function callback( WP_REST_Request $request ): WP_REST_Response;

	/**
	 * Permission callback for the endpoint.
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function permission_callback( WP_REST_Request $request ): bool;
}
