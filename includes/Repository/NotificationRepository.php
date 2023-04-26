<?php
/**
 * Notification Repository class.
 *
 * @since 1.4.1
 *
 * @package Masteriyo\Repository
 */

namespace Masteriyo\Repository;

use Masteriyo\Database\Model;

/**
 * Notification Repository class.
 */
class NotificationRepository extends AbstractRepository implements RepositoryInterface {
	/**
	 * Create notification in database.
	 *
	 * @since 1.4.1
	 *
	 * @param \Masteriyo\Models\Notification $notification Notification object.
	 */
	public function create( Model &$notification ) {
		global $wpdb;

		if ( ! $notification->get_created_at( 'edit' ) ) {
			$notification->set_created_at( current_time( 'mysql', true ) );
		}

		if ( empty( $notification->get_created_by() ) ) {
			$notification->set_created_by( get_current_user_id() );
		}

		$result = $wpdb->insert(
			"{$wpdb->prefix}masteriyo_notifications",
			/**
			 * Filters new notification data before creating.
			 *
			 * @since 1.4.1
			 *
			 * @param array $data New notification data.
			 * @param Masteriyo\Models\Notification $notification Notification object.
			 */
			apply_filters(
				'masteriyo_new_notification_data',
				array(
					'title'         => $notification->get_title( 'edit' ),
					'description'   => $notification->get_description( 'edit' ),
					'user_id'       => $notification->get_user_id( 'edit' ),
					'created_by'    => $notification->get_created_by( 'edit' ),
					'code'          => $notification->get_code( 'edit' ),
					'status'        => $notification->get_status( 'edit' ),
					'type'          => $notification->get_type( 'edit' ),
					'level'         => $notification->get_level( 'edit' ),
					'action_ok'     => $notification->get_action_ok( 'edit' ),
					'action_cancel' => $notification->get_action_cancel( 'edit' ),
					'action_1'      => $notification->get_action_1( 'edit' ),
					'action_2'      => $notification->get_action_2( 'edit' ),
					'action_3'      => $notification->get_action_3( 'edit' ),
					'created_at'    => gmdate( 'Y-m-d H:i:s', $notification->get_created_at( 'edit' )->getTimestamp() ),
					'modified_at'   => $notification->get_modified_at( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_modified_at( 'edit' )->getTimestamp() ) : '',
					'expire_at'     => $notification->get_expire_at( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_expire_at( 'edit' )->getTimestamp() ) : '',
				)
			)
		);

		if ( $result && $wpdb->insert_id ) {
			$notification->set_id( $wpdb->insert_id );
			$notification->apply_changes();
			$this->clear_cache( $notification );

			/**
			 * Fires after creating a notification.
			 *
			 * @since 1.4.1
			 *
			 * @param integer $id The notification ID.
			 * @param \Masteriyo\Models\Notification $object The notification object.
			 */
			do_action( 'masteriyo_new_notification', $notification->get_id(), $notification );
		}
	}

	/**
	 * Read a notification.
	 *
	 * @since 1.4.1
	 *
	 * @param \Masteriyo\Models\Notification $notification notification object.
	 *
	 * @throws \Exception If invalid notification.
	 */
	public function read( Model &$notification ) {
		global $wpdb;

		$notification_obj = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}masteriyo_notifications WHERE id = %d;",
				$notification->get_id()
			)
		);

		if ( ! $notification_obj ) {
			throw new \Exception( __( 'Invalid notification.', 'masteriyo' ) );
		}

		$notification->set_props(
			array(
				'title'         => $notification_obj->title,
				'description'   => $notification_obj->description,
				'user_id'       => $notification_obj->user_id,
				'created_by'    => $notification_obj->created_by,
				'code'          => $notification_obj->code,
				'status'        => $notification_obj->status,
				'type'          => $notification_obj->type,
				'level'         => $notification_obj->level,
				'action_ok'     => $notification_obj->action_ok,
				'action_cancel' => $notification_obj->action_cancel,
				'action_1'      => $notification_obj->action_1,
				'action_2'      => $notification_obj->action_2,
				'action_3'      => $notification_obj->action_3,
				'created_at'    => $this->string_to_timestamp( $notification_obj->created_at ),
				'modified_at'   => $this->string_to_timestamp( $notification_obj->modified_at ),
				'expire_at'     => $this->string_to_timestamp( $notification_obj->expire_at ),
			)
		);

		$notification->set_object_read( true );

		/**
		 * Notification read hook.
		 *
		 * @since 1.4.1
		 *
		 * @param int $id Notification ID.
		 * @param \Masteriyo\Models\Notification $notification Notification object.
		 */
		do_action( 'masteriyo_notification_read', $notification->get_id(), $notification );
	}

	/**
	 * Update a notification.
	 *
	 * @since 1.4.1
	 *
	 * @param \Masteriyo\Models\Notification $notification notification object.
	 *
	 * @return void.
	 */
	public function update( Model &$notification ) {
		global $wpdb;

		$changes = $notification->get_changes();

		$notification_data_keys = array(
			'title',
			'description',
			'user_id',
			'created_by',
			'code',
			'status',
			'type',
			'level',
			'action_ok',
			'action_cancel',
			'action_1',
			'action_2',
			'action_3',
			'created_at',
			'modified_at',
			'expire_at',
		);

		if ( array_intersect( $notification_data_keys, array_keys( $changes ) ) ) {
			if ( ! isset( $changes['modified_at'] ) ) {
				$notification->set_modified_at( current_time( 'mysql', true ) );
			}

			$wpdb->update(
				$notification->get_table_name(),
				array(
					'title'         => $notification->get_title( 'edit' ),
					'description'   => $notification->get_description( 'edit' ),
					'user_id'       => $notification->get_user_id( 'edit' ),
					'created_by'    => $notification->get_created_by( 'edit' ),
					'code'          => $notification->get_code( 'edit' ),
					'status'        => $notification->get_status( 'edit' ),
					'type'          => $notification->get_type( 'edit' ),
					'level'         => $notification->get_level( 'edit' ),
					'action_ok'     => $notification->get_action_ok( 'edit' ),
					'action_cancel' => $notification->get_action_cancel( 'edit' ),
					'action_1'      => $notification->get_action_1( 'edit' ),
					'action_2'      => $notification->get_action_2( 'edit' ),
					'action_3'      => $notification->get_action_3( 'edit' ),
					'created_at'    => gmdate( 'Y-m-d H:i:s', $notification->get_started_at( 'edit' )->getTimestamp() ),
					'modified_at'   => gmdate( 'Y-m-d H:i:s', $notification->get_modified_at( 'edit' )->getTimestamp() ),
					'expire_at'     => gmdate( 'Y-m-d H:i:s', $notification->get_expire_at( 'edit' )->getTimestamp() ),
				),
				array( 'id' => $notification->get_id() )
			);
		}

		$notification->apply_changes();
		$this->clear_cache( $notification );

		/**
		 * Fires after updating a notification.
		 *
		 * @since 1.4.1
		 *
		 * @param integer $id The notification ID.
		 * @param \Masteriyo\Models\Notification $object The notification object.
		 */
		do_action( 'masteriyo_update_notification', $notification->get_id(), $notification );
	}

	/**
	 * Delete a notification.
	 *
	 * @since 1.4.1
	 *
	 * @param Model $notification notification object.
	 * @param array $args Array of args to pass.alert-danger.
	 */
	public function delete( Model &$notification, $args = array() ) {
		global $wpdb;

		if ( $notification->get_id() ) {
			/**
			 * Fires before deleting a notification.
			 *
			 * @since 1.4.1
			 *
			 * @param integer $id The notification ID.
			 */
			do_action( 'masteriyo_before_delete_notification', $notification->get_id() );

			$wpdb->delete( $wpdb->prefix . 'masteriyo_notifications', array( 'id' => $notification->get_id() ) );

			/**
			 * Fires after deleting a notification.
			 *
			 * @since 1.4.1
			 *
			 * @param integer $id The notification ID.
			 */
			do_action( 'masteriyo_delete_notification', $notification->get_id() );

			$this->clear_cache( $notification );
		}
	}

	/**
	 * Clear meta cache.
	 *
	 * @since 1.4.1
	 *
	 * @param Notification $notification Notification object.
	 */
	public function clear_cache( &$notification ) {
		wp_cache_delete( 'item' . $notification->get_id(), 'masteriyo-notification' );
		wp_cache_delete( 'items-' . $notification->get_id(), 'masteriyo-notification' );
	}

	/**
	 * Fetch notifications.
	 *
	 * @since 1.4.1
	 *
	 * @param array $query_vars Query vars.
	 * @param Masteriyo\Query\NotificationQuery $query Notification query object.
	 *
	 * @return \Masteriyo\Models\Notification[]
	 */
	public function query( $query_vars, $query ) {
		global $wpdb;

		$search_criteria = array();
		$sql[]           = "SELECT * FROM {$wpdb->prefix}masteriyo_notifications";

		// Construct where clause.
		if ( ! empty( $query_vars['user_id'] ) ) {
			$search_criteria[] = $wpdb->prepare( 'user_id = %d', $query_vars['user_id'] );
		}

		if ( ! empty( $query_vars['created_by'] ) ) {
			$search_criteria[] = $wpdb->prepare( 'created_by = %d', $query_vars['created_by'] );
		}

		if ( ! empty( $query_vars['code'] ) ) {
			$search_criteria[] = $wpdb->prepare( 'code = %s', $query_vars['code'] );
		}

		if ( ! empty( $query_vars['status'] ) && 'any' !== $query_vars['status'] ) {
			$search_criteria[] = $this->create_sql_in_query( 'status', $query_vars['status'] );
		}

		if ( ! empty( $query_vars['type'] ) ) {
			$search_criteria[] = $this->create_sql_in_query( 'type', $query_vars['type'] );
		}

		if ( ! empty( $query_vars['level'] ) ) {
			$search_criteria[] = $this->create_sql_in_query( 'level', $query_vars['level'] );
		}

		if ( 1 <= count( $search_criteria ) ) {
			$criteria = implode( ' AND ', $search_criteria );
			$sql[]    = 'WHERE ' . $criteria;
		}

		// Construct order and order by part.
		$sql[] = 'ORDER BY ' . sanitize_sql_orderby( $query_vars['orderby'] . ' ' . $query_vars['order'] );

		$query->rows_count = $this->get_rows_count( $sql );

		// Construct limit part.
		$per_page = $query_vars['per_page'];

		if ( $query_vars['page'] > 0 ) {
			$offset = ( $query_vars['page'] - 1 ) * $per_page;
			$sql[]  = $wpdb->prepare( 'LIMIT %d, %d', $offset, $per_page );
		}

		// Generate SQL from the SQL parts.
		$sql = implode( ' ', $sql ) . ';';

		// Fetch the results.
		$notifications = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$ids = wp_list_pluck( $notifications, 'id' );

		$query->found_rows = count( $ids );

		return array_filter( array_map( 'masteriyo_get_notification', $ids ) );
	}

	/**
	 * Get total rows or rows count
	 *
	 * @since 1.4.1
	 *
	 * @param string[] $sql SQL Array.
	 * @return void
	 */
	protected function get_rows_count( $sql ) {
		global $wpdb;

		$sql[0] = "SELECT COUNT(*) FROM {$wpdb->prefix}masteriyo_notifications";

		// Generate SQL from the SQL parts.
		$sql = implode( ' ', $sql ) . ';';

		return absint( $wpdb->get_var( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
}
