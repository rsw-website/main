<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/

function wooc_extra_register_fields() {
    ?>
    <p class="form-row form-row-first">
      <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?>
        <span class="required">*</span>
      </label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?>
          <span class="required">*</span>
        </label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
    <div class="clear"></div>
    <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

/**
* Validate the extra register fields.
*
* @param string $username Current username.
* @param string $email Current email.
* @param object $validation_errors WP_Error object.
*
* @return void
*/

function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
              $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );


/**
* Save the extra register fields.
*
* @param int $customer_id Current customer ID.
*
* @return void
*/

function wooc_save_extra_register_fields( $customer_id ) {
  if ( isset( $_POST['billing_first_name'] ) ) {
      // WordPress default first name field.
      update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
      
      // WooCommerce billing first name.
      update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
  }
  if ( isset( $_POST['billing_last_name'] ) ) {
      // WordPress default last name field.
      update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
      
      // WooCommerce billing last name.
      update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
  }
}

add_action( 'woocommerce_created_customer', 
  'wooc_save_extra_register_fields' );

/**
 * Redirect after registration.
 *
 * @param $redirect
 *
 * @return string
 */

// function custom_register_redirect( $redirect ) {
//     wp_redirect('/client-dashboard');
// }
// add_filter( 'woocommerce_registration_redirect',
//  'custom_register_redirect' );

/**
 * Redirect after login.
 *
 * @param $redirect
 *
 * @return string
 */

// function custom_login_redirect( $redirect ) {
//   wp_redirect('/client-dashboard');
// }
// add_filter( 'woocommerce_login_redirect', 'custom_login_redirect' );

/**
* Create custom footer logo widgit.
*
* @return void
*/

function custom_footer_logo() {
  register_sidebar( array(
    'name'          => 'Footer Logo',
    'id'            => 'footer_logo',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );
}
add_action( 'widgets_init', 'custom_footer_logo' );

/**
* Create custom footer quote widgit.
*
* @return void
*/

function custom_footer_quote() {
  register_sidebar( array(
    'name'          => 'Footer Quote',
    'id'            => 'footer_quote',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );
}
add_action( 'widgets_init', 'custom_footer_quote' );

/**
* Add new custom script file
*
* @return void
*/

function add_custom_theme_scripts() {
  wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array ( 'jquery' ), null, true);
}
add_action( 'wp_enqueue_scripts', 'add_custom_theme_scripts' );

add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( $phpmailer ) {
  $phpmailer->isSMTP();
  $phpmailer->Host       = 'smtp.gmail.com';
  $phpmailer->SMTPAuth   = true;
  $phpmailer->Port       = '587';
  $phpmailer->Username   = 'sachdevaayush.sachdeva39@gmail.com';
  $phpmailer->Password   = '@yush1004';
  $phpmailer->SMTPSecure = 'tls';
  $phpmailer->From       = 'sachdevaayush.sachdeva39@gmail.com';
  $phpmailer->FromName   = 'Reliable Softworks';
}


function user_autologout(){
       if ( is_user_logged_in() ) {
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
                $approved_status = get_user_meta($user_id, 'wp-approve-user', true);
                //if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
        if ( $approved_status == 1 ){
            return $redirect_url;
        }
                else{
            wp_logout();
                        return get_permalink(woocommerce_get_page_id('myaccount')) . "?approved=false";
                }
        }
}
add_action('woocommerce_registration_redirect', 'user_autologout', 2);