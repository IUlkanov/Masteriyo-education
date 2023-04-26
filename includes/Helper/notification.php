<?php
/**
 * Notification utilities.
 */

/**
 * Get notification.
 *
 * @since 1.4.1
 *
 * @param int|Masteriyo\Models\Notification $notification Notification ID or object.
 * @return Masteriyo\Models\Notification
 */
function masteriyo_get_notification( $notification ) {
	if ( is_a( $notification, 'Masteriyo\Database\Model' ) ) {
		$id = $notification->get_id();
	} else {
		$id = absint( $notification );
	}

	try {
		$notification_obj = masteriyo( 'notification' );
		$store            = masteriyo( 'notification.store' );

		$notification_obj->set_id( $id );
		$store->read( $notification_obj );
	} catch ( \Exception $e ) {
		$notification_obj = null;
	}

	/**
	 * Filters notification object.
	 *
	 * @since 1.4.1
	 *
	 * @param Masteriyo\Models\Notification $notification_obj The notification object.
	 * @param int|Masteriyo\Models\Notification $notification Notification ID or object.
	 */
	return apply_filters( 'masteriyo_get_notification', $notification_obj, $notification );
}
