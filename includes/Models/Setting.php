<?php
/**
 * Abstract Setting API Class
 *
 * Admin Settings API used by Integrations, Shipping Methods, and Payment Gateways.
 *
 * @since 1.0.0
 *
 * @package  Masteriyo\Models
 */

namespace Masteriyo\Models;

defined( 'ABSPATH' ) || exit;

use Masteriyo\Database\Model;
use Masteriyo\Repository\SettingRepository;

/**
 * Setting class.r
 */
class Setting extends Model {

	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'setting';

	/**
	 * Callbacks for sanitize.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $sanitize_callbacks = array(
		'general'        => array(
			'pages' => array(
				'account_page_id'                 => 'absint',
				'courses_page_id'                 => 'absint',
				'checkout_page_id'                => 'absint',
				'learn_page_id'                   => 'absint',
				'instructor_registration_page_id' => 'absint',
			),
		),
		'payments'       => array(
			'currency' => array(
				'number_of_decimals' => 'absint',
			),
		),
		'course_archive' => array(
			'display' => array(
				'enable_search' => 'masteriyo_string_to_bool',
				'per_page'      => 'absint',
				'per_row'       => 'absint',
			),
		),
		'single_course'  => array(
			'display'         => array(
				'enable_review' => 'masteriyo_string_to_bool',
			),
			'related_courses' => array(
				'enable' => 'masteriyo_string_to_bool',
			),
		),
		'advance'        => array(
			'permalinks' => array(
				'category_base'           => 'sanitize_title',
				'tag_base'                => 'sanitize_title',
				'difficulty_base'         => 'sanitize_title',
				'single_course_permalink' => 'sanitize_text',
			),

			'checkout'   => array(
				'pay'                        => 'sanitize_title',
				'order_received'             => 'sanitize_title',
				'add_payment_method'         => 'sanitize_title',
				'delete_payment_method'      => 'sanitize_title',
				'set_default_payment_method' => 'sanitize_title',
			),
		),
		'quiz'           => array(
			'styling' => array(
				'questions_display_per_page' => 'absint',
			),
		),
		'payments'       => array(
			'offline' => array(
				'enable' => 'masteriyo_string_to_bool',
			),
			'paypal'  => array(
				'enable'                  => 'masteriyo_string_to_bool',
				'ipn_email_notifications' => 'masteriyo_string_to_bool',
				'sandbox'                 => 'masteriyo_string_to_bool',
				'debug'                   => 'masteriyo_string_to_bool',
			),
		),
		'emails'         => array(
			'general'    => array(
				'enable' => 'masteriyo_string_to_bool',
			),
			'admin'      => array(
				'new_order' => array(
					'enable' => 'masteriyo_string_to_bool',
				),
			),
			'instructor' => array(
				'instructor_registration' => array(
					'enable' => 'masteriyo_string_to_bool',
				),
			),
			'student'    => array(
				'student_registration' => array(
					'enable' => 'masteriyo_string_to_bool',
				),
				'completed_order'      => array(
					'enable' => 'masteriyo_string_to_bool',
				),
				'onhold_order'         => array(
					'enable' => 'masteriyo_string_to_bool',
				),
				'cancelled_order'      => array(
					'enable' => 'masteriyo_string_to_bool',
				),
				'completed_course'     => array(
					'enable' => 'masteriyo_string_to_bool',
				),
			),
		),
		'advance'        => array(
			'uninstall' => array(
				'remove_data' => 'masteriyo_string_to_bool',
			),
		),
	);

	/**
	 * The posted settings data. When empty, $_POST data will be used.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $data = array(
		'general'        => array(
			'styling'     => array(
				'primary_color' => '#4584FF',
				'theme'         => 'minimum',
			),
			'widgets_css' => '',
			'pages'       => array(
				'courses_page_id'                 => '',
				'account_page_id'                 => '',
				'checkout_page_id'                => '',
				'learn_page_id'                   => '',
				'instructor_registration_page_id' => '',
			),
		),
		'course_archive' => array(
			'display' => array(
				'enable_search'  => true,
				'per_page'       => 12,
				'per_row'        => 3,
				'thumbnail_size' => 'masteriyo_thumbnail',
			),
		),
		'single_course'  => array(
			'display'         => array(
				'enable_review' => true,
			),
			'related_courses' => array(
				'enable' => true,
			),
		),
		'learn_page'     => array(
			'general' => array(
				'logo_id' => '',
			),
			'display' => array(
				'enable_questions_answers' => true,
			),
		),
		'payments'       => array(
			'store'    => array(
				'country'       => '',
				'city'          => '',
				'state'         => '',
				'address_line1' => '',
				'address_line2' => '',
			),
			'currency' => array(
				'currency'           => 'USD',
				'currency_position'  => 'left',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'number_of_decimals' => 2,
			),
			'offline'  => array(
				// Offline payment
				'enable'       => false,
				'title'        => 'Offline payment',
				'description'  => 'Pay with offline payment.',
				'instructions' => 'Pay with offline payment',
			),
			'paypal'   => array(
				// Standard Paypal
				'enable'                  => false,
				'title'                   => 'Paypal',
				'description'             => 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.',
				'ipn_email_notifications' => true,
				'sandbox'                 => false,
				'email'                   => '',
				'receiver_email'          => '',
				'identity_token'          => '',
				'invoice_prefix'          => 'masteriyo-',
				'payment_action'          => 'sale',
				'image_url'               => '',
				'debug'                   => false,
				'sandbox_api_username'    => '',
				'sandbox_api_password'    => '',
				'live_api_username'       => '',
				'live_api_password'       => '',
				'live_api_signature'      => '',
				'sandbox_api_signature'   => '',

			),
		),
		'quiz'           => array(
			'styling' => array(
				'questions_display_per_page' => 5,
			),
		),
		'emails'         => array(
			'general'    => array(
				'enable'     => true,
				'from_name'  => '',
				'from_email' => '',
			),
			'admin'      => array(
				'new_order' => array(
					'enable'     => true,
					'recipients' => array(),
					'subject'    => 'You made a sale!',
					'content'    => '',
				),
			),
			'instructor' => array(
				'instructor_registration' => array(
					'enable'     => true,
					'recipients' => array(),
					'subject'    => 'Registration Complete!',
					'content'    => '',
				),
			),
			'student'    => array(
				'student_registration' => array(
					'enable'  => true,
					'subject' => 'Registration Complete!',
					'content' => '',
				),
				'completed_order'      => array(
					'enable'  => true,
					'subject' => 'Thanks for your purchase!',
					'content' => '',
				),
				'onhold_order'         => array(
					'enable'  => true,
					'subject' => 'Order On-Hold!',
					'content' => '',
				),
				'cancelled_order'      => array(
					'enable'     => true,
					'recipients' => array(),
					'subject'    => 'Order Cancelled!',
					'content'    => '',
				),
			),
		),
		'advance'        => array(
			'permalinks' => array(
				'category_base'           => 'course-category',
				'tag_base'                => 'course-tag',
				'difficulty_base'         => 'course-difficulty',
				'single_course_permalink' => 'course',
			),
			// Checkout endpoints.
			'checkout'   => array(
				'pay'                        => 'order-pay',
				'order_received'             => 'order-received',
				'add_payment_method'         => 'add-payment-method',
				'delete_payment_method'      => 'delete-payment-method',
				'set_default_payment_method' => 'set-default-payment-method',
			),
			'debug'      => array(
				'template_debug' => false,
				'debug'          => false,
			),
			'uninstall'  => array(
				'remove_data' => false,
			),
		),
	);

	/**
	 * Get the setting if ID
	 *
	 * @since 1.0.0
	 *
	 * @param SettingRepository $setting_repository Setting Repository,
	 */
	public function __construct( SettingRepository $setting_repository ) {
		$this->repository = $setting_repository;
		$this->set_default_values();
	}

	/**
	 * Set default values.
	 *
	 * @since 1.3.4
	 */
	protected function set_default_values() {
		if ( empty( trim( strval( $this->get( 'email.general.from_email' ) ) ) ) ) {
			$this->set( 'emails.general.from_email', get_bloginfo( 'admin_email' ) );
		}

		if ( empty( trim( strval( $this->get( 'email.general.from_name' ) ) ) ) ) {
			$this->set( 'emails.general.from_name', get_bloginfo( 'name' ) );
		}
	}

	/**
	 * Get data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 */
	public function set_data( $data ) {
		$data_dot_arr = masteriyo_array_dot( $data );

		foreach ( $data_dot_arr as $prop => $value ) {
			$this->set( $prop, $value );
		}

		$this->set_default_values();
	}

	/**
	 * Sanitize the settings
	 *
	 * @since 1.0.0
	 *
	 * @param string $prop    Name of prop to set.
	 * @param mixed  $value   Value of the prop.
	 *
	 * @return mixed
	 */
	protected function sanitize( $prop, $value ) {
		$callback = masteriyo_array_get( $this->sanitize_callbacks, $prop );

		if ( is_callable( $callback ) ) {
			$value = call_user_func_array( $callback, array( $value ) );
		}

		return $value;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 1.0.0
	 * @param string $prop    Name of prop to set.
	 * @param mixed  $value   Value of the prop.
	 */
	public function set( $prop, $value ) {
		$value = $this->sanitize( $prop, $value );
		masteriyo_array_set( $this->data, $prop, $value );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since  1.0.0
	 * @param  string $prop Name of prop to get.
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'. What the value is for. Valid values are view and edit.
	 * @return mixed
	 */
	public function get( $prop, $context = 'view' ) {
		$value = masteriyo_array_get( $this->data, $prop );

		if ( 'view' === $context ) {
			/**
			 * Filters setting value.
			 *
			 * @since 1.0.0
			 *
			 * @param mixed $value Setting value.
			 * @param string $prop Setting name.
			 * @param Masteriyo\Models\Setting $setting Setting object.
			 */
			$value = apply_filters( 'masteriyo_get_setting_value', $value, $prop, $this );
		}

		return $value;
	}

	/**
	 * Reset defaults.
	 *
	 * @since 1.4.2
	 */
	public function reset() {
		$setting    = masteriyo( 'setting' );
		$this->data = $setting->get_data();
	}
}
