<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php include_once('get-user-details.php'); ?>
<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $first_name ) ); ?>
<?php /* translators: %s: Store name */ ?>
<p><?php printf( esc_html__( 'Someone has requested a new password for the email account associated with your access to %s.', 'woocommerce' ), esc_html( get_option( 'siteurl' ) ) ); ?></p>
<?php /* translators: %s Customer username */ ?>
<p><?php esc_html_e( 'If you didn\'t make this request, please ignore this email.', 'woocommerce' ); ?></p>
<p><?php esc_html_e( 'If you\'d like to proceed, please', 'woocommerce' ); ?>
	<a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>"><?php // phpcs:ignore ?>
		<?php esc_html_e( 'click here', 'woocommerce' ); ?>
	</a>
	<?php esc_html_e( ' to reset your password', 'woocommerce' ); ?>
</p>
<p><?php esc_html_e( 'Thanks for reading.', 'woocommerce' ); ?></p>
<p><?php esc_html_e( 'Best Regards,', 'woocommerce' ); ?></p>
<p><?php printf( esc_html__( '%s', 'woocommerce' ), esc_html( $blogname ) ); ?></p>

