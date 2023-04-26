<?php
/**
 * Order model service provider.
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use Masteriyo\OrderNotes;
use Masteriyo\Models\Order\Order;
use Masteriyo\Models\Order\OrderItem;
use Masteriyo\Repository\OrderRepository;
use Masteriyo\Models\Order\OrderItemCourse;
use Masteriyo\Repository\OrderItemRepository;
use Masteriyo\Repository\OrderItemCourseRepository;
use Masteriyo\RestApi\Controllers\Version1\OrdersController;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\RestApi\Controllers\Version1\OrderItemsController;

class OrderServiceProvider extends AbstractServiceProvider {
	/**
	 * The provided array is a way to let the container
	 * know that a service is provided by this service
	 * provider. Every service that is registered via
	 * this service provider must have an alias added
	 * to this array or it will be ignored
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $provides = array(
		'order',
		'order.store',
		'order.rest',
		'\Masteriyo\RestApi\Controllers\Version1\OrdersController',
		'order-item',
		'order-item.store',
		'order-item.rest',
		'\Masteriyo\RestApi\Controllers\Version1\OrderItemsController',
		'order-item.course',
		'order-item.course.store',
		'order-notes',
	);

	/**
	 * This is where the magic happens, within the method you can
	* access the container and register or retrieve anything
	* that you need to, but remember, every alias registered
	* within this method must be declared in the `$provides` array.
	*
	* @since 1.0.0
	*/
	public function register() {
		$this->getContainer()->add( 'order.store', OrderRepository::class );

		$this->getContainer()->add( 'order.rest', OrdersController::class )
			->addArgument( 'permission' );

		$this->getContainer()->add( '\Masteriyo\RestApi\Controllers\Version1\OrdersController' )
			->addArgument( 'permission' );

		$this->getContainer()->add( 'order', Order::class )
			->addArgument( 'order.store' );

		$this->getContainer()->add( 'order-item.store', OrderItemRepository::class );

		$this->getContainer()->add( 'order-item.course.store', OrderItemCourseRepository::class );

		$this->getContainer()->add( 'order-item.rest', OrderItemsController::class )
			->addArgument( 'permission' );

		$this->getContainer()->add( '\Masteriyo\RestApi\Controllers\Version1\OrderItemsController' )
			->addArgument( 'permission' );

		$this->getContainer()->add( 'order-item', OrderItem::class )
			->addArgument( 'order-item.store' );

		$this->getContainer()->add( 'order-item.course', OrderItemCourse::class )
			->addArgument( 'order-item.course.store' );

		$this->getContainer()->add( 'order-notes', OrderNotes::class, true );
	}
}
