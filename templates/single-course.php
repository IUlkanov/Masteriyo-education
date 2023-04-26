<?php
/**
 * The Template for displaying single course.
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'masteriyo-course' );

/**
 * Wrapper div opening.
 *
 * @since 1.0.0
 */
echo '<div class="masteriyo-w-100 masteriyo-container">';

/**
 * Fires before rendering single course page template.
 *
 * @since 1.0.0
 */
do_action( 'masteriyo_before_single_course' );

masteriyo_get_template_part( 'content', 'single-course' );

/**
 * Fires after rendering single course page template.
 *
 * @since 1.0.0
 */
do_action( 'masteriyo_after_single_course' );

/**
 * Wrapper div closing.
 *
 * @since 1.0.0
 */
echo '</div>';

get_footer( 'masteriyo-course' );
