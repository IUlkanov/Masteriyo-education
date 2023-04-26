<?php
/**
 * HTTP helper functions.
 *
 * @since 1.5.5
 * @package Masteriyo\Helper
 */

if ( ! function_exists( 'masteriyo_current_http_request' ) ) {
	/**
	 * Return current HTTP request.
	 *
	 * @since 1.5.5
	 *
	 * @return WP_HTTP_Request
	 */
	function masteriyo_current_http_request() {
		global $wp_rest_server;

		if ( null === $wp_rest_server ) {
			return null;
		}

		// phpcs:disable
		if ( empty( $path ) ) {
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				$path = $_SERVER['PATH_INFO'];
			} else {
				$path = '/';
			}
		}

		$request = new \WP_REST_Request( $_SERVER['REQUEST_METHOD'], $path );

		$request->set_query_params( wp_unslash( $_GET ) );
		$request->set_body_params( wp_unslash( $_POST ) );
		$request->set_file_params( $_FILES );
		$request->set_headers( $wp_rest_server->get_headers( wp_unslash( $_SERVER ) ) );
		$request->set_body( \WP_REST_Server::get_raw_data() );

		// Initialize json params properly.g
		$request->get_json_params();

		/*
		 * HTTP method override for clients that can't use PUT/PATCH/DELETE. First, we check
		 * $_GET['_method']. If that is not set, we check for the HTTP_X_HTTP_METHOD_OVERRIDE
		 * header.
		 */
		if ( isset( $_GET['_method'] ) ) {
			$request->set_method( $_GET['_method'] );
		} elseif ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
			$request->set_method( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] );
		}

		// phpcs:enable
		return $request;
	}
}
