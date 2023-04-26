<?php
/**
 * Notification service provider.
 *
 * @since 1.4.1
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\Models\Notification;
use Masteriyo\Repository\NotificationRepository;
use Masteriyo\RestApi\Controllers\Version1\NotificationsController;

class NotificationServiceProvider extends AbstractServiceProvider {
	/**
	 * The provided array is a way to let the container
	 * know that a service is provided by this service
	 * provider. Every service that is registered via
	 * this service provider must have an alias added
	 * to this array or it will be ignored
	 *
	 * @since 1.4.1
	 *
	 * @var array
	 */
	protected $provides = array(
		'notification',
		'notification.store',
		'notification.rest',
	);

	/**
	 * This is where the magic happens, within the method you can
	 * access the container and register or retrieve anything
	 * that you need to, but remember, every alias registered
	 * within this method must be declared in the `$provides` array.
	 *
	 * @since 1.4.1
	 */
	public function register() {
		$this->getContainer()->add( 'notification.store', NotificationRepository::class );

		$this->getContainer()->add( 'notification.rest', NotificationsController::class )
			->addArgument( 'permission' );

		$this->getContainer()->add( 'notification', Notification::class )
			->addArgument( 'notification.store' );
	}
}
