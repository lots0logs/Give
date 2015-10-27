<?php
/**
 *  Give Form Template - User Info Fields
 *
 * @description: This template is used to display the user info fields within Give - Donation Forms
 *               variables passed:
 *                  $form_id
 *
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.3.2
 */

if ( is_user_logged_in() ) :
	$user_data = get_userdata( get_current_user_id() );
endif;

do_action( 'give_purchase_form_before_personal_info', $form_id );
?>
	<fieldset id="give_checkout_user_info">
		<legend><?php echo apply_filters( 'give_checkout_personal_info_text', __( 'Personal Info', 'give' ) ); ?></legend>
		<p id="give-first-name-wrap" class="form-row form-row-first">
			<label class="give-label" for="give-first">
				<?php _e( 'First Name', 'give' ); ?>
				<?php if ( give_field_is_required( 'give_first', $form_id ) ) { ?>
					<span class="give-required-indicator">*</span>
				<?php } ?>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'We will use this to personalize your account experience.', 'give' ); ?>"></span>
			</label>
			<input class="give-input required" type="text" name="give_first" placeholder="<?php _e( 'First name', 'give' ); ?>" id="give-first" value="<?php echo is_user_logged_in() ? $user_data->first_name : ''; ?>"<?php if ( give_field_is_required( 'give_first', $form_id ) ) {
				echo ' required ';
			} ?>/>
		</p>

		<p id="give-last-name-wrap" class="form-row form-row-last">
			<label class="give-label" for="give-last">
				<?php _e( 'Last Name', 'give' ); ?>
				<?php if ( give_field_is_required( 'give_last', $form_id ) ) { ?>
					<span class="give-required-indicator">*</span>
				<?php } ?>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'We will use this as well to personalize your account experience.', 'give' ); ?>"></span>
			</label>

			<input class="give-input<?php if ( give_field_is_required( 'give_last', $form_id ) ) {
				echo ' required';
			} ?>" type="text" name="give_last" id="give-last" placeholder="<?php _e( 'Last name', 'give' ); ?>" value="<?php echo is_user_logged_in() ? $user_data->last_name : ''; ?>"<?php if ( give_field_is_required( 'give_last', $form_id ) ) {
				echo ' required ';
			} ?> />
		</p>

		<?php do_action( 'give_purchase_form_before_email', $form_id ); ?>
		<p id="give-email-wrap" class="form-row form-row-wide">
			<label class="give-label" for="give-email">
				<?php _e( 'Email Address', 'give' ); ?>
				<?php if ( give_field_is_required( 'give_email', $form_id ) ) { ?>
					<span class="give-required-indicator">*</span>
				<?php } ?>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'We will send the purchase receipt to this address.', 'give' ); ?>"></span>
			</label>

			<input class="give-input required" type="email" name="give_email" placeholder="<?php _e( 'Email address', 'give' ); ?>" id="give-email" value="<?php echo is_user_logged_in() ? $user_data->user_email : ''; ?>"<?php if ( give_field_is_required( 'give_email', $form_id ) ) {
				echo ' required ';
			} ?>/>

		</p>
		<?php do_action( 'give_purchase_form_after_email', $form_id ); ?>

		<?php do_action( 'give_purchase_form_user_info', $form_id ); ?>
	</fieldset>
<?php
do_action( 'give_purchase_form_after_personal_info', $form_id );