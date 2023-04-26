<?php
/**
 * User courses class.
 *
 * @package Masteriyo
 *
 * @since 1.5.37
 */

namespace Masteriyo;

defined( 'ABSPATH' ) || exit;

/**
 * User courses class.
 *
 * @package Masteriyo
 *
 * @since 1.5.37
 */
class UserCourses {

	/**
	 * Initialize.
	 *
	 * @since 1.5.37
	 */
	public function init() {
		add_filter( 'masteriyo_after_delete_course', array( $this, 'delete_user_courses_after_course_deletion' ), 10, 2 );
	}

	/**
	 * Delete user-course records after a course is deleted.
	 *
	 * @since 1.5.37
	 *
	 * @param integer $course_id The course ID.
	 * @param \Masteriyo\Models\Course $course The deleted course object.
	 */
	public function delete_user_courses_after_course_deletion( $course_id, $course ) {
		global $wpdb;

		/**
		 * Filters boolean: True if user-course records should be deleted after a course is deleted. Otherwise, false.
		 *
		 * @since 1.5.37
		 *
		 * @param boolean $bool True if user-course records should be deleted after a course is deleted. Otherwise, false.
		 * @param integer $course_id The deleted course ID.
		 * @param \Masteriyo\Models\Course $course The deleted course object.
		 */
		$delete = apply_filters( 'masteriyo_is_delete_user_courses_after_course_deletion', true, $course_id, $course );

		if ( $delete ) {
			$wpdb->delete(
				"{$wpdb->prefix}masteriyo_user_items",
				array(
					'item_id' => $course_id,
				)
			);
			$wpdb->query( "DELETE meta FROM {$wpdb->prefix}masteriyo_user_itemmeta meta LEFT JOIN {$wpdb->prefix}masteriyo_user_items user_items ON user_items.id = meta.user_item_id WHERE user_items.id IS NULL;" );
		}
	}
}
