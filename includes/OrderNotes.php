<?php
/**
 * Order Notes handlers.
 *
 * @package Masteriyo
 *
 * @since 1.0.4
 */

namespace Masteriyo;

defined( 'ABSPATH' ) || exit;

/**
 * OrderNotes class.
 */
class OrderNotes {

	/**
	 * Constructor.
	 *
	 * @since 1.0.4
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init.
	 *
	 * @since 1.0.4
	 */
	protected function init() {
		add_filter( 'comments_open', array( $this, 'comments_open' ), 10, 2 );
		add_action( 'comment_moderation_recipients', array( $this, 'comment_moderation_recipients' ), 10, 2 );
		add_action( 'parse_comment_query', array( $this, 'remove_order_note_from_query' ) );
	}

	/**
	 * See if comments are open.
	 *
	 * @since 1.0.4
	 *
	 * @param  bool $open    Whether the current post is open for comments.
	 * @param  int  $post_id Post ID.
	 *
	 * @return bool
	 */
	public function comments_open( $open, $post_id ) {
		if ( 'mto-course' === get_post_type( $post_id ) ) {
			$open = false;
		}
		return $open;
	}

	/**
	 * Modify recipient of review email.
	 *
	 * @since 1.0.4
	 *
	 * @param array $emails     Emails.
	 * @param int   $comment_id Comment ID.
	 *
	 * @return array
	 */
	public function comment_moderation_recipients( $emails, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( $comment && 'mto-course' === get_post_type( $comment->comment_post_ID ) ) {
			$emails = array( get_option( 'admin_email' ) );
		}

		return $emails;
	}

	/**
	 * Remove the course review from the comments query.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Comment_Query $query
	 */
	public function remove_order_note_from_query( $query ) {
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
		$query->query_vars['type__not_in'] = array_unique( array_merge( $query->query_vars['type__not_in'], array( 'mto_order_note' ) ) );
	}
}
