<?php
/**
 * Blocks class.
 *
 * @since 1.3.0
 */

namespace Masteriyo;

use Masteriyo\Query\CourseCategoryQuery;
use Masteriyo\Query\CourseQuery;

defined( 'ABSPATH' ) || exit;

class Blocks {
	/**
	 * Init.
	 *
	 * @since 1.3.0
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 */
	private function init_hooks() {
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			add_filter( 'block_categories_all', array( $this, 'block_categories' ), 999999, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'block_categories' ), 999999, 2 );
		}
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Add "Masteriyo" category to the the blocks listing in post edit screen.
	 *
	 * @since 1.3.0
	 *
	 * @param array $block_categories
	 *
	 * @return array
	 */
	public function block_categories( $block_categories ) {
		array_unshift(
			$block_categories,
			array(
				'slug'  => 'masteriyo',
				'title' => esc_html__( 'Masteriyo LMS', 'masteriyo' ),
			)
		);
		return $block_categories;
	}

	/**
	 * Register all the blocks.
	 *
	 * @since 1.3.0
	 */
	public function register_blocks() {
		$this->register_courses_block();
		$this->register_course_categories_block();
	}

	/**
	 * Register the course categories block.
	 *
	 * @since 1.3.0
	 */
	private function register_course_categories_block() {
		register_block_type(
			'masteriyo/course-categories',
			array(
				'attributes'      => array(
					'clientId'               => array(
						'type'    => 'string',
						'default' => '',
					),
					'count'                  => array(
						'type'    => 'number',
						'default' => 12,
					),
					'columns'                => array(
						'type'    => 'number',
						'default' => 3,
					),
					'hide_courses_count'     => array(
						'type'    => 'string',
						'default' => 'no',
					),
					'include_sub_categories' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
				'style'           => 'masteriyo-public',
				'editor_script'   => 'masteriyo-blocks',
				'editor_style'    => 'masteriyo-public',
				'render_callback' => array( $this, 'render_course_categories_block' ),
			)
		);
	}

	/**
	 * Render callback for the course categories block.
	 *
	 * @since 1.3.0
	 */
	public function render_course_categories_block( $attr ) {
		$columns = absint( $attr['columns'] );

		if ( 0 === $columns ) {
			$columns = 1;
		}
		if ( 4 < $columns ) {
			$columns = 4;
		}
		$attr['columns']    = $columns;
		$categories         = $this->get_categories( $attr );
		$attr['categories'] = $categories;

		\ob_start();

		/**
		 * Fires before rendering course categories in course-categories block.
		 *
		 * @since 1.3.0
		 *
		 * @param array $attr Block attributes.
		 * @param \Masteriyo\Models\CourseCategory[] $categories The course categories objects.
		 */
		do_action( 'masteriyo_blocks_before_course_categories', $attr, $categories );

		masteriyo_get_template( 'shortcodes/course-categories/list.php', $attr );

		/**
		 * Fires after rendering course categories in course-categories block.
		 *
		 * @since 1.3.0
		 *
		 * @param array $attr Block attributes.
		 * @param \Masteriyo\Models\CourseCategory[] $categories The course categories objects.
		 */
		do_action( 'masteriyo_blocks_after_course_categories', $attr, $categories );
		return \ob_get_clean();
	}

	/**
	 * Get categories to display for the shortcode.
	 *
	 * @since 1.3.0
	 *
	 * @return CourseCategory[]
	 */
	protected function get_categories( $attr ) {
		$args = array(
			'order'   => 'ASC',
			'orderby' => 'name',
			'number'  => absint( $attr['count'] ),
		);

		if ( ! masteriyo_string_to_bool( $attr['include_sub_categories'] ) ) {
			$args['parent'] = 0;
		}

		$query      = new CourseCategoryQuery( $args );
		$categories = $query->get_categories();

		/**
		 * Filters categories to display in course categories block.
		 *
		 * @since 1.3.0
		 *
		 * @param \Masteriyo\Models\CourseCategory[] $categories Categories list.
		 * @param mixed[] $args Query args.
		 * @param \Masteriyo\Query\CourseCategoryQuery $query The course category query object.
		 */
		return apply_filters( 'masteriyo_shortcode_course_categories', $categories, $args, $query );
	}

	/**
	 * Register the courses block.
	 *
	 * @since 1.3.0
	 */
	private function register_courses_block() {
		register_block_type(
			'masteriyo/courses',
			array(
				'attributes'      => array(
					'clientId'                => array(
						'type'    => 'string',
						'default' => '',
					),
					'count'                   => array(
						'type'    => 'number',
						'default' => 12,
					),
					'columns'                 => array(
						'type'    => 'number',
						'default' => 3,
					),
					'categoryIds'             => array(
						'type'    => 'array',
						'default' => array(),
					),
					'sortBy'                  => array(
						'type'    => 'string',
						'default' => 'date',
					),
					'sortOrder'               => array(
						'type'    => 'string',
						'default' => 'desc',
					),
					'startCourseButtonBorder' => array(
						'type' => 'object',
					),
				),
				'style'           => 'masteriyo-public',
				'editor_script'   => 'masteriyo-blocks',
				'editor_style'    => 'masteriyo-public',
				'render_callback' => array( $this, 'render_courses_block' ),
			)
		);
	}

	/**
	 * Render callback for the courses block.
	 *
	 * @since 1.3.0
	 */
	public function render_courses_block( $attr ) {
		$args         = array(
			'limit'    => absint( $attr['count'] ),
			'order'    => 'DESC',
			'orderby'  => 'date',
			'category' => empty( $attr['categoryIds'] ) ? null : $attr['categoryIds'],
		);
		$course_query = new CourseQuery( $args );
		$client_id    = (string) $attr['clientId'];

		/**
		 * Filters courses to display in courses block.
		 *
		 * @since 1.3.0
		 *
		 * @param \Masteriyo\Models\Course[]|\Masteriyo\Models\Course $courses Single course object or list of course objects.
		 */
		$courses = apply_filters( 'masteriyo_shortcode_courses_result', $course_query->get_courses() );

		masteriyo_set_loop_prop( 'columns', absint( $attr['columns'] ) );

		\ob_start();

		printf( '<div class="masteriyo-block masteriyo-block-%s masteriyo-courses-block">', esc_attr( $client_id ) );

		if ( count( $courses ) > 0 ) {
			$original_course = isset( $GLOBALS['course'] ) ? $GLOBALS['course'] : null;

			/**
			 * Fires before rendering courses in the courses block.
			 *
			 * @since 1.3.0
			 *
			 * @param array $attr The courses block attributes.
			 * @param \Masteriyo\Models\Course[] $courses The course objects.
			 */
			do_action( 'masteriyo_blocks_before_courses_loop', $attr, $courses );
			masteriyo_course_loop_start();

			foreach ( $courses as $course ) {
				$GLOBALS['course'] = $course;

				\masteriyo_get_template_part( 'content', 'course' );
			}

			$GLOBALS['course'] = $original_course;

			masteriyo_course_loop_end();

			/**
			 * Fires before rendering courses in the courses block.
			 *
			 * @since 1.3.0
			 *
			 * @param array $attr The courses block attributes.
			 * @param \Masteriyo\Models\Course[] $courses The course objects.
			 */
			do_action( 'masteriyo_blocks_after_courses_loop', $attr, $courses );
			masteriyo_reset_loop();
		} else {
			/**
			 * Fires when there are no courses found in the courses block.
			 *
			 * @since 1.3.0
			 */
			do_action( 'masteriyo_blocks_no_courses_found' );
		}
		echo '</div>';

		return \ob_get_clean();
	}
}
