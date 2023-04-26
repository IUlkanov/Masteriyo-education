<?php
/**
 * Migrator service provider.
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\Rewrite;

class RewriteServiceProvider extends AbstractServiceProvider {
	/**
	 * The provided array is a way to let the container
	 * know that a service is provided by this service
	 * provider. Every service that is registered via
	 * this service provider must have an alias added
	 * to this array or it will be ignored
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected $provides = array(
		'rewrite',
		'\Masteriyo\Rewrite',
	);

	/**
	 * This is where the magic happens, within the method you can
	 * access the container and register or retrieve anything
	 * that you need to, but remember, every alias registered
	 * within this method must be declared in the `$provides` array.
	 *
	 * @since 1.3.4
	 */
	public function register() {
		$this->getContainer()->add( 'rewrite', Rewrite::class, true );
	}
}
