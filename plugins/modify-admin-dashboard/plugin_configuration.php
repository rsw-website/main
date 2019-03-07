<?php
/*
Plugin Name: Update wordpress admin dashboard
Description: A plugin to customize wordpress admin dashboard 
Author: Mindfire Solutions
Version: 1.0
*/

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
    if ( isset( $_POST['billing_company'] ) && empty( $_POST['billing_company'] ) ) {
        $validation_errors->add( 'billing_company_error', __( '<strong>Error</strong>: Company name is required!.', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone number is required!.', 'woocommerce' ) );
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
//     wp_redirect('dashboard');
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

function custom_login_redirect( $redirect ) {
  wp_redirect('dashboard');
}
add_filter( 'woocommerce_login_redirect', 'custom_login_redirect' );

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

function new_modify_user_table( $column ) {
    $column['document_access'] = 'Access';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'document_access' :
            $documentAccess = intval(get_user_meta
              ( $user_id, 'document_access', true ));
            if($documentAccess === 1){
                $accessText = 'Granted';
            } elseif ($documentAccess === 2) {
              $accessText = 'Revoked';
            } elseif ($documentAccess === 3) {
              $accessText = 'Pending';
            } else{
              $accessText = '<span aria-hidden="true">—</span>';
            }
            return $accessText;
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { 
  $documentAccess = intval(get_user_meta( $user->ID, 'document_access', true )); ?>
<h3><?php _e("Document Access Permission", "blank"); ?></h3>
  <select name="document_access">
  <option value="0" <?php echo $documentAccess === 0 ? 'selected' : '' ?> ><span aria-hidden="true">—</span></option>
  <option value="1" <?php echo $documentAccess === 1 ? 'selected' : '' ?>>Grant</option>
  <option value="2" <?php echo $documentAccess === 2 ? 'selected' : '' ?>>Revoke</option>
  <option value="3" <?php echo $documentAccess === 3 ? 'selected' : '' ?>>Pending</option>
</select>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {

if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

update_user_meta( $user_id, 'document_access', $_POST['document_access'] );
}

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
                $approved_status = get_user_meta($user_id, 'pw_user_status', true);
                //if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
        if ( $approved_status == 'approved' ){
            return $redirect_url;
        }
                else{
            wp_logout();
                        return get_permalink( get_page_by_path( 'client-registration' ) );
                }
        }
}
add_action('woocommerce_registration_redirect', 'user_autologout', 2);