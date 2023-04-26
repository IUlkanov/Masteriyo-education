<?php
/**
 * Ajax.
 *
 * @package Masteriyo
 *
 * @since 1.0.0
 * @since 1.4.3 Removed static keyword.
 */

namespace Masteriyo;

use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Ajax class.
 *
 * @class Masteriyo\Ajax
 */

class Ajax {

	/**
	 * Actions.
	 *
	 * @since 1.0.0
	 * @since 1.4.3 Removed static keyword.
	 *
	 * @var array
	 */
	private static $actions = array();

	/**
	 * Ajax Handlers.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $handlers = array();

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 * @since 1.4.3 Removed static keyword.
	 */
	public function init() {
		// $this->init_hooks();
		$this->init_handlers();
	}

	/**
	 * Initialize ajax handlers.
	 *
	 * @since 1.4.3
	 */
	public function init_handlers() {
		$namespace = 'Masteriyo\\AjaxHandlers';

		$handlers = array_unique(
			/**
			 * Filters ajax handler classes.
			 *
			 * @since 1.4.3
			 *
			 * @param string[] $ajax_handlers Ajax handler classes.
			 */
			apply_filters(
				'masteriyo_ajax_handlers',
				array(
					"{$namespace}\\LoginAjaxHandler",
					"{$namespace}\\CheckoutAjaxHandler",
					"{$namespace}\\ReviewNoticeAjaxHandler",
					"{$namespace}\\CourseReviewsInfiniteLoadingAjaxHandler",
				)
			)
		);

		$handlers = array_filter(
			$handlers,
			function( $handler ) {
				return class_exists( $handler );
			}
		);

		foreach ( $handlers as $handler ) {
			$object = new $handler();

			if ( is_callable( array( $object, 'register' ) ) ) {
				$object->register();
				$this->handlers[ $object->action ] = $object;
			}
		}
	}

	/**
	 * Return ajax handler by action/handle.
	 *
	 * @since 1.4.3
	 *
	 * @param string $action Ajax Handler action.
	 *
	 * @return Masteriyo\Abstracts\AjaxHandler|null
	 */
	public function get_handler( $action ) {
		if ( isset( $this->handlers[ $action ] ) ) {
			return $this->handlers[ $action ];
		}

		return null;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @deprecated 1.4.3
	 */
	private static function init_hooks() {
		self::$actions = apply_filters(
			'masteriyo_ajax_actions',
			array(
				'login' => array(
					'priv'   => array( __CLASS__, 'login' ),
					'nopriv' => array( __CLASS__, 'login' ),
				),
				'test'  => array(
					'priv'   => array( __CLASS__, 'test' ),
					'nopriv' => array( __CLASS__, 'test' ),
				),
			)
		);

		foreach ( self::$actions as $key => $action ) {
			foreach ( $action as $type => $callback ) {
				$type = 'priv' === $type ? '' : '_nopriv';
				$slug = MASTERIYO_SLUG;
				add_action( "wp_ajax{$type}_{$slug}_{$key}", $callback );
			}
		}
	}

	/**
	 * Login a user.
	 *
	 * @since 1.0.0
	 * @deprecated
	 * @since 1.4.3 Moved to LoginAjaxHandler class.
	 */
	public static function login() {
		if ( isset( $_POST['nonce'] ) ) {
			try {
				if ( ! wp_verify_nonce( $_POST['nonce'], 'masteriyo_login_nonce' ) ) {
					throw new Exception( __( 'Invalid nonce. Maybe you should reload the page.', 'masteriyo' ) );
				}
				if ( ! isset( $_POST['payload'] ) || ! is_array( $_POST['payload'] ) ) {
					throw new Exception( __( 'Missing login credentials.', 'masteriyo' ) );
				}

				$username = isset( $_POST['payload']['username'] ) ? sanitize_text_field( $_POST['payload']['username'] ) : '';
				$password = isset( $_POST['payload']['password'] ) ? sanitize_text_field( $_POST['payload']['password'] ) : '';
				$remember = isset( $_POST['payload']['remember'] ) ? sanitize_text_field( $_POST['payload']['remember'] ) : 'no';

				if ( empty( $username ) ) {
					throw new Exception( __( 'Username cannot be empty.', 'masteriyo' ) );
				}
				if ( empty( $password ) ) {
					throw new Exception( __( 'Password cannot be empty.', 'masteriyo' ) );
				}

				$credentials = array(
					'user_password' => $password,
					'remember'      => 'yes' === $remember,
				);

				if ( is_email( $username ) ) {
					$user = get_user_by( 'email', $username );

					if ( isset( $user->user_login ) ) {
						$credentials['user_login'] = $user->user_login;
					} else {
						throw new Exception( __( 'No user found with the given email address.', 'masteriyo' ) );
					}
				} else {
					$credentials['user_login'] = $username;
				}

				$user = wp_signon( $credentials, is_ssl() );

				if ( is_wp_error( $user ) ) {
					if ( 'incorrect_password' === $user->get_error_code() ) {
						throw new Exception( __( 'Incorrect password. Please try again.', 'masteriyo' ) );
					}
					throw new Exception( $user->get_error_message() );
				}

				wp_send_json_success(
					array(
						'message' => __( 'Signed in successfully.', 'masteriyo' ),
					)
				);
			} catch ( Exception $e ) {
				wp_send_json_error(
					array(
						'message' => $e->getMessage(),
					)
				);
			}
		}

		wp_send_json_error(
			array(
				'message' => __( 'Nonce is required.', 'masteriyo' ),
			)
		);
	}
}
