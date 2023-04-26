<?php
/**
 * Handle registration form.
 * @since 1.0.0
 *
 * @package Masteriyo\Classes\
 */

namespace Masteriyo\FormHandler;

use Masteriyo\Notice;

defined( 'ABSPATH' ) || exit;

/**
 * Registration class.
 */
class RegistrationFormHandler {
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'process' ), 20 );
	}

	/**
	 * Handle registration.
	 *
	 * @since 1.0.0
	 */
	public function process() {
		try {
			if ( ! isset( $_POST['masteriyo-registration'] ) ) {
				return;
			}

			$nonce_value = isset( $_POST['_wpnonce'] ) ? wp_unslash( $_POST['_wpnonce'] ) : '';

			if ( empty( $nonce_value ) ) {
				throw new \WP_Error( 'nonce_missing', __( 'Nonce is missing.', 'masteriyo' ) );
			}

			if ( ! wp_verify_nonce( $nonce_value, 'masteriyo-register' ) ) {
				throw new \WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'masteriyo' ) );
			}

			$result = $this->register_user();

			if ( is_wp_error( $result ) ) {
				foreach ( $result->get_error_messages() as $message ) {
					masteriyo_add_notice( $message, Notice::ERROR );
				}
				$this->set_form_session();
			}
			if ( $result instanceof \Throwable ) {
				masteriyo_add_notice( $result->getMessage(), Notice::ERROR );
				$this->set_form_session();
			}
		} catch ( \Throwable $e ) {
			masteriyo_add_notice( $e->getMessage(), Notice::ERROR );
			$this->set_form_session();
		}
	}

	/**
	 * Set user registration form session.
	 *
	 * @since 1.0.0
	 */
	protected function set_form_session() {
		$data    = $this->get_form_data();
		$session = masteriyo( 'session' );

		foreach ( $data as $key => $value ) {
			$session->put( "user-registration.{$key}", $value );
		}
	}

	/**
	 * Register user with submitted detail.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Error|boolean
	 */
	protected function register_user() {
		$data  = $this->get_form_data();
		$error = $this->validate_form( $data );

		if ( is_wp_error( $error ) ) {
			return $error;
		}

		$user = masteriyo_create_new_user(
			$data['email'],
			$data['username'],
			$data['password'],
			'masteriyo_student',
			array(
				'first_name' => $data['first-name'],
				'last_name'  => $data['last-name'],
			)
		);

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( masteriyo_registration_is_generate_password() ) {
			masteriyo_add_notice( __( 'Your account was created successfully and a password has been sent to your email address.', 'masteriyo' ) );
		} else {
			masteriyo_add_notice( __( 'Your account was created successfully. Your login details have been sent to your email address.', 'masteriyo' ) );
		}

		$this->redirect( $user );

		return true;
	}

	/**
	 * Validate the submitted form.
	 *
	 * @param array $data Form data.
	 *
	 * @since 1.0.0
	 */
	protected function validate_form( $data ) {
		$error = new \WP_Error();

		if ( ! masteriyo_registration_is_generate_username() && empty( $data['username'] ) ) {
			$error->add( 'username_required', __( 'Username is required.', 'masteriyo' ) );
		}

		if ( empty( $data['email'] ) ) {
			$error->add( 'email_required', __( 'Email is required.', 'masteriyo' ) );
		}

		if ( ! is_email( $data['email'] ) ) {
			$error->add( 'invalid_email', __( 'Email is invalid.', 'masteriyo' ) );
		}

		if ( empty( $data['first-name'] ) ) {
			$error->add( 'first_name_required', __( 'First name is required.', 'masteriyo' ) );
		}

		if ( empty( $data['last-name'] ) ) {
			$error->add( 'last_name_required', __( 'Last name is required.', 'masteriyo' ) );
		}

		if ( ! masteriyo_registration_is_generate_password() ) {
			if ( empty( $data['password'] ) ) {
				$error->add( 'password_required', __( 'Password is required..', 'masteriyo' ) );
			}

			if ( empty( $data['confirm-password'] ) ) {
				$error->add( 'confirm_password_required', __( 'Confirm password is required...', 'masteriyo' ) );
			}

			if ( $data['password'] !== $data['confirm-password'] ) {
				$error->add( 'passwords_do_not_match', __( 'The passwords do not match.', 'masteriyo' ) );
			}
		}

		/**
		 * Validate user registration form data.
		 *
		 * @since 1.0.0
		 *
		 * @param \WP_Error $validation_error Error object which should contain validation errors if there is any.
		 * @param array $data Submitted form data.
		 */
		$error = apply_filters( 'masteriyo_validate_registration_form_data', $error, $data );

		if ( $error->has_errors() ) {
			return $error;
		} else {
			return true;
		}
	}

	/**
	 * Redirect after registration.
	 *
	 * @since 1.0.0
	 *
	 * @param User $user
	 */
	protected function redirect( $user ) {
		/**
		 * Filters boolean: True if new users should be logged in after registration.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $is_auth_new_user True if new users should be logged in after registration.
		 * @param \Masteriyo\Models\User $user The registered user object.
		 */
		$is_auth_new_user = apply_filters( 'masteriyo_registration_auth_new_user', masteriyo_registration_is_auth_new_user(), $user );

		// Only redirect after a forced login - otherwise output a success notice.
		if ( $is_auth_new_user ) {
			masteriyo_set_customer_auth_cookie( $user->get_id() );

			/**
			 * Filters redirection URL to redirect to after user registration.
			 *
			 * @since 1.0.0
			 *
			 * @param string $url Redirection URL.
			 */
			$account_page_permalink = masteriyo_get_page_permalink( 'account' ) . '/#/dashboard';
			$redirection_url        = apply_filters( 'masteriyo_registration_redirect_url', $account_page_permalink );
			$redirection_url        = wp_validate_redirect( $redirection_url, $account_page_permalink );

			wp_redirect( $redirection_url ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			exit;
		}
	}

	/**
	 * Get the submitted form data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_form_data() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$data   = array();
		$fields = array( 'first-name', 'last-name', 'username', 'email', 'password', 'confirm-password' );

		foreach ( $fields as $key ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				$data[ $key ] = '';
				continue;
			}

			if ( 'email' === $key ) {
				$data[ $key ] = sanitize_email( wp_unslash( trim( $_POST[ $key ] ) ) );
			}

			if ( 'username' === $key ) {
				$data[ $key ] = sanitize_user( trim( $_POST[ $key ] ) );
			}

			$data[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		}

		return $data;
		// phpcs:enable
	}
}
