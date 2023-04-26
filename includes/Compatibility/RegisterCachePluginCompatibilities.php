<?php
/**
 * Register cache plugin compatibilities.
 *
 * @since 1.5.36
 *
 * @package Masteriyo\Compatibility
 */

namespace Masteriyo\Compatibility;

use Masteriyo\Compatibility\Cache\HummingBird;
use Masteriyo\Compatibility\Cache\LiteSpeed;
use Masteriyo\Traits\Singleton;
use Masteriyo\Compatibility\Cache\W3TotalCache;
use Masteriyo\Compatibility\Cache\WPSuperCache;
use Masteriyo\Compatibility\Cache\WPFastestCache;
use Masteriyo\Compatibility\Cache\WPOptimize;
use Masteriyo\Compatibility\Cache\WPRocket;

class RegisterCachePluginCompatibilities {
	use Singleton;

	/**
	 * Cache plugin compatibilities class.
	 *
	 * @since 1.5.36
	 *
	 * @var array
	 */
	private $cache_plugins = array(
		'w3-total-cache'   => W3TotalCache::class,
		'wp-fastest-cache' => WPFastestCache::class,
		'wp-super-cache'   => WPSuperCache::class,
		'wp-optimize'      => WPOptimize::class,
		'humming-bird'     => HummingBird::class,
		'litespeed'        => LiteSpeed::class,
		'wp-rocket'        => WPRocket::class,
	);

	/**
	 * Register cache plugins.
	 *
	 * @since 1.5.36
	 */
	public function register() {
		if ( ! is_blog_installed() ) {
			return;
		}

		/**
		 * Fires before registering cache plugin compatibilities.
		 *
		 * @since 1.5.36
		 */
		do_action( 'masteriyo_register_cache_plugin_compatibilities' );

		/**
		 * Filters cache plugins classes.
		 *
		 * @since 1.5.36
		 *
		 * @param string[] $cache_plugins Cache plugins classes.
		 */
		$cache_plugins = apply_filters( 'masteriyo_register_cache_plugin_compatibilities', $this->cache_plugins );
		foreach ( $cache_plugins as $key => $class ) {
			$cache_plugin = new $class();
			$cache_plugin->init();
		}

		/**
		 * Fires after registering cache plugin compatibilities.
		 *
		 * @since 1.5.36
		 */
		do_action( 'masteriyo_after_register_cache_plugin_compatibilities' );
	}
}
