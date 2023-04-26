<?php
/**
 * Course progress service provider.
 *
 * @since 1.0.0
 *
 * @package Masteriyo\Providers
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\Models\UserCourse;
use Masteriyo\Repository\UserCourseRepository;
use Masteriyo\RestApi\Controllers\Version1\UserCourseController;

class UserCourseServiceProvider extends AbstractServiceProvider {
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
		'user-course',
		'user-course.store',
		'user-course.rest',
		'\Masteriyo\RestApi\Controllers\Version1\UserCourseController',
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
		$this->getContainer()->add( 'user-course.store', UserCourseRepository::class );

		$this->getContainer()
			->add( 'user-course.rest', UserCourseController::class )
			->addArgument( 'permission' );

		$this->getContainer()
			->add( '\Masteriyo\RestApi\Controllers\Version1\UserCourseController' )
			->addArgument( 'permission' );

		$this->getContainer()
			->add( 'user-course', UserCourse::class )
			->addArgument( 'user-course.store' );
	}
}
