<?php

/**
 * Masteriyo billing form.
 *
 * @package Masteriyo\Templates
 * @since 1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

use Masteriyo\Notice;

/**
 * Fires before rendering billing address form in checkout page.
 *
 * @since 1.0.0
 */
do_action( 'masteriyo_checkout_before_billing' );
?>

<div class="masteriyo-checkout-main">
	<h3 class="masteriyo-checkout--title">
		<?php esc_html_e( 'Payment Details', 'masteriyo' ); ?>
	</h3>

	<form action="" class="masteriyo-checkout--form">
		<div class="masteriyo-checkout---fname-lname-wrapper masteriyo-col-2">
			<div class="masteriyo-checkout----fname">
				<label for="billing-first-name" class="masteriyo-label">
					<?php esc_html_e( 'First Name', 'masteriyo' ); ?>
					<span>*</span>
				</label>

				<input
					type="text"
					id="billing-first-name"
					class="masteriyo-input"
					name="billing_first_name"
					value="<?php echo esc_attr( $user->get_first_name() ); ?>"
					/>

				<?php if ( masteriyo_notice_exists( 'billing_first_name', Notice::ERROR ) ) : ?>
					<div class="masteriyo-error masteriyo-danger-msg">
						<?php echo wp_kses_post( masteriyo_notice_by_id( 'billing_first_name', Notice::ERROR ) ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="masteriyo-checkout----lname">
				<label for="billing-last-name" class="masteriyo-label">
					<?php esc_html_e( 'Last Name', 'masteriyo' ); ?>
					<span>*</span>
				</label>

				<input
					type="text"
					id="billing-last-name"
					class="masteriyo-input"
					name="billing_last_name"
					value="<?php echo esc_attr( $user->get_last_name() ); ?>"
					/>

				<?php if ( masteriyo_notice_exists( 'billing_last_name', Notice::ERROR ) ) : ?>
					<div class="masteriyo-error masteriyo-danger-msg">
						<?php echo wp_kses_post( masteriyo_notice_by_id( 'billing_last_name', Notice::ERROR ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="masteriyo-checkout---email-wrapper">
			<div class="masteriyo-checkout----email">
				<label for="billing-email" class="masteriyo-label">
					<?php esc_html_e( 'Email Address', 'masteriyo' ); ?>
					<span>*</span>
				</label>
				<input
					type="text"
					id="billing-email"
					class="masteriyo-input"
					name="billing_email"
					value="<?php echo esc_attr( $user->get_email() ); ?>"
					/>
			</div>
			<?php if ( masteriyo_notice_exists( 'billing_email', Notice::ERROR ) ) : ?>
				<div class="masteriyo-error masteriyo-danger-msg">
					<?php echo wp_kses_post( masteriyo_notice_by_id( 'billing_email', Notice::ERROR ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</form>
</div>
<?php
/**
 * Fires after rendering billing address form in checkout page.
 *
 * @since 1.0.0
 */
do_action( 'masteriyo_checkout_after_billing' );
