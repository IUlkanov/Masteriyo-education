<?php
/**
 * Initialize form handlers.
 *
 * @package Masetriyo\Classes\
 */

namespace Masteriyo\FormHandler;

defined( 'ABSPATH' ) || exit;


/**
 * Form Handlers class.
 */
class FormHandlers {
	/**
	 * List of form handlers.
	 *
	 * @since 1.0.0
	 *
	 * @var FormHandler[]
	 */
	public static $form_handlers;

	/**
	 * Initialize the form handlers.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init() {
		$namespace = '\Masteriyo\FormHandler';

		/**
		 * Filters form handler classes.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $form_handlers Form handler classes.
		 */
		self::$form_handlers = apply_filters(
			'masteriyo_form_handlers',
			array(
				"{$namespace}\\RegistrationFormHandler",
				"{$namespace}\\RequestPasswordResetFormHandler",
				"{$namespace}\\PasswordResetFormHandler",
				"{$namespace}\\ChangePasswordFormHandler",
				"{$namespace}\\AddToCartFormHandler",
				"{$namespace}\\CheckoutFormHandler",
				"{$namespace}\\InstructorRegistrationFormHandler",
			)
		);

		foreach ( self::$form_handlers as $form_handler ) {
			$instance = new $form_handler();
		}
	}
}
