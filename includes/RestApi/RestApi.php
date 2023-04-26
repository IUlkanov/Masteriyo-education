<?php
/**
 * Initialize REST API.
 *
 * @since 1.0.0
 *
 * @package  Masteriyo\RestApi
 */

namespace Masteriyo\RestApi;

defined( 'ABSPATH' ) || exit;

class RestApi {

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static $controllers = array();

	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 */
	public static function register_rest_routes() {
		foreach ( self::get_rest_namespaces() as $namespace => $controllers ) {
			foreach ( $controllers as $controller_name => $controller_class ) {
				self::$controllers[ $namespace ][ $controller_name ] = masteriyo( $controller_class );
				self::$controllers[ $namespace ][ $controller_name ]->register_routes();
			}
		}
	}

	/**
	 * Get API namespaces - new namespaces should be registered here.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of Namespaces and Main controller classes.
	 */
	protected static function get_rest_namespaces() {
		/**
		 * Filters rest API controller classes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $controllers API namespace to API controllers index array.
		 */
		return apply_filters(
			'masteriyo_rest_api_get_rest_namespaces',
			array(
				'masteriyo/v1' => self::get_v1_controllers(),
			)
		);
	}

	/**
	 * List of controllers in the masteriyo/v1 namespace.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @return array
	 */
	protected static function get_v1_controllers() {
		$namespace = '\\Masteriyo\\RestApi\\Controllers\\Version1';

		return array(
			'courses'               => "{$namespace}\\CoursesController",
			'courses.categories'    => "{$namespace}\\CourseCategoriesController",
			'courses.tags'          => "{$namespace}\\CourseTagsController",
			'courses.difficulties'  => "{$namespace}\\CourseDifficultiesController",
			'courses.children'      => "{$namespace}\\CourseChildrenController",
			'lessons'               => "{$namespace}\\LessonsController",
			'questions'             => "{$namespace}\\QuestionsController",
			'quizzes'               => "{$namespace}\\QuizesController",
			'quizzes.attempts'      => "{$namespace}\\QuizAttemptsController",
			'sections'              => "{$namespace}\\SectionsController",
			'sections.children'     => "{$namespace}\\SectionChildrenController",
			'orders'                => "{$namespace}\\OrdersController",
			'orders.items'          => "{$namespace}\\OrderItemsController",
			'users'                 => "{$namespace}\\UsersController",
			'settings'              => "{$namespace}\\SettingsController",
			'courses.reviews'       => "{$namespace}\\CourseReviewsController",
			'courses.qas'           => "{$namespace}\\CourseQuestionAnswersController",
			'courses.builder'       => "{$namespace}\\CourseBuilderController",
			'quizzes.builder'       => "{$namespace}\\QuizBuilderController",
			'course-progress'       => "{$namespace}\\CourseProgressController",
			'course-progress.items' => "{$namespace}\\CourseProgressItemsController",
			'data'                  => "{$namespace}\\DataController",
			'blocks'                => "{$namespace}\\BlocksController",
			'instructors'           => "{$namespace}\\InstructorsController",
			'users.courses'         => "{$namespace}\\UserCoursesController",
			'notifications'         => "{$namespace}\\NotificationsController",
			'pages'                 => "{$namespace}\\PagesController",
		);
	}

	/**
	 * Return the path to the package.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @return string
	 */
	public static function get_path() {
		return dirname( __DIR__ );
	}
}
