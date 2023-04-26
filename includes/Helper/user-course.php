<?php
/**
 * User course functions.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Masteriyo\Helper
 */

use Masteriyo\Enums\UserCourseStatus;
use Masteriyo\Query\UserCourseQuery;

/**
 * Get user course.
 *
 * @since 1.0.0
 *
 * @param int $user_course_id User course ID.
 * @return Masteriyo\Models\UserCourse|null
 */
function masteriyo_get_user_course( $user_course_id ) {
	try {
		$user_course = masteriyo( 'user-course' );
		$user_course->set_id( $user_course_id );

		$user_course_repo = masteriyo( 'user-course.store' );
		$user_course_repo->read( $user_course );

		return $user_course;
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * Get list of status for user course.
 *
 * @since 1.0.0
 * @deprecated 1.5.3
 *
 * @return array
 */
function masteriyo_get_user_course_statuses() {
	$statuses = array(
		'active' => array(
			'label' => _x( 'Active', 'User Course status', 'masteriyo' ),
		),
	);

	/**
	 * Filters statuses for user course.
	 *
	 * @since 1.0.0
	 *
	 * @param array $statuses The statuses for user course.
	 */
	return apply_filters( 'masteriyo_user_course_statuses', $statuses );
}

/**
 * Count enrolled users in a course.
 *
 * @since 1.0.0
 * @since Added support $course parameter to be Masteriyo\Models\Course or WP_Post.
 *
 * @param \Masteriyo\Models\Course|WP_Post|int $course Course ID or Course object.
 *
 * @return integer
 */
function masteriyo_count_enrolled_users( $course ) {
	global $wpdb;

	$count  = 0;
	$course = masteriyo_get_course( $course );

	if ( $wpdb && $course ) {
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->prefix}masteriyo_user_items WHERE item_id = %d AND ( status = %s OR status = %s )",
				array(
					$course->get_id(),
					UserCourseStatus::ACTIVE,
					UserCourseStatus::ENROLLED,
				)
			)
		);
	}

	/**
	 * Filters enrolled users count for a course.
	 *
	 * @since 1.0.0
	 * @since 1.5.17 Removed third $query parameter.
	 *
	 * @param integer $count The enrolled users count for the given course.
	 * @param \Masteriyo\Models\Course|null $course Course ID or Course object.
	 */
	return apply_filters( 'masteriyo_count_enrolled_users', absint( $count ), $course );
}

/**
 * Get the number of active courses.
 *
 * @since 1.0.0
 *
 * @param Masteriyo\Models\User|int $user User.
 *
 * @return int
 */
function masteriyo_get_active_courses_count( $user ) {
	global $wpdb;

	$user_id = is_a( $user, 'Masteriyo\Models\User' ) ? $user->get_id() : $user;

	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}masteriyo_user_activities
			WHERE user_id = %d AND activity_type = 'course_progress'
			AND ( activity_status = 'started' OR activity_status = 'progress' )  AND parent_id = 0",
			$user_id
		)
	);

	return $count;
}

/**
 * Get the number of user courses.
 *
 * @since 1.0.0
 *
 * @param Masteriyo\Models\User|int $user User.
 *
 * @return int
 */
function masteriyo_get_user_courses_count_by_course( $course ) {
	global $wpdb;

	$course_id = is_a( $course, 'Masteriyo\Models\Course' ) ? $course->get_id() : $course;

	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}masteriyo_user_items
			WHERE item_id = %d AND item_type = 'user_course'",
			$course_id
		)
	);

	return $count;
}

/**
 * Get user/enrolled course by user ID and course ID.
 *
 * @since 1.5.4
 *
 * @param int $user_id User ID.
 * @param int $course_id Course ID.
 * @return Masteriyo\Models\UserCourse
 */
function masteriyo_get_user_course_by_user_and_course( $user_id, $course_id ) {
	$query = new UserCourseQuery(
		array(
			'course_id' => $course_id,
			'user_id'   => $user_id,
		)
	);

	return current( $query->get_user_courses() );
}
