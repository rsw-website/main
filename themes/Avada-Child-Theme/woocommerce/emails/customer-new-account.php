<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php /* translators: %s Customer username */ ?>
<?php include_once('get-user-details.php'); ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $first_name ) ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<p><?php printf( __( 'Thanks for creating an account with %1$s. You can access
your account to view tutorials, change your password and more at : %2$s', 'woocommerce' ), esc_html( $blogname ), make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
	<?php /* translators: %s Auto generated password */ ?>
	<p><?php printf( esc_html__( 'Username: %s', 'woocommerce' ), '<strong>' . esc_html( $current_email ) . '</strong>' ); ?></p>
	<p><?php printf( esc_html__( 'Password: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
<?php endif; ?>

<p><?php esc_html_e( 'We look forward to seeing you online soon!', 'woocommerce' ); ?></p>
<p><?php printf( esc_html__( '%s', 'woocommerce' ), esc_html( $blogname ) ); ?></p>
