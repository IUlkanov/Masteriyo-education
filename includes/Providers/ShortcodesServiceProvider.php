<?php
/**
 * Shortcodes service provider.
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\Shortcodes\CartShortcode;
use Masteriyo\Shortcodes\CheckoutShortcode;
use Masteriyo\Shortcodes\AccountShortcode;

class ShortcodesServiceProvider extends AbstractServiceProvider {
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
		'shortcode.account',
		'shortcode.checkout',
		'shortcode.cart',
		'shortcode.instructor-registration',
		'\Masteriyo\Shortcodes\AccountShortcode',
		'\Masteriyo\Shortcodes\CartShortcode',
		'\Masteriyo\Shortcodes\CheckoutShortcode',
		'\Masteriyo\Shortcodes\InstructorRegistrationShortcode',
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
		$this->getContainer()->add( 'shortcode.account', AccountShortcode::class );
		$this->getContainer()->add( 'shortcode.checkout', CheckoutShortcode::class );
		$this->getContainer()->add( 'shortcode.cart', CartShortcode::class );
		$this->getContainer()->add( 'shortcode.instructor-registration', CartShortcode::class );

		$this->getContainer()->add( '\Masteriyo\Shortcodes\AccountShortcode' );
		$this->getContainer()->add( '\Masteriyo\Shortcodes\CartShortcode' );
		$this->getContainer()->add( '\Masteriyo\Shortcodes\CheckoutShortcode' );
		$this->getContainer()->add( '\Masteriyo\Shortcodes\InstructorRegistrationShortcode' );
	}
}
