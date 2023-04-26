<?php
/**
 * Create the application.
 *
 * @since 1.0.0
 */
return array_unique(
	/**
	 * Filters service providers.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $service_providers Service provider classes.
	 */
	apply_filters(
		'masteriyo_service_providers',
		array(
			'Masteriyo\Providers\CacheServiceProvider',
			'Masteriyo\Providers\NoticeServiceProvider',
			'Masteriyo\Providers\CourseServiceProvider',
			'Masteriyo\Providers\PermissionServiceProvider',
			'Masteriyo\Providers\SessionServiceProvider',
			'Masteriyo\Providers\LessonServiceProvider',
			'Masteriyo\Providers\QuizServiceProvider',
			'Masteriyo\Providers\SectionServiceProvider',
			'Masteriyo\Providers\UserServiceProvider',
			'Masteriyo\Providers\OrderServiceProvider',
			'Masteriyo\Providers\CourseTagServiceProvider',
			'Masteriyo\Providers\CourseCategoryServiceProvider',
			'Masteriyo\Providers\CourseDifficultyServiceProvider',
			'Masteriyo\Providers\CartServiceProvider',
			'Masteriyo\Providers\TemplateServiceProvider',
			'Masteriyo\Providers\QuestionServiceProvider',
			'Masteriyo\Providers\ShortcodesServiceProvider',
			'Masteriyo\Providers\SettingsServiceProvider',
			'Masteriyo\Providers\QueriesServiceProvider',
			'Masteriyo\Providers\EmailsServiceProvider',
			'Masteriyo\Providers\CourseReviewServiceProvider',
			'Masteriyo\Providers\CourseQuestionAnswerServiceProvider',
			'Masteriyo\Providers\CountriesServiceProvider',
			'Masteriyo\Providers\CheckoutServiceProvider',
			'Masteriyo\Providers\PaymentGatewaysServiceProvider',
			'Masteriyo\Providers\CourseProgressServiceProvider',
			'Masteriyo\Providers\UserCourseServiceProvider',
			'Masteriyo\Providers\CourseProgressItemServiceProvider',
			'Masteriyo\Providers\FrontendQueryServiceProvider',
			'Masteriyo\Providers\AppServiceProvider',
			'Masteriyo\Providers\BlocksServiceProvider',
			'Masteriyo\Providers\QuizAttemptServiceProvider',
			'Masteriyo\Providers\MigratorServiceProvider',
			'Masteriyo\Providers\NotificationServiceProvider',
			'Masteriyo\Providers\RewriteServiceProvider',
			'Masteriyo\Providers\AjaxServiceProvider',
			'Masteriyo\Providers\ApiServiceProvider',
			'Masteriyo\Providers\UserCoursesServiceProvider',
		)
	)
);
