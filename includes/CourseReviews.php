<?php
/**
 * CourseReviews handlers.
 *
 * @package Masteriyo
 *
 * @since 1.0.0
 */

namespace Masteriyo;

defined( 'ABSPATH' ) || exit;

/**
 * CourseReviews class.
 */
class CourseReviews {
	/**
	 * Init.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_filter( 'comments_open', array( __CLASS__, 'comments_open' ), 10, 2 );
		add_action( 'comment_moderation_recipients', array( __CLASS__, 'comment_moderation_recipients' ), 10, 2 );
		add_action( 'wp_update_comment_count', array( __CLASS__, 'wp_update_comment_count' ) );
		add_filter( 'get_avatar_comment_types', array( __CLASS__, 'add_avatar_for_review_comment_type' ) );
		add_action( 'parse_comment_query', array( __CLASS__, 'remove_course_review_from_query' ) );

	}

	/**
	 * See if comments are open.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool $open    Whether the current post is open for comments.
	 * @param  int  $post_id Post ID.
	 *
	 * @return bool
	 */
	public static function comments_open( $open, $post_id ) {
		if ( 'mto-course' === get_post_type( $post_id ) ) {
			$open = false;
		}
		return $open;
	}

	/**
	 * Ensure course average rating and review count is kept up to date.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post
	 */
	public static function wp_update_comment_count( $post ) {
		if ( 'mto-course' === get_post_type( $post ) ) {
			self::update_course_review_stats( $post );
		}
	}

	/**
	 * Update average rating and review counts of course.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|WP_Post|Course $course
	 */
	public static function update_course_review_stats( $course ) {
		$course = masteriyo_get_course( $course );

		if ( is_null( $course ) ) {
			return;
		}

		$course->set_rating_counts( self::get_rating_counts( $course ) );
		$course->set_average_rating( self::get_average_rating( $course ) );
		$course->set_review_count( self::get_review_count( $course ) );
		$course->save();
	}

	/**
	 * Make sure WP displays avatars for comments with the `course_review` type.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $comment_types Comment types.
	 *
	 * @return array
	 */
	public static function add_avatar_for_review_comment_type( $comment_types ) {
		return array_merge( $comment_types, array( 'mto_course_review' ) );
	}

	/**
	 * Modify recipient of review email.
	 *
	 * @since 1.0.0
	 *
	 * @param array $emails     Emails.
	 * @param int   $comment_id Comment ID.
	 *
	 * @return array
	 */
	public static function comment_moderation_recipients( $emails, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( $comment && 'mto-course' === get_post_type( $comment->comment_post_ID ) ) {
			$emails = array( get_option( 'admin_email' ) );
		}

		return $emails;
	}

	/**
	 * Get course rating for a course. Please note this is not cached.
	 *
	 * @since 1.0.0
	 *
	 * @param Course $course course instance.
	 *
	 * @return float
	 */
	public static function get_average_rating( &$course ) {
		global $wpdb;

		$count = $course->get_rating_count();

		if ( $count ) {
			$ratings = $wpdb->get_var(
				$wpdb->prepare(
					"
					SELECT SUM(comment_karma) FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND comment_approved = '1'
					AND comment_type = 'mto_course_review'
					AND comment_karma > 0
					",
					$course->get_id()
				)
			);
			$average = number_format( $ratings / $count, 2, '.', '' );
		} else {
			$average = 0;
		}

		return $average;
	}

	/**
	 * Get course review count for a course (not replies). Please note this is not cached.
	 *
	 * @since 1.0.0
	 *
	 * @param Course $course course instance.
	 *
	 * @return int
	 */
	public static function get_review_count( &$course ) {
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_parent = 0
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND comment_type = 'mto_course_review'
				",
				$course->get_id()
			)
		);

		return $count;
	}

	/**
	 * Get course rating count for a course. Please note this is not cached.
	 *
	 * @since 1.0.0
	 *
	 * @param Course $course course instance.
	 *
	 * @return int[]
	 */
	public static function get_rating_counts( &$course ) {
		global $wpdb;

		$counts     = array();
		$raw_counts = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT comment_karma, COUNT( * ) as rating_count FROM $wpdb->comments
				WHERE comment_post_ID = %d
				AND comment_approved = '1'
				AND comment_karma > 0
				AND comment_type = 'mto_course_review'
				GROUP BY comment_karma
				",
				$course->get_id()
			)
		);

		foreach ( $raw_counts as $count ) {
			$counts[ $count->comment_karma ] = absint( $count->rating_count ); // WPCS: slow query ok.
		}

		return $counts;
	}

	/**
	 * Remove the course review from the comments query.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Comment_Query $query
	 */
	public static function remove_course_review_from_query( $query ) {
		// Bail early if  global pagnow is not set or isn't admin dashboard.
		if ( ! isset( $GLOBALS['pagenow'] ) || ! is_admin() ) {
			return;
		}

		// Bail if the page is not wp comments list page or dashboard.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'edit-comments.php', 'index.php' ), true ) ) {
			return;
		}

		if ( ! isset( $query->query_vars['type__not_in'] ) ) {
			$query->query_vars['type__not_in'] = array();
		}

		$query->query_vars['type__not_in'] = (array) $query->query_vars['type__not_in'];
		$query->query_vars['type__not_in'] = array_unique( array_merge( $query->query_vars['type__not_in'], array( 'mto_course_review' ) ) );
	}
}
