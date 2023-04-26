<?php
/**
 * Activation class.
 *
 * @since 1.0.0
 */

namespace Masteriyo;

class Activation {

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init() {
		register_activation_hook( Constants::get( 'MASTERIYO_PLUGIN_FILE' ), array( __CLASS__, 'on_activate' ) );
	}

	/**
	 * Callback for plugin activation hook.
	 *
	 * @since 1.0.0
	 */
	public static function on_activate() {
		self::create_roles();
		self::create_pages();
		self::assign_core_capabilities_to_admin();
		self::attach_placeholder_image();

		/**
		 * Fire after masteriyo is activated.
		 *
		 * @since 1.5.37
		 */
		do_action( 'masteriyo_activation' );
	}

	/**
	 * Create roles.
	 *
	 * @since 1.5.37
	 */
	private static function create_roles() {
		foreach ( Roles::get_all() as $role_slug => $role ) {
			add_role( $role_slug, $role['display_name'], $role['capabilities'] );
		}
	}

	/**
	 * Create pages that the plugin relies on, storing page IDs in variables.
	 *
	 * @since 1.0.0
	 */
	public static function create_pages() {
		/**
		 * Filters account page shortcode tag.
		 *
		 * @since 1.0.0
		 *
		 * @param string $tag The shortcode tag.
		 */
		$account_page_shortcode_tag = apply_filters( 'masteriyo_account_shortcode_tag', 'masteriyo_account' );

		/**
		 * Filters checkout page shortcode tag.
		 *
		 * @since 1.0.0
		 *
		 * @param string $tag The shortcode tag.
		 */
		$checkout_page_shortcode_tag = apply_filters( 'masteriyo_checkout_shortcode_tag', 'masteriyo_checkout' );

		/**
		 * Filters instructor registration form shortcode tag.
		 *
		 * @since 1.3.0
		 *
		 * @param string $tag The shortcode tag.
		 */
		$instructor_registration_form_shortcode_tag = apply_filters( 'masteriyo_instructor_registration_shortcode_tag', 'masteriyo_instructor_registration' );

		/**
		 * Filters the list of pages that will be created on plugin activation.
		 *
		 * @since 1.0.0
		 *
		 * @param array[] $pages List of pages.
		 */
		$pages = apply_filters(
			'masteriyo_create_pages',
			array(
				'courses'                 => array(
					'name'         => _x( 'courses', 'Page slug', 'masteriyo' ),
					'title'        => _x( 'Courses', 'Page title', 'masteriyo' ),
					'content'      => '',
					'setting_name' => 'courses_page_id',
				),
				'account'                 => array(
					'name'         => _x( 'account', 'Page slug', 'masteriyo' ),
					'title'        => _x( 'Account', 'Page title', 'masteriyo' ),
					'content'      => '<!-- wp:shortcode -->[' . $account_page_shortcode_tag . ']<!-- /wp:shortcode -->',
					'setting_name' => 'account_page_id',
				),
				'checkout'                => array(
					'name'         => _x( 'checkout', 'Page slug', 'masteriyo' ),
					'title'        => _x( 'Checkout', 'Page title', 'masteriyo' ),
					'content'      => '<!-- wp:shortcode -->[' . $checkout_page_shortcode_tag . ']<!-- /wp:shortcode -->',
					'setting_name' => 'checkout_page_id',
				),
				'learn'                   => array(
					'name'         => _x( 'learn', 'Page slug', 'masteriyo' ),
					'title'        => _x( 'Learn', 'Page title', 'masteriyo' ),
					'content'      => '',
					'setting_name' => 'learn_page_id',
				),
				'instructor-registration' => array(
					'name'         => _x( 'instructor-registration', 'Page slug', 'masteriyo' ),
					'title'        => _x( 'Instructor Registration', 'Page title', 'masteriyo' ),
					'content'      => '<!-- wp:shortcode -->[' . $instructor_registration_form_shortcode_tag . ']<!-- /wp:shortcode -->',
					'setting_name' => 'instructor_registration_page_id',
				),
			)
		);
		foreach ( $pages as $key => $page ) {
			$setting_name = $page['setting_name'];
			$post_id      = masteriyo_get_setting( "general.pages.{$setting_name}" );
			$post         = get_post( $post_id );

			if ( $post && 'page' === $post->post_type ) {
				continue;
			}

			$page_id = masteriyo_create_page( esc_sql( $page['name'] ), $setting_name, $page['title'], $page['content'], ! empty( $page['parent'] ) ? masteriyo_get_page_id( $page['parent'] ) : '' );
			masteriyo_set_setting( "general.pages.{$setting_name}", $page_id );
		}
	}


	/**
	 * Assign core capabilities to admin role.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function assign_core_capabilities_to_admin() {
		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		$capabilities = Capabilities::get_admin_capabilities();

		foreach ( $capabilities as $cap => $bool ) {
			wp_roles()->add_cap( 'administrator', $cap );
		}
	}

	/**
	 * Insert masteriyo placeholder image to WP Media library.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function attach_placeholder_image() {
		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			return false;
		}

		$wp_filesystem = new \WP_Filesystem_Direct( null );

		// Get upload directory.
		$upload_dir = wp_upload_dir();
		// Making masteriyo directory on uploads folder.
		$upload_masteriyo_dir = $upload_dir['basedir'] . '/masteriyo';

		$img_file           = masteriyo_get_plugin_dir() . '/assets/img/placeholder.jpg';
		$filename           = basename( $img_file );
		$prev_attachment_id = get_option( 'masteriyo_placeholder_image', 0 );
		$attach_file        = $upload_masteriyo_dir . '/' . sanitize_file_name( $filename );

		// Return if image already exists.
		if ( $wp_filesystem->exists( $attach_file ) && wp_attachment_is_image( $prev_attachment_id ) ) {
			return;
		}

		if ( ! file_exists( $upload_masteriyo_dir ) ) {
			wp_mkdir_p( $upload_masteriyo_dir );
		}

		$upload = $wp_filesystem->copy( $img_file, $attach_file, true );

		if ( $upload ) {
			$wp_filetype = wp_check_filetype( $filename, null );

			$attachment    = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', sanitize_file_name( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			$attachment_id = wp_insert_attachment( $attachment, $attach_file );

			// Update attachment ID.
			update_option( 'masteriyo_placeholder_image', $attachment_id );

			if ( ! is_wp_error( $attachment_id ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $attach_file );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );
			}
		}
	}
}
