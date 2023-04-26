<?php
/**
 * Course question-answer model service provider.
 */

namespace Masteriyo\Providers;

defined( 'ABSPATH' ) || exit;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Masteriyo\Models\CourseQuestionAnswer;
use Masteriyo\Repository\CourseQuestionAnswerRepository;
use Masteriyo\RestApi\Controllers\Version1\CourseQuestionAnswersController;

class CourseQuestionAnswerServiceProvider extends AbstractServiceProvider {
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
		'course-qa',
		'course-qa.store',
		'course-qa.rest',
		'\Masteriyo\RestApi\Controllers\Version1\CourseQuestionAnswersController',
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
		$this->getContainer()->add( 'course-qa.store', CourseQuestionAnswerRepository::class );

		$this->getContainer()->add( 'course-qa.rest', CourseQuestionAnswersController::class )
		->addArgument( 'permission' );

		$this->getContainer()->add( '\Masteriyo\RestApi\Controllers\Version1\CourseQuestionAnswersController' )
		->addArgument( 'permission' );

		$this->getContainer()->add( 'course-qa', CourseQuestionAnswer::class )
		->addArgument( 'course-qa.store' );
	}
}
