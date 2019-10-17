<?php
/**
 * Template Name: Contact Us Template
 * This template file is used for contact pages.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
get_header(); 
/**
 * Instantiate the Avada_Contact class.
 */
$avada_contact = new Avada_Contact();

?>
<section id="content" class="full-width">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // WPCS: XSS ok. ?>
			<?php avada_featured_images_for_pages(); ?>
			<div class="post-content">
				<?php the_content(); ?>
				<div class="contact-form-details">
					<?php if ( ! Avada()->settings->get( 'email_address' ) ) : // Email address not set. ?>
						<?php if ( shortcode_exists( 'fusion_alert' ) ) : ?>
							<?php echo do_shortcode( '[fusion_alert type="error"]' . esc_html__( 'Form email address is not set in Theme Options. Please fill in a valid address to make contact form work.', 'Avada' ) . '[/fusion_alert]' ); ?>
						<?php else : ?>
							<h3 style="color:#b94a48;"><?php esc_html_e( 'Form email address is not set in Theme Options. Please fill in a valid address to make contact form work.', 'Avada' ); ?></h3>
						<?php endif; ?>
						<br />
					<?php endif; ?>

					<?php if ( $avada_contact->has_error ) : // If errors are found. ?>
						<?php if ( shortcode_exists( 'fusion_alert' ) ) : ?>
							<?php echo do_shortcode( '[fusion_alert type="error"]' . esc_html( $avada_contact->error_message ) . '[/fusion_alert]' ); ?>
						<?php else : ?>
							<h3 style="color:#b94a48;"><?php echo esc_html( $avada_contact->error_message ); ?></h3>
						<?php endif; ?>
						<br />
					<?php endif; ?>

					<?php if ( $avada_contact->email_sent && Avada()->settings->get( 'email_address' ) ) : // If email is sent. ?>
						<?php if ( shortcode_exists( 'fusion_alert' ) ) : ?>
							<?php /* translators: The name from the contact form. */ ?>
							<?php echo do_shortcode( '[fusion_alert type="success"]' . sprintf( __( 'Thank you %s for using our contact form! Your email was successfully sent!', 'Avada' ), '<strong>' . $avada_contact->name . '</strong>' ) . '[/fusion_alert]' ); ?>
						<?php else : ?>
							<?php /* translators: The name from the contact form. */ ?>
							<h3 style="color:#468847;"><?php printf( esc_html__( 'Thank you %s for using our contact form! Your email was successfully sent!', 'Avada' ), '<strong>' . esc_html( $avada_contact->name ) . '</strong>' ); ?></h3>
						<?php endif; ?>
						<br />
					<?php endif; ?>
				</div>
			</div>
			<form action="" method="post" class="avada-contact-form contact-form-details">
					<?php if ( 'above' === Avada()->settings->get( 'contact_comment_position' ) ) : ?>
					<?php endif; ?>
					<div class="custom-form-row">
						<div class="custom-form-col">
							<label>Name<span class="required">*</span></label>
							<div class="custom-form-holder">
								<input type="text" name="contact_name" id="author" value="<?php echo esc_attr( $avada_contact->name ); ?>" size="22" required aria-required="true" aria-label="<?php esc_attr_e( 'Name (required)', 'Avada' ); ?>" class="custom-form-control input-name">
							</div>
						</div>
						<div class="custom-form-col">
							<label>Email<span class="required">*</span></label>
							<div class="custom-form-holder">
								<input type="email" name="email" id="email" value="<?php echo esc_attr( $avada_contact->email ); ?>" size="22" required aria-required="true" aria-label="<?php esc_attr_e( 'Email (required)', 'Avada' ); ?>" class="custom-form-control input-email">
							</div>
						</div>
					</div>
					<div class="custom-form-row">
						<div class="custom-form-col">
							<label>Company<span class="required">*</span></label>
							<div class="custom-form-holder">
								<input type="text" name="company" id="company" value="<?php echo esc_attr( $avada_contact->company ); ?>" aria-label="<?php esc_attr_e( 'Company', 'Avada' ); ?>" size="22" class="custom-form-control input-website">
							</div>
						</div>
						<div class="custom-form-col">
							<label>Number of Employees<span class="required">*</span></label>
							<div class="custom-form-holder">
								<input type="number" name="number_of_employees" id="number_of_employees" min="1" value="<?php echo esc_attr( $avada_contact->number_of_employees ); ?>" aria-label="<?php esc_attr_e( 'Number of Employees', 'Avada' ); ?>" size="22" class="custom-form-control input-website">
							</div>
						</div>
					</div>
					<div class="custom-form-row">
						<div class="custom-form-col">
							<label>Phone<span class="required">*</span></label>
							<div class="custom-form-holder">
								<input type="text" name="url" id="url" maxlength="10" value="<?php echo esc_attr( $avada_contact->phone ); ?>" aria-label="<?php esc_attr_e( 'Phone', 'Avada' ); ?>" size="22" class="custom-form-control input-website isPhoneNumber">
							</div>
						</div>
						<div class="custom-form-col">
							<label>City</label>
							<div class="custom-form-holder">
								<input type="text" name="city" id="city" value="<?php echo esc_attr( $avada_contact->city ); ?>" aria-label="<?php esc_attr_e( 'City', 'Avada' ); ?>" size="22" class="custom-form-control input-website">
							</div>
						</div>
					</div>
					<div class="custom-form-row">
						<div class="custom-form-col">
							<label>Zip</label>
							<div class="custom-form-holder">
								<input type="text" name="zip" id="zip" maxlength="5" value="<?php echo esc_attr( $avada_contact->zip ); ?>" aria-label="<?php esc_attr_e( 'Zip', 'Avada' ); ?>" size="22" class="custom-form-control input-website">
							</div>
						</div>
						<div class="custom-form-col">
							<label>State</label>
							<div class="custom-form-holder">
								<select name="state" id="state" class="custom-form-control">
		                            <option value="AL" selected="selected">AL</option>
		                            <option value="AK">AK</option>
		                            <option value="AZ">AZ</option>
		                            <option value="AR">AR</option>
		                            <option value="CA">CA</option>
		                            <option value="CO">CO</option>
		                            <option value="CT">CT</option>
		                            <option value="DE">DE</option>
		                            <option value="DC">DC</option>
		                            <option value="FL">FL</option>
		                            <option value="GA">GA</option>
		                            <option value="HI">HI</option>
		                            <option value="ID">ID</option>
		                            <option value="IL">IL</option>
		                            <option value="IN">IN</option>
		                            <option value="IA">IA</option>
		                            <option value="KS">KS</option>
		                            <option value="KY">KY</option>
		                            <option value="LA">LA</option>
		                            <option value="ME">ME</option>
		                            <option value="MD">MD</option>
		                            <option value="MA">MA</option>
		                            <option value="MI">MI</option>
		                            <option value="MN">MN</option>
		                            <option value="MS">MS</option>
		                            <option value="MO">MO</option>
		                            <option value="MT">MT</option>
		                            <option value="NE">NE</option>
		                            <option value="NV">NV</option>
		                            <option value="NH">NH</option>
		                            <option value="NJ">NJ</option>
		                            <option value="NM">NM</option>
		                            <option value="NY">NY</option>
		                            <option value="NC">NC</option>
		                            <option value="ND">ND</option>
		                            <option value="OH">OH</option>
		                            <option value="OK">OK</option>
		                            <option value="OR">OR</option>
		                            <option value="PA">PA</option>
		                            <option value="RI">RI</option>
		                            <option value="SC">SC</option>
		                            <option value="SD">SD</option>
		                            <option value="TN">TN</option>
		                            <option value="TX">TX</option>
		                            <option value="UT">UT</option>
		                            <option value="VT">VT</option>
		                            <option value="VA">VA</option>
		                            <option value="WA">WA</option>
		                            <option value="WV">WV</option>
		                            <option value="WI">WI</option>
		                            <option value="WY">WY</option>
                            	</select>
							</div>
						</div>
					</div>
					<div class="custom-form-row">
						<div class="custom-form-col custom-form-area">
							<label>Areas of Interest</label>
							<textarea name="msg" id="comment" cols="39" rows="4" class="" aria-label="<?php esc_attr_e( 'Message', 'Avada' ); ?>"><?php echo esc_textarea( $avada_contact->message ); // WPCS: CSRF ok. ?></textarea>
						</div>
					</div>
					<div class="custom-form-row button-row">
						<?php
						global $fusion_settings;
						if ( ! $fusion_settings ) {
							$fusion_settings = Fusion_Settings::get_instance();
						}

						$button_shape = $fusion_settings->get( 'button_shape' );
						$button_size  = $fusion_settings->get( 'button_size' );
						$button_type  = $fusion_settings->get( 'button_type' );
						?>
						<?php if ( 'v3' === Avada()->settings->get( 'recaptcha_version' ) ) : ?>
							<input type="hidden" name="fusion-recaptcha-response" id="fusion-recaptcha-response" value="">
						<?php endif; ?>

						<?php if ( Avada()->settings->get( 'recaptcha_public' ) && Avada()->settings->get( 'recaptcha_private' ) ) : ?>
						<div id="comment-recaptcha">
							<?php if ( 'v2' === Avada()->settings->get( 'recaptcha_version' ) ) : ?>
								<div class="g-recaptcha" data-type="audio" data-theme="<?php echo esc_attr( Avada()->settings->get( 'recaptcha_color_scheme' ) ); ?>" data-sitekey="<?php echo esc_attr( Avada()->settings->get( 'recaptcha_public' ) ); ?>"></div>
							<?php else : ?>
								<?php $hide_badge_class   = 'hide' === Avada()->settings->get( 'recaptcha_badge_position' ) ? ' fusion-hide-recaptcha-badge' : ''; ?>
								<div id="recaptcha-container" class="recaptcha-container<?php echo esc_attr( $hide_badge_class ); ?>"></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

						<input name="submit" type="submit" id="submit" value="<?php esc_html_e( 'Submit', 'Avada' ); ?>" class="btn-c2 comment-submit fusion-button fusion-button-default fusion-button-default-size fusion-button-<?php echo esc_attr( strtolower( $button_size ) ); ?> fusion-button-<?php echo esc_attr( strtolower( $button_shape ) ); ?> fusion-button-<?php echo esc_attr( strtolower( $button_type ) ); ?>">
					</div>
					<div id="comment-input">
						
						
						
					</div>

					<?php if ( 'above' !== Avada()->settings->get( 'contact_comment_position' ) ) : ?>
						<div id="comment-textarea" class="fusion-contact-comment-below">
							
						</div>
					<?php endif; ?>

					<?php if ( Avada()->settings->get( 'contact_form_privacy_checkbox' ) ) : ?>
						<div id="comment-privacy-checkbox-wrapper" class="fusion-comment-privacy-checkbox-wrapper">
							<input type="checkbox" value="1" <?php checked( $avada_contact->data_privacy_confirmation, 1 ); ?> required aria-required="true" id="data-privacy-confirmation" name="data_privacy_confirmation" class="fusion-comment-privacy-checkbox" />
							<label for="data-privacy-confirmation"><?php echo Avada()->settings->get( 'contact_form_privacy_label' ); // WPCS: XSS ok. ?></label>
						</div>
					<?php endif; ?>

					<div id="comment-submit-container">
						
					</div>
				</form>
				<?php if ( is_active_sidebar( 'testimonial_widgit' ) ) : ?>
					<div class="fusion-row testimonial-widgit">
						<div class="fusion-columns fusion-columns-1 fusion-widget-area">
							<div class="fusion-column col-lg-12 col-md-12 col-sm-12">
								<div id="partner-logo-widgit" class="primary-sidebar widget-area" role="complementary">
									<?php dynamic_sidebar( 'testimonial_widgit' ); ?>
								</div><!-- #primary-sidebar -->
							</div>
						</div>
					</div>
				<?php endif; ?>
		</div>
	<?php endwhile; ?>
</section>
<?php
get_footer();