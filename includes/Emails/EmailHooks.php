<?php
/**
 * EmailHooks class.
 *
 * @package Masteriyo\Emails
 *
 * @since 1.0.0
 */

namespace Masteriyo\Emails;

use Masteriyo\Emails\Admin\NewOrderEmailToAdmin;
use Masteriyo\Emails\Student\OnHoldOrderEmailToStudent;
use Masteriyo\Emails\Student\CancelledOrderEmailToStudent;
use Masteriyo\Emails\Student\CompletedOrderEmailToStudent;
use Masteriyo\Emails\Student\StudentRegistrationEmailToStudent;
use Masteriyo\Emails\Instructor\InstructorRegistrationEmailToInstructor;

defined( 'ABSPATH' ) || exit;

/**
 * EmailHooks Class.
 *
 * @since 1.0.0
 *
 * @package Masteriyo\Emails
 */
class EmailHooks {
	/**
	 * Register email hooks.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'masteriyo_user_password_reset', array( __CLASS__, 'schedule_password_reset_email' ), 10, 2 );

		add_action( 'masteriyo_after_order_object_save', array( __CLASS__, 'schedule_new_order_email_to_admin' ), 10, 3 );

		add_action( 'masteriyo_order_status_completed', array( __CLASS__, 'schedule_completed_order_email_to_student' ), 10, 2 );
		add_action( 'masteriyo_order_status_on-hold', array( __CLASS__, 'schedule_onhold_order_email_to_student' ), 10, 2 );
		add_action( 'masteriyo_order_status_cancelled', array( __CLASS__, 'schedule_cancelled_order_email_to_student' ), 10, 2 );

		add_action( 'masteriyo_new_user', array( __CLASS__, 'schedule_student_registration_email_to_student' ), 10, 2 );
		add_action( 'masteriyo_new_user', array( __CLASS__, 'schedule_instructor_registration_email_to_instructor' ), 10, 2 );
	}

	/**
	 * Schedule password reset email.
	 *
	 * @since 1.5.35
	 *
	 * @param \WP_User $user WP User object.
	 * @param array $data Submitted data through form.
	 * @return void
	 */
	public static function schedule_password_reset_email( $user, $data ) {
		$email = new ResetPasswordEmail();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action(
				$email->get_schedule_handle(),
				array(
					'id' => $user->get_id(),
				),
				'masteriyo'
			);
		} else {
			$email->trigger( $user->get_id(), wp_generate_password() );
		}
	}

	/**
	 * Schedule order completed email to student.
	 *
	 * @since 1.5.35
	 *
	 * @param int $order_id
	 * @param \Masteriyo\Models\Order $order
	 */
	public static function schedule_completed_order_email_to_student( $order_id, $order ) {
		$email = new CompletedOrderEmailToStudent();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $order_id ), 'masteriyo' );
		} else {
			$email->trigger( $order_id );
		}

	}

	/**
	 * Schedule order onhold email to student.
	 *
	 * @since 1.5.35
	 *
	 * @param int $order_id
	 * @param \Masteriyo\Models\Order $order
	 */
	public static function schedule_onhold_order_email_to_student( $order_id, $order ) {
		$email = new OnHoldOrderEmailToStudent();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $order_id ), 'masteriyo' );
		} else {
			$email->trigger( $order_id );
		}
	}

	/**
	 * Schedule order cancelled email to student.
	 *
	 * @since 1.5.35
	 *
	 * @param int $order_id
	 * @param \Masteriyo\Models\Order $order
	 */
	public static function schedule_cancelled_order_email_to_student( $order_id, $order ) {
		$email = new CancelledOrderEmailToStudent();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $order_id ), 'masteriyo' );
		} else {
			$email->trigger( $order_id );
		}
	}

	/**
	 * Schedule new student registration email to student.
	 *
	 * @since 1.5.35
	 *
	 *  @param int $user_id User ID.
	 * @param \Masteriyo\Models\User $user User object.
	 */
	public static function schedule_student_registration_email_to_student( $user_id, $user ) {
		if ( ! $user->has_roles( 'masteriyo_student' ) ) {
			return;
		}

		$email = new StudentRegistrationEmailToStudent();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $user->get_id() ), 'masteriyo' );
		} else {
			$email->trigger( $user_id );
		}
	}

	/**
	 * Schedule new instructor registration email to instructor.
	 *
	 * @since 1.5.35
	 *
	 *  @param int $user_id User ID.
	 * @param \Masteriyo\Models\User $user User object.
	 */
	public static function schedule_instructor_registration_email_to_instructor( $user_id, $user ) {
		if ( ! $user->has_roles( 'masteriyo_instructor' ) ) {
			return;
		}

		$email = new InstructorRegistrationEmailToInstructor();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( self::is_email_schedule_enabled() ) {
			as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $user_id ), 'masteriyo' );
		} else {
			$email->trigger( $user_id );
		}
	}

	/**
	 * Schedule new order email.
	 *
	 * @since 1.0.0
	 *
	 * @param \Masteriyo\Models\Order $order Order object.
	 * @param \Masteriyo\Repository\OrderRepository $repository THe data store persisting the data.
	 * @param boolean $create True if the order is created.
	 *
	 */
	public static function schedule_new_order_email_to_admin( $order, $repository, $create ) {
		$email = new NewOrderEmailToAdmin();

		if ( ! $email->is_enabled() ) {
			return;
		}

		if ( $create ) {
			if ( self::is_email_schedule_enabled() ) {
				as_enqueue_async_action( $email->get_schedule_handle(), array( 'id' => $order->get_id() ), 'masteriyo' );
			} else {
				$email->trigger( $order->get_id() );
			}
		}

	}

	/**
	 * Return true if the action schedule is enabled for Email.
	 *
	 * @since 1.5.35
	 *
	 * @return boolean
	 */
	public static function is_email_schedule_enabled() {
		return masteriyo_is_email_schedule_enabled();
	}
}
