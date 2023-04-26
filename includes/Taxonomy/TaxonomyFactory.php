<?php
/**
 * Register post types.
 */

namespace Masteriyo\Taxonomy;

class TaxonomyFactory {

	/**
	 * Post type taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy Post type taxonomy.
	 *
	 * @return Masteriyo\Taxonomy\Taxonomy;
	 */
	public static function create( $taxonomy, $labels = array(), $args = array() ) {
		if ( 'course_cat' === $taxonomy ) {
			return new Course\Category();
		} elseif ( 'course_tag' === $taxonomy ) {
			return new Course\Tag();
		} elseif ( 'course_difficulty' === $taxonomy ) {
			return new Course\Difficulty();
		} elseif ( 'course_visibility' === $taxonomy ) {
			return new Course\Visibility();
		}
	}
}
