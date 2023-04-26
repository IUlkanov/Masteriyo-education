<?php
/**
 * Shortcode for listing courses.
 *
 * @since 1.0.6
 * @class CoursesShortcode
 * @package Masteriyo\Shortcodes
 */

namespace Masteriyo\Shortcodes;

use Masteriyo\Abstracts\Shortcode;
use Masteriyo\Query\CourseQuery;

defined( 'ABSPATH' ) || exit;

class CoursesShortcode extends Shortcode {

	/**
	 * Shortcode tag.
	 *
	 * @since 1.0.6
	 *
	 * @var string
	 */
	protected $tag = 'masteriyo_courses';

	/**
	 * Shortcode default attributes.
	 *
	 * @since 1.0.6
	 *
	 * @var array
	 */
	protected $default_attributes = array(
		'count'    => 12,
		'columns'  => 3,
		'category' => null,
	);

	/**
	 * Get shortcode content.
	 *
	 * @since 1.0.6
	 *
	 * @return string
	 */
	public function get_content() {
		$attr         = $this->get_attributes();
		$args         = array(
			'limit'    => absint( $attr['count'] ),
			'order'    => 'DESC',
			'orderby'  => 'date',
			'category' => empty( $attr['category'] ) ? array() : $this->parse_categories_attribute( $attr['category'] ),
		);
		$course_query = new CourseQuery( $args );

		/**
		 * Filters courses that will be displayed in courses shortcode.
		 *
		 * @since 1.0.6
		 *
		 * @param Masteriyo\Models\Course[] $courses The courses objects.
		 */
		$courses = apply_filters( 'masteriyo_shortcode_courses_result', $course_query->get_courses() );

		masteriyo_set_loop_prop( 'columns', absint( $attr['columns'] ) );

		\ob_start();

		echo '<div class="masteriyo-w-100">';

		if ( count( $courses ) > 0 ) {
			$original_course = isset( $GLOBALS['course'] ) ? $GLOBALS['course'] : null;

			/**
			 * Fires before course loop in courses shortcode.
			 *
			 * @since 1.0.6
			 *
			 * @param array $attr Shortcode attributes.
			 * @param \Masteriyo\Models\Course[] $courses The courses objects.
			 */
			do_action( 'masteriyo_shortcode_before_courses_loop', $attr, $courses );

			masteriyo_course_loop_start();

			foreach ( $courses as $course ) {
				$GLOBALS['course'] = $course;

				\masteriyo_get_template_part( 'content', 'course' );
			}

			$GLOBALS['course'] = $original_course;

			masteriyo_course_loop_end();

			/**
			 * Fires after course loop in courses shortcode.
			 *
			 * @since 1.0.6
			 *
			 * @param array $attr Shortcode attributes.
			 * @param \Masteriyo\Models\Course[] $courses The courses objects.
			 */
			do_action( 'masteriyo_shortcode_after_courses_loop', $attr, $courses );

			masteriyo_reset_loop();
		} else {
			/**
			 * Fires when there is no course to display in courses shortcode.
			 *
			 * @since 1.0.6
			 */
			do_action( 'masteriyo_shortcode_no_courses_found' );
		}

		echo '</div>';

		return \ob_get_clean();
	}

	/**
	 * Parse and clean multiple categories separated by comma in a string.
	 *
	 * @since 1.5.11
	 *
	 * @param string $value
	 *
	 * @return string[]
	 */
	protected function parse_categories_attribute( $value ) {
		return array_filter( array_map( 'trim', explode( ',', $value ) ) );
	}
}
